<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service;

use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

/**
 * Wraps spomky-labs/otphp to provide TOTP secret generation,
 * provisioning-URI construction, and code verification.
 */
final class TotpService
{
    private const ISSUER = 'Janus';
    private const DIGITS = 6;
    private const PERIOD = 30;

    /**
     * Generates a cryptographically random base32-encoded TOTP secret.
     */
    public function generateSecret(): string
    {
        return Base32::encodeUpperUnpadded(random_bytes(20));
    }

    /**
     * Returns an otpauth:// provisioning URI suitable for QR-code generation.
     */
    public function buildProvisioningUri(string $email, string $secret): string
    {
        $totp = TOTP::createFromSecret($secret);
        $totp->setLabel($email);
        $totp->setIssuer(self::ISSUER);
        $totp->setDigits(self::DIGITS);
        $totp->setPeriod(self::PERIOD);

        return $totp->getProvisioningUri();
    }

    /**
     * Verifies a 6-digit OTP code against the stored secret.
     * Accepts codes from the current window ±1 step to tolerate clock skew.
     */
    public function verifyCode(string $secret, string $code): bool
    {
        $totp = TOTP::createFromSecret($secret);
        $totp->setDigits(self::DIGITS);
        $totp->setPeriod(self::PERIOD);

        return $totp->verify($code, null, 1);
    }
}
