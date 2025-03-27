<?php


require_once __DIR__.'/../database/DBSession.php';
require_once __DIR__.'/../routing/Request.php';


class MainController {

    private DBSession $dbSession;

    public function __construct($dbSession) {
        $this->dbSession = $dbSession;
    }

    public function view(Request $request) {
        return file_get_contents(__DIR__.'/../views/main.html');    
    }

}
