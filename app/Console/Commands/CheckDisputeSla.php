<?php

namespace App\Console\Commands;

use App\Models\Dispute;
use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDisputeSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revaro:check-dispute-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor dispute SLA deadlines and alert resolvers and owners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning dispute SLA deadlines...');

        $disputes = Dispute::where('status', '!=', 'RESOLVED')
            ->whereNotNull('sla_deadline')
            ->get();

        $warned = 0;
        $escalated = 0;

        // Fetch Super Admin Pemilik system owners
        $ownerRole = Role::where('nama', 'Super Admin (Pemilik Sistem)')->first();
        $owners = $ownerRole ? User::where('role_id', $ownerRole->id)->get() : collect();

        foreach ($disputes as $dispute) {
            $order = $dispute->order;
            if (!$order) {
                continue;
            }

            $now = Carbon::now();
            $deadline = Carbon::parse($dispute->sla_deadline);

            if ($now->greaterThan($deadline)) {
                // EXCEEDED SLA deadline
                // Notify assigned admin resolver
                if ($dispute->admin_id) {
                    Notifikasi::create([
                        'user_id' => $dispute->admin_id,
                        'judul' => 'Tenggat SLA Terlewati',
                        'pesan' => "PERINGATAN: Sengketa untuk order {$order->kode_order} yang ditugaskan kepada Anda telah melewati batas waktu SLA 48 jam.",
                        'dibaca' => false,
                    ]);
                }

                // Escalate to Super Admin Pemilik(s)
                foreach ($owners as $owner) {
                    Notifikasi::create([
                        'user_id' => $owner->id,
                        'judul' => 'Eskalasi SLA Sengketa',
                        'pesan' => "ESKALASI: Sengketa order {$order->kode_order} telah melampaui SLA 48 jam tanpa resolusi.",
                        'dibaca' => false,
                    ]);
                }

                $escalated++;
            } elseif ($now->diffInHours($deadline, false) <= 6) {
                // APPROACHING SLA (< 6 hours left)
                if ($dispute->admin_id) {
                    Notifikasi::create([
                        'user_id' => $dispute->admin_id,
                        'judul' => 'Mendekati Tenggat SLA',
                        'pesan' => "Peringatan: Sengketa order {$order->kode_order} mendekati batas waktu SLA dalam kurang dari 6 jam.",
                        'dibaca' => false,
                    ]);
                    $warned++;
                }
            }
        }

        $this->info("Completed. Warned: {$warned}, Escalated: {$escalated}");
    }
}
