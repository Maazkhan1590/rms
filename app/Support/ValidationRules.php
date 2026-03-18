<?php

namespace App\Support;

final class ValidationRules
{
    /**
     * Allows letters (unicode), spaces, apostrophes, dots and hyphens.
     */
    public static function personName(bool $required = true, int $max = 255): array
    {
        $rules = ['string', 'max:' . $max, 'regex:/^[\\pL\\pM\\s\\.\'\\-]+$/u'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function employeeId(bool $required = false, int $max = 255): array
    {
        // Common: letters/numbers, dash/underscore/slash
        $rules = ['string', 'max:' . $max, 'regex:/^[A-Za-z0-9][A-Za-z0-9_\\-\\/]*$/'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function phone(bool $required = false, int $max = 20): array
    {
        // E.164-ish with spaces/dashes allowed
        $rules = ['string', 'max:' . $max, 'regex:/^\\+?[0-9][0-9\\s\\-()]{6,19}$/'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function title(bool $required = true, int $max = 500): array
    {
        // Avoid control chars; keep broad unicode text
        $rules = ['string', 'max:' . $max, 'regex:/^[^\\pC]+$/u'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function organization(bool $required = false, int $max = 255): array
    {
        // Broad: letters/numbers, spaces and common punctuation
        $rules = ['string', 'max:' . $max, 'regex:/^[\\pL\\pM0-9\\s\\.&,\'\\-()\\/]+$/u'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function referenceCode(bool $required = false, int $max = 255): array
    {
        $rules = ['string', 'max:' . $max, 'regex:/^[A-Za-z0-9][A-Za-z0-9_\\-\\.\\/]*$/'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function orcid(bool $required = false): array
    {
        // 0000-0000-0000-0000 (last can be X)
        $rules = ['string', 'max:19', 'regex:/^\\d{4}-\\d{4}-\\d{4}-\\d{3}[\\dX]$/'];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }

    public static function url(bool $required = false, int $max = 500): array
    {
        $rules = ['url', 'max:' . $max];
        array_unshift($rules, $required ? 'required' : 'nullable');
        return $rules;
    }
}

