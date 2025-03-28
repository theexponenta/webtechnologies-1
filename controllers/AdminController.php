<?php


require_once __DIR__.'/../routing/Request.php';
require_once __DIR__.'/../services/AdminService.php';
require_once __DIR__.'/../template_engine/TemplateEngine.php';


class AdminController {

    private TemplateEngine $templateEngine;
    private static array $filetypeIconsUrls = [
        "directory" => "/admin_public/img/directory.svg",
        "regular" => "/admin_public/img/regular.svg"
    ];

    public function __construct() {
        $this->templateEngine = new TemplateEngine(__DIR__.'/../admin_public/html');
    }

    public function show(Request $request): string {
        $path = "";
        if (array_key_exists("path", $request->getParams()))
            $path = $request->getParams()["path"];

        $files = AdminService::listDirectory($path);
        return $this->templateEngine->render("admin.html", ["files" => $files, "filetypeIcons" => AdminController::$filetypeIconsUrls]);
    }

    public function action(Request $request): string {
        $params = $request->getParams();
        if ($request->getMethod() == "GET" && !array_key_exists("action", $params))
            return $this->show($request);

        $action = $params["action"];

        $json = null;
        if ($request->getMethod() == "POST")
            $json = $request->json();
        
        if ($action == "createDirectory") {
            return $this->createDirectory($json["path"]);
        }

        if ($action == "createFile") {
            return $this->createFile($json["path"]);
        }

        if ($action == "saveFile") {
            return $this->saveFile($json["path"], $json["contents"], $json["isBase64"]);
        }

        if ($action == "deleteFile") {
            return $this->deleteFile($json["path"]);
        }

        if ($action == "getFileContents") {
            return $this->getFileContents($params["path"]);
        }

        return json_encode(["error" => "Unknown action"]);
    }

    private function createDirectory(string $path): string {
        if (AdminService::directoryExists($path))
            return json_encode(["error" => "Directory already exists"]);

        AdminService::createDirectory($path);
        return json_encode(["success" => true]);
    }

    private function createFile($path): string {
        if (file_exists($path))
            return json_encode(["error" => "File already exists"]);

        AdminService::createFile($path);
        return json_encode(["success" => true]);
    }

    private function saveFile(string $path, string $content, bool $isBase64): string {
        AdminService::saveFile($path, $content, $isBase64);
        return json_encode(["success" => true]);
    }

    private function deleteFile(string $path): string {
        AdminService::deleteFile($path);
        return json_encode(["success" => true]);
    }

    private function getFileContents(string $path): string {
        return json_encode(["contents" => AdminService::getFileContents($path)]);
    }
}
