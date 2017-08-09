<?php
namespace App\Html\Controllers;

class IndexController extends ControllerBase {   

    public function indexAction() { 
        $this->view->setVar("title", "welcome to skin html!");
    }
}
