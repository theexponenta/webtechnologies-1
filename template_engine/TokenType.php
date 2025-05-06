<?php

enum TokenType {
    case DATA;
    case BLOCK_BEGIN;
    case BLOCK_END;
    case KEYWORD;
    case OPEN_PAREN;
    case CLOSE_PAREN;
    case OPEN_BRACE;
    case CLOSE_BRACE;
    case STRING_LITERAL;
    case IDENTIFIER;
    case GREATER_THAN;
    case LESS_THAN;
    case GREATER_THAN_OR_EQUAL;
    case LESS_THAN_OR_EQUAL;
    case EQUAL;
    case NOT_EQUAL;
    case EOF;
}
