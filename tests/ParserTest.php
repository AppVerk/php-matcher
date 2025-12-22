<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests;

use AppVerk\PHPMatcher\AST\Expander;
use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Lexer;
use AppVerk\PHPMatcher\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private ?Parser $parser = null;

    public static function expandersWithArrayArguments()
    {
        return [
            [
                '@type@.expander({"foo":"bar"})',
                [['foo' => 'bar']],
            ],
            [
                '@type@.expander({1 : "bar"})',
                [[1 => 'bar']],
            ],
            [
                '@type@.expander({"foo":"bar"}, {"foz" : "baz"})',
                [['foo' => 'bar'], ['foz' => 'baz']],
            ],
            [
                '@type@.expander({1 : 1})',
                [[1 => 1]],
            ],
            [
                '@type@.expander({1 : true})',
                [[1 => true]],
            ],
            [
                '@type@.expander({1 : 1}, {1 : 1})',
                [[1 => 1], [1 => 1]],
            ],
            [
                '@type@.expander({1 : {"foo" : "bar"}}, {1 : 1})',
                [[1 => ['foo' => 'bar']], [1 => 1]],
            ],
            [
                '@type@.expander({null: "bar"})',
                [['' => 'bar']],
            ],
            [
                '@type@.expander({"foo": null})',
                [['foo' => null]],
            ],
            [
                '@type@.expander({"foo" : "bar", "foz" : "baz"})',
                [['foo' => 'bar', 'foz' => 'baz']],
            ],
            [
                '@type@.expander({"foo" : "bar", "foo" : "baz"})',
                [['foo' => 'baz']],
            ],
            [
                '@type@.expander({"foo" : "bar", 1 : {"first" : 1, "second" : 2}})',
                [['foo' => 'bar', 1 => ['first' => 1, 'second' => 2]]],
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->parser = new Parser(new Lexer(), new Parser\ExpanderInitializer(new Backtrace\InMemoryBacktrace()));
    }

    public function testSimplePatternWithoutExpanders(): void
    {
        $pattern = '@type@';

        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $this->assertFalse($this->parser->getAST($pattern)->hasExpanders());
    }

    public function testSingleExpanderWithoutArgs(): void
    {
        $pattern = '@type@.expander()';

        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals('expander', $expanders[0]->getName());
        $this->assertFalse($expanders[0]->hasArguments());
    }

    public function testSingleExpanderWithArguments(): void
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\")";
        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $expectedArguments = [
            'arg1',
            2,
            2.24,
            'arg3',
        ];
        $this->assertEquals($expectedArguments, $expanders[0]->getArguments());
    }

    public function testManyExpanders(): void
    {
        $pattern = "@type@.expander('arg1', 2, 2.24, \"arg3\", null, false).expander1().expander(1,2,3, true, null)";
        $expanderArguments = [
            ['arg1', 2, 2.24, 'arg3', null, false],
            [],
            [1, 2, 3, true, null],
        ];

        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals('type', $this->parser->getAST($pattern)->getType());
        $this->assertEquals('expander', $expanders[0]->getName());
        $this->assertEquals('expander1', $expanders[1]->getName());
        $this->assertEquals('expander', $expanders[2]->getName());

        $this->assertEquals($expanderArguments[0], $expanders[0]->getArguments());
        $this->assertEquals($expanderArguments[1], $expanders[1]->getArguments());
        $this->assertEquals($expanderArguments[2], $expanders[2]->getArguments());
    }

    #[DataProvider('expandersWithArrayArguments')]
    public function testSingleArrayArgumentWithStringKeyValue($pattern, $expectedArgument): void
    {
        $expanders = $this->parser->getAST($pattern)->getExpanders();
        $this->assertEquals($expectedArgument, $expanders[0]->getArguments());
    }

    public function testExpandersThatTakesOtherExpandersAsArguments(): void
    {
        $pattern = '@type@.expander(expander("test"), expander(1))';
        $expanders = $this->parser->getAST($pattern)->getExpanders();

        $firstExpander = new Expander('expander');
        $firstExpander->addArgument('test');
        $secondExpander = new Expander('expander');
        $secondExpander->addArgument(1);

        $this->assertEquals(
            $expanders[0]->getArguments(),
            [
                $firstExpander,
                $secondExpander,
            ],
        );
    }
}
