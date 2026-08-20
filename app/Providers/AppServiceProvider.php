<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kustomkan kandungan emel pengesahan akaun supaya jelas ia untuk
        // PENGAKTIFAN SISTEM Blade & Fade — bukan mesej generik Laravel.
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Aktifkan Akaun Anda — Blade & Fade')
                ->greeting('Salam, ' . $notifiable->name . '!')
                ->line('Terima kasih kerana mendaftar dengan Blade & Fade — Sistem Giliran Barbershop.')
                ->line('Emel ini adalah untuk PENGAKTIFAN akaun anda dalam sistem. Sila klik butang di bawah untuk mengesahkan alamat emel dan mengaktifkan akaun anda.')
                ->action('Aktifkan Akaun Saya', $url)
                ->line('Akaun anda tidak boleh digunakan untuk log masuk sebelum pengaktifan ini selesai.')
                ->line('Jika anda tidak membuat pendaftaran ini, sila abaikan emel ini — tiada tindakan lanjut diperlukan.')
                ->salutation('Terima kasih, Pasukan Blade & Fade');
        });
    }
}
