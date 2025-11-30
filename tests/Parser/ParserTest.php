<?php

namespace App\Tests\Parser;

use App\Lexer\Lexer;
use App\Parser\Node\AbstractNode;
use App\Parser\Node\FloatNode;
use App\Parser\Node\HashmapItemNode;
use App\Parser\Node\HashmapNode;
use App\Parser\Node\IntNode;
use App\Parser\Node\ListItemNode;
use App\Parser\Node\ListNode;
use App\Parser\Node\NullNode;
use App\Parser\Node\ResourceNode;
use App\Parser\Node\StdObjectItemNode;
use App\Parser\Node\StdObjectNode;
use App\Parser\Node\StringNode;
use App\Parser\Parser;
use App\Tests\VarDumper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    #[DataProvider('dumpProvider')]
    public function testParse(mixed $input, AbstractNode $expected): void
    {
        $dump = VarDumper::dump($input);
        $lexer = new Lexer();
        $tokens = $lexer->tokenize($dump);
        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $this->assertEquals($expected, $ast);
    }

    public static function dumpProvider(): iterable
    {
        yield 'Simple Hashmap' => [
            'input' => ['foo' => 'bar', 0 => 42, 'baz' => null, 1 => 4.5, 'gut' => fopen('php://memory', 'r')],
            'expected' => new HashmapNode([
                new HashmapItemNode(new StringNode('foo'), new StringNode('bar')),
                new HashmapItemNode(new IntNode(0), new IntNode(42)),
                new HashmapItemNode(new StringNode('baz'), new NullNode()),
                new HashmapItemNode(new IntNode(1), new FloatNode(4.5)),
                new HashmapItemNode(new StringNode('gut'), new ResourceNode('stream')),
            ]),
        ];

        yield 'Simple List' => [
            'input' => ['bar', 42, null, 4.5, fopen('php://memory', 'r')],
            'expected' => new ListNode([
                new ListItemNode(new StringNode('bar')),
                new ListItemNode(new IntNode(42)),
                new ListItemNode(new NullNode()),
                new ListItemNode(new FloatNode(4.5)),
                new ListItemNode(new ResourceNode('stream')),
            ]),
        ];

        yield 'Inner array and object' => [
            'input' => [
                'list' => ['foo', 'bar'],
                'object' => (object)['foo' => 'bar', 'baz' => 42],
            ],
            'expected' => new HashmapNode([
                new HashmapItemNode(
                    new StringNode('list'),
                    new ListNode([
                        new ListItemNode(new StringNode('foo')),
                        new ListItemNode(new StringNode('bar')),
                    ])
                ),
                new HashmapItemNode(
                    new StringNode('object'),
                    new StdObjectNode([
                        new StdObjectItemNode(new StringNode('foo'), new StringNode('bar')),
                        new StdObjectItemNode(new StringNode('baz'), new IntNode(42)),
                    ])
                ),
            ]),
        ];
    }
}
