<?php

declare(strict_types=1);

class DataStatement extends Statement {
    public string $data;

    public function __construct(string $data) {
        $this->data = $data;
    }
}
