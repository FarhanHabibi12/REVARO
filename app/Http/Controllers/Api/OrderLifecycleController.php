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

        if ($user->isPelanggan() && $user->pelanggan) {
            $orders = Order::with(['teknisi', 'transaksi', 'spareparts'])->where('pelanggan_id', $user->pelanggan->id)->latest()->get();
        } elseif ($user->isTeknisi() && $user->teknisi) {
            $orders = Order::with(['pelanggan', 'transaksi', 'spareparts'])->where('teknisi_id', $user->teknisi->id)->latest()->get();
        } elseif ($user->isToko() && $user->toko) {
            $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts'])
                ->whereHas('spareparts', function ($query) use ($user) {
                    $query->where('toko_id', $user->toko->id);
                })->latest()->get();
        } else {
            $orders = Order::with(['pelanggan', 'teknisi', 'transaksi', 'spareparts'])->latest()->get();
        }

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * List available/searching orders for technicians in radius.
     */
    public function available(Request $request)
    {
        $user = $request->user();
        if (!$user->isTeknisi() || !$user->teknisi) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Fetch searching orders in status 'SEARCHING'
        $orders = Order::with(['pelanggan', 'transaksi'])
            ->where('status', 'SEARCHING')
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Show detailed order by ID.
     */
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if ($user->isPelanggan() && $order->pelanggan_id !== $user->pelanggan?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        if ($user->isTeknisi() && $order->teknisi_id !== $user->teknisi?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'order' => $order->load(['pelanggan', 'teknisi', 'transaksi', 'spareparts'])
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

        $order = Order::create([
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
        ]);

        // Trigger asynchronous dispatch job
        DispatchOrderJob::dispatch($order->id);

        return response()->json([
            'message' => 'SOS berhasil diajukan. Sistem sedang mencari teknisi terdekat...',
            'order' => $order
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

        if ($teknisi->status !== 'ACTIVE') {
            return response()->json(['message' => 'Profil Teknisi Anda belum aktif.'], 403);
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
                'order' => $lockedOrder
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
                'status' => 'PENDING_SHOP', // Sent to Shop for confirmation
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

        return DB::transaction(function () use ($order, $transaksi, $pelanggan) {
            // Deduct customer balance
            $pelanggan->decrement('saldo', $transaksi->total);

            // Update transaction payment & escrow status (LANGSUNG RELEASE)
            $transaksi->update([
                'status_pembayaran' => 'PAID',
                'status_escrow' => 'RELEASED', // Dana langsung cair
                'paid_at' => now(),
            ]);

            $order->update(['status' => 'COMPLETED']);

            // Set technician back to available
            $order->teknisi->update(['tersedia' => true]);

            // Disperse service fee to technician (LANGSUNG MASUK)
            $teknisi = $order->teknisi;
            if ($teknisi) {
                $teknisi->increment('saldo', $transaksi->biaya_jasa);
                
                Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul' => 'Pembayaran & Pencairan Diterima',
                    'pesan' => "Pelanggan telah membayar order {$order->kode_order}. Dana Rp " . number_format($transaksi->biaya_jasa, 0, ',', '.') . " langsung masuk ke saldo Anda.",
                    'dibaca' => false,
                ]);
            }

            // Disperse sparepart subtotals to shop(s) (LANGSUNG MASUK)
            $shopPayments = [];
            foreach ($order->spareparts as $sparepart) {
                if ($sparepart->pivot->status === 'CONFIRMED') {
                    $shopPayments[$sparepart->toko_id] = ($shopPayments[$sparepart->toko_id] ?? 0) + $sparepart->pivot->subtotal;
                }
            }

            foreach ($shopPayments as $tokoId => $amount) {
                $toko = \App\Models\Toko::find($tokoId);
                if ($toko) {
                    $toko->increment('saldo', $amount);

                    Notifikasi::create([
                        'user_id' => $toko->user_id,
                        'judul' => 'Pencairan Hasil Penjualan',
                        'pesan' => "Dana Rp " . number_format($amount, 0, ',', '.') . " hasil penjualan sparepart order {$order->kode_order} telah masuk ke saldo.",
                        'dibaca' => false,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Pembayaran berhasil. Dana langsung diteruskan ke Teknisi dan Toko (jika ada).',
                'transaksi' => $transaksi
            ]);
        });
    }

    /**
     * Customer confirms service completion.
     * Escrow RELEASED, wallets updated.
     */
    public function confirmRelease(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->pelanggan_id !== $user->pelanggan?->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $transaksi = $order->transaksi;

        if (!$transaksi || $transaksi->status_escrow !== 'HELD') {
            return response()->json(['message' => 'Transaksi tidak berada di escrow HELD.'], 400);
        }

        return DB::transaction(function () use ($order, $transaksi) {
            $transaksi->update(['status_escrow' => 'RELEASED']);

            // Disperse service fee to technician
            $teknisi = $order->teknisi;
            if ($teknisi) {
                $teknisi->increment('saldo', $transaksi->biaya_jasa);
                
                Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul' => 'Pencairan Jasa Servis',
                    'pesan' => "Dana Rp " . number_format($transaksi->biaya_jasa, 0, ',', '.') . " untuk order {$order->kode_order} telah dicairkan ke dompet Anda.",
                    'dibaca' => false,
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
                    $toko->increment('saldo', $amount);

                    Notifikasi::create([
                        'user_id' => $toko->user_id,
                        'judul' => 'Pencairan Hasil Penjualan',
                        'pesan' => "Dana Rp " . number_format($amount, 0, ',', '.') . " hasil penjualan alat untuk order {$order->kode_order} telah dicairkan.",
                        'dibaca' => false,
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
}
