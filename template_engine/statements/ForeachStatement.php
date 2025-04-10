<?php


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
