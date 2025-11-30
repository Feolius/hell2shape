<?php

namespace Parser;

use App\Lexer\Lexer;
use App\Parser\Node\FloatNode;
use App\Parser\Node\HashmapItemNode;
use App\Parser\Node\HashmapNode;
use App\Parser\Node\IntNode;
use App\Parser\Node\ListItemNode;
use App\Parser\Node\ListNode;
use App\Parser\Node\ResourceNode;
use App\Parser\Node\StringNode;
use App\Parser\Node\NullNode;
use App\Parser\Node\StdObjectItemNode;
use App\Parser\Node\StdObjectNode;
use App\Parser\Parser;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function testParseSimpleHashmap(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('array(5) {
  ["foo"]=>
  string(3) "bar"
  [0]=>
  int(42)
  ["baz"]=>
  NULL
  [1]=>
  float(4.5)
  ["gut"]=>
  resource(5) of type (stream)
}
');
        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $this->assertEquals(
            new HashmapNode([
                new HashmapItemNode(new StringNode('foo'), new StringNode('bar')),
                new HashmapItemNode(new IntNode(0), new IntNode(42)),
                new HashmapItemNode(new StringNode('baz'), new NullNode()),
                new HashmapItemNode(new IntNode(1), new FloatNode(4.5)),
                new HashmapItemNode(new StringNode('gut'), new ResourceNode('stream')),
            ]),
            $ast
        );
    }

    public function testParseSimpleList(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('array(5) {
  [0]=>
  string(3) "bar"
  [1]=>
  int(42)
  [2]=>
  NULL
  [3]=>
  float(4.5)
  [4]=>
  resource(5) of type (stream)
}');
        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $this->assertEquals(
            new ListNode([
                new ListItemNode(new StringNode('bar')),
                new ListItemNode(new IntNode(42)),
                new ListItemNode(new NullNode()),
                new ListItemNode(new FloatNode(4.5)),
                new ListItemNode(new ResourceNode('stream')),
            ]),
            $ast
        );
    }

    public function testParseComplexStructure(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('array(2) {
  ["list"]=>
  array(2) {
    [0]=>
    string(3) "foo"
    [1]=>
    string(3) "bar"
  }
  ["object"]=>
  object(stdClass)#1 (2) {
    ["foo"]=>
    string(3) "bar"
    ["baz"]=>
    int(42)
  }
}
');
        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $this->assertEquals(
            new HashmapNode([
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
            $ast
        );
    }
}
