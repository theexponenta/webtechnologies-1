<?php

declare(strict_types=1);


require_once __DIR__.'/../config/Config.php';


class LoginController {

    private TemplateEngine $templateEngine;

    public function __construct(DBSession $dbSession, TemplateEngine $templateEngine) {
        $this->templateEngine = $templateEngine;
    }

    public function view(Request $request): string {
        $config = Config::getConfig();
        return $this->templateEngine->render("login.html", ["captcha" => $config["captcha"]]);    
    }

}
