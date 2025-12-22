<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Exception\PatternException;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\TestCase;

class ParserSyntaxErrorTest extends TestCase
{
    private ?Parser $parser = null;

    protected function setUp(): void
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer(new Backtrace\InMemoryBacktrace()));
    }

    public function testUnexpectedStatementAtTypePosition(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 0: Error: Expected "@type@ pattern", got "not"');

        $this->parser->getAST('not_valid_type');
    }

    public function testUnexpectedStatementInsteadOfExpander(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 6: Error: Expected ".expanderName(args) definition", got "anything"');

        $this->parser->getAST('@type@anything');
    }

    public function testEndOfStringAfterOpeningParenthesis(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 14: Error: Expected ")", got end of string.end of string');

        $this->parser->getAST('@type@.expander(');
    }

    public function testNotArgumentAfterOpeningParenthesis(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 16: Error: Expected "string, number, boolean or null argument", got "not"');

        $this->parser->getAST('@type@.expander(not_argument');
    }

    public function testMissingCloseParenthesisAfterSingleArgument(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 22: Error: Expected ")", got end of string.end of string');

        $this->parser->getAST("@type@.expander('string'");
    }

    public function testMissingCloseParenthesisAfterMultipleArguments(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 26: Error: Expected ")", got end of string.end of string');

        $this->parser->getAST("@type@.expander('string',1");
    }

    public function testMissingArgumentAfterComma(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 25: Error: Expected "string, number, boolean or null argument", got ")"');

        $this->parser->getAST("@type@.expander('string',)");
    }

    public function testNotArgumentAfterComma(): void
    {
        $this->expectException(PatternException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 25: Error: Expected "string, number, boolean or null argument", got "not"');

        $this->parser->getAST("@type@.expander('string',not_argument");
    }
}
