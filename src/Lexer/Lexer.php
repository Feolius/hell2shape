<?php

namespace App\Lexer;

final class Lexer
{
    public const string SPACE_CHAR = ' ';

    public const string NEW_LINE_CHAR = "\n";

    public const string OPEN_BRACE_CHAR = "{";

    public const string CLOSE_BRACE_CHAR = "}";

    public const string T_ARRAY = 'array';

    public const string T_ARROW = 'arrow';

    public const string T_STRING_KEY = 'string_key';

    public const string T_INT_KEY = 'int_key';

    public const string T_OPEN_BRACE = 'open_brace';

    public const string T_CLOSE_BRACE = 'close_brace';

    public const string T_INT = 'int';

    public const string T_STRING = 'string';

    public const string T_BOOL = 'bool';

    public const string T_DOUBLE = 'double';

    public const string T_NULL = 'null';

    public const string T_OBJECT = 'object';

    public const string T_RESOURCE = 'resource';

    public const string T_CLASS = 'class';

    /**
     * @return list<Token>
     * @throws LexerException
     */
    public function tokenize(string $input): array
    {
        $tokens = [];

        $patterns = [
            self::T_STRING => '/^string\(\d+\) \"[^\"]*\"/',
            self::T_INT => '/^int\(\d+\)/',
            self::T_BOOL => '/^bool\((?:true|false)\)/',
            self::T_DOUBLE => '/^double\(\d+\.\d+\)/',
            self::T_NULL => '/^NULL/',
            self::T_ARRAY => '/^array\(\d+\)/',
            self::T_ARROW => '/^=>/',
            self::T_STRING_KEY => '/^\'[a-zA-Z0-9_]+\'/',
            self::T_INT_KEY => '/^\[\d+\]/',
            self::T_OPEN_BRACE => '/^\{/',
            self::T_CLOSE_BRACE => '/^\}/',
            self::T_OBJECT => '/^class stdClass#\d+ \(\d+\)/',
            self::T_RESOURCE => '/^resource\(\d+\) of type \(\w+\)/',
            self::T_CLASS => '/^class [\w\\\\]+#\d+ \(\d+\)/u',
        ];

        $offset = 0;
        $line = 1;
        $column = 1;
        $inClass = false;
        $inClassBraces = 0;
        while ($offset < mb_strlen($input)) {
            $firstChar = mb_substr($input, $offset, 1);
            if ($firstChar === self::SPACE_CHAR) {
                $offset++;
                $column++;
                continue;
            }
            if ($firstChar === self::NEW_LINE_CHAR) {
                $offset++;
                $column = 1;
                $line++;
                continue;
            }
            if ($inClass) {
                if ($firstChar !== self::OPEN_BRACE_CHAR) {
                    throw new LexerException(mb_substr($input, $offset), $line, $column);
                }
                $inClassBraces++;
                continue;

            }

            $matched = false;
            $current = mb_substr($input, $offset);
            foreach ($patterns as $type => $pattern) {
                if (preg_match($pattern, $current, $matches) === 1) {
                    $valueLength = mb_strlen($matches[0]);
                    $tokens[] = new Token($type, $matches[0], $line, $column);
                    $offset += $valueLength;
                    $column += $valueLength;
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                throw new LexerException($current, $line, $column);
            }

            if ($type === self::T_CLASS) {
                $inClass = true;
            }
        }

        return $tokens;
    }
}
