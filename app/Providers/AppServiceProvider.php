<?php

namespace App\Providers;

use App\Models\AlertStatus;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        // Paksa HTTPS jika bukan di environment lokal
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function (\Illuminate\View\View $view): void {
            if (! array_key_exists('alertStatus', $view->getData())) {
                $status = Schema::hasTable('alert_statuses')
                    ? AlertStatus::current()
                    : new AlertStatus([
                        'level' => AlertStatus::LEVEL_SAFE,
                        'headline' => 'Status Desa Kalikurmo: AMAN (Normal)',
                        'message' => 'Tidak ada ancaman bencana aktif saat ini.',
                    ]);

                $view->with('alertStatus', $status);
            }

            if (! array_key_exists('emergencyContacts', $view->getData())) {
                $contacts = Schema::hasTable('emergency_contacts')
                    ? EmergencyContact::getAllSorted()
                    : collect();

                $view->with('emergencyContacts', $contacts);
            }
        });
    }
}
