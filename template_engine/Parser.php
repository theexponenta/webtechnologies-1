<?php

declare(strict_types=1);

require_once 'Lexer.php';
require_once 'statements/Statement.php';
require_once 'statements/IdentifierStatement.php';
require_once 'statements/DataStatement.php';
require_once 'statements/EndBlockStatement.php';
require_once 'statements/ForeachStatement.php';
require_once 'statements/AccessArrayStatement.php';
require_once 'statements/StringStatement.php';
require_once 'statements/IfStatement.php';


class Parser {

    private Lexer $lexer;
    private int $blockStatementDepth;

    public function __construct(Lexer $lexer) {
        $this->lexer = $lexer;
        $this->blockStatementDepth = 0;
    }

    public function parse() {
        $statements = [];
        while (($currentStatement = $this->parseStatement()) !== null) {
            array_push($statements, $currentStatement);
        }

        return $statements;
    }

    private function parseStatement() : Statement | null {
        $currentToken = $this->lexer->nextToken();

        if ($currentToken->type === TokenType::EOF)
            return null;

        if ($currentToken->type === TokenType::DATA)
            return new DataStatement($currentToken->value);
        
        if ($currentToken->type === TokenType::BLOCK_BEGIN) {
            $blockStatement = $this->parseBlockStatement();
            return $blockStatement;
        }

        throw new Exception("Unexpected token type: ".$currentToken->type->name);       
    }

    private function parseBlockStatement() : Statement {
        $this->blockStatementDepth++;

        $currentToken = $this->lexer->nextToken();
        $nextToken = null;
        $readNextToken = true;

        $result = null;

        if ($currentToken->type === TokenType::IDENTIFIER) {
            $nextToken = $this->lexer->nextToken();
            $readNextToken = false;

            if ($nextToken->type === TokenType::OPEN_BRACE)
                $result = $this->parseAccessArrayExpression($currentToken->value);
            else
                $result = new IdentifierStatement($currentToken->value);
        } else if ($currentToken->type === TokenType::KEYWORD && substr($currentToken->value, 0, strlen("end")) === "end") {            
            $result = new EndBlockStatement(substr($currentToken->value, strlen("end")));
        } else if ($currentToken->type === TokenType::KEYWORD && $currentToken->value === "foreach") {
            $result = $this->parseForeach();
        } else if ($currentToken->type === TokenType::STRING_LITERAL) {
            $result = new StringStatement($currentToken->value);
        } else if ($currentToken->type === TokenType::KEYWORD && $currentToken->value === "if") {
            $result = $this->parseIf();
        } else if ($currentToken->type === TokenType::KEYWORD && $currentToken->value === "endif") {
            $result = new EndBlockStatement($currentToken->value);
            $this->lexer->nextToken();
        } else if ($currentToken->type === TokenType::KEYWORD && $currentToken->value === "else") {
            $result = new EndBlockStatement($currentToken->value);
            $this->lexer->nextToken();
        }

        if ($this->blockStatementDepth === 1 && $this->lexer->nextToken()->type !== TokenType::BLOCK_END)
            throw new Exception("Unexpected token type: ".$currentToken->type->name);

        $this->blockStatementDepth--;

        return $result;
    }

    private function parseForeach() : ForeachStatement {
        if ($this->lexer->nextToken()->type !== TokenType::OPEN_PAREN)
            throw new Exception("Expected '('");

        $iterableIdentifierToken = $this->lexer->nextToken();
        if ($iterableIdentifierToken->type !== TokenType::IDENTIFIER)
            throw new Exception("Expected identifier");        

        if ($this->lexer->nextToken()->value !== "as")
            throw new Exception("Expected 'as'");

        $asIdentifierToken = $this->lexer->nextToken();
        if ($asIdentifierToken->type !== TokenType::IDENTIFIER)
            throw new Exception("Expected identifier");  

        if ($this->lexer->nextToken()->type !== TokenType::CLOSE_PAREN)
            throw new Exception("Expected ')'");

        if ($this->lexer->nextToken()->type !== TokenType::BLOCK_END)
            throw new Exception("Expected '}}'");

        $statements = [];

        while (!$this->lexer->eof()) {
            $statement = $this->parseStatement();
            if ($statement instanceof EndBlockStatement) {
                if ($statement->blockName !== "foreach")
                    throw new Exception("Expected end block for foreach statement");

                return new ForeachStatement($iterableIdentifierToken->value, $asIdentifierToken->value, $statements);
            }

            array_push($statements, $statement);
        }
    }

    private function parseIf() : IfStatement {
        if ($this->lexer->nextToken()->type !== TokenType::OPEN_PAREN)
            throw new Exception("Expected '('");

        $identifierToken = $this->lexer->nextToken();
        if ($identifierToken->type !== TokenType::IDENTIFIER)
            throw new Exception("Expected identifier");
        
        if ($this->lexer->nextToken()->type !== TokenType::CLOSE_PAREN)
            throw new Exception("Expected ')'");

        if ($this->lexer->nextToken()->type !== TokenType::BLOCK_END)
            throw new Exception("Expected '}}'");
        
        $trueStatements = [];
        $falseStatements = [];

        $statementsArr = &$trueStatements;

        while (!$this->lexer->eof()) {
            $statement = $this->parseStatement();

            if ($statement instanceof EndBlockStatement) {
                if ($statement->blockName === "else") {
                    $statementsArr = &$falseStatements;
                } else if ($statement->blockName === "if") {
                    return new IfStatement($identifierToken->value, $identifierToken->value, ConditionType::EQUAL, $trueStatements, $falseStatements);
                }
            }

            array_push($statementsArr, $statement);
        }
    }

    private function parseAccessArrayExpression(string $arrayIdentifier) : AccessArrayStatement {
        $keyStatement = $this->parseBlockStatement();

        if ($this->lexer->nextToken()->type !== TokenType::CLOSE_BRACE)
            throw new Exception("Expected ']'");

        if ($this->blockStatementDepth === 2 && $this->lexer->nextToken()->type !== TokenType::BLOCK_END)
            throw new Exception("Expected '}}'");

        return new AccessArrayStatement($arrayIdentifier, $keyStatement);
    }
}
