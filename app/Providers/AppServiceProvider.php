<?php

namespace App\Providers;

use App\Models\Izin;
use App\Models\Lembur;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menggunakan View Composer untuk membagikan data ke semua view
        View::composer('*', function ($view) {
            $notifIzin = Izin::with('user')
                ->where('status', 'PENDING')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $notifLembur = Lembur::with('user')
                ->where('status', 'PENDING')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $view->with([
                'notifIzin' => $notifIzin,
                'notifLembur' => $notifLembur
            ]);
        });
    }
}
