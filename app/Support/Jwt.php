<?php

namespace App\Support;

/**
 * Minimal HS256 JSON Web Token encoder/decoder (RFC 7519), so no extra package is needed.
 * Only HS256 is accepted on decode, which rules out "alg: none" and algorithm-confusion tokens.
 */
class Jwt
{
    private const ALGORITHM = 'HS256';

    /**
     * Seconds of clock difference tolerated when checking exp, nbf and iat.
     */
    private const LEEWAY_SECONDS = 30;

    public function __construct(private string $key, private string $issuer) {}

    /**
     * @param  array{sub: int|string, jti: string, iat: int, exp: int}  $claims
     */
    public function encode(array $claims): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => self::ALGORITHM, 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $payload = $this->base64UrlEncode(json_encode(['iss' => $this->issuer] + $claims, JSON_THROW_ON_ERROR));

        return $header.'.'.$payload.'.'.$this->sign($header.'.'.$payload);
    }

    /**
     * The token's claims, or null when it is malformed, wrongly signed, from another issuer, or expired.
     *
     * @return array{iss: string, sub: int|string, jti: string, iat: int, exp: int}|null
     */
    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        if (! hash_equals($this->sign($header.'.'.$payload), $signature)) {
            return null;
        }

        $headerData = json_decode($this->base64UrlDecode($header), true);
        $claims = json_decode($this->base64UrlDecode($payload), true);

        if (! is_array($headerData) || ($headerData['alg'] ?? null) !== self::ALGORITHM || ! is_array($claims)) {
            return null;
        }

        $now = time();

        $isValid = ($claims['iss'] ?? null) === $this->issuer
            && isset($claims['sub'], $claims['jti']) && is_string($claims['jti'])
            && is_int($claims['exp'] ?? null) && $claims['exp'] > $now - self::LEEWAY_SECONDS
            && is_int($claims['iat'] ?? null) && $claims['iat'] <= $now + self::LEEWAY_SECONDS
            && (! isset($claims['nbf']) || (is_int($claims['nbf']) && $claims['nbf'] <= $now + self::LEEWAY_SECONDS));

        return $isValid ? $claims : null;
    }

    private function sign(string $data): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->key, true));
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return (string) base64_decode(strtr($data, '-_', '+/'), true);
    }
}
