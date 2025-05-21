<?php

declare(strict_types=1);


require_once 'ErrorCode.php';
require_once __DIR__.'/../repositories/UserRepository.php';


class RegisterController {

    private TemplateEngine $templateEngine;

    public function __construct(DBSession $dbSession, TemplateEngine $templateEngine) {
        $this->templateEngine = $templateEngine;
    }

    public function view(Request $request): string {
        $config = Config::getConfig();
        return $this->templateEngine->render("register.html", ["captcha" => $config["captcha"]]);    
    }

}
