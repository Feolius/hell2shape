<?php

namespace Feolius\Hell2Shape\Parser;

use Feolius\Hell2Shape\Lexer\Lexer;
use Feolius\Hell2Shape\Lexer\Token;
use Feolius\Hell2Shape\Parser\Node\AbstractNode;
use Feolius\Hell2Shape\Parser\Node\AnonymousObjectNode;
use Feolius\Hell2Shape\Parser\Node\BoolNode;
use Feolius\Hell2Shape\Parser\Node\FloatNode;
use Feolius\Hell2Shape\Parser\Node\HashmapItemNode;
use Feolius\Hell2Shape\Parser\Node\HashmapNode;
use Feolius\Hell2Shape\Parser\Node\IntNode;
use Feolius\Hell2Shape\Parser\Node\ListItemNode;
use Feolius\Hell2Shape\Parser\Node\ListNode;
use Feolius\Hell2Shape\Parser\Node\NullNode;
use Feolius\Hell2Shape\Parser\Node\ObjectNode;
use Feolius\Hell2Shape\Parser\Node\ResourceNode;
use Feolius\Hell2Shape\Parser\Node\StdObjectItemNode;
use Feolius\Hell2Shape\Parser\Node\StdObjectNode;
use Feolius\Hell2Shape\Parser\Node\StringNode;

final class Parser
{
    /**
     * @var list<Token>
     */
    private array $tokens;

    private int $position = 0;

    /**
     * @param list<Token> $tokens
     * @throws ParserException
     */
    public function parse(array $tokens): AbstractNode
    {
        $this->tokens = array_values(array_filter(
            $tokens,
            fn(Token $t) => !in_array($t->type, [Lexer::T_WS, Lexer::T_NEWLINE], true)
        ));
        $this->position = 0;

        $node = $this->parseValue();

        $this->expect(Lexer::T_END);

        return $node;
    }

    /**
     * @throws ParserException
     */
    private function parseValue(): AbstractNode
    {
        $token = $this->getCurrentToken();

        return match ($token->type) {
            Lexer::T_INT => $this->parseInt(),
            Lexer::T_FLOAT => $this->parseFloat(),
            Lexer::T_STRING => $this->parseString(),
            Lexer::T_BOOL => $this->parseBool(),
            Lexer::T_NULL => $this->parseNull(),
            Lexer::T_ARRAY => $this->parseArray(),
            Lexer::T_STD_OBJECT => $this->parseStdObject(),
            Lexer::T_OBJECT => $this->parseObject(),
            Lexer::T_ANONYMOUS_OBJECT => $this->parseAnonymousObject(),
            Lexer::T_RESOURCE => $this->parseResource(),
            default => throw new ParserException(sprintf('Unexpected token %s on line %d', $token->type, $token->line)),
        };
    }

    /**
     * @throws ParserException
     */
    private function parseInt(): IntNode
    {
        $token = $this->consume(Lexer::T_INT);
        preg_match('/int\((\d+)\)/A', $token->value, $matches);
        return new IntNode((int)$matches[1]);
    }

    /**
     * @throws ParserException
     */
    private function parseFloat(): FloatNode
    {
        $token = $this->consume(Lexer::T_FLOAT);
        preg_match('/float\(([\d.]+)\)/A', $token->value, $matches);
        return new FloatNode((float)$matches[1]);
    }

    /**
     * @throws ParserException
     */
    private function parseString(): StringNode
    {
        $token = $this->consume(Lexer::T_STRING);
        preg_match('/string\(\d+\) "(.+)"/Asu', $token->value, $matches);
        return new StringNode($matches[1]);
    }

    /**
     * @throws ParserException
     */
    private function parseBool(): BoolNode
    {
        $token = $this->consume(Lexer::T_BOOL);
        preg_match('/bool\((true|false)\)/A', $token->value, $matches);
        return new BoolNode($matches[1] === 'true');
    }

    /**
     * @throws ParserException
     */
    private function parseNull(): NullNode
    {
        $this->consume(Lexer::T_NULL);
        return new NullNode();
    }

    /**
     * @throws ParserException
     */
    private function parseStdObject(): StdObjectNode
    {
        $this->consume(Lexer::T_STD_OBJECT);
        $this->consume(Lexer::T_OPEN_BRACE);

        $items = [];
        while ($this->getCurrentToken()->type !== Lexer::T_CLOSE_BRACE) {
            if ($this->getCurrentToken()->type === Lexer::T_CLOSE_BRACE) {
                break;
            }
            $items[] = $this->parseStdObjectItem();
        }

        $this->consume(Lexer::T_CLOSE_BRACE);

        return new StdObjectNode($items);
    }

    /**
     * @throws ParserException
     */
    private function parseStdObjectItem(): StdObjectItemNode
    {
        $key = $this->parseStdObjectKey();
        $this->consume(Lexer::T_ARROW);
        $value = $this->parseValue();

        return new StdObjectItemNode($key, $value);
    }

    /**
     * @throws ParserException
     */
    private function parseStdObjectKey(): StringNode
    {
        $token = $this->getCurrentToken();
        if ($token->type === Lexer::T_STRING_KEY) {
            $this->consume(Lexer::T_STRING_KEY);
            preg_match('/\["(.+)"\]/', $token->value, $matches);
            return new StringNode($matches[1]);
        }

        throw new ParserException(sprintf('Unexpected std object key token %s on line %d', $token->type, $token->line));
    }

    /**
     * @throws ParserException
     */
    private function parseObject(): ObjectNode
    {
        $token = $this->consume(Lexer::T_OBJECT);
        preg_match('/object\(([\w\\\\]+)\)/Aui', $token->value, $matches);
        $className = $matches[1];

        $this->skipObjectInternals();

        return new ObjectNode($className);
    }

    /**
     * @throws ParserException
     */
    private function parseAnonymousObject(): AnonymousObjectNode
    {
        $this->consume(Lexer::T_ANONYMOUS_OBJECT);

        $this->skipObjectInternals();

        return new AnonymousObjectNode();
    }

    /**
     * Skip object internals by counting braces
     * @throws ParserException
     */
    private function skipObjectInternals(): void
    {
        $this->consume(Lexer::T_OPEN_BRACE);
        $braceDepth = 1;

        while ($braceDepth > 0) {
            $currentToken = $this->getCurrentToken();

            if ($currentToken->type === Lexer::T_OPEN_BRACE) {
                $braceDepth++;
            } elseif ($currentToken->type === Lexer::T_CLOSE_BRACE) {
                $braceDepth--;
            }

            if ($braceDepth > 0) {
                $this->position++;
            }
        }

        $this->consume(Lexer::T_CLOSE_BRACE);
    }

    /**
     * @throws ParserException
     */
    private function parseResource(): ResourceNode
    {
        $token = $this->consume(Lexer::T_RESOURCE);
        preg_match('/resource\(\d+\) of type \((\w+)\)/Au', $token->value, $matches);
        return new ResourceNode($matches[1]);
    }

    /**
     * @throws ParserException
     */
    private function parseArray(): ListNode|HashmapNode
    {
        $this->consume(Lexer::T_ARRAY);
        $this->consume(Lexer::T_OPEN_BRACE);

        $items = [];
        while ($this->getCurrentToken()->type !== Lexer::T_CLOSE_BRACE) {
            if ($this->getCurrentToken()->type === Lexer::T_CLOSE_BRACE) {
                break;
            }
            $items[] = $this->parseArrayItem();
        }

        $this->consume(Lexer::T_CLOSE_BRACE);

        $hashmapNode = new HashmapNode($items);

        return $this->tryConvertToListNode($hashmapNode);
    }

    /**
     * @throws ParserException
     */
    private function parseArrayItem(): HashmapItemNode
    {
        $key = $this->parseArrayKey();
        $this->consume(Lexer::T_ARROW);
        $value = $this->parseValue();

        return new HashmapItemNode($key, $value);
    }

    /**
     * @throws ParserException
     */
    private function parseArrayKey(): IntNode|StringNode
    {
        $token = $this->getCurrentToken();
        if ($token->type === Lexer::T_INT_KEY) {
            $this->consume(Lexer::T_INT_KEY);
            preg_match('/\[(\d+)\]/', $token->value, $matches);
            return new IntNode((int)$matches[1]);
        }

        if ($token->type === Lexer::T_STRING_KEY) {
            $this->consume(Lexer::T_STRING_KEY);
            preg_match('/\["(.+)"\]/', $token->value, $matches);
            return new StringNode($matches[1]);
        }

        throw new ParserException(sprintf('Unexpected array key token %s on line %d', $token->type, $token->line));
    }

    private function tryConvertToListNode(HashmapNode $hashmapNode): ListNode|HashmapNode
    {
        $items = $hashmapNode->items;
        if (empty($items)) {
            return $hashmapNode;
        }

        $expectedKey = 0;
        foreach ($items as $item) {
            if (!$item->key instanceof IntNode || $item->key->value !== $expectedKey) {
                return $hashmapNode;
            }
            $expectedKey++;
        }

        $listItems = array_map(fn(HashmapItemNode $item) => new ListItemNode($item->value), $items);

        return new ListNode($listItems);
    }

    private function getCurrentToken(): Token
    {
        return $this->tokens[$this->position];
    }

    /**
     * @throws ParserException
     */
    /**
     * @throws ParserException
     */
    private function consume(string $expectedType): Token
    {
        $token = $this->getCurrentToken();
        if ($token->type !== $expectedType) {
            throw new ParserException(sprintf('Expected token %s, got %s on line %d', $expectedType, $token->type, $token->line));
        }
        $this->position++;
        return $token;
    }

    /**
     * @throws ParserException
     */
    private function expect(string $expectedType): void
    {
        $token = $this->getCurrentToken();
        if ($token->type !== $expectedType) {
            throw new ParserException(sprintf('Expected token %s, got %s on line %d', $expectedType, $token->type, $token->line));
        }
    }
}
