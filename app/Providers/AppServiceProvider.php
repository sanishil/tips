<?php

namespace App\Providers;

use App\Support\EmailOtp;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

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
        Paginator::useBootstrapFive();

        $this->configureRateLimiting();
    }

    /**
     * Sign-in and email-code limits, counted per email + IP, with a looser cap per IP
     * so one address can't be used to try many emails. Going over shows a form error, not a 429 page.
     */
    private function configureRateLimiting(): void
    {
        $typedEmail = fn (Request $request): string => Str::lower(trim($request->string('email')));

        /* Code pages don't post the email, so use the one the code was sent to. */
        $pendingEmail = fn (Request $request): string => (string) $request->session()->get(
            'email_otp.'.($request->routeIs('register.*') ? 'register' : 'reset').'.email',
        );

        $tooMany = fn (string $field, string $message) => fn (Request $request): Response => back()
            ->withInput($request->only('name', 'email'))
            ->withErrors([$field => $message]);

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by($typedEmail($request).'|'.$request->ip())
                ->response($tooMany('email', 'Too many sign-in attempts. Please wait a minute and try again.')),
            Limit::perMinute(20)->by($request->ip())
                ->response($tooMany('email', 'Too many sign-in attempts from this network. Please wait a minute.')),
        ]);

        RateLimiter::for('send-code', fn (Request $request) => [
            Limit::perMinutes(10, 3)->by($typedEmail($request).'|'.$request->ip())
                ->response($tooMany('email', 'We\'ve sent several codes to this email already. Please wait 10 minutes.')),
            Limit::perHour(20)->by($request->ip())
                ->response($tooMany('email', 'Too many requests from this network. Please try again later.')),
        ]);

        RateLimiter::for('verify-code', fn (Request $request) => [
            Limit::perMinute(10)->by($pendingEmail($request).'|'.$request->ip())
                ->response($tooMany('code', 'Too many tries. Please wait a minute.')),
        ]);

        RateLimiter::for('resend-code', fn (Request $request) => [
            Limit::perMinutes(10, EmailOtp::MAX_ATTEMPTS - 2)->by($pendingEmail($request).'|'.$request->ip())
                ->response($tooMany('code', 'We\'ve sent several codes already. Please wait 10 minutes.')),
        ]);

        RateLimiter::for('new-password', fn (Request $request) => [
            Limit::perMinute(5)->by(($request->user()?->email ?? $pendingEmail($request)).'|'.$request->ip())
                ->response($tooMany('password', 'Too many tries. Please wait a minute.')),
        ]);
    }
}
