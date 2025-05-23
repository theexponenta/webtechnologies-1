<?php

declare(strict_types=1);

require_once 'Parser.php';
require_once 'statements/Statement.php';
require_once 'statements/IdentifierStatement.php';
require_once 'statements/DataStatement.php';
require_once 'statements/EndBlockStatement.php';
require_once 'statements/ForeachStatement.php';
require_once 'statements/AccessArrayStatement.php';
require_once 'statements/StringStatement.php';


class Compiler {

    private array $statements;
    private array $parameters;

    public function __construct($statements) {
        $this->statements = $statements;
    }

    public function compile($parameters): string {
        $this->parameters = $parameters;
        return $this->compileStatements($this->statements);
    }

    private function compileStatements(array $statements) {
        $result = "";

        foreach ($statements as $statement) {
            $result .= $this->compileStatement($statement);
        }

        return $result;
    }

    private function compileStatement($statement): string|null {
        if ($statement instanceof DataStatement)
            return $statement->data; 

        if ($statement instanceof IdentifierStatement)
            return strval($this->parameters[$statement->name]);

        if ($statement instanceof ForeachStatement)
            return $this->compileForeachStatement($statement);

        if ($statement instanceof StringStatement)
            return $statement->value;

        if ($statement instanceof AccessArrayStatement)
            return strval($this->parameters[$statement->arrayIdentifier][$this->compileStatement($statement->keyStatement)]);
    
        if ($statement instanceof IfStatement) {
            if ($this->parameters[$statement->var1Identifier]) {
                return $this->compileStatements($statement->trueStatements);
            }
            else
                return $this->compileStatements($statement->falseStatements);
        }

        return null;
    }

    private function compileForeachStatement($statement): string {
        $result = "";

        foreach ($this->parameters[$statement->iterableIdentifier] as $elem) {
            $this->parameters[$statement->asIdentifier] = $elem;

            foreach ($statement->statements as $statement_) {
                $result .= $this->compileStatement($statement_);
            }
        }

        unset($this->parameters[$statement->asIdentifier]);

        return $result;
    }
}
