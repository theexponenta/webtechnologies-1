<?php


declare(strict_types=1);


class IdentifierStatement extends Statement {
    public string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}
