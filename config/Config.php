<?php

declare(strict_types=1);

class Config
{
    private static ?array $config = null;
    private static $FILE_PATH = 'config.json';

    public static function getConfig(): array
    {
        if (self::$config === null) {
            $json = file_get_contents(self::$FILE_PATH);
            self::$config = json_decode($json, true);
        }  

        return self::$config;
    }

}
