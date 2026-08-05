<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\Dispute::class => \App\Policies\DisputePolicy::class,
        \App\Models\PendaftaranVerifikasi::class => \App\Policies\PendaftaranVerifikasiPolicy::class,
        \App\Models\Sparepart::class => \App\Policies\SparepartPolicy::class,
        \App\Models\Transaksi::class => \App\Policies\TransaksiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
