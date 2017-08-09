<?php
namespace App\Html\Controllers;

use Phalcon\Mvc\Controller;
use App\Models\UserModel;
use App\Exception\ServiceException;
use App\Exception\ApiException;

abstract class ControllerBase extends Controller
{         
   /**
     *
     * @var request body object
     */
    protected $_body = null;

    /**
     * don't use `initialize`,`initialize` only once per request
     * we need exec in every action
     *
     * @param unknown $dispatcher            
     */
    public function beforeExecuteRoute($dispatcher)
    {
        $this->_body = $this->request->getReqBody(); // default object
    }
    
    /**
     * 
     * @param token token
     * @return type
     * @throws ApiException
     */
      protected function checkToken($token)
    {      
      
        // Check Token
        if($token){
            $userModel = new UserModel();
            try{
                $user = $userModel->checkToken($token); 
                return $user;
            }catch(ServiceException $se){                
                if($se->getCode() == 3114){
                    $return = array("errorCode"=>413114,"errorMessage"=>"User get failed!");
                    $this->jsonReturn($return);
                }else{
                    $return = array("errorCode"=>413116,"errorMessage"=>"Token disabled");
                    $this->jsonReturn($return);
                }               
            }
        }else{
//            $return = array("errorCode"=>400001,"errorMessage"=>"Paramter Error!");
//             $this->jsonReturn($return);
            return array("uid"=>1);
        }
    }
    
   /**
    * 
    * @param array $return
    */
    public function jsonReturn($return){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($return));
    }
}
