<?php


declare(strict_types=1);


class AccessArrayStatement extends Statement {
    public string $arrayIdentifier;
    public Statement $keyStatement;

    public function __construct(string $arrayIdentifier, Statement $keyStatement) {
        $this->arrayIdentifier = $arrayIdentifier;
        $this->keyStatement = $keyStatement;
    }
}
