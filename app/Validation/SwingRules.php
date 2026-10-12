<?php

namespace App\Validation;

use DateTimeImmutable;

/**
 * Egna valideringsregler. Registreras i Config\Validation::$ruleSets.
 */
class SwingRules
{
    /**
     * Slutdatum för kampanj: valfritt för utkast, obligatoriskt när kampanjen
     * publiceras (inga ständigt aktiva erbjudanden). Användning: campaign_end[status]
     */
    public function campaign_end(?string $value, string $statusField, array $data, ?string &$error = null): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            if (($data[$statusField] ?? '') === 'published') {
                $error = 'En publicerad kampanj måste ha ett slutdatum.';

                return false;
            }

            return true;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            $error = 'Ange slutdatum som ÅÅÅÅ-MM-DD.';

            return false;
        }

        return true;
    }

    /**
     * Svenskt personnummer (ÅÅÅÅMMDD-NNNN eller ÅÅMMDD-NNNN) med kontrollsiffra.
     */
    public function personnummer(?string $value, ?string &$error = null): bool
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) !== 10) {
            $error = 'Ange personnummer som ÅÅÅÅMMDD-NNNN.';

            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $n = (int) $digits[$i] * ($i % 2 === 0 ? 2 : 1);
            $sum += intdiv($n, 10) + $n % 10;
        }

        if ((10 - $sum % 10) % 10 !== (int) $digits[9]) {
            $error = 'Personnumret verkar inte stämma — kontrollera siffrorna.';

            return false;
        }

        return true;
    }
}
