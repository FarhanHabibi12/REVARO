<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dispute;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\PelanggaranLog;
use App\Models\Role;
use App\Models\Toko;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class DisputeController extends Controller
{
    /**
     * Customer files a dispute for an order.
     */
    public function createDispute(Request $request)
    {
        $user = $request->user();

        if (!$user->isPelanggan() || !$user->pelanggan) {
            return response()->json(['message' => 'Hanya Pelanggan yang dapat mengajukan sengketa.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'alasan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::find($request->order_id);

        // Verify order ownership
        if ($order->pelanggan_id !== $user->pelanggan->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Verify order status
        if (!in_array($order->status, ['IN_PROGRESS', 'AWAITING_PAYMENT', 'COMPLETED'])) {
            return response()->json(['message' => 'Sengketa hanya dapat diajukan pada order yang aktif atau selesai.'], 400);
        }

        // Verify warranty limit if order is COMPLETED
        if ($order->status === 'COMPLETED') {
            $transaksi = $order->transaksi;
            if ($transaksi && $transaksi->paid_at) {
                // Determine warranty duration in days: AC = 30, others = 7
                $warrantyDays = (stripos($order->keluhan, 'AC') !== false || stripos($order->alamat_servis, 'AC') !== false) ? 30 : 7;
                $daysSincePaid = Carbon::parse($transaksi->paid_at)->diffInDays(Carbon::now());

                if ($daysSincePaid > $warrantyDays) {
                    return response()->json([
                        'message' => "Masa garansi perbaikan ({$warrantyDays} hari) telah kedaluwarsa."
                    ], 400);
                }
            }
        }

        return DB::transaction(function () use ($order, $request) {
            // Create Dispute with INVESTIGATING status and 48-hour SLA
            $dispute = Dispute::create([
                'order_id' => $order->id,
                'alasan' => $request->alasan,
                'status' => 'INVESTIGATING',
                'sla_deadline' => Carbon::now()->addHours(48),
            ]);

            // Load-balanced assignment: find the Admin Resolusi with the lowest open disputes load
            $adminResolusiRole = Role::where('nama', 'Admin Resolusi')->first();
            if ($adminResolusiRole) {
                // Lock row to prevent race conditions during concurrent dispute assignments
                $admin = User::where('role_id', $adminResolusiRole->id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->leftJoin('dispute', 'users.id', '=', 'dispute.admin_id')
                    ->select('users.id')
                    ->selectRaw('count(case when dispute.status != "RESOLVED" then 1 end) as open_disputes_count')
                    ->groupBy('users.id')
                    ->orderBy('open_disputes_count', 'asc')
                    ->orderBy('users.id', 'asc')
                    ->first();

                if ($admin) {
                    $dispute->update([
                        'admin_id' => $admin->id,
                    ]);

                    Notifikasi::create([
                        'user_id' => $admin->id,
                        'judul' => 'Tugas Sengketa Baru',
                        'pesan' => "Sengketa untuk order {$order->kode_order} telah ditugaskan kepada Anda. Batas SLA: " . $dispute->sla_deadline->toDateTimeString(),
                        'dibaca' => false,
                    ]);
                }
            }

            // Update order status
            $order->update(['status' => 'DISPUTED']);

            // Send notification to customer and tech
            Notifikasi::create([
                'user_id' => $order->pelanggan->user_id,
                'judul' => 'Sengketa Berhasil Diajukan',
                'pesan' => "Pengaduan sengketa Anda untuk order {$order->kode_order} berhasil didaftarkan dan sedang ditangani oleh Admin Resolusi.",
                'dibaca' => false,
            ]);

            if ($order->teknisi) {
                Notifikasi::create([
                    'user_id' => $order->teknisi->user_id,
                    'judul' => 'Pesanan Disengketakan',
                    'pesan' => "Pelanggan mengajukan sengketa untuk order {$order->kode_order}. Proses escrow dibekukan.",
                    'dibaca' => false,
                ]);
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'aktivitas' => "Pelanggan mengajukan sengketa untuk order: {$order->kode_order}",
            ]);

            return response()->json([
                'message' => 'Sengketa berhasil diajukan.',
                'dispute' => $dispute->load('admin')
            ], 201);
        });
    }

    /**
     * Retrieve dispute details.
     */
    public function getDisputeDetails(Request $request, Dispute $dispute)
    {
        $user = $request->user();

        // Access checks
        $isCustomer = $user->isPelanggan() && $user->pelanggan && $dispute->order->pelanggan_id === $user->pelanggan->id;
        $isTechnician = $user->isTeknisi() && $user->teknisi && $dispute->order->teknisi_id === $user->teknisi->id;
        $isAssignedAdmin = $user->isAdminResolusi() && $dispute->admin_id === $user->id;
        $isOwner = $user->isSuperAdminPemilik();

        if (!$isCustomer && !$isTechnician && !$isAssignedAdmin && !$isOwner) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json($dispute->load(['order', 'order.pelanggan', 'order.teknisi', 'order.transaksi']));
    }

    /**
     * Admin Resolusi submits a decision.
     */
    public function resolveDispute(Request $request, Dispute $dispute)
    {
        $user = $request->user();

        // Any Admin Resolusi or super admin owner can resolve
        $isAssignedAdmin = $user->isAdminResolusi();
        $isOwner = $user->isSuperAdminPemilik();

        if (!$isAssignedAdmin && !$isOwner) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'keputusan' => 'required|in:REFUND_PELANGGAN,RELEASE_TEKNISI,KUNJUNGAN_ULANG,FREEZE_AKUN',
            'resolusi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = $dispute->order;
        $transaksi = $order->transaksi;
        $decision = $request->keputusan;

        return DB::transaction(function () use ($dispute, $order, $transaksi, $decision, $request, $user) {
            $dispute->update([
                'admin_id' => $user->id,
                'status' => 'RESOLVED',
                'keputusan' => $decision,
                'resolusi' => $request->resolusi,
                'resolved_at' => now(),
            ]);

            if ($decision === 'REFUND_PELANGGAN') {
                $order->update(['status' => 'RESOLVED']);

                if ($transaksi) {
                    $transaksi->update(['status_escrow' => 'REFUNDED']);
                    // Return funds to customer
                    $order->pelanggan->increment('saldo', $transaksi->total);

                    // Potong kembali dari Teknisi (karena bypass escrow langsung cair)
                    $teknisi = $order->teknisi;
                    if ($teknisi && $transaksi->status_pembayaran === 'PAID') {
                        $teknisi->decrement('saldo', $transaksi->biaya_jasa);
                    }

                    // Potong kembali dari Toko (karena bypass escrow langsung cair)
                    if ($transaksi->status_pembayaran === 'PAID') {
                        $shopPayments = [];
                        foreach ($order->spareparts as $sparepart) {
                            if ($sparepart->pivot->status === 'CONFIRMED') {
                                $shopPayments[$sparepart->toko_id] = ($shopPayments[$sparepart->toko_id] ?? 0) + $sparepart->pivot->subtotal;
                            }
                        }
                        foreach ($shopPayments as $tokoId => $amount) {
                            $toko = \App\Models\Toko::find($tokoId);
                            if ($toko) {
                                $toko->decrement('saldo', $amount);
                            }
                        }
                    }
                }

                if ($order->teknisi) {
                    $order->teknisi->update(['tersedia' => true]);

                    Notifikasi::create([
                        'user_id' => $order->teknisi->user_id,
                        'judul' => 'Sengketa Selesai — Pengembalian Dana',
                        'pesan' => "Admin Resolusi memutuskan melakukan pengembalian dana penuh (refund) ke Pelanggan untuk order {$order->kode_order}. Saldo Anda telah dipotong kembali.",
                        'dibaca' => false,
                    ]);
                }

                Notifikasi::create([
                    'user_id' => $order->pelanggan->user_id,
                    'judul' => 'Sengketa Selesai — Dana Dikembalikan',
                    'pesan' => "Pengaduan sengketa untuk order {$order->kode_order} telah selesai. Dana pembayaran telah dikembalikan penuh ke saldo dompet Anda.",
                    'dibaca' => false,
                ]);

            } elseif ($decision === 'RELEASE_TEKNISI') {
                $order->update(['status' => 'RESOLVED']);
                
                if ($transaksi) {
                    $wasHeld = $transaksi->status_escrow !== 'RELEASED';
                    $transaksi->update(['status_escrow' => 'RELEASED']);
                    
                    if ($wasHeld && $order->teknisi) {
                        $order->teknisi->increment('saldo', $transaksi->biaya_jasa);
                    }

                    // Also disburse sparepart payment to Toko if held
                    if ($wasHeld) {
                        $shopPayments = [];
                        foreach ($order->spareparts as $sparepart) {
                            if ($sparepart->pivot->status === 'CONFIRMED') {
                                $shopPayments[$sparepart->toko_id] = ($shopPayments[$sparepart->toko_id] ?? 0) + $sparepart->pivot->subtotal;
                            }
                        }
                        $kebijakan = \App\Models\KebijakanKomisi::first();
                        $persenFee = $kebijakan ? (float)$kebijakan->persen_fee_sparepart : 5.0;
                        foreach ($shopPayments as $tokoId => $subtotal) {
                            $toko = \App\Models\Toko::find($tokoId);
                            if ($toko) {
                                $netAmount = $subtotal - round($subtotal * ($persenFee / 100), 2);
                                $toko->increment('saldo', $netAmount);
                                \App\Models\WalletTransaction::create([
                                    'user_id' => $toko->user_id,
                                    'tipe' => 'IN',
                                    'jumlah' => $netAmount,
                                    'deskripsi' => "Hasil penjualan sparepart order {$order->kode_order}",
                                ]);
                                Notifikasi::create([
                                    'user_id' => $toko->user_id,
                                    'judul' => 'Pencairan Hasil Penjualan',
                                    'pesan' => "Dana Rp " . number_format($netAmount, 0, ',', '.') . " hasil penjualan sparepart order {$order->kode_order} telah masuk ke saldo.",
                                    'dibaca' => false,
                                ]);
                            }
                        }
                    }
                }

                if ($order->teknisi) {
                    $order->teknisi->update(['tersedia' => true]);

                    Notifikasi::create([
                        'user_id' => $order->teknisi->user_id,
                        'judul' => 'Sengketa Selesai — Pencairan Dana',
                        'pesan' => "Admin Resolusi telah menyelesaikan sengketa untuk order {$order->kode_order}. Dana jasa servis berhasil dicairkan ke saldo dompet Anda.",
                        'dibaca' => false,
                    ]);
                }

                Notifikasi::create([
                    'user_id' => $order->pelanggan->user_id,
                    'judul' => 'Sengketa Selesai — Hasil Resolusi Kasus',
                    'pesan' => "Admin Resolusi telah memeriksa bukti dan memutuskan mencairkan dana order {$order->kode_order} kepada Teknisi.",
                    'dibaca' => false,
                ]);

            } elseif ($decision === 'KUNJUNGAN_ULANG') {
                // Return order back to work stage
                $order->update(['status' => 'IN_PROGRESS']);

                if ($order->teknisi) {
                    $order->teknisi->update(['tersedia' => false]);

                    Notifikasi::create([
                        'user_id' => $order->teknisi->user_id,
                        'judul' => 'Sengketa Selesai — Instruksi Kunjungan Ulang',
                        'pesan' => "Admin Resolusi memutuskan Kunjungan Ulang. Anda wajib datang kembali ke lokasi pelanggan untuk menyelesaikan perbaikan order {$order->kode_order} tanpa biaya tambahan.",
                        'dibaca' => false,
                    ]);
                }

                Notifikasi::create([
                    'user_id' => $order->pelanggan->user_id,
                    'judul' => 'Sengketa Selesai — Penjadwalan Servis Ulang',
                    'pesan' => "Mitra Teknisi telah diinstruksikan oleh Admin Resolusi untuk mendatangi lokasi Anda kembali guna menyelesaikan perbaikan order {$order->kode_order} tanpa biaya tambahan.",
                    'dibaca' => false,
                ]);

            } elseif ($decision === 'FREEZE_AKUN') {
                $order->update(['status' => 'RESOLVED']);

                // By default, freeze the technician associated with the disputed order
                $teknisi = $order->teknisi;
                if ($teknisi) {
                    // Penalti bertingkat (auto-freeze jika warning >= 3)
                    // Log the new warning first
                    PelanggaranLog::create([
                        'teknisi_id' => $teknisi->id,
                        'toko_id' => null,
                        'dispute_id' => $dispute->id,
                        'jenis_pelanggaran' => 'Kecurangan / Fraud terbukti pada penyelesaian sengketa',
                        'tingkat_penalti' => 'WARNING',
                    ]);

                    // Count total active warnings for this technician
                    $warningCount = PelanggaranLog::where('teknisi_id', $teknisi->id)
                        ->where('tingkat_penalti', 'WARNING')
                        ->count();

                    if ($warningCount >= 3) {
                        $teknisi->update(['status' => 'FROZEN']);
                        $teknisi->user->update(['is_active' => false]);
                    }

                    // Release technician availability in systems
                    $teknisi->update(['tersedia' => true]);
                }

                // Refund the customer
                if ($transaksi && $transaksi->status_escrow === 'HELD') {
                    $transaksi->update(['status_escrow' => 'REFUNDED']);
                    $order->pelanggan->increment('saldo', $transaksi->total);
                }

                if ($teknisi) {
                    Notifikasi::create([
                        'user_id' => $teknisi->user_id,
                        'judul' => 'Peringatan Pelanggaran Sengketa',
                        'pesan' => "Anda menerima surat peringatan sanksi pelanggaran (WARNING) karena terbukti melakukan kelalaian/kecurangan pada sengketa order {$order->kode_order}. Total peringatan aktif: {$warningCount}.",
                        'dibaca' => false,
                    ]);
                }

                Notifikasi::create([
                    'user_id' => $order->pelanggan->user_id,
                    'judul' => 'Sengketa Selesai — Sanksi Pelanggaran Mitra',
                    'pesan' => "Mitra terbukti melakukan pelanggaran layanan. Akun mitra telah ditindak sanksi, dan seluruh dana perbaikan order {$order->kode_order} telah dikembalikan penuh ke saldo dompet Anda.",
                    'dibaca' => false,
                ]);
            }

            $decisionLabels = [
                'REFUND_PELANGGAN' => 'Pengembalian Dana Penuh (Refund) ke Pelanggan',
                'RELEASE_TEKNISI' => 'Pencairan Dana ke Mitra Teknisi',
                'KUNJUNGAN_ULANG' => 'Instruksi Kunjungan Ulang Perbaikan oleh Teknisi',
                'FREEZE_AKUN' => 'Sanksi Pembekuan Akun Pelaku Pelanggaran',
            ];
            $decisionText = $decisionLabels[$decision] ?? $decision;

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'aktivitas' => "Sengketa untuk order {$order->kode_order} telah diselesaikan dengan keputusan: {$decisionText}.",
            ]);

            return response()->json([
                'message' => "Sengketa berhasil diselesaikan dengan keputusan: {$decisionText}."
            ]);
        });
    }

    /**
     * List all disputes for Admins or Customer's own disputes.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isPelanggan() && $user->pelanggan) {
            $disputes = Dispute::with(['order.pelanggan', 'order.teknisi', 'admin'])
                ->whereHas('order', function ($q) use ($user) {
                    $q->where('pelanggan_id', $user->pelanggan->id);
                })
                ->latest()
                ->get();
            return response()->json($disputes);
        }

        if (!$user->isAdminResolusi() && !$user->isSuperAdminPemilik() && !$user->isSuperAdminMonitoring()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $disputes = Dispute::with(['order.pelanggan', 'order.teknisi', 'admin'])->latest()->get();

        return response()->json($disputes);
    }

    /**
     * Get disputes for Admin Resolusi queue.
     */
    public function myAssigned(Request $request)
    {
        $admin = $request->user();
        $query = Dispute::with(['order.pelanggan', 'order.teknisi', 'order.transaksi'])
            ->where('status', '!=', 'RESOLVED');

        if ($request->query('type') === 'sengketa') {
            $query->where('alasan', 'not like', 'Eskalasi Mutu Servis%');
        } elseif ($request->query('type') === 'rating') {
            $query->where('alasan', 'like', 'Eskalasi Mutu Servis%');
        }

        $disputes = $query->orderByRaw("CASE WHEN admin_id = ? THEN 0 WHEN admin_id IS NULL THEN 1 ELSE 2 END", [$admin->id])
            ->orderBy('sla_deadline', 'asc')
            ->get();
        return response()->json($disputes);
    }

    /**
     * Get rating complaint cases for Admin Resolusi.
     */
    public function ratingEvaluations(Request $request)
    {
        $admin = $request->user();

        if (!$admin->isAdminResolusi() && !$admin->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $cases = Dispute::with(['order.pelanggan', 'order.teknisi.user', 'order.transaksi'])
            ->where('alasan', 'like', 'Eskalasi Mutu Servis%')
            ->orderByRaw("CASE WHEN status != 'RESOLVED' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($dispute) {
                $teknisi = $dispute->order->teknisi ?? null;
                $teknisiId = $teknisi ? $teknisi->id : null;

                $totalBadRatingCases = 0;
                $violationCount = 0;
                $currentStatus = 'UNKNOWN';
                $isUserActive = true;

                if ($teknisi) {
                    $totalBadRatingCases = Dispute::where('alasan', 'like', 'Eskalasi Mutu Servis%')
                        ->whereHas('order', function ($q) use ($teknisiId) {
                            $q->where('teknisi_id', $teknisiId);
                        })
                        ->count();

                    $violationCount = PelanggaranLog::where('teknisi_id', $teknisiId)->count();
                    $currentStatus = $teknisi->status;
                    $isUserActive = $teknisi->user ? $teknisi->user->is_active : true;
                }

                $arr = $dispute->toArray();
                $arr['total_bad_rating_cases'] = $totalBadRatingCases;
                $arr['violation_count'] = $violationCount;
                $arr['teknisi_status'] = $currentStatus;
                $arr['is_banned'] = ($currentStatus === 'FROZEN' || !$isUserActive);
                return $arr;
            });

        return response()->json($cases);
    }

    /**
     * Admin Resolusi action on bad rating complaint.
     */
    public function evaluateRatingCase(Request $request, Dispute $dispute)
    {
        $user = $request->user();

        if (!$user->isAdminResolusi() && !$user->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'keputusan' => 'required|in:BAN_TEKNISI,SURAT_PERINGATAN,TURUNKAN_TIER,SELESAIKAN_EVALUASI',
            'catatan' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = $dispute->order;
        $teknisi = $order ? $order->teknisi : null;

        if (!$teknisi) {
            return response()->json(['message' => 'Teknisi pada order ini tidak ditemukan.'], 404);
        }

        $decision = $request->keputusan;
        $notes = $request->catatan;

        return DB::transaction(function () use ($dispute, $order, $teknisi, $decision, $notes, $user, $request) {
            $message = '';

            if ($decision === 'BAN_TEKNISI') {
                $teknisi->update([
                    'status' => 'FROZEN',
                    'tersedia' => false,
                ]);
                if ($teknisi->user) {
                    $teknisi->user->update(['is_active' => false]);
                    $teknisi->user->tokens()->delete();
                }

                PelanggaranLog::create([
                    'teknisi_id' => $teknisi->id,
                    'toko_id' => null,
                    'dispute_id' => $dispute->id,
                    'jenis_pelanggaran' => 'BANNED / Pembekuan Akun oleh Admin Resolusi akibat akumulasi rating jelek: ' . $notes,
                    'tingkat_penalti' => 'BAN',
                ]);

                Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul' => '🚫 Akun Anda Telah Di-BANNED',
                    'pesan' => "Akun Mitra Teknisi Anda resmi di-BANNED dan dinonaktifkan oleh Admin Resolusi. Catatan: {$notes}",
                    'dibaca' => false,
                ]);

                $message = "Akun Mitra Teknisi {$teknisi->nama} berhasil di-BANNED permanen. Akun dibekukan dan tidak dapat login kembali.";

            } elseif ($decision === 'SURAT_PERINGATAN') {
                PelanggaranLog::create([
                    'teknisi_id' => $teknisi->id,
                    'toko_id' => null,
                    'dispute_id' => $dispute->id,
                    'jenis_pelanggaran' => 'Surat Peringatan (SP) Evaluasi Rating Buruk: ' . $notes,
                    'tingkat_penalti' => 'WARNING',
                ]);

                $warningCount = PelanggaranLog::where('teknisi_id', $teknisi->id)
                    ->where('tingkat_penalti', 'WARNING')
                    ->count();

                if ($warningCount >= 3) {
                    $teknisi->update(['status' => 'FROZEN', 'tersedia' => false]);
                    if ($teknisi->user) {
                        $teknisi->user->update(['is_active' => false]);
                        $teknisi->user->tokens()->delete();
                    }
                    $message = "Surat Peringatan ke-{$warningCount} diterbitkan. Karena mencapai batas 3x pelanggaran, akun teknisi otomatis di-BANNED.";
                } else {
                    $message = "Surat Peringatan (SP) ke-{$warningCount} berhasil diterbitkan untuk Teknisi {$teknisi->nama}.";
                }

                Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul' => '⚠️ Surat Peringatan Kinerja (SP)',
                    'pesan' => "Anda menerima Surat Peringatan atas kualitas layanan order {$order->kode_order}. Total SP: {$warningCount}/3. Catatan: {$notes}",
                    'dibaca' => false,
                ]);

            } elseif ($decision === 'TURUNKAN_TIER') {
                $tiers = ['Gold' => 'Silver', 'Silver' => 'Bronze', 'Bronze' => 'Bronze'];
                $oldTier = $teknisi->tier ?? 'Bronze';
                $newTier = $tiers[$oldTier] ?? 'Bronze';
                $teknisi->update(['tier' => $newTier]);

                PelanggaranLog::create([
                    'teknisi_id' => $teknisi->id,
                    'toko_id' => null,
                    'dispute_id' => $dispute->id,
                    'jenis_pelanggaran' => "Penurunan Tier Kinerja dari {$oldTier} ke {$newTier}",
                    'tingkat_penalti' => 'WARNING',
                ]);

                Notifikasi::create([
                    'user_id' => $teknisi->user_id,
                    'judul' => '📉 Penurunan Tier Kemitraan',
                    'pesan' => "Tier kemitraan Anda diturunkan dari {$oldTier} ke {$newTier} akibat evaluasi ulasan rendah. Catatan: {$notes}",
                    'dibaca' => false,
                ]);

                $message = "Tier teknisi berhasil diturunkan menjadi {$newTier}.";

            } elseif ($decision === 'SELESAIKAN_EVALUASI') {
                $message = "Kasus evaluasi rating berhasil diselesaikan dan diarsipkan.";
            }

            $dispute->update([
                'admin_id' => $user->id,
                'status' => 'RESOLVED',
                'keputusan' => $decision,
                'resolusi' => $notes,
                'resolved_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => "Admin Resolusi menyelesaikan evaluasi mutu/rating order {$order->kode_order} dengan keputusan: {$decision}.",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'dispute' => $dispute->fresh(),
            ]);
        });
    }

    /**
     * Direct Ban/Freeze technician by Admin Resolusi or Super Admin.
     */
    public function banTeknisiDirect(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isAdminResolusi() && !$user->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'alasan' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teknisi = \App\Models\Teknisi::with('user')->find($id);
        if (!$teknisi) {
            return response()->json(['message' => 'Mitra Teknisi tidak ditemukan.'], 404);
        }

        $alasan = $request->alasan;

        DB::transaction(function () use ($teknisi, $alasan, $user, $request) {
            $teknisi->update([
                'status' => 'FROZEN',
                'tersedia' => false,
            ]);

            if ($teknisi->user) {
                $teknisi->user->update(['is_active' => false]);
                $teknisi->user->tokens()->delete();
            }

            PelanggaranLog::create([
                'teknisi_id' => $teknisi->id,
                'toko_id' => null,
                'dispute_id' => null,
                'jenis_pelanggaran' => 'Direct BANNED oleh Admin Resolusi: ' . $alasan,
                'tingkat_penalti' => 'BAN',
            ]);

            Notifikasi::create([
                'user_id' => $teknisi->user_id,
                'judul' => '🚫 Akun Anda Telah Di-BANNED',
                'pesan' => "Akun Mitra Teknisi Anda telah di-BANNED oleh Admin Resolusi. Anda tidak dapat login lagi. Alasan: {$alasan}",
                'dibaca' => false,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => "Admin Resolusi mem-BANNED akun teknisi {$teknisi->nama} (ID: {$teknisi->id}).",
                'ip_address' => $request->ip(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Akun Teknisi {$teknisi->nama} berhasil di-BANNED secara permanen. Pengguna tidak dapat login lagi.",
        ]);
    }

    /**
     * Get violation history logs for a specific technician or shop.
     */
    public function getMitraViolations(Request $request)
    {
        $techId = $request->query('teknisi_id');
        $shopId = $request->query('toko_id');

        $query = PelanggaranLog::query();
        if ($techId) {
            $query->where('teknisi_id', $techId);
        } elseif ($shopId) {
            $query->where('toko_id', $shopId);
        } else {
            return response()->json([]);
        }

        $logs = $query->orderBy('id', 'desc')->get();
        return response()->json($logs);
    }

    /**
     * Get resolved disputes resolved by logged-in resolver admin.
     */
    public function myDecisions(Request $request)
    {
        $admin = $request->user();
        $disputes = Dispute::with(['order.pelanggan', 'order.teknisi'])
            ->where('admin_id', $admin->id)
            ->where('status', 'RESOLVED')
            ->orderBy('resolved_at', 'desc')
            ->get();
        return response()->json($disputes);
    }
}
