<?php

declare(strict_types=1);


require_once __DIR__.'/../routing/Request.php';
require_once __DIR__.'/../services/AdminService.php';
require_once __DIR__.'/../template_engine/TemplateEngine.php';


class AdminController {

    private AdminService $adminService;
    private TemplateEngine $templateEngine;
    private array $filetypeIconsUrls = [
        "directory" => "/admin_public/img/directory.svg",
        "regular" => "/admin_public/img/regular.svg"
    ];

    public function __construct() {
        $this->templateEngine = new TemplateEngine(__DIR__.'/../admin_public/html');
        $this->adminService = new AdminService();
    }

    public function show(Request $request): string {
        $path = "";
        if (array_key_exists("path", $request->getParams()))
            $path = $request->getParams()["path"];

        $files = $this->adminService->listDirectory($path);
        return $this->templateEngine->render("admin.html", ["files" => $files, "filetypeIcons" => $this->filetypeIconsUrls]);
    }

    public function action(Request $request): string {
        $params = $request->getParams();
        if ($request->getMethod() === "GET" && !array_key_exists("action", $params))
            return $this->show($request);

        $action = $params["action"];
        if (method_exists($this, $action))
            return $this->$action($request);

        return json_encode(["error" => "Unknown action: $action"]);
    }

    private function createDirectory(Request $request): string {
        $json = $request->json();
        $path = $json["path"];

        if ($this->adminService->directoryExists($path))
            return json_encode(["error" => "Directory already exists"]);

        $this->adminService->createDirectory($path);
        return json_encode(["success" => true]);
    }

    private function createFile(Request $request): string {
        $json = $request->json();
        $path = $json["path"];

        if (file_exists($path))
            return json_encode(["error" => "File already exists"]);

        $this->adminService->createFile($path);
        return json_encode(["success" => true]);
    }

    private function saveFile(Request $request): string {
        $json = $request->json();
        $this->adminService->saveFile($json["path"], $json["contents"], $json["isBase64"]);
        return json_encode(["success" => true]);
    }

    private function deleteFile(Request $request): string {
        $json = $request->json();
        $this->adminService->deleteFile($json["path"]);
        return json_encode(["success" => true]);
    }

    private function getFileContents(Request $request): string {
        $path = $request->getParams()["path"];
        return json_encode(["contents" => $this->adminService->getFileContents($path)]);
    }
}
