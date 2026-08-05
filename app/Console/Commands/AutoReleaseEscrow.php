<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoReleaseEscrow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revaro:auto-release-escrow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically release escrow funds held for more than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for escrow transactions to release...');

        // Find transactions PAID and HELD where paid_at is older than 24 hours
        $threshold = Carbon::now()->subHours(24);
        $transactions = Transaksi::where('status_pembayaran', 'PAID')
            ->where('status_escrow', 'HELD')
            ->where('paid_at', '<=', $threshold)
            ->get();

        $releasedCount = 0;

        foreach ($transactions as $transaksi) {
            DB::transaction(function () use ($transaksi, &$releasedCount) {
                $order = $transaksi->order;
                if (!$order) {
                    return;
                }

                // 1. Release escrow state
                $transaksi->update(['status_escrow' => 'RELEASED']);

                // 2. Transfer service fee to Technician
                $teknisi = $order->teknisi;
                if ($teknisi) {
                    $teknisi->increment('saldo', $transaksi->biaya_jasa);

                    Notifikasi::create([
                        'user_id' => $teknisi->user_id,
                        'judul' => 'Pencairan Dana Escrow',
                        'pesan' => "Dana jasa servis sebesar Rp " . number_format($transaksi->biaya_jasa, 0, ',', '.') . " untuk order {$order->kode_order} telah dicairkan ke saldo Anda.",
                        'dibaca' => false,
                    ]);
                }

                // 3. Transfer sparepart costs to Shop(s)
                // Group by shop to aggregate payments if multiple parts from same shop
                $shopPayments = [];
                foreach ($order->spareparts as $sparepart) {
                    if ($sparepart->pivot->status === 'CONFIRMED') {
                        $tokoId = $sparepart->toko_id;
                        $subtotal = $sparepart->pivot->subtotal;
                        $shopPayments[$tokoId] = ($shopPayments[$tokoId] ?? 0) + $subtotal;
                    }
                }

                foreach ($shopPayments as $tokoId => $amount) {
                    $toko = \App\Models\Toko::find($tokoId);
                    if ($toko) {
                        $toko->increment('saldo', $amount);

                        Notifikasi::create([
                            'user_id' => $toko->user_id,
                            'judul' => 'Pencairan Dana Sparepart',
                            'pesan' => "Dana penjualan suku cadang sebesar Rp " . number_format($amount, 0, ',', '.') . " untuk order {$order->kode_order} telah dicairkan ke saldo Anda.",
                            'dibaca' => false,
                        ]);
                    }
                }

                // 4. Activity Log
                ActivityLog::create([
                    'user_id' => null, // System action
                    'aktivitas' => "Pencairan dana escrow otomatis (auto-release) berhasil dilakukan untuk order: {$order->kode_order}",
                ]);

                $releasedCount++;
            });
        }

        $this->info("Completed. Auto-released {$releasedCount} transactions.");
    }
}
