<?php

declare(strict_types=1);

require_once 'Parser.php';


class Compiler {

    private array $statements;
    private array $parameters;

    public function __construct($statements) {
        $this->statements = $statements;
    }

    public function compile($parameters): string {
        $this->parameters = $parameters;
        $result = "";

        foreach ($this->statements as $statement) {
            $result .= $this->compileStatement($statement);
        }

        return $result;
    }

    private function compileStatement($statement): string {
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
