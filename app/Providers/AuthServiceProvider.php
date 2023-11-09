<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $spaUrl = "http://spa.test?email_verify_url=" . $url;

            return (new MailMessage)
                ->subject('Verifição de E-mail')
                ->line('Clique no botão abaixo para verificar o seu e-mail')
                ->action('Verificar E-mail', $spaUrl);
        });

        Gate::define('verifyAuthorization', function(User $user, $id) {
            return $user->type == 'admin' || $user->id == $id;
        });

        Gate::define('storeAdmin', function(User $user) {
            return $user->type == 'admin';
        });
    }
}
