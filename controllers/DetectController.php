<?php

namespace App\Controllers;

use App\Models\ResultModel;
use App\Exception\ApiException;
use App\Exception\ServiceException;

class DetectController extends ControllerBase {
    
    /**
     * add  test type
     * 
     * POST /v1/detect/result
     */
    public function addAction()
    {
        $validators = array(
            'test_type' => 'required',
            'result' => array(
                array(
                    'required'
                ),
                array(
                    'include',
                    array(
                        'domain' => array(
                            1,2,3,4,5
                        )
                    )
                )
            )
        );       
        
        $this->checkParams($validators);   
        $user  = $this->checkToken();       
        $resultModel = new ResultModel();
        
        try{
            $result = $resultModel->add($user["uid"],  $this->_body->test_type, $this->_body->result);
            $this->responseJson($result);
        }  catch (ServiceException $se){
            if($se->getCode() == 7113){
                $this->throwException($se, array(7113,7111), 420000);
            }
        }
                
    }   
    

    /**
     * submit  user answers
     * 
     * POST /v1/detect/answers
     */
    public function submitAction()
    {
        $validators = array(
            'test_type' => 'required',
            'answers' => 'required'
        );       
        
        $this->checkParams($validators);   
        $user  = $this->checkToken();     
        
        try{
            $resultModel = new ResultModel();
            $result = $resultModel->addUserAnswers($user["uid"],  $this->_body->test_type, $this->_body->answers);
            $this->responseJson($result);
        }  catch (ServiceException $se){    
                $this->throwException($se, array(7113,7111), 420000);          
        }
                
    }    
    
}