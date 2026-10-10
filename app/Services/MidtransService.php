<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected string $merchantId;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = trim((string) config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', '')));
        $this->clientKey = trim((string) config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', '')));
        $this->merchantId = trim((string) config('services.midtrans.merchant_id', env('MIDTRANS_MERCHANT_ID', '')));
        
        $cfgProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        
        // Cerdas mendeteksi environment dari prefix server key jika tersedia
        if (str_starts_with(strtoupper($this->serverKey), 'SB-')) {
            $this->isProduction = false;
        } elseif (str_starts_with(strtoupper($this->serverKey), 'MID-')) {
            $this->isProduction = true;
        } else {
            $this->isProduction = $cfgProduction;
        }
    }

    /**
     * Dapatkan URL Snap API Midtrans berdasarkan environment (Sandbox / Production).
     */
    public function getSnapBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Dapatkan Client Key publik untuk integrasi frontend Snap JS.
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Dapatkan Server Key privat.
     */
    public function getServerKey(): string
    {
        return $this->serverKey;
    }

    /**
     * Jalankan request HTTP ke Midtrans dengan penanganan fallback SSL cURL (aman untuk semua jenis hosting).
     */
    protected function requestWithSslFallback(string $method, string $url, array $payload = [])
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ];
        if (!empty($payload)) {
            $headers['Content-Type'] = 'application/json';
        }

        try {
            // Pertama coba dengan verifikasi SSL standar
            $req = Http::withHeaders($headers)->timeout(15);
            $response = $method === 'post' ? $req->post($url, $payload) : $req->get($url);
            return $response;
        } catch (\Throwable $e) {
            // Jika ada kendala SSL certificate (umum terjadi di cPanel/shared hosting), fallback dengan verify: false
            Log::info("Midtrans HTTP request fallback without SSL verify due to: " . $e->getMessage());
            $req = Http::withHeaders($headers)->withOptions(['verify' => false])->timeout(15);
            return $method === 'post' ? $req->post($url, $payload) : $req->get($url);
        }
    }

    /**
     * Buat transaksi Snap Midtrans untuk pembayaran Top-up atau Tagihan.
     *
     * @param array $payload
     * @return array ['token' => ..., 'redirect_url' => ...]
     * @throws \Exception
     */
    public function createSnapTransaction(array $payload): array
    {
        $url = $this->getSnapBaseUrl();
        $response = $this->requestWithSslFallback('post', $url, $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            Log::error('Midtrans Snap Error: ' . $response->body(), ['payload' => $payload]);
            $msg = $errorBody['error_messages'][0] ?? ($errorBody['message'] ?? 'Gagal membuat transaksi ke Midtrans.');
            throw new \Exception($msg);
        }

        return $response->json();
    }

    /**
     * Dapatkan URL Core API Midtrans untuk cek status transaksi.
     */
    public function getApiBaseUrl(?bool $forceProduction = null): string
    {
        $isProd = $forceProduction !== null ? $forceProduction : $this->isProduction;
        return $isProd
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Cek status transaksi langsung ke server Midtrans API.
     *
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function getTransactionStatus(string $orderId): array
    {
        $url = $this->getApiBaseUrl() . '/v2/' . urlencode($orderId) . '/status';
        $response = $this->requestWithSslFallback('get', $url);

        // Jika 404 atau 401, coba query endpoint alternatif (Sandbox <-> Production)
        if (!$response->successful()) {
            $altUrl = $this->getApiBaseUrl(!$this->isProduction) . '/v2/' . urlencode($orderId) . '/status';
            $altResponse = $this->requestWithSslFallback('get', $altUrl);
            if ($altResponse->successful()) {
                return $altResponse->json();
            }

            $errorBody = $response->json() ?: $altResponse->json();
            $msg = $errorBody['status_message'] ?? 'Status transaksi belum tercatat di Midtrans.';
            Log::warning("Midtrans Check Status ({$orderId}): " . $response->body());
            throw new \Exception($msg);
        }

        return $response->json();
    }

    /**
     * Validasi keaslian webhook signature dari Midtrans:
     * SHA512(order_id + status_code + gross_amount + ServerKey)
     */
    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $receivedSignature): bool
    {
        if (empty($receivedSignature) || empty($this->serverKey)) {
            return false;
        }

        $receivedSignature = strtolower(trim($receivedSignature));

        // Format 1: Gross amount asli dari request
        $sig1 = strtolower(hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey));
        if (hash_equals($sig1, $receivedSignature)) {
            return true;
        }

        // Format 2: Gross amount dengan 2 digit desimal (mis. 50000.00)
        $floatVal = (float) $grossAmount;
        $sig2 = strtolower(hash('sha512', $orderId . $statusCode . number_format($floatVal, 2, '.', '') . $this->serverKey));
        if (hash_equals($sig2, $receivedSignature)) {
            return true;
        }

        // Format 3: Gross amount tanpa desimal (mis. 50000)
        $sig3 = strtolower(hash('sha512', $orderId . $statusCode . number_format($floatVal, 0, '', '') . $this->serverKey));
        if (hash_equals($sig3, $receivedSignature)) {
            return true;
        }

        return false;
    }
}
