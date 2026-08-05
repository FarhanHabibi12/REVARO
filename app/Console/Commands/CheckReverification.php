<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\PendaftaranVerifikasi;
use App\Models\Teknisi;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckReverification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revaro:check-reverification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan active technicians and shops for periodic re-verification expiration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting periodic re-verification check...');

        // 1. Scan active technicians (threshold: 365 days)
        $activeTechnicians = Teknisi::where('status', 'ACTIVE')->get();
        $expiredTechsCount = 0;

        foreach ($activeTechnicians as $tech) {
            $latestVerification = PendaftaranVerifikasi::where('teknisi_id', $tech->id)
                ->where('status', 'APPROVED')
                ->latest('verified_at')
                ->first();

            if ($latestVerification) {
                $verifiedAt = Carbon::parse($latestVerification->verified_at);
                if ($verifiedAt->diffInDays(now()) > 365) {
                    $tech->update(['status' => 'PENDING_REVIEW']);
                    
                    Notifikasi::create([
                        'user_id' => $tech->user_id,
                        'judul' => 'Verifikasi Ulang Berkala',
                        'pesan' => 'Masa berlaku verifikasi Teknisi Anda (12 bulan) telah kedaluwarsa. Profil Anda dikembalikan ke status PENDING_REVIEW. Silakan ajukan ulang dokumen verifikasi.',
                        'dibaca' => false,
                    ]);

                    ActivityLog::create([
                        'user_id' => null, // System action
                        'aktivitas' => "Mengembalikan status Teknisi ID: {$tech->id} (User: {$tech->nama}) ke PENDING_REVIEW karena masa verifikasi habis",
                    ]);

                    $expiredTechsCount++;
                }
            }
        }

        // 2. Scan active shops (threshold: 180 days)
        $activeShops = Toko::where('status', 'ACTIVE')->get();
        $expiredShopsCount = 0;

        foreach ($activeShops as $shop) {
            $latestVerification = PendaftaranVerifikasi::where('toko_id', $shop->id)
                ->where('status', 'APPROVED')
                ->latest('verified_at')
                ->first();

            if ($latestVerification) {
                $verifiedAt = Carbon::parse($latestVerification->verified_at);
                if ($verifiedAt->diffInDays(now()) > 180) {
                    $shop->update(['status' => 'PENDING_REVIEW']);

                    Notifikasi::create([
                        'user_id' => $shop->user_id,
                        'judul' => 'Verifikasi Ulang Berkala',
                        'pesan' => 'Masa berlaku izin legalitas usaha Toko Anda (6 bulan) telah kedaluwarsa. Profil Anda dikembalikan ke status PENDING_REVIEW. Silakan ajukan ulang dokumen verifikasi.',
                        'dibaca' => false,
                    ]);

                    ActivityLog::create([
                        'user_id' => null, // System action
                        'aktivitas' => "Mengembalikan status Toko ID: {$shop->id} (User: {$shop->nama}) ke PENDING_REVIEW karena masa verifikasi habis",
                    ]);

                    $expiredShopsCount++;
                }
            }
        }

        $this->info("Completed. Expired Technicians: {$expiredTechsCount}, Expired Shops: {$expiredShopsCount}");
    }
}
