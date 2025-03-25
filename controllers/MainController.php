<?php


require_once __DIR__.'/../database/DBSession.php';


class MainController {

    private DBSession $dbSession;

    public function __construct($dbSession) {
        $this->dbSession = $dbSession;
    }

    public function view() {
        include __DIR__.'/../views/main.html';    
    }

}
