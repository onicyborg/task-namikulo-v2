<?php

namespace App\Support;

use libphonenumber\PhoneNumberUtil;

class CountryList
{
    public static function all(): array
    {
        $util = PhoneNumberUtil::getInstance();
        $countries = [];

        foreach ($util->getSupportedRegions() as $code) {
            $name = class_exists('Locale')
                ? (\Locale::getDisplayRegion($code . '_' . $code, 'id_ID') ?: $code)
                : $code;

            $countries[] = [
                'code' => $code,
                'name' => $name,
                'dial_code' => '+' . $util->getCountryCodeForRegion($code),
            ];
        }

        usort($countries, function (array $left, array $right): int {
            if ($left['code'] === 'ID') return -1;
            if ($right['code'] === 'ID') return 1;
            return strcasecmp($left['name'], $right['name']);
        });

        return $countries;
    }
}
