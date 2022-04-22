<?php

namespace App\Helper;

class StringHelper
{
    public static function capitalize(string $word): string
    {
        return ucfirst(strtolower($word));
    }
}
