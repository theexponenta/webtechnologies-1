<?php

require_once 'TokenType.php';


class Token {
    public TokenType $type;
    public string | null $value;

    public function __construct(TokenType $type, string | null $value) {
        $this->type = $type;
        $this->value = $value;
    }
}
