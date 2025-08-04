<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\TransferInvoice;
use App\Observers\AppointmentObserver;
use App\Observers\OrderObserver;
use App\Observers\PrescriptionObserver;
use App\Observers\StockMovementObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Invoice::observe(StockMovementObserver::class);
        TransferInvoice::observe(StockMovementObserver::class);
        Order::observe(OrderObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Prescription::observe(PrescriptionObserver::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
