<?php

declare(strict_types=1);

require_once 'Lexer.php';


abstract class Statement {}

class DataStatement extends Statement {
    public string $data;

    public function __construct(string $data) {
        $this->data = $data;
    }
}



class ForeachStatement extends Statement {
    public string $iterableIdentifier;
    public string $asIdentifier;
    public array $statements;

    public function __construct(string $iterableIdentifier, string $asIdentifier, array $statements) {
        $this->iterableIdentifier = $iterableIdentifier;
        $this->asIdentifier = $asIdentifier;
        $this->statements = $statements;
    }
}


class IdentifierStatement extends Statement {
    public string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}


class AccessArrayStatement extends Statement {
    public string $arrayIdentifier;
    public Statement $keyStatement;

    public function __construct(string $arrayIdentifier, Statement $keyStatement) {
        $this->arrayIdentifier = $arrayIdentifier;
        $this->keyStatement = $keyStatement;
    }
}


class StringStatement extends Statement {
    public string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }
}


class EndBlockStatement extends Statement {
    public string $blockName;

    public function __construct(string $blockName) {
        $this->blockName = $blockName;
    }
}


class Parser {

    private Lexer $lexer;
    private int $blockStatementDepth;

    public function __construct(Lexer $lexer) {
        $this->lexer = $lexer;
        $this->blockStatementDepth = 0;
    }

    public function parse() {
        $statements = [];
        while (($currentStatement = $this->parseStatement()) != null) {
            array_push($statements, $currentStatement);
        }

        return $statements;
    }

    private function parseStatement() : Statement | null {
        $currentToken = $this->lexer->nextToken();

        if ($currentToken->type == TokenType::EOF)
            return null;

        if ($currentToken->type == TokenType::DATA)
            return new DataStatement($currentToken->value);
        
        if ($currentToken->type == TokenType::BLOCK_BEGIN) {
            $blockStatement = $this->parseBlockStatement();
            return $blockStatement;
        }

        throw new Exception("Unexpected token type: ".$currentToken->type->name);       
    }

    private function parseBlockStatement() : Statement {
        $this->blockStatementDepth++;

        $currentToken = $this->lexer->nextToken();

        $result = null;

        if ($currentToken->type == TokenType::IDENTIFIER) {
            $nextToken = $this->lexer->nextToken();

            if ($nextToken->type == TokenType::OPEN_BRACE)
                $result = $this->parseAccessArrayExpression($currentToken->value);
            else
                $result = new IdentifierStatement($currentToken->value);
        } else if ($currentToken->type == TokenType::KEYWORD && substr($currentToken->value, 0, strlen("end")) == "end") {            
            $result = new EndBlockStatement(substr($currentToken->value, strlen("end")));
        } else if ($currentToken->type == TokenType::KEYWORD && $currentToken->value == "foreach") {
            $result = $this->parseForeach();
        } else if ($currentToken->type == TokenType::STRING_LITERAL) {
            $result = new StringStatement($currentToken->value);
        } 
        
        if ($this->blockStatementDepth == 1 && $this->lexer->nextToken()->type != TokenType::BLOCK_END)
            throw new Exception("Unexpected token type: ".$currentToken->type->name);

        $this->blockStatementDepth--;

        return $result;
    }

    private function parseForeach() : ForeachStatement {
        if ($this->lexer->nextToken()->type != TokenType::OPEN_PAREN)
            throw new Exception("Expected '('");

        $iterableIdentifierToken = $this->lexer->nextToken();
        if ($iterableIdentifierToken->type != TokenType::IDENTIFIER)
            throw new Exception("Expected identifier");        

        if ($this->lexer->nextToken()->value != "as")
            throw new Exception("Expected 'as'");

        $asIdentifierToken = $this->lexer->nextToken();
        if ($asIdentifierToken->type != TokenType::IDENTIFIER)
            throw new Exception("Expected identifier");  

        if ($this->lexer->nextToken()->type != TokenType::CLOSE_PAREN)
            throw new Exception("Expected ')'");

        if ($this->lexer->nextToken()->type != TokenType::BLOCK_END)
            throw new Exception("Expected '}}'");

        $statements = [];

        while (!$this->lexer->eof()) {
            $statement = $this->parseStatement();
            if ($statement instanceof EndBlockStatement) {
                if ($statement->blockName != "foreach")
                    throw new Exception("Expected end block for foreach statement");

                return new ForeachStatement($iterableIdentifierToken->value, $asIdentifierToken->value, $statements);
            }

            array_push($statements, $statement);
        }
    }

    private function parseAccessArrayExpression(string $arrayIdentifier) : AccessArrayStatement {
        $keyStatement = $this->parseBlockStatement();

        if ($this->lexer->nextToken()->type != TokenType::CLOSE_BRACE)
            throw new Exception("Expected ']'");

        if ($this->blockStatementDepth == 2 && $this->lexer->nextToken()->type != TokenType::BLOCK_END)
            throw new Exception("Expected '}}'");

        return new AccessArrayStatement($arrayIdentifier, $keyStatement);
    }
}
