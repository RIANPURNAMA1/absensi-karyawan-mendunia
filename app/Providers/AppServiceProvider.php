<?php

namespace App\Providers;

use App\Models\Izin;
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
    View::composer('*', function ($view) {
        if (auth()->check()) {
            // Ambil izin yang statusnya PENDING (untuk HR/Manager)
            $notifIzin = Izin::with('user')
                ->where('status', 'PENDING')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $countIzin = Izin::where('status', 'PENDING')->count();
            
            $view->with([
                'notifIzin' => $notifIzin,
                'countIzin' => $countIzin
            ]);
        }
    });
}
}
