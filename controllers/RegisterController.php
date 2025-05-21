<?php

declare(strict_types=1);


require_once 'ErrorCode.php';
require_once __DIR__.'/../repositories/UserRepository.php';


class RegisterController {

    private TemplateEngine $templateEngine;

    private static string $REGISTER_TEMPLATE = "register.html";

    public function __construct(DBSession $dbSession, TemplateEngine $templateEngine) {
        $this->templateEngine = $templateEngine;
    }

    public function view(Request $request): string {
        $config = Config::getConfig();
        return $this->templateEngine->render(self::$REGISTER_TEMPLATE, ["captcha" => $config["captcha"]]);    
    }

}
