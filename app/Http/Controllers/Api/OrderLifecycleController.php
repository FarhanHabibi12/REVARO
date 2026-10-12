<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchOrderJob;
use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\PelanggaranLog;
use App\Models\Sparepart;
use App\Models\Toko;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderLifecycleController extends Controller
{
    /**
     * List orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = collect();
        $roleParam = strtolower($request->query('role', $request->query('as', '')));
        $referer = strtolower($request->header('Referer', ''));

        if ($roleParam === 'teknisi' || (!$roleParam && str_contains($referer, '/teknisi'))) {
            if ($user->teknisi) {
                $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->where('teknisi_id', $user->teknisi->id)
                    ->latest()
                    ->get();
            }
        } elseif ($roleParam === 'toko' || (!$roleParam && str_contains($referer, '/toko'))) {
            if ($user->toko) {
                $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->whereHas('spareparts', function ($query) use ($user) {
                        $query->where('toko_id', $user->toko->id);
                    })->latest()->get();
            }
        } elseif ($roleParam === 'pelanggan' || (!$roleParam && str_contains($referer, '/pelanggan'))) {
            if ($user->pelanggan) {
                $orders = Order::with(['teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->where('pelanggan_id', $user->pelanggan->id)
                    ->latest()
                    ->get();
            }
        } else {
            // General multi-role fallback based on referrer or profile
            if ($user->teknisi && str_contains($referer, 'teknisi')) {
                $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->where('teknisi_id', $user->teknisi->id)
                    ->latest()
                    ->get();
            } elseif ($user->pelanggan) {
                $orders = Order::with(['teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->where('pelanggan_id', $user->pelanggan->id)
                    ->latest()
                    ->get();
            } elseif ($user->teknisi) {
                $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->where('teknisi_id', $user->teknisi->id)
                    ->latest()
                    ->get();
            } elseif ($user->toko) {
                $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko', 'rating'])
                    ->whereHas('spareparts', function ($query) use ($user) {
                        $query->where('toko_id', $user->toko->id);
                    })->latest()->get();
            } else {
                $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko', 'rating'])->latest()->get();
            }
        }

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * List available/searching orders for technicians in radius matching their specialization.
     */
    public function available(Request $request)
    {
        $user = $request->user();
        if (!$user->isTeknisi() || !$user->teknisi) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $teknisi = $user->teknisi;

        // Validasi status verifikasi teknisi: Hanya teknisi yang telah diverifikasi/ACTIVE oleh Admin yang berhak melihat & menerima order
        if ($teknisi->status !== 'ACTIVE' && $teknisi->status !== 'APPROVED') {
            return response()->json([
                'message' => 'Akun kemitraan Teknisi Anda sedang dalam antrean verifikasi atau belum disetujui oleh Admin Verifikator.',
                'orders' => []
            ], 403);
        }

        // Fetch searching orders in status 'SEARCHING'
        $orders = Order::with(['pelanggan', 'transaksi'])
            ->where('status', 'SEARCHING')
            ->latest()
            ->get();

        // Filter orders sesuai spesialisasi keahlian teknisi
        $techSpecialization = $teknisi->kategori;
        $orders = $orders->filter(function ($ord) use ($techSpecialization) {
            $ordKategori = $ord->kategori;
            return $this->matchSpecialization($techSpecialization, $ordKategori);
        })->values();

        $tLat = $teknisi->latitude;
        $tLon = $teknisi->longitude;

        if ($tLat && $tLon) {
            $orders = $orders->map(function ($ord) use ($tLat, $tLon) {
                if ($ord->latitude && $ord->longitude) {
                    $dLat = deg2rad($ord->latitude - $tLat);
                    $dLon = deg2rad($ord->longitude - $tLon);
                    $a = sin($dLat / 2) * sin($dLat / 2) +
                         cos(deg2rad($tLat)) * cos(deg2rad($ord->latitude)) *
                         sin($dLon / 2) * sin($dLon / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $dist = 6371 * $c; // km
                    $ord->distance_km = round($dist, 1);
                } else {
                    $ord->distance_km = null;
                }
                return $ord;
            });
        }

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Determine if order category matches technician's registered specializations.
     */
    public function matchSpecialization(?string $techKategori, ?string $orderKategori): bool
    {
        if (empty($orderKategori) || empty($techKategori)) {
            return true;
        }

        $techKat = strtolower(trim($techKategori));
        $ordKat = strtolower(trim($orderKategori));

        if ($techKat === $ordKat) {
            return true;
        }

        // Mapping alias kategori umum
        $aliasMap = [
            'ac' => ['ac', 'air conditioner', 'air conditioner (ac)', 'pendingin'],
            'kulkas' => ['kulkas', 'lemari es', 'lemari es / kulkas', 'freezer', 'refrigerator'],
            'mesin cuci' => ['mesin cuci', 'washing machine'],
            'tv' => ['tv', 'audio visual', 'tv & audio visual', 'televisi'],
            'komputer' => ['komputer', 'laptop', 'pc', 'komputer & laptop'],
            'pompa air' => ['pompa air', 'pompa', 'pompa air rumah tangga', 'water pump'],
        ];

        $techKeys = [];
        $orderKeys = [];

        foreach ($aliasMap as $groupKey => $aliases) {
            foreach ($aliases as $alias) {
                if (str_contains($techKat, $alias)) {
                    $techKeys[$groupKey] = true;
                }
                if (str_contains($ordKat, $alias)) {
                    $orderKeys[$groupKey] = true;
                }
            }
        }

        foreach (array_keys($orderKeys) as $oKey) {
            if (isset($techKeys[$oKey])) {
                return true;
            }
        }

        if (str_contains($techKat, $ordKat) || str_contains($ordKat, $techKat)) {
            return true;
        }

        return false;
    }

    /**
     * Show detailed order by ID.
     */
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        $isPelanggan = $user->pelanggan && $order->pelanggan_id === $user->pelanggan->id;
        $isTeknisi = $user->teknisi && $order->teknisi_id === $user->teknisi->id;
        $isToko = $user->toko && $order->spareparts()->where('toko_id', $user->toko->id)->exists();
        $isAdmin = $user->isAdminSuper() || $user->isAdminVerifikator() || $user->isAdminResolusi();

        if (!$isPelanggan && !$isTeknisi && !$isToko && !$isAdmin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'order' => $order->load(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko'])
        ]);
    }

    /**
     * Create SOS order and trigger dispatch.
     */
    public function createSOS(\App\Http\Requests\CreateOrderRequest $request)
    {
        $user = $request->user();

        // Auto-create/fetch Pelanggan profile if missing (allows Teknisi & Toko to place orders)
        $pelanggan = $user->pelanggan;
        if (!$pelanggan) {
            $pelanggan = \App\Models\Pelanggan::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'hp' => $user->no_telp ?? '081234567890',
                    'alamat' => $request->alamat_servis ?? 'Alamat Pemesan',
                    'saldo' => 0.00,
                ]
            );
        }

        $isForOther = (bool) $request->is_for_other;
        $recipientName = $isForOther ? $request->recipient_name : ($request->pemesan_nama ?: $user->nama);
        $recipientPhone = $isForOther ? $request->recipient_phone : ($request->pemesan_hp ?: $pelanggan->hp);
        $recipientAddress = $isForOther ? ($request->recipient_address ?: $request->alamat_servis) : $request->alamat_servis;

        $kodeOrder = 'REV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Check if premium 20km radius feature is selected (default regular: 7km)
        $isPremium = (bool) $request->input('is_premium', false) || (int) $request->input('search_radius', 7) >= 20;
        $maxRadius = $isPremium ? 20 : 7;
        $premiumFee = $isPremium ? (float) \App\Models\SystemSetting::get('maps_premium_fee', 25000) : 0.0;

        $kategoriOrder = $request->input('kategori_servis') ?: ($request->input('kategori') ?: 'Air Conditioner (AC)');

        $orderData = [
            'pelanggan_id' => $pelanggan->id,
            'teknisi_id' => null,
            'kode_order' => $kodeOrder,
            'status' => 'SEARCHING',
            'keluhan' => substr($request->keluhan, 0, 500),
            'pemesan_nama' => $recipientName,
            'pemesan_hp' => $recipientPhone,
            'pemesan_alamat' => $recipientAddress,
            'is_for_other' => $isForOther,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'recipient_address' => $recipientAddress,
            'additional_notes' => $request->additional_notes,
            'formatted_address' => $request->formatted_address ?: $request->alamat_servis,
            'alamat_servis' => $request->alamat_servis,
            'tanggal_servis' => now(),
            'latitude' => $request->latitude ?? -6.2088,
            'longitude' => $request->longitude ?? 106.8456,
            'is_premium' => $isPremium,
            'biaya_prioritas' => $premiumFee,
        ];

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'kategori')) {
                $orderData['kategori'] = $kategoriOrder;
            }
        } catch (\Throwable $e) {}

        try {
            $order = Order::create($orderData);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'kategori') || str_contains($e->getMessage(), 'Unknown column')) {
                unset($orderData['kategori']);
                $order = Order::create($orderData);
            } else {
                throw $e;
            }
        }

        \App\Models\SystemSetting::set('order_kategori_' . $order->id, $kategoriOrder, 'order_meta');

        // Trigger asynchronous dispatch job (initial: 7km, max: 7km or 20km)
        DispatchOrderJob::dispatch($order->id, 7, $maxRadius);

        return response()->json([
            'message' => $isPremium 
                ? 'SOS berhasil diajukan dengan Fitur Premium (Jangkauan Prioritas hingga 20 KM, Biaya Tambahan Rp ' . number_format($premiumFee, 0, ',', '.') . '). Mencari teknisi...' 
                : 'SOS berhasil diajukan (Radius 7 KM). Sistem sedang mencari teknisi terdekat...',
            'order' => $order,
            'is_premium' => $isPremium,
            'biaya_prioritas' => $premiumFee,
            'max_radius_km' => $maxRadius
        ], 201);
    }

    /**
     * Accept a searching order (Technician action).
     * Pessimistic locking handles race conditions.
     */
    public function acceptOrder(Request $request, Order $order)
    {
        $user = $request->user();

        if (!$user->isTeknisi() || !$user->teknisi) {
            return response()->json(['message' => 'Hanya Teknisi yang dapat menerima pesanan.'], 403);
        }

        $teknisi = $user->teknisi;

        if ($teknisi->status !== 'ACTIVE' && $teknisi->status !== 'APPROVED') {
            return response()->json(['message' => 'Profil Teknisi Anda belum aktif atau belum diverifikasi oleh Admin.'], 403);
        }

        if (!$this->matchSpecialization($teknisi->kategori, $order->kategori)) {
            return response()->json(['message' => 'Pesanan ini tidak sesuai dengan bidang spesialisasi keahlian terdaftar Anda.'], 422);
        }

        if (!$teknisi->tersedia) {
            return response()->json(['message' => 'Status Anda sedang tidak tersedia (sedang melayani order lain).'], 400);
        }

        if (!$teknisi->tersedia) {
            return response()->json(['message' => 'Status Anda sedang tidak tersedia (sedang melayani order lain).'], 400);
        }

        return DB::transaction(function () use ($order, $teknisi) {
            // Apply pessimistic lock on the order row
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status !== 'SEARCHING') {
                return response()->json(['message' => 'Order sudah diterima oleh teknisi lain.'], 409);
            }

            $lockedOrder->update([
                'status' => 'DISPATCHED',
                'teknisi_id' => $teknisi->id,
            ]);

            $teknisi->update(['tersedia' => false]);

            Notifikasi::create([
                'user_id' => $lockedOrder->pelanggan->user_id,
                'judul' => 'Teknisi Ditemukan',
                'pesan' => "Teknisi {$teknisi->nama} telah menerima pesanan Anda dan segera menuju ke lokasi Anda.",
                'dibaca' => false,
            ]);

            return response()->json([
                'message' => 'Pesanan berhasil diterima.',
                'order' => $lockedOrder->fresh(['pelanggan', 'teknisi', 'transaksi', 'spareparts.toko'])
            ]);
        });
    }

    /**
     * Start traveling to customer (Technician action).
     */
    public function startOnTheWay(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->teknisi_id !== $user->teknisi?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($order->status !== 'DISPATCHED') {
            return response()->json(['message' => 'Status order tidak valid.'], 400);
        }

        $order->update(['status' => 'ON_THE_WAY']);

        Notifikasi::create([
            'user_id' => $order->pelanggan->user_id,
            'judul' => 'Teknisi Sedang di Jalan',
            'pesan' => "Teknisi {$order->teknisi->nama} sedang dalam perjalanan ke lokasi Anda.",
            'dibaca' => false,
        ]);

        return response()->json([
            'message' => 'Anda sedang dalam perjalanan.',
            'order' => $order
        ]);
    }

    /**
     * Arrive and start diagnosing (Technician action).
     */
    public function arriveAndDiagnose(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->teknisi_id !== $user->teknisi?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($order->status !== 'ON_THE_WAY') {
            return response()->json(['message' => 'Status order tidak valid.'], 400);
        }

        $order->update(['status' => 'IN_PROGRESS']);

        Notifikasi::create([
            'user_id' => $order->pelanggan->user_id,
            'judul' => 'Teknisi Tiba di Lokasi',
            'pesan' => "Teknisi {$order->teknisi->nama} telah tiba di lokasi dan memulai proses perbaikan.",
            'dibaca' => false,
        ]);

        return response()->json([
            'message' => 'Anda telah tiba dan memulai perbaikan.',
            'order' => $order
        ]);
    }

    /**
     * Request spareparts from shops (Technician action).
     */
    public function requestSpareparts(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->teknisi_id !== $user->teknisi?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($order->status !== 'IN_PROGRESS') {
            return response()->json(['message' => 'Tidak dapat mengajukan sparepart di luar status pengerjaan.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'spareparts' => 'required|array',
            'spareparts.*.sparepart_id' => 'required|exists:sparepart,id',
            'spareparts.*.qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Attach parts to order
        $order->spareparts()->detach(); // Clear old requests
        foreach ($request->spareparts as $item) {
            $part = Sparepart::find($item['sparepart_id']);
            $subtotal = $part->harga * $item['qty'];
            
            $order->spareparts()->attach($part->id, [
                'qty' => $item['qty'],
                'harga' => $part->harga,
                'subtotal' => $subtotal,
                'status' => 'PENDING_CUSTOMER', // Sent to Customer for approval first
            ]);
        }

        Notifikasi::create([
            'user_id' => $order->pelanggan->user_id,
            'judul' => 'Persetujuan Suku Cadang',
            'pesan' => "Teknisi mengajukan kebutuhan suku cadang untuk perbaikan Anda. Silakan beri persetujuan.",
            'dibaca' => false,
        ]);

        return response()->json([
            'message' => 'Suku cadang berhasil diajukan. Menunggu persetujuan pelanggan...',
            'order' => $order->load('spareparts')
        ]);
    }

    /**
     * Customer approves or rejects requested spareparts.
     */
    public function approveSpareparts(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->pelanggan_id !== $user->pelanggan?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'approve' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->approve) {
            // Update sparepart status to PENDING_SHOP
            foreach ($order->spareparts as $part) {
                if ($part->pivot->status === 'PENDING_CUSTOMER' || $part->pivot->status === 'PENDING_SHOP') {
                    $order->spareparts()->updateExistingPivot($part->id, ['status' => 'PENDING_SHOP']);
                }
            }
            // Send notification to each shop involved to confirm order
            $shopIds = $order->spareparts->pluck('toko_id')->unique();
            foreach ($shopIds as $tokoId) {
                $toko = Toko::find($tokoId);
                if ($toko) {
                    Notifikasi::create([
                        'user_id' => $toko->user_id,
                        'judul' => 'Pesanan Sparepart Masuk',
                        'pesan' => "Terdapat pesanan suku cadang baru masuk untuk order {$order->kode_order}. Silakan lakukan konfirmasi.",
                        'dibaca' => false,
                    ]);
                }
            }

            Notifikasi::create([
                'user_id' => $order->teknisi->user_id,
                'judul' => 'Suku Cadang Disetujui Pelanggan',
                'pesan' => 'Pelanggan telah menyetujui pengajuan suku cadang. Menunggu konfirmasi ketersediaan barang dari pihak Toko.',
                'dibaca' => false,
            ]);

            return response()->json(['message' => 'Pengajuan disetujui. Menunggu konfirmasi ketersediaan dari Toko Sparepart.']);
        } else {
            // Reject: detach and notify tech
            $order->spareparts()->detach();

            Notifikasi::create([
                'user_id' => $order->teknisi->user_id,
                'judul' => 'Suku Cadang Ditolak Pelanggan',
                'pesan' => 'Pelanggan menolak pengajuan suku cadang Anda.',
                'dibaca' => false,
            ]);

            return response()->json(['message' => 'Pengajuan suku cadang berhasil ditolak.']);
        }
    }

    /**
     * Shop confirms or rejects sparepart order.
     * Deducts stock only when CONFIRMED.
     */
    public function confirmSparepartsByShop(Request $request, Order $order)
    {
        $user = $request->user();

        if (!$user->isToko() || !$user->toko) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $toko = $user->toko;

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:CONFIRMED,REJECTED',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $decision = $request->status;

        return DB::transaction(function () use ($order, $toko, $decision) {
            // Fetch spareparts of this order belonging to this shop
            $shopParts = $order->spareparts()->where('toko_id', $toko->id)->get();

            if ($shopParts->isEmpty()) {
                return response()->json(['message' => 'Tidak ada pesanan sparepart untuk Toko Anda pada order ini.'], 404);
            }

            foreach ($shopParts as $part) {
                // If confirmed, verify stock and deduct
                if ($decision === 'CONFIRMED') {
                    if ($part->stok < $part->pivot->qty) {
                        return response()->json(['message' => "Stok untuk part '{$part->nama}' tidak mencukupi."], 400);
                    }
                    $part->decrement('stok', $part->pivot->qty);
                }

                // Update pivot status
                $order->spareparts()->updateExistingPivot($part->id, ['status' => $decision]);
            }

            if ($decision === 'CONFIRMED') {
                Notifikasi::create([
                    'user_id' => $order->teknisi->user_id,
                    'judul' => 'Sparepart Dikonfirmasi Toko',
                    'pesan' => "Toko {$toko->nama} mengonfirmasi ketersediaan alat dan siap dikirim.",
                    'dibaca' => false,
                ]);
            } else {
                Notifikasi::create([
                    'user_id' => $order->teknisi->user_id,
                    'judul' => 'Sparepart Ditolak Toko',
                    'pesan' => "Toko {$toko->nama} menolak pesanan sparepart karena tidak tersedia. Silakan pilih toko alternatif.",
                    'dibaca' => false,
                ]);
            }

            return response()->json([
                'message' => "Pesanan berhasil di-{$decision} oleh Toko."
            ]);
        });
    }

    /**
     * Technician completes service.
     * Order status changes to AWAITING_PAYMENT, generates unpaid Transaksi.
     */
    public function completeService(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->teknisi_id !== $user->teknisi?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($order->status !== 'IN_PROGRESS') {
            return response()->json(['message' => 'Status order tidak valid.'], 400);
        }

        // Set order status to awaiting payment
        $order->update(['status' => 'AWAITING_PAYMENT']);

        // Calculate bill
        $biayaJasa = 100000.00; // Flat mock fee
        $biayaPrioritas = ($order->is_premium && $order->biaya_prioritas > 0) ? (float) $order->biaya_prioritas : 0.00;
        $biayaJasa += $biayaPrioritas;
        $biayaPart = 0.00;

        foreach ($order->spareparts as $part) {
            if ($part->pivot->status === 'CONFIRMED') {
                $biayaPart += $part->pivot->subtotal;
            }
        }

        $total = $biayaJasa + $biayaPart;

        // Generate Transaction with PAID = UNPAID and ESCROW = NONE
        Transaksi::create([
            'order_id' => $order->id,
            'biaya_jasa' => $biayaJasa,
            'biaya_part' => $biayaPart,
            'total' => $total,
            'metode_pembayaran' => 'WALLET',
            'status_pembayaran' => 'UNPAID',
            'status_escrow' => 'NONE',
        ]);

        Notifikasi::create([
            'user_id' => $order->pelanggan->user_id,
            'judul' => 'Perbaikan Selesai, Menunggu Pembayaran',
            'pesan' => "Pekerjaan perbaikan telah selesai. Total invoice: Rp " . number_format($total, 0, ',', '.') . ". Silakan lakukan pembayaran.",
            'dibaca' => false,
        ]);

        return response()->json([
            'message' => 'Pekerjaan selesai. Menunggu pembayaran dari pelanggan.',
            'order' => $order
        ]);
    }

    /**
     * Customer pays invoice.
     * Wallet balance deducted, status PAID, escrow HELD.
     */
    public function payOrder(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->pelanggan_id !== $user->pelanggan?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($order->status !== 'AWAITING_PAYMENT') {
            return response()->json(['message' => 'Order tidak sedang dalam fase pembayaran.'], 400);
        }

        $transaksi = $order->transaksi;
        if (!$transaksi || $transaksi->status_pembayaran === 'PAID') {
            return response()->json(['message' => 'Tagihan tidak ditemukan atau sudah terbayar.'], 404);
        }

        $pelanggan = $user->pelanggan;

        if ($pelanggan->saldo < $transaksi->total) {
            return response()->json(['message' => 'Saldo dompet tidak mencukupi untuk pembayaran.'], 400);
        }

        return DB::transaction(function () use ($user, $order, $transaksi, $pelanggan) {
            // Deduct customer balance with pessimistic locking
            $user->deductUnifiedSaldo((float) $transaksi->total);

            // Update transaction payment & escrow status (MASUK ESCROW HELD)
            $transaksi->update([
                'status_pembayaran' => 'PAID',
                'status_escrow' => 'HELD', // Dana diamankan di Escrow REVARO
                'paid_at' => now(),
            ]);

            $order->update(['status' => 'COMPLETED']);

            // Set technician back to available
            if ($order->teknisi) {
                $order->teknisi->update(['tersedia' => true]);
            }

            // Catat ledger wallet transaksi keluar untuk pelanggan
            \App\Models\WalletTransaction::create([
                'user_id'   => $user->id,
                'tipe'      => 'OUT',
                'jumlah'    => $transaksi->total,
                'deskripsi' => "Pembayaran order {$order->kode_order} (Escrow HELD)",
            ]);

            // Kirim notifikasi ke pelanggan
            Notifikasi::create([
                'user_id' => $user->id,
                'judul'   => 'Pembayaran Berhasil (Dana di Escrow)',
                'pesan'   => "Pembayaran order {$order->kode_order} sebesar Rp " . number_format($transaksi->total, 0, ',', '.') . " berhasil. Dana Anda diamankan di Escrow REVARO.",
                'dibaca'  => false,
            ]);

            // Kirim notifikasi ke teknisi bahwa pembayaran sudah masuk ke escrow
            if ($order->teknisi) {
                Notifikasi::create([
                    'user_id' => $order->teknisi->user_id,
                    'judul'   => 'Pembayaran Pelanggan Masuk ke Escrow',
                    'pesan'   => "Pelanggan telah membayar order {$order->kode_order}. Dana Rp " . number_format($transaksi->biaya_jasa, 0, ',', '.') . " tersimpan aman di Escrow REVARO dan akan dicairkan saat konfirmasi selesai.",
                    'dibaca'  => false,
                ]);
            }

            return response()->json([
                'message' => 'Pembayaran berhasil. Dana diamankan di Escrow REVARO.',
                'transaksi' => $transaksi
            ]);
        });
    }

    /**
     * Customer confirms service completion.
     * Escrow RELEASED, platform commission 10% deducted, wallets updated.
     */
    public function confirmRelease(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->pelanggan_id !== $user->pelanggan?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $transaksi = $order->transaksi;

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($transaksi->status_escrow === 'RELEASED') {
            return response()->json([
                'message' => 'Dana telah berhasil dicairkan ke pihak teknisi dan toko mitra.',
                'order' => $order->fresh()->load('transaksi')
            ], 200);
        }

        if ($transaksi->status_escrow !== 'HELD') {
            return response()->json(['message' => 'Transaksi tidak berada di escrow HELD.'], 400);
        }

        return DB::transaction(function () use ($order, $transaksi) {
            $transaksi->update(['status_escrow' => 'RELEASED']);

            // Ambil persentase komisi dari KebijakanKomisi (default 10%)
            $kebijakan = \App\Models\KebijakanKomisi::first();
            $persenKomisi = $kebijakan ? (float)$kebijakan->persen_komisi_jasa : 10.0;
            $komisi = round($transaksi->biaya_jasa * ($persenKomisi / 100));
            $teknisiShare = max(0, $transaksi->biaya_jasa - $komisi);

            // Disperse service fee to technician (setelah potongan komisi)
            $teknisi = $order->teknisi;
            if ($teknisi) {
                $teknisiUser = $teknisi->user;
                if ($teknisiUser) {
                    $teknisiUser->addUnifiedSaldo((float) $teknisiShare);
                } else {
                    $teknisi->increment('saldo', $teknisiShare);
                }
                
                \App\Models\WalletTransaction::create([
                    'user_id'   => $teknisi->user_id,
                    'tipe'      => 'IN',
                    'jumlah'    => $teknisiShare,
                    'deskripsi' => "Pencairan jasa servis order {$order->kode_order} (Komisi platform {$persenKomisi}%: Rp " . number_format($komisi, 0, ',', '.') . ")",
                ]);

                Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul'   => 'Pencairan Jasa Servis',
                    'pesan'   => "Dana Rp " . number_format($teknisiShare, 0, ',', '.') . " untuk order {$order->kode_order} telah dicairkan ke dompet Anda (Potongan komisi {$persenKomisi}%: Rp " . number_format($komisi, 0, ',', '.') . ").",
                    'dibaca'  => false,
                ]);
            }

            // Disperse sparepart subtotals to shop(s)
            $shopPayments = [];
            foreach ($order->spareparts as $sparepart) {
                if ($sparepart->pivot->status === 'CONFIRMED') {
                    $shopPayments[$sparepart->toko_id] = ($shopPayments[$sparepart->toko_id] ?? 0) + $sparepart->pivot->subtotal;
                }
            }

            foreach ($shopPayments as $tokoId => $amount) {
                $toko = Toko::find($tokoId);
                if ($toko) {
                    $tokoUser = $toko->user;
                    if ($tokoUser) {
                        $tokoUser->addUnifiedSaldo((float) $amount);
                    } else {
                        $toko->increment('saldo', $amount);
                    }

                    \App\Models\WalletTransaction::create([
                        'user_id'   => $toko->user_id,
                        'tipe'      => 'IN',
                        'jumlah'    => $amount,
                        'deskripsi' => "Pencairan penjualan sparepart order {$order->kode_order}",
                    ]);

                    Notifikasi::create([
                        'user_id' => $toko->user_id,
                        'judul'   => 'Pencairan Hasil Penjualan',
                        'pesan'   => "Dana Rp " . number_format($amount, 0, ',', '.') . " hasil penjualan alat untuk order {$order->kode_order} telah dicairkan.",
                        'dibaca'  => false,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Konfirmasi selesai. Dana escrow telah dicairkan ke masing-masing pihak.'
            ]);
        });
    }

    /**
     * Cancel order.
     */
    public function cancelOrder(Request $request, Order $order)
    {
        $user = $request->user();

        // Access checks
        $isCustomer = $user->isPelanggan() && $user->pelanggan && $order->pelanggan_id === $user->pelanggan->id;
        $isTechnician = $user->isTeknisi() && $user->teknisi && $order->teknisi_id === $user->teknisi->id;

        if (!$isCustomer && !$isTechnician) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if (in_array($order->status, ['COMPLETED', 'CANCELLED', 'AWAITING_PAYMENT'])) {
            return response()->json(['message' => 'Order tidak dapat dibatalkan di fase ini.'], 400);
        }

        return DB::transaction(function () use ($order, $user, $isCustomer, $isTechnician) {
            $oldStatus = $order->status;
            $order->update(['status' => 'CANCELLED']);

            // Release technician availability
            if ($order->teknisi) {
                $order->teknisi->update(['tersedia' => true]);
            }

            if ($isCustomer) {
                // Customer cancelled
                if (in_array($oldStatus, ['ON_THE_WAY', 'IN_PROGRESS'])) {
                    // Compensation fee Rp 20,000 to technician
                    $pelanggan = $user->pelanggan;
                    $compensation = 20000.00;

                    if ($pelanggan->saldo >= $compensation) {
                        $pelanggan->decrement('saldo', $compensation);
                        if ($order->teknisi) {
                            $order->teknisi->increment('saldo', $compensation);

                            Notifikasi::create([
                                'user_id' => $order->teknisi->user_id,
                                'judul' => 'Kompensasi Pembatalan',
                                'pesan' => "Anda menerima biaya kompensasi sebesar Rp " . number_format($compensation, 0, ',', '.') . " karena Pelanggan membatalkan order setelah perjalanan.",
                                'dibaca' => false,
                            ]);
                        }
                    }
                }

                if ($order->teknisi) {
                    Notifikasi::create([
                        'user_id' => $order->teknisi->user_id,
                        'judul' => 'Pesanan Dibatalkan Pelanggan',
                        'pesan' => "Pesanan dengan kode {$order->kode_order} telah dibatalkan oleh Pelanggan.",
                        'dibaca' => false,
                    ]);
                }

                return response()->json(['message' => 'Order berhasil dibatalkan oleh Pelanggan.']);

            } else {
                // Technician cancelled unilaterally -> penalty warning log
                PelanggaranLog::create([
                    'teknisi_id' => $order->teknisi_id,
                    'toko_id' => null,
                    'dispute_id' => null,
                    'jenis_pelanggaran' => 'Pembatalan sepihak setelah pesanan diterima',
                    'tingkat_penalti' => 'WARNING',
                ]);

                Notifikasi::create([
                    'user_id' => $order->pelanggan->user_id,
                    'judul' => 'Order Dibatalkan Teknisi',
                    'pesan' => "Teknisi membatalkan pesanan Anda secara sepihak. Sistem akan mencatat hal ini.",
                    'dibaca' => false,
                ]);

                return response()->json(['message' => 'Order dibatalkan secara sepihak oleh Teknisi. Penalti WARNING tercatat.']);
            }
        });
    }

    /**
     * Submit rating for completed order.
     */
    public function submitRating(Request $request, Order $order)
    {
        $user = $request->user();

        // Customer access check
        if (!$user->isPelanggan() || !$user->pelanggan || $order->pelanggan_id !== $user->pelanggan->id) {
            return response()->json(['message' => 'Hanya Pelanggan pemesan yang dapat memberikan ulasan.'], 403);
        }

        // Validate rating inputs
        $validator = Validator::make($request->all(), [
            'skor' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $skor = (int) $request->skor;
        $komentar = $request->komentar ?: '';

        return DB::transaction(function () use ($order, $user, $skor, $komentar) {
            $teknisi = $order->teknisi;
            if (!$teknisi) {
                return response()->json(['message' => 'Teknisi tidak ditemukan pada pesanan ini.'], 404);
            }

            // Check existing rating
            $existingRating = \App\Models\Rating::where('order_id', $order->id)
                ->where('dari_user_id', $user->id)
                ->first();

            if ($existingRating) {
                return response()->json(['message' => 'Anda sudah memberikan penilaian untuk pesanan ini.'], 400);
            }

            // Create rating
            $rating = \App\Models\Rating::create([
                'order_id' => $order->id,
                'dari_user_id' => $user->id,
                'ke_user_id' => $teknisi->user_id,
                'skor' => $skor,
                'komentar' => $komentar,
            ]);

            // Recalculate average rating of technician
            $avgScore = \App\Models\Rating::where('ke_user_id', $teknisi->user_id)->avg('skor');
            $roundedAvg = round($avgScore, 2);
            $teknisi->update(['rating' => $roundedAvg]);

            // Notification to technician
            Notifikasi::create([
                'user_id' => $teknisi->user_id,
                'judul' => 'Ulasan Baru Diterima',
                'pesan' => "Pelanggan memberikan rating {$skor}/5 untuk order {$order->kode_order}. Rating akumulasi Anda kini {$roundedAvg}.",
                'dibaca' => false,
            ]);

            // Handling bad / severe rating
            $isVeryBad = ($skor <= 2);
            $isCritical = ($skor == 1 || $roundedAvg < 3.0);

            if ($isVeryBad) {
                // Record violation penalty log
                PelanggaranLog::create([
                    'teknisi_id' => $teknisi->id,
                    'toko_id' => null,
                    'dispute_id' => null,
                    'jenis_pelanggaran' => "Rating Buruk dari Pelanggan (Bintang {$skor}/5): " . ($komentar ?: 'Keluhan performa pengerjaan'),
                    'tingkat_penalti' => $isCritical ? 'BERAT' : 'SEDANG',
                ]);

                // Reputation damage
                if ($isCritical) {
                    $teknisi->update(['tier' => 'Bronze']);
                }

                // Escalation to Admin Resolusi
                $adminResolusiRole = \App\Models\Role::where('nama', 'Admin Resolusi')
                    ->orWhere('nama', 'admin-resolusi')
                    ->first();

                $adminResolusi = null;
                if ($adminResolusiRole) {
                    $adminResolusi = \App\Models\User::where('role_id', $adminResolusiRole->id)
                        ->where('is_active', true)
                        ->leftJoin('dispute', 'users.id', '=', 'dispute.admin_id')
                        ->select('users.id')
                        ->selectRaw('count(case when dispute.status != "RESOLVED" then 1 end) as open_disputes_count')
                        ->groupBy('users.id')
                        ->orderBy('open_disputes_count', 'asc')
                        ->orderBy('users.id', 'asc')
                        ->first();
                }

                $dispute = \App\Models\Dispute::create([
                    'order_id' => $order->id,
                    'admin_id' => $adminResolusi ? $adminResolusi->id : null,
                    'status' => 'INVESTIGATING',
                    'alasan' => "Eskalasi Mutu Servis: Rating Buruk ({$skor} Bintang) untuk Teknisi {$teknisi->nama}. Ulasan Pelanggan: \"{$komentar}\"",
                    'sla_deadline' => \Carbon\Carbon::now()->addHours(24),
                ]);

                // Note: Dispute record is created above for Admin Resolusi investigation,
                // while order remains COMPLETED so customer and technician active dashboard screens reset properly.

                // Notify Admin Resolusi
                if ($adminResolusi) {
                    \App\Models\Notifikasi::create([
                        'user_id' => $adminResolusi->id,
                        'judul' => '⚠️ Investigasi Resolusi: Rating Sangat Buruk',
                        'pesan' => "Order {$order->kode_order} menerima rating bintang {$skor} untuk Teknisi {$teknisi->nama}. Kasus otomatis ditugaskan kepada Anda untuk investigasi mutu layanan. Batas SLA: 24 Jam.",
                        'dibaca' => false,
                    ]);
                }

                // Warning notification to technician
                \App\Models\Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul' => '⚠️ Peringatan Kinerja Platform',
                    'pesan' => "Ulasan rendah ({$skor} Bintang) pada order {$order->kode_order} telah dilaporkan ke Tim Admin Resolusi untuk evaluasi mutu layanan.",
                    'dibaca' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil disimpan. Terima kasih atas masukan Anda!',
                'rating' => $rating,
                'teknisi_rating' => $roundedAvg,
            ], 201);
        });
    }
}
