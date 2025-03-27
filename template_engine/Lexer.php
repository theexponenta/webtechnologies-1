<?php

declare(strict_types=1);


enum TokenType {
    case DATA;
    case BLOCK_BEGIN;
    case BLOCK_END;
    case KEYWORD;
    case OPEN_PAREN;
    case CLOSE_PAREN;
    case OPEN_BRACE;
    case CLOSE_BRACE;
    case STRING_LITERAL;
    case IDENTIFIER;
    case EOF;
}


class Token {
    public TokenType $type;
    public string | null $value;

    public function __construct(TokenType $type, string | null $value) {
        $this->type = $type;
        $this->value = $value;
    }
}


const CONST_DATA_TOKENS = [
    "endforeach" => TokenType::KEYWORD,
    "foreach" => TokenType::KEYWORD,
    "as" => TokenType::KEYWORD,
    "{{" => TokenType::BLOCK_BEGIN,
    "}}" => TokenType::BLOCK_END,
    "(" => TokenType::OPEN_PAREN,
    ")" => TokenType::CLOSE_PAREN,
    "[" => TokenType::OPEN_BRACE,
    "]" => TokenType::CLOSE_BRACE
];


const WHITESPACE_CHARS = " \r\n\t";


class Lexer {
    private string $source;
    private int $index;
    private bool $readingData;
    private bool $eof;

    public function __construct(string $source) {
        $this->source = $source;
        $this->index = 0;
        $this->readingData = true;
        $this->eof = false;
    }

    public function nextToken(): Token {
        if ($this->index >= strlen($this->source))
            return new Token(TokenType::EOF, null);

        if ($this->readingData) {
            $startIndex = $this->index;
            while ($this->index < strlen($this->source) && !$this->nextStringEquals("{{"))
                $this->index++;

            if ($this->index >= strlen($this->source))
                $this->eof = true;

            $this->readingData = false;
            if ($startIndex != $this->index)
                return new Token(TokenType::DATA, substr($this->source, $startIndex, $this->index - $startIndex));
        
            return $this->readConstDataToken();
        }
        
        $this->skipWhitespaces();
        
        $constDataToken = $this->readConstDataToken();
        if ($constDataToken) {
            $this->readingData = $constDataToken->type == TokenType::BLOCK_END;
            return $constDataToken;
        }

        $identifierToken = $this->readIdentifierToken();
        if ($identifierToken)
            return $identifierToken;

        $stringLiteralTOken = $this->readStringLiteral();
        if ($stringLiteralTOken)
            return $stringLiteralTOken;

        $curChar = $this->source[$this->index];
        throw new Exception("Unknown token: $curChar");
    }

    public function eof(): bool {
        return $this->eof;
    }

    private function nextStringEquals(string $string): bool {
        $result = true;
        $strInd = 0;
        $sourceInd = $this->index;
        while ($result && $sourceInd < strlen($this->source) && $strInd < strlen($string)) {
            $result = $this->source[$sourceInd] == $string[$strInd];
            $sourceInd++;
            $strInd++;
        }

        return $result;
    }

    private function readConstDataToken(): Token | null {
        foreach (CONST_DATA_TOKENS as $tokenValue => $tokenType) {            
            if ($this->nextStringEquals($tokenValue)) {
                $this->index += strlen($tokenValue);
                return new Token($tokenType, $tokenValue);
            }
        }

        return null;
    }

    private function isIdentifierChar(string $c): bool {
        return ctype_alnum($c) || $c == '_';
    }

    private function readIdentifierToken(): Token | null {
        $curChar = $this->source[$this->index];
        if (!$this->isIdentifierChar($curChar) || is_numeric($curChar))
            return null;

        $startIndex = $this->index;
        while ($this->isIdentifierChar($this->source[$this->index])) {
            $this->index++;
        }

        return new Token(TokenType::IDENTIFIER, substr($this->source, $startIndex, $this->index - $startIndex));
    }

    private function readStringLiteral(): Token | null {
        $enclosingChar = $this->source[$this->index];
        if ($enclosingChar != '"' && $enclosingChar != "'")
            return null;

        $startIndex = $this->index;
        $this->index++;
        while ($this->source[$this->index] != $enclosingChar) {
            $this->index++;
        }

        $this->index++;
        return new Token(TokenType::STRING_LITERAL, substr($this->source, $startIndex + 1, $this->index - $startIndex - 2));
    }

    private function skipWhitespaces() {
        while (strpos(WHITESPACE_CHARS, $this->source[$this->index]) !== false) {
            $this->index++;
        }
    }
}
