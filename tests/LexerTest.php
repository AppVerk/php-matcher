<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests;

use AppVerk\PHPMatcher\Lexer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public static function validStringValuesProvider()
    {
        return [
            ['"String"'],
            ["'String'"],
        ];
    }

    public static function validNumberValuesProvider()
    {
        return [
            ['125', 125],
            ['12.15', 12.15],
            ['-10', -10],
            ['-1.24', -1.24],
        ];
    }

    public static function validBooleanValuesProvider()
    {
        return [
            ['true', true],
            ['false', false],
            ['TRUE', true],
            ['fAlSe', false],
        ];
    }

    public static function validNullValuesProvider()
    {
        return [
            ['null'],
            ['NULL'],
            ['NuLl'],
        ];
    }

    public static function validNonTokenValuesProvider()
    {
        return [
            ['@integer'],
            ['integer@'],
            ['test'],
            ['@'],
        ];
    }

    public static function validMatcherTypePatterns()
    {
        return [
            ['@string@'],
            ['@boolean@'],
            ['@integer@'],
            ['@number@'],
            ['@*@'],
            ['@...@'],
            ['@wildcard@'],
        ];
    }

    public static function validExpanderNamesProvider()
    {
        return [
            ['expanderName(', 'expanderName'],
            ['e(', 'e'],
            ['.e(', 'e'],
        ];
    }

    #[DataProvider('validStringValuesProvider')]
    public function testStringValues(string $value): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_STRING);
        $this->assertEquals($lexer->lookahead->value, trim(trim($value, "'"), '"'));
    }

    #[DataProvider('validNumberValuesProvider')]
    public function testNumberValues($value, $expectedValue): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_NUMBER);
        $this->assertEquals($expectedValue, $lexer->lookahead->value);
    }

    #[DataProvider('validBooleanValuesProvider')]
    public function testBooleanValues($value, $expectedValue): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_BOOLEAN);
        $this->assertEquals($lexer->lookahead->value, strtolower((string) $value));
    }

    #[DataProvider('validNullValuesProvider')]
    public function testNullValues($value): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_NULL);
        $this->assertEquals($lexer->lookahead->value, 'null');
    }

    #[DataProvider('validNonTokenValuesProvider')]
    public function testNonTokenValues($value): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_NONE);
    }

    public function testCloseParenthesis(): void
    {
        $lexer = new Lexer();
        $lexer->setInput(')');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_CLOSE_PARENTHESIS);
    }

    public function testCloseOpenBrace(): void
    {
        $lexer = new Lexer();
        $lexer->setInput('{');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_OPEN_CURLY_BRACE);
    }

    public function testCloseCurlyBrace(): void
    {
        $lexer = new Lexer();
        $lexer->setInput('}');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_CLOSE_CURLY_BRACE);
    }

    public function testColon(): void
    {
        $lexer = new Lexer();
        $lexer->setInput(':');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_COLON);
    }

    public function testComma(): void
    {
        $lexer = new Lexer();
        $lexer->setInput(',');
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_COMMA);
    }

    #[DataProvider('validMatcherTypePatterns')]
    public function testTypePattern($value): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_TYPE_PATTERN);
        $this->assertEquals($lexer->lookahead->value, trim($value, '@'));
    }

    #[DataProvider('validExpanderNamesProvider')]
    public function testExpanderName($value, $expectedTokenValue): void
    {
        $lexer = new Lexer();
        $lexer->setInput($value);
        $lexer->moveNext();
        $this->assertEquals($lexer->lookahead->type, Lexer::T_EXPANDER_NAME);
        $this->assertEquals($lexer->lookahead->value, $expectedTokenValue);
    }

    public function testIgnoreWhitespacesBetweenParenthesis(): void
    {
        $expectedTokens = ['type', 'expander', 'arg1', ',', 2, ',', 'arg3', ',', 4, ')'];
        $lexer = new Lexer();
        $lexer->setInput("@type@.expander( 'arg1',    2    ,'arg3',4)");

        $this->assertEquals($expectedTokens, $this->collectTokens($lexer));
    }

    
    protected function collectTokens(Lexer $lexer)
    {
        $tokens = [];

        while ($lexer->moveNext()) {
            $tokens[] = $lexer->lookahead->value;
        }

        return $tokens;
    }
}
