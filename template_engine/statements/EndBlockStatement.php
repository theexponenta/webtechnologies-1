<?php


class EndBlockStatement extends Statement {
    public string $blockName;

    public function __construct(string $blockName) {
        $this->blockName = $blockName;
    }
}
