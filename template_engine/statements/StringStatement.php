<?php


declare(strict_types=1);


class StringStatement extends Statement {
    public string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }
}
