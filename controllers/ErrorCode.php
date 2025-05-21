<?php

declare(strict_types=1);

enum ErrorCode: int
{
    case EMAIL_EXISTS = 1;
    case ALL_FIELDS_REQUIRED = 2;
    case INCORRECT_EMAIL_OR_PASSWORD = 3;
    case CAPTCHA_FAILED = 4;
}
