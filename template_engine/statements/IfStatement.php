<?php

declare(strict_types=1);


enum ConditionType
{
    case EQUAL;
    case NOT_EQUAL;
    case GREATER_THAN;
    case LESS_THAN;
    case GREATER_THAN_OR_EQUAL;
    case LESS_THAN_OR_EQUAL;
}


class IfStatement extends Statement {
    public string $var1Identifier;
    public string $var2Identifier;
    public ConditionType $conditionType;
    public array $trueStatements;
    public ?array $falseStatements = null;

    public function __construct(string $var1Identifier, string $var2Identifier, ConditionType $conditionType, array $trueStatements, ?array $falseStatements = null) {
        $this->var1Identifier = $var1Identifier;
        $this->var2Identifier = $var2Identifier;
        $this->conditionType = $conditionType;
        $this->trueStatements = $trueStatements;
        $this->falseStatements = $falseStatements;
    }
}
