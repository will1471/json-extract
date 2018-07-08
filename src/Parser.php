<?php
declare(strict_types=1);

namespace Will1471\JsonExtract;

final class Parser
{

    /**
     * @var Lexer
     */
    private $l;

    /**
     * @var Token
     */
    private $c;

    /**
     * @var Token[]
     */
    private $current = [];

    public function __construct(Lexer $l)
    {
        $this->l = $l;
    }

    private function consume(): void
    {
        $this->c = $this->l->nextToken();
        $this->current[] = $this->c;
    }

    private function match(int $type): void
    {
        if ($this->c->type() === $type) {
            $this->consume();
        } else {
            throw new UnexpectedToken(
                "Expecting token " . Lexer::getTokenName($type)
                . " found " . Lexer::getTokenName($this->c->type())
            );
        }
    }

    private function reset(): void
    {
        $this->current = [$this->c];
    }

    public function parse(Collector $collector)
    {
        $this->consume();
        while ($this->c->type() !== Lexer::EOF) {
            try {
                if ($this->c->type() === Lexer::LEFT_BRACKET) {
                    $this->parseArray();
                    $collector->collectArray(
                        implode(
                            '',
                            array_slice($this->current, 0, -1)
                        )
                    );
                    $this->current = [$this->c];
                    continue;
                }

                if ($this->c->type() === Lexer::LEFT_BRACE) {
                    $this->parseObject();
                    $collector->collectObject(
                        implode(
                            '',
                            array_slice($this->current, 0, -1)
                        )
                    );
                    $this->current = [$this->c];
                    continue;
                }
            } catch (\Exception $e) {
                $this->reset();
                continue;
            }

            $this->consume();
            $this->reset();
        }
    }

    private function parseArray(): void
    {
        $this->match(Lexer::LEFT_BRACKET);
        if ($this->c->type() === Lexer::RIGHT_BRACKET) {
            $this->match(Lexer::RIGHT_BRACKET);
        } else {
            $this->parseValues();
            $this->match(Lexer::RIGHT_BRACKET);
        }
    }

    private function parseObject(): void
    {
        $this->match(Lexer::LEFT_BRACE);
        if ($this->c->type() === Lexer::RIGHT_BRACE) {
            $this->match(Lexer::RIGHT_BRACE);
        } else {
            $this->parseMembers();
            $this->match(Lexer::RIGHT_BRACE);
        }
    }

    private function parseValues(): void
    {
        $this->parseValue();
        if ($this->c->type() == Lexer::COMMA) {
            $this->match(Lexer::COMMA);
            $this->parseValues();
        }
    }

    private function parseMembers(): void
    {
        $this->parsePair();
        if ($this->c->type() == Lexer::COMMA) {
            $this->match(Lexer::COMMA);
            $this->parseMembers();
        }
    }

    private function parsePair(): void
    {
        $this->match(Lexer::STRING);
        $this->match(Lexer::COLON);
        $this->parseValue();
    }

    private function parseValue(): void
    {
        if ($this->c->type() == Lexer::STRING) {
            $this->match(Lexer::STRING);
        }
        if ($this->c->type() == Lexer::NUMBER) {
            $this->match(Lexer::NUMBER);
        }
        if ($this->c->type() == Lexer::KEYWORD) {
            $this->match(Lexer::KEYWORD);
        }
        if ($this->c->type() == Lexer::LEFT_BRACE) {
            $this->parseObject();
        }
        if ($this->c->type() == Lexer::LEFT_BRACKET) {
            $this->parseArray();
        }
    }
}
