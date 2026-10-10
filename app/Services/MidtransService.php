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
        $this->serverKey = (string) config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $this->clientKey = (string) config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $this->merchantId = (string) config('services.midtrans.merchant_id', env('MIDTRANS_MERCHANT_ID', ''));
        $this->isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
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
     * Buat transaksi Snap Midtrans untuk pembayaran Top-up atau Tagihan.
     *
     * @param array $payload
     * @return array ['token' => ..., 'redirect_url' => ...]
     * @throws \Exception
     */
    public function createSnapTransaction(array $payload): array
    {
        $caPath = 'C:/laragon/etc/ssl/cacert.pem';
        $verify = file_exists($caPath) ? $caPath : (app()->environment('local') ? false : true);
        $url = $this->getSnapBaseUrl();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->withOptions([
            'verify' => $verify,
        ])->timeout(20)->post($url, $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            Log::error('Midtrans Snap Error: ' . $response->body(), ['payload' => $payload]);
            $msg = $errorBody['error_messages'][0] ?? ($errorBody['message'] ?? 'Gagal menghubungi server pembayaran Midtrans.');
            throw new \Exception($msg);
        }

        return $response->json();
    }

    /**
     * Dapatkan URL Core API Midtrans untuk cek status transaksi.
     */
    public function getApiBaseUrl(): string
    {
        return $this->isProduction
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
        $caPath = 'C:/laragon/etc/ssl/cacert.pem';
        $verify = file_exists($caPath) ? $caPath : (app()->environment('local') ? false : true);
        $url = $this->getApiBaseUrl() . '/v2/' . urlencode($orderId) . '/status';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->withOptions([
            'verify' => $verify,
        ])->timeout(15)->get($url);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $msg = $errorBody['status_message'] ?? 'Transaksi belum ditemukan di Midtrans.';
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
        $signatureString = $orderId . $statusCode . $grossAmount . $this->serverKey;
        $expectedSignature = hash('sha512', $signatureString);

        return hash_equals(strtolower($expectedSignature), strtolower($receivedSignature));
    }
}
