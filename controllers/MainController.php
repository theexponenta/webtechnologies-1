<?php

declare(strict_types=1);


require_once __DIR__.'/../database/DBSession.php';
require_once __DIR__.'/../routing/Request.php';


class MainController {

    private DBSession $dbSession;

    public function __construct($dbSession) {
        $this->dbSession = $dbSession;
    }

    public function view(Request $request): string {
        return file_get_contents(__DIR__.'/../public/views/main.html');    
    }

}
