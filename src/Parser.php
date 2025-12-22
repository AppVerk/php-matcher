<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher;

use AppVerk\PHPMatcher\Matcher\Pattern\TypePattern;
use AppVerk\PHPMatcher\AST\Type;
use AppVerk\PHPMatcher\AST\Expander;
use AppVerk\PHPMatcher\Exception\Exception;
use AppVerk\PHPMatcher\Exception\PatternException;
use AppVerk\PHPMatcher\Matcher\Pattern;
use AppVerk\PHPMatcher\Parser\ExpanderInitializer;
use Doctrine\Common\Lexer\Token;

use function is_int;
use function sprintf;
use function strlen;

final readonly class Parser
{
    public const string NULL_VALUE = 'null';

    public function __construct(
        private Lexer $lexer,
        private ExpanderInitializer $expanderInitializer
    ) {
    }

    public function hasValidSyntax(string $pattern): bool
    {
        try {
            $this->getAST($pattern);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function parse(string $pattern): TypePattern
    {
        $AST = $this->getAST($pattern);
        $pattern = new TypePattern((string) $AST->getType());

        foreach ($AST->getExpanders() as $expander) {
            $pattern->addExpander($this->expanderInitializer->initialize($expander));
        }

        return $pattern;
    }

    public function getAST(string $pattern): AST\Pattern
    {
        if ($pattern === '') {
            return new AST\Pattern(new Type(''));
        }

        $this->lexer->setInput($pattern);

        return $this->getPattern();
    }

    private function getPattern(): AST\Pattern
    {
        $this->lexer->moveNext();

        $pattern = null;

        if ($this->lexer->lookahead->type == Lexer::T_TYPE_PATTERN) {
            $pattern = new AST\Pattern(new Type($this->lexer->lookahead->value));
        } else {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '@type@ pattern'));
        }

        $this->lexer->moveNext();

        if (!$this->endOfPattern() && $pattern instanceof AST\Pattern) {
            $this->addExpanderNodes($pattern);
        }

        return $pattern;
    }

    private function addExpanderNodes(AST\Pattern $pattern): void
    {
        while (($expander = $this->getNextExpanderNode()) instanceof Expander) {
            $pattern->addExpander($expander);
        }
    }

    
    private function getNextExpanderNode(): ?Expander
    {
        if ($this->endOfPattern()) {
            return null;
        }

        $expander = new Expander($this->getExpanderName());

        
        if ($this->endOfPattern()) {
            $this->unexpectedEndOfString(')');
        }

        $this->addArgumentValues($expander);

        if ($this->endOfPattern()) {
            $this->unexpectedEndOfString(')');
        }

        if (!$this->isNextCloseParenthesis()) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, ')'));
        }

        return $expander;
    }

    private function getExpanderName(): string
    {
        if ($this->lexer->lookahead->type !== Lexer::T_EXPANDER_NAME) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '.expanderName(args) definition'));
        }

        $expander = $this->lexer->lookahead->value;

        
        if ($expander === null) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '.expanderName(args) definition'));
        }

        $this->lexer->moveNext();

        if (is_int($expander)) {
            return (string) $expander;
        }

        return $expander;
    }

    
    private function addArgumentValues(Expander $expander): void
    {
        while (($argument = $this->getNextArgumentValue()) !== null) {
            $argument = ($argument === self::NULL_VALUE) ? null : $argument;
            $expander->addArgument($argument);

            if (!$this->lexer->isNextToken(Lexer::T_COMMA)) {
                break;
            }

            $this->lexer->moveNext();

            if ($this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
                throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, 'string, number, boolean or null argument'));
            }
        }
    }

    
    private function getNextArgumentValue(): null|array|Expander|float|int|string|bool
    {
        $validArgumentTypes = [
            Lexer::T_STRING,
            Lexer::T_NUMBER,
            Lexer::T_BOOLEAN,
            Lexer::T_NULL,
        ];

        if ($this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
            return null;
        }

        if ($this->lexer->isNextToken(Lexer::T_OPEN_CURLY_BRACE)) {
            return $this->getArrayArgument();
        }

        if ($this->lexer->isNextToken(Lexer::T_EXPANDER_NAME)) {
            return $this->getNextExpanderNode();
        }

        if (!$this->lexer->isNextTokenAny($validArgumentTypes)) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, 'string, number, boolean or null argument'));
        }

        $tokenType = $this->lexer->lookahead->type;
        $argument = $this->lexer->lookahead->value;
        $this->lexer->moveNext();

        if ($tokenType === Lexer::T_NULL) {
            $argument = self::NULL_VALUE;
        }

        if ($tokenType === Lexer::T_BOOLEAN) {
            $argument = strtolower((string) $argument) === 'true';
        }

        if ($tokenType === Lexer::T_NUMBER) {
            return (str_contains((string) $argument, '.')) ? (float) $argument : (int) $argument;
        }

        return $argument;
    }

    private function getArrayArgument(): array
    {
        $arrayArgument = [];
        $this->lexer->moveNext();

        while ($this->getNextArrayElement($arrayArgument) !== null) {
            $this->lexer->moveNext();
        }

        if (!$this->lexer->isNextToken(Lexer::T_CLOSE_CURLY_BRACE)) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '}'));
        }

        $this->lexer->moveNext();

        return $arrayArgument;
    }

    private function getNextArrayElement(array &$array): ?bool
    {
        if ($this->lexer->isNextToken(Lexer::T_CLOSE_CURLY_BRACE)) {
            return null;
        }

        $key = $this->getNextArgumentValue();

        if ($key === self::NULL_VALUE) {
            $key = '';
        }

        if (!$this->lexer->isNextToken(Lexer::T_COLON)) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, ':'));
        }

        $this->lexer->moveNext();

        $value = $this->getNextArgumentValue();

        if ($value === self::NULL_VALUE) {
            $value = null;
        }

        $array[$key] = $value;

        if (!$this->lexer->isNextToken(Lexer::T_COMMA)) {
            return null;
        }

        return true;
    }

    private function isNextCloseParenthesis(): bool
    {
        $isCloseParenthesis = $this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS);
        $this->lexer->moveNext();

        return $isCloseParenthesis;
    }

    private function unexpectedSyntaxError(Token $unexpectedToken, ?string $expected = null): string
    {
        $tokenPos = $unexpectedToken->position ?? '-1';
        $message = sprintf('line 0, col %d: Error: ', $tokenPos);
        $message .= (isset($expected)) ? sprintf('Expected "%s", got ', $expected) : 'Unexpected';

        return $message . sprintf('"%s"', $unexpectedToken->value);
    }

    
    private function unexpectedEndOfString(?string $expected = null): never
    {
        $tokenPos = (isset($this->lexer->token->position)) ? $this->lexer->token->position + strlen((string) $this->lexer->token->value) : '-1';
        $message = sprintf('line 0, col %d: Error: ', $tokenPos);
        $message .= (isset($expected)) ? sprintf('Expected "%s", got end of string.', $expected) : 'Unexpected';
        $message .= 'end of string';

        throw PatternException::syntaxError($message);
    }

    private function endOfPattern(): bool
    {
        return !$this->lexer->lookahead instanceof Token;
    }
}
