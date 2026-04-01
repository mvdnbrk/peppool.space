<?php

declare(strict_types=1);

namespace App\Services\Inscriptions;

use App\Models\Inscription;

class InscriptionClassificationParser
{
    private const UINT64_MAX = '18446744073709551615';

    private const UINT128_MAX = '340282366920938463463374607431768211455';

    private const PRC20_MAX_DECIMALS = 18;

    private const PRC20_OPS = ['deploy', 'mint', 'transfer'];

    /**
     * Parse inscription content and return classification flags.
     *
     * @return array{flags: int, prc20: array<string, mixed>|null}
     */
    public function parse(?string $contentType, ?string $content): array
    {
        $flags = 0;
        $prc20 = null;

        if ($content === null || $content === '') {
            return ['flags' => $flags, 'prc20' => null];
        }

        if ($this->isPepemap($contentType, $content)) {
            $flags |= Inscription::FLAG_BITMAP;
        }

        $prc20Result = $this->parsePrc20($contentType, $content);
        if ($prc20Result !== null) {
            $flags |= Inscription::FLAG_PRC20;
            $prc20 = $prc20Result;
        }

        $flags |= Inscription::FLAG_ANALYZED;

        return ['flags' => $flags, 'prc20' => $prc20];
    }

    public function isPepemap(?string $contentType, string $content): bool
    {
        if ($contentType !== null && ! str_starts_with($contentType, 'text/plain')) {
            return false;
        }

        return (bool) preg_match('/^\d+\.pepemap$/', trim($content));
    }

    /**
     * Parse and validate PRC-20 content.
     *
     * @return array{valid: bool, op: string|null, tick: string|null, errors: list<string>}|null
     */
    public function parsePrc20(?string $contentType, string $content): ?array
    {
        if (! $this->couldBePrc20($contentType)) {
            return null;
        }

        $json = json_decode(trim($content), true);

        if (! is_array($json)) {
            return null;
        }

        $p = $json['p'] ?? null;

        if (! is_string($p) || strtolower($p) !== 'prc-20') {
            return null;
        }

        $op = isset($json['op']) && is_string($json['op']) ? strtolower($json['op']) : null;
        $tick = isset($json['tick']) && is_string($json['tick']) ? $json['tick'] : null;
        $errors = [];

        if ($op === null || ! in_array($op, self::PRC20_OPS, true)) {
            $errors[] = 'Invalid or missing op';
        }

        if ($tick === null || mb_strlen($tick) !== 4) {
            $errors[] = 'Tick must be exactly 4 characters';
        }

        if ($op !== null) {
            $errors = array_merge($errors, match ($op) {
                'deploy' => $this->validateDeploy($json),
                'mint' => $this->validateMint($json),
                'transfer' => $this->validateTransfer($json),
                default => [],
            });
        }

        return [
            'valid' => $errors === [],
            'op' => $op,
            'tick' => $tick,
            'errors' => $errors,
        ];
    }

    /**
     * @return list<string>
     */
    private function validateDeploy(array $json): array
    {
        $errors = [];

        if (! isset($json['max']) || ! is_string($json['max'])) {
            $errors[] = 'Missing or invalid max supply';
        } elseif (! $this->isValidUint($json['max']) || ! $this->fitsUint64($json['max'])) {
            $errors[] = 'Max supply must be a positive integer not exceeding uint64';
        }

        if (isset($json['lim'])) {
            if (! is_string($json['lim']) || ! $this->isValidUint($json['lim']) || ! $this->fitsUint128($json['lim'])) {
                $errors[] = 'Mint limit must be a positive integer not exceeding uint128';
            }
        }

        if (isset($json['dec'])) {
            if (! is_string($json['dec']) || ! ctype_digit($json['dec']) || (int) $json['dec'] > self::PRC20_MAX_DECIMALS) {
                $errors[] = 'Decimals must be between 0 and 18';
            }
        }

        return $errors;
    }

    /**
     * @return list<string>
     */
    private function validateMint(array $json): array
    {
        $errors = [];

        if (! isset($json['amt']) || ! is_string($json['amt'])) {
            $errors[] = 'Missing or invalid amount';
        } elseif (! $this->isValidUint($json['amt']) || ! $this->fitsUint128($json['amt'])) {
            $errors[] = 'Amount must be a positive integer not exceeding uint128';
        }

        return $errors;
    }

    /**
     * @return list<string>
     */
    private function validateTransfer(array $json): array
    {
        $errors = [];

        if (! isset($json['amt']) || ! is_string($json['amt'])) {
            $errors[] = 'Missing or invalid amount';
        } elseif (! $this->isValidUint($json['amt']) || ! $this->fitsUint128($json['amt'])) {
            $errors[] = 'Amount must be a positive integer not exceeding uint128';
        }

        return $errors;
    }

    private function couldBePrc20(?string $contentType): bool
    {
        if ($contentType === null) {
            return false;
        }

        $type = strtolower(explode(';', $contentType)[0]);

        return in_array(trim($type), ['application/json', 'text/plain'], true);
    }

    private function isValidUint(string $value): bool
    {
        return (bool) preg_match('/^[1-9]\d*$/', $value);
    }

    private function fitsUint64(string $value): bool
    {
        return strlen($value) < strlen(self::UINT64_MAX)
            || (strlen($value) === strlen(self::UINT64_MAX) && strcmp($value, self::UINT64_MAX) <= 0);
    }

    private function fitsUint128(string $value): bool
    {
        return strlen($value) < strlen(self::UINT128_MAX)
            || (strlen($value) === strlen(self::UINT128_MAX) && strcmp($value, self::UINT128_MAX) <= 0);
    }
}
