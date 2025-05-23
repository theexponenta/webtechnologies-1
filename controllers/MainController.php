<?php

declare(strict_types=1);


require_once __DIR__.'/../database/DBSession.php';
require_once __DIR__.'/../routing/Request.php';
require_once __DIR__.'/../services/ProductService.php';
require_once __DIR__.'/../repositories/ProductRepository.php';
require_once __DIR__.'/../template_engine/TemplateEngine.php';


class MainController {

    private DBSession $dbSession;
    private TemplateEngine $templateEngine;

    private static string $MAIN_TEMPLATE = "main.html";

    public function __construct(DBSession $dbSession, TemplateEngine $templateEngine) {
        $this->dbSession = $dbSession;
        $this->templateEngine = $templateEngine;
    }

    public function view(Request $request): string {
        $service = new ProductService(new ProductRepository($this->dbSession));
        $products = $service->getAllProducts($this->dbSession);
        for ($i = 0; $i < count($products); $i++) {
            $products[$i] = $products[$i]->toArray();
        }

        $user = null;
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
        }

        return $this->templateEngine->render(self::$MAIN_TEMPLATE, ["products" => $products, "user" => $user]);    
    }

}
