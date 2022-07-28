<?php

namespace MultilingualMenuDuplication\Helpers;

class Language
{
    public static function getLanguages()
    {
        if (function_exists('pll_the_languages')) {
            return pll_the_languages([
                'raw' => 1,
            ]);
        }
    }
}
