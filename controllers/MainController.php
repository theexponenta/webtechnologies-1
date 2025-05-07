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

        return $this->templateEngine->render("main.html", ["products" => $products]);    
    }

}
