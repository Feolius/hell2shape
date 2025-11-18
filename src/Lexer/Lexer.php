<?php

namespace App\Lexer;

final class Lexer
{
    public const string T_ARRAY = 'array';

    public const string T_ARROW = 'arrow';

    public const string T_STRING_KEY = 'string_key';

    public const string T_INT_KEY = 'int_key';

    public const string T_PROTECTED_PROP = 'protected';

    public const string T_PRIVATE_PROP = 'private';

    public const string T_OPEN_BRACE = 'open_brace';

    public const string T_CLOSE_BRACE = 'close_brace';

    public const string T_INT = 'int';

    public const string T_STRING = 'string';

    public const string T_BOOL = 'bool';

    public const string T_FLOAT = 'float';

    public const string T_NULL = 'null';

    public const string T_UNINITIALIZED = 'uninitialized';

    public const string T_STD_OBJECT = 'std_object';

    public const string T_RESOURCE = 'resource';

    public const string T_OBJECT = 'object';

    public const string T_NEWLINE = 'newline';

    public const string T_WS = 'ws';

    private ?string $regexp = null;

    /**
     * @return list<Token>
     * @throws LexerException
     */
    public function tokenize(string $input): array
    {
        if (is_null($this->regexp)) {
            $this->regexp = $this->buildRegexp();
        }

        preg_match_all($this->regexp, $input, $matches, PREG_SET_ORDER);

        $tokens = [];
        $inputLen = mb_strlen($input);
        $line = 1;
        $column = 1;
        $tokensLen = 0;
        foreach ($matches as $match) {
            $type = $match['MARK'];
            $tokens[] = new Token($type, $match[0], $line, $column);
            $tokensLen += mb_strlen($match[0]);
            if ($type === self::T_NEWLINE) {
                $line++;
                $column = 1;
                continue;
            }
            if ($type === self::T_STRING) {
                $textLines = explode("\n", $match[0]);
                $line += count($textLines) - 1;
                $column += mb_strlen(end($textLines));
                continue;
            }
            $column += mb_strlen($match[0]);
        }

        if ($inputLen !== $tokensLen) {
            $wrongInput = mb_substr($input, $tokensLen);
            throw new LexerException($wrongInput, $line, $column);
        }

        return $tokens;
    }

    private function buildRegexp(): string
    {
        $patterns = [
            self::T_STRING => 'string\(\d+\) \"[^\"]*\"',
            self::T_INT => 'int\(\d+\)',
            self::T_BOOL => 'bool\((?:true|false)\)',
            self::T_FLOAT => 'float\(\d+\.\d+\)',
            self::T_NULL => 'NULL',
            self::T_UNINITIALIZED => 'uninitialized\([\w\\\\]+\)',
            self::T_ARRAY => 'array\(\d+\)',
            self::T_ARROW => '=>',
            self::T_STRING_KEY => '\[\"[a-zA-Z0-9_]+\"\]',
            self::T_PROTECTED_PROP => '\["\w+":protected\]',
            self::T_PRIVATE_PROP => '\["\w+":"[\w\\\\]+":private\]',
            self::T_INT_KEY => '\[\d+\]',
            self::T_OPEN_BRACE => '\{',
            self::T_CLOSE_BRACE => '\}',
            self::T_STD_OBJECT => 'object\(stdClass\)#\d+ \(\d+\)',
            self::T_RESOURCE => 'resource\(\d+\) of type \(\w+\)',
            self::T_OBJECT => 'object\([\w\\\\]+\)#\d+ \(\d+\)',
            self::T_NEWLINE => '\\r?+\\n',
            self::T_WS => '[ \t]++',
        ];

        foreach ($patterns as $type => &$pattern) {
            $pattern = '(?:'.$pattern.')(*MARK:'.$type.')';
        }

        return '~'.implode('|', $patterns).'~Asui';
    }
}
