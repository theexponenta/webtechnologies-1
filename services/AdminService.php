<?php

declare(strict_types=1);

require_once __DIR__.'/../utils.php';


class AdminService {
    private static string $basePath = __DIR__."/../public";

    private static function isInBasePath(string $path): bool {
        return str_starts_with(realpath($path), realpath(AdminService::$basePath));
    }

    public static function listDirectory(string $dir): array {
        $result = [];

        $dirTrimmed = trim($dir, "/");
        $fullPath = AdminService::$basePath."/".$dirTrimmed;

        if (!AdminService::isInBasePath($fullPath))
            return $result;

        $files = scandir($fullPath, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if ($file == ".")
                continue;

            $filetype = "regular";
            if (is_dir($fullPath.'/'.$file))
                $filetype = "directory";

            array_push($result, ["name" => $file, "type" => $filetype]);
        }

        return $result;
    }

    public static function directoryExists(string $path): bool {
        $pathTrimmed = trim($path, "/");
        $fullPath = realpath(AdminService::$basePath."/".$pathTrimmed);
        if (!$fullPath)
            return false;

        return is_dir($fullPath);
    }

    public static function createDirectory(string $path): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = AdminService::$basePath."/".$pathTrimmed;
        
        $parentDirectory = realpath(dirname($fullPath));
        if (!AdminService::isInBasePath($parentDirectory))
            return;

        if (is_dir($parentDirectory."/".$pathTrimmed))
            return;

        mkdir($fullPath);
    }

    public static function createFile(string $path): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = AdminService::$basePath."/".$pathTrimmed;
        
        $parentDirectory = dirname($fullPath);
        if (!AdminService::isInBasePath($parentDirectory))
            return;

        if (file_exists($parentDirectory."/".$pathTrimmed))
            return;

        touch($fullPath);
    }

    public static function saveFile(string $path, string $content, bool $isBase64): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = AdminService::$basePath."/".$pathTrimmed;
        
        $parentDirectory = dirname($fullPath);
        if (!AdminService::isInBasePath($parentDirectory))
            return;

        if ($isBase64)
            $content = base64_decode($content);

        file_put_contents($fullPath, $content);
    }

    public static function deleteFile(string $path): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = AdminService::$basePath."/".$pathTrimmed;
        
        if (!AdminService::isInBasePath($fullPath))
            return;

        if (is_dir($fullPath))
            rrmdir($fullPath);
        else
            unlink($fullPath);
    }

    public static function getFileContents(string $path): string {
        $pathTrimmed = trim($path, "/");
        $fullPath = AdminService::$basePath."/".$pathTrimmed;
        
        if (!AdminService::isInBasePath($fullPath))
            return "";

        return file_get_contents($fullPath);
    }
}
