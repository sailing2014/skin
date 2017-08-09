<?php

namespace App\Controllers;

use App\Models\ResultModel;
use App\Exception\ServiceException;

class ResultController extends ControllerBase {    
   
    

    /**
     * get user test result
     * 
     * GET /v1/storage/user_skin_list/{uid}
     */
    public function getAction($uid)
    {         
        $user = $this->checkToken();
        $resultModel = new ResultModel();          
        $data = $resultModel->getResultByUid($user["uid"]);
        $this->responseJson($data);        
    }
    
    /**
     * get participant total
     * 
     * GET /v1/storage/total/{type}
     */
    public function getTotalAction($type)
    {        
        $validators =  array(
                         'type' => array(
                                                   array('include', array('domain' => array(1,2) ) )
                                                )
                          );
        $this->checkParams($validators, array("type"=>$type));
        
        try{           
            if($type == 1){
                $total = 0;
            }else{
                $total = 319911;
            }
            $data = array("type"=>intval($type),"total"=>$total);
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111, 430000);
        }
        
    }
  

}
