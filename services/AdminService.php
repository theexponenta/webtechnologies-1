<?php

declare(strict_types=1);

require_once __DIR__.'/../utils/Utils.php';


class AdminService {
    private string $basePath = __DIR__."/../public";

    private function isInBasePath(string $path): bool {
        return str_starts_with(realpath($path), realpath($this->basePath));
    }

    public function listDirectory(string $dir): array {
        $result = [];

        $dirTrimmed = trim($dir, "/");
        $fullPath = $this->basePath."/".$dirTrimmed;

        if (!$this->isInBasePath($fullPath))
            return $result;

        $files = scandir($fullPath, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if ($file === ".")
                continue;

            $filetype = "regular";
            if (is_dir($fullPath.'/'.$file))
                $filetype = "directory";

            array_push($result, ["name" => $file, "type" => $filetype]);
        }

        return $result;
    }

    public function directoryExists(string $path): bool {
        $pathTrimmed = trim($path, "/");
        $fullPath = realpath($this->basePath."/".$pathTrimmed);
        if (!$fullPath)
            return false;

        return is_dir($fullPath);
    }

    public function createDirectory(string $path): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = $this->basePath."/".$pathTrimmed;
        
        $parentDirectory = realpath(dirname($fullPath));
        if (!$this->isInBasePath($parentDirectory))
            return;

        if (is_dir($parentDirectory."/".$pathTrimmed))
            return;

        mkdir($fullPath);
    }

    public function createFile(string $path): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = $this->basePath."/".$pathTrimmed;
        
        $parentDirectory = dirname($fullPath);
        if (!$this->isInBasePath($parentDirectory))
            return;

        if (file_exists($parentDirectory."/".$pathTrimmed))
            return;

        touch($fullPath);
    }

    public function saveFile(string $path, string $content, bool $isBase64): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = $this->basePath."/".$pathTrimmed;
        
        $parentDirectory = dirname($fullPath);
        if (!$this->isInBasePath($parentDirectory))
            return;

        if ($isBase64)
            $content = base64_decode($content);

        file_put_contents($fullPath, $content);
    }

    public function deleteFile(string $path): void {
        $pathTrimmed = trim($path, "/");
        $fullPath = $this->basePath."/".$pathTrimmed;
        
        if (!$this->isInBasePath($fullPath))
            return;

        if (is_dir($fullPath))
            Utils::rrmdir($fullPath);
        else
            unlink($fullPath);
    }

    public function getFileContents(string $path): string {
        $pathTrimmed = trim($path, "/");
        $fullPath = $this->basePath."/".$pathTrimmed;
        
        if (!$this->isInBasePath($fullPath))
            return "";

        return file_get_contents($fullPath);
    }
}
