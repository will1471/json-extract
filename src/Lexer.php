<?php
declare(strict_types=1);

namespace Will1471\JsonExtract;

final class Lexer
{

    public const EOF = 0;
    public const LEFT_BRACE = 1;
    public const RIGHT_BRACE = 2;
    public const LEFT_BRACKET = 3;
    public const RIGHT_BRACKET = 4;
    public const COMMA = 5;
    public const COLON = 6;
    public const STRING = 7;
    public const NUMBER = 8;
    public const KEYWORD = 9;
    public const ERROR = 10;

    private const NAMES = [
        self::EOF => 'EOF',
        self::LEFT_BRACE => 'LEFT_BRACE',
        self::RIGHT_BRACE => 'RIGHT_BRACE',
        self::LEFT_BRACKET => 'LEFT_BRACKET',
        self::RIGHT_BRACKET => 'RIGHT_BRACKET',
        self::COLON => 'COLON',
        self::COMMA => 'COMMA',
        self::STRING => 'STRING',
        self::NUMBER => 'NUMBER',
        self::KEYWORD => 'KEYWORD',
        self::ERROR => 'ERROR'
    ];

    /**
     * @var string
     */
    private $input;

    /**
     * pointer / index
     *
     * @var int
     */
    private $p = 0;

    /**
     * current char
     *
     * @var string
     */
    private $c;

    private $left;

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->c = $input[$this->p];
    }

    private function consume(): void
    {
        $this->p++;
        if ($this->p >= strlen($this->input)) {
            $this->c = self::EOF;
        } else {
            $this->c = $this->input[$this->p];
        }
        $this->left = substr($this->input, $this->p);
    }

    public static function getTokenName($int): string
    {
        if (!array_key_exists($int, self::NAMES)) {
            throw new \InvalidArgumentException('Invalid token type.');
        }
        return self::NAMES[$int];
    }

    public function nextToken(): Token
    {
        while ($this->c !== self::EOF) {
            switch ($this->c) {
                case ' ':
                case '\t':
                case '\n':
                case '\r':
                    $this->skipWhiteSpace();
                    break;

                case ',':
                    $this->consume();
                    return new Token(self::COMMA, ',');

                case ':':
                    $this->consume();
                    return new Token(self::COLON, ':');

                case '[':
                    $this->consume();
                    return new Token(self::LEFT_BRACKET, '[');

                case ']':
                    $this->consume();
                    return new Token(self::RIGHT_BRACKET, ']');

                case '{':
                    $this->consume();
                    return new Token(self::LEFT_BRACE, '{');

                case '}':
                    $this->consume();
                    return new Token(self::RIGHT_BRACE, '}');

                case '-':
                case '0':
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    return $this->extractNumber();

                case '"':
                    return $this->extractString();

                case 'n':
                case 't':
                case 'f':
                    return $this->extractKeyword();

                default:
                    $c = $this->c;
                    $this->consume();
                    return new Token(self::ERROR, $c);
            }
        }
        return new Token(self::EOF, '');
    }

    private function skipWhiteSpace(): void
    {
        while (ctype_space($this->c)) {
            $this->consume();
        }
    }

    private function extractString(): Token
    {
        return $this->extractRegex(
            self::STRING,
            '~^"([^"\\\\]|\\\\"|\\\\|\\\\/|\\\\b|\\\\f|\\\\n|\\\\r|\\\\t|\\\\u[0-9a-f]{4})*"~'
        );
    }

    private function extractNumber(): Token
    {
        return $this->extractRegex(
            self::NUMBER,
            '~^-?(0|[1-9]\d*)(\.\d+)?([eE][+-]?\d+)?~'
        );
    }

    private function extractKeyword(): Token
    {
        return $this->extractRegex(self::KEYWORD, '~^(true|false|null)~');
    }

    private function extractRegex(int $tokenType, string $regex): Token
    {
        $input = substr($this->input, $this->p);

        $r = preg_match(
            $regex,
            $input,
            $matches
        );
        #var_dump($matches[0]);
        if (!$r) {
            $t = new Token(self::ERROR, $this->c);
            $this->consume();
            return $t;
        }

        $this->p += strlen($matches[0]);
        $this->c = $this->input[$this->p];

        return new Token($tokenType, $matches[0]);
    }
}
