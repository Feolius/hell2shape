<?php

namespace Feolius\Hell2Shape\Helper;

final class VarDumper
{
    public static function dump(mixed $variable): string
    {
        ob_start();
        var_dump($variable);
        $result = ob_get_clean();
        assert($result !== false);
        return $result;
    }
}
