<?php

namespace Feolius\Hell2Shape;

use Feolius\Hell2Shape\Generator\Generator;
use Feolius\Hell2Shape\Generator\GeneratorConfig;
use Feolius\Hell2Shape\Helper\VarDumper;
use Feolius\Hell2Shape\Lexer\Lexer;
use Feolius\Hell2Shape\Lexer\LexerException;
use Feolius\Hell2Shape\Parser\Parser;
use Feolius\Hell2Shape\Parser\ParserException;

final class Hell2Shape
{
    /**
     * Generate PHPStan shape type annotation from a variable
     *
     * @param mixed $variable The variable to analyze
     * @param ?GeneratorConfig $config Configuration for the generator. Use defaults if not provided.
     * @return string PHPStan shape type annotation
     * @throws LexerException
     * @throws ParserException
     */
    public static function generate(mixed $variable, ?GeneratorConfig $config = null): string
    {
        $varDumpOutput = VarDumper::dump($variable);

        $lexer = new Lexer();
        $tokens = $lexer->tokenize($varDumpOutput);

        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $config = $config ?? new GeneratorConfig();
        $generator = new Generator($config);
        return $generator->generate($ast);
    }
}
