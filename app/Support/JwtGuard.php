<?php

namespace App\Support;

use App\Models\AuthToken;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

/**
 * Signs users in with a JWT kept in an httpOnly cookie. Each token's jti has a row in auth_tokens, so a token
 * can be revoked before it expires. Tokens slide: once past half their lifetime they are re-issued.
 */
class JwtGuard implements StatefulGuard
{
    use GuardHelpers;

    /**
     * Only write last_used_at once per this many seconds, so page loads don't each write to the database.
     */
    private const TOUCH_EVERY_SECONDS = 60;

    private ?AuthToken $token = null;

    private bool $loggedOut = false;

    public function __construct(
        UserProvider $provider,
        private Request $request,
        private Jwt $jwt,
        private string $cookieName,
        private int $ttlMinutes,
    ) {
        $this->provider = $provider;
    }

    public function user(): ?Authenticatable
    {
        if ($this->loggedOut) {
            return null;
        }

        if ($this->user !== null) {
            return $this->user;
        }

        $cookie = $this->request->cookie($this->cookieName);
        $claims = is_string($cookie) ? $this->jwt->decode($cookie) : null;

        if ($claims === null) {
            return null;
        }

        $token = AuthToken::query()
            ->where('jti', $claims['jti'])
            ->where('user_id', $claims['sub'])
            ->where('expires_at', '>', now())
            ->first();

        $user = $token ? $this->provider->retrieveById($claims['sub']) : null;

        if ($user === null || $user->deactivated_at !== null) {
            $token?->delete();
            Cookie::queue($this->forgetCookie());

            return null;
        }

        $this->token = $token;
        $this->user = $user;

        $this->keepAlive($token);

        return $user;
    }

    /**
     * The jti of the token this request signed in with, e.g. to keep it when signing out other devices.
     */
    public function tokenId(): ?string
    {
        $this->user();

        return $this->token?->jti;
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function validate(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function attempt(array $credentials = [], $remember = false): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user === null || ! $this->provider->validateCredentials($user, $credentials)) {
            return false;
        }

        $this->provider->rehashPasswordIfRequired($user, $credentials);
        $this->login($user, $remember);

        return true;
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function once(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user === null || ! $this->provider->validateCredentials($user, $credentials)) {
            return false;
        }

        $this->setUser($user);

        return true;
    }

    public function login(Authenticatable $user, $remember = false): void
    {
        AuthToken::query()->where('expires_at', '<=', now())->delete();

        $token = AuthToken::create([
            'jti' => (string) Str::uuid(),
            'user_id' => $user->getAuthIdentifier(),
            'ip_address' => $this->request->ip(),
            'user_agent' => Str::limit((string) $this->request->userAgent(), 250, ''),
            'last_used_at' => now(),
            'expires_at' => now()->addMinutes($this->ttlMinutes),
        ]);

        Cookie::queue($this->makeCookie($token));

        $this->token = $token;
        $this->loggedOut = false;
        $this->setUser($user);
    }

    public function loginUsingId($id, $remember = false): Authenticatable|false
    {
        $user = $this->provider->retrieveById($id);

        if ($user === null) {
            return false;
        }

        $this->login($user, $remember);

        return $user;
    }

    public function onceUsingId($id): Authenticatable|false
    {
        $user = $this->provider->retrieveById($id);

        if ($user === null) {
            return false;
        }

        $this->setUser($user);

        return $user;
    }

    public function viaRemember(): bool
    {
        return false;
    }

    public function logout(): void
    {
        $this->user();
        $this->token?->delete();

        Cookie::queue($this->forgetCookie());

        $this->token = null;
        $this->user = null;
        $this->loggedOut = true;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Record activity, and re-issue the cookie with a fresh expiry once half its lifetime has passed.
     */
    private function keepAlive(AuthToken $token): void
    {
        $changes = [];

        if ($token->last_used_at === null || $token->last_used_at->diffInSeconds(now()) >= self::TOUCH_EVERY_SECONDS) {
            $changes['last_used_at'] = now();
        }

        if (now()->diffInMinutes($token->expires_at) < $this->ttlMinutes / 2) {
            $changes['expires_at'] = now()->addMinutes($this->ttlMinutes);
        }

        if ($changes === []) {
            return;
        }

        $token->forceFill($changes)->save();

        if (isset($changes['expires_at'])) {
            Cookie::queue($this->makeCookie($token));
        }
    }

    private function makeCookie(AuthToken $token): SymfonyCookie
    {
        $jwt = $this->jwt->encode([
            'sub' => $token->user_id,
            'jti' => $token->jti,
            'iat' => now()->getTimestamp(),
            'exp' => $token->expires_at->getTimestamp(),
        ]);

        return Cookie::make(
            name: $this->cookieName,
            value: $jwt,
            minutes: $this->ttlMinutes,
            path: '/',
            domain: config('session.domain'),
            secure: config('session.secure') ?? $this->request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    private function forgetCookie(): SymfonyCookie
    {
        return Cookie::forget($this->cookieName, '/', config('session.domain'));
    }
}
