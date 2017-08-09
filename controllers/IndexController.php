<?php
namespace App\Controllers;

use App\Exception\ApiException;
class IndexController extends ControllerBase
{   
    public function route404Action()
    {
        throw new ApiException(404, 404);
    }
}
