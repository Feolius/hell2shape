<?php

namespace App\Tests;

final class VarDumper
{
    public static function dump(mixed $variable): string
    {
        ob_start();
        var_dump($variable);
        return ob_get_clean();
    }
}
