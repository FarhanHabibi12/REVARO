<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\Teknisi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $radius;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId, $radius = 5)
    {
        $this->orderId = $orderId;
        $this->radius = $radius;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $order = Order::find($this->orderId);

        if (!$order || $order->status !== 'SEARCHING') {
            return;
        }

        // Get all active and available technicians
        $techs = Teknisi::where('status', 'ACTIVE')
            ->where('tersedia', true)
            ->get();

        $foundTechs = [];

        foreach ($techs as $tech) {
            if ($tech->latitude && $tech->longitude && $order->latitude && $order->longitude) {
                $distance = $this->calculateDistance(
                    $order->latitude,
                    $order->longitude,
                    $tech->latitude,
                    $tech->longitude
                );

                if ($distance <= $this->radius) {
                    $foundTechs[] = $tech;
                }
            }
        }

        if (count($foundTechs) > 0) {
            // Notify nearby technicians
            foreach ($foundTechs as $tech) {
                Notifikasi::create([
                    'user_id' => $tech->user_id,
                    'judul' => 'Order Baru Tersedia',
                    'pesan' => "Order baru dari pelanggan dengan kode {$order->kode_order} tersedia dalam radius {$this->radius} KM. Silakan terima pesanan ini.",
                    'dibaca' => false,
                ]);
            }

            // In real app, we wait for technician acceptance.
            // If none accepts within 3 minutes, we escalate.
            // For simulation / tests, we chain the escalation.
            if ($order->status === 'SEARCHING') {
                if ($this->radius < 15) {
                    $nextRadius = $this->radius + 5;
                    // Dispatch next check with delay
                    self::dispatch($this->orderId, $nextRadius)->delay(now()->addMinutes(3));
                }
            }
        } else {
            // No technicians found in this radius, escalate immediately or cancel if max
            if ($this->radius < 15) {
                $nextRadius = $this->radius + 5;
                // Dispatch next radius check immediately
                self::dispatchSync($this->orderId, $nextRadius);
            } else {
                // Reached max radius 15km, cancel order
                $order->update(['status' => 'CANCELLED']);

                Notifikasi::create([
                    'user_id' => $order->pelanggan->user_id,
                    'judul' => 'Pencarian Teknisi Gagal',
                    'pesan' => 'Maaf, tidak ada teknisi yang tersedia di sekitar lokasi Anda dalam radius 15 KM saat ini. Silakan coba kembali.',
                    'dibaca' => false,
                ]);

                ActivityLog::create([
                    'user_id' => null, // System action
                    'aktivitas' => "Order {$order->kode_order} dibatalkan otomatis karena tidak menemukan teknisi dalam radius 15 KM",
                ]);
            }
        }
    }

    /**
     * Haversine formula to calculate distance in km.
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
