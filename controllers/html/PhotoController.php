<?php
namespace App\Html\Controllers;

class PhotoController extends ControllerBase {   

    public function indexAction() { 
        $this->view->setVar("title", "photo list by uid");
    }
    public function bbcAction()
    {
        $this->view->setVar("title", "welcome to bbc data deleting html!");
    }
}
