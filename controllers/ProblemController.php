<?php

namespace App\Controllers;

use App\Models\ProblemModel;
use App\Exception\ServiceException;

class ProblemController extends ControllerBase {
    
    /**
     * add  test type
     * 
     * POST /v1/test/problems/intl
     */
    public function addAction()
    {
        $validators =array(
            'test_type' => 'required',
            'title' => "required",
            "choices" => "required",
        );
        
        $this->checkParams($validators);       
        $this->checkApiToken();
        
        $problemModel = new ProblemModel();
        
        $doc_id = $problemModel->add(intval($this->_body->test_type),$this->_body->title, $this->_body->choices);
        $this->responseJson(array("doc_id"=>$doc_id));
       
                
    }   
    
    /**
     * get all test type problems
     * 
     * GET /v1/test/problems/intl/{test_type}
     */
    public function getIntlAction($test_type)
    {   
        $this->checkApiToken();
        $problemModel = new ProblemModel();
        try{
            $data = $problemModel->getIntlByType($test_type);
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
        
    }
    
    /**
     * get all  problems
     * 
     * GET /v1/test/problems/intl
     */
    public function getAllAction()
    {   
        $this->checkApiToken();
        $problemModel = new ProblemModel();
        try{
            $data = $problemModel->getIntlAll();
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
        
    }
    
    /**
     * get all test type problems
     * 
     * GET /v1/test/problems/intl/{test_type}
     */
    public function getIntlProblemAction($question_id)
    {   
        $this->checkApiToken();
        $problemModel = new ProblemModel();
        try{
            $data = $problemModel->get($question_id);
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
        
    }
    
    /**
     * get all test type problems
     * 
     * PUT /v1/test/problems/intl/{question_id}
     */
    public function updateAction($question_id)
    {   
        $this->checkApiToken();
        
        $validators =array(
            'test_type' => 'required',
            'title' => "required",
            "choices" => "required"
        );        
        $this->checkParams($validators);       
            
        try{                
            $problemModel = new ProblemModel(); 
            $problemModel->update($question_id,$this->_body->test_type,$this->_body->title, $this->_body->choices);
            $this->responseJson(0);
        } catch (ServiceException $se) {
            $this->throwException($se, array(7113,7112),420000);
        }
        
    }
    
      /**
     * get all test type problems
     * 
     * DELETE /v1/test/problems/intl/{question_id}
     */
    public function deleteProblemAction($question_id)
    {   
        $this->checkApiToken();       
            
        try{                
            $problemModel = new ProblemModel(); 
            $problemModel->deleteProblem($question_id);
            $this->responseJson(0);
        } catch (ServiceException $se) {
            $this->throwException($se, 7115,420000);
        }
        
    }
    
    /**
     * get all test type problems
     * 
     * GET /v1/test/problems/intl/{test_type}
     */
    public function deleteAction($test_type)
    {   
        $this->checkApiToken();
        $problemModel = new ProblemModel();
        try{
            $data = $problemModel->getIntlByType($test_type);
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
        
    }
    
    
   
    
    /**
     * delete all test types
     * 
     * DELETE /v1/test/intl/{doc_id}
     */
    public function deleteBydocidAction($doc_id)
    {                    
        $this->checkApiToken();
        
        $problemModel = new ProblemModel();
        try{
            $problemModel->delete($doc_id);
            $this->responseJson(0);
        }  catch (ServiceException $se){
            if($se->getCode() == 7115){
                $this->throwException($se, 7115,420000);
            }
        }
                
    }   
    
     /**
     * get all test type problems
     * 
     * GET /v1/test/problems/{test_type}
     */
    public function getAction($test_type)
    {   
        $problemModel = new ProblemModel();
        try{
            $data = $problemModel->getByType($test_type);
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
        
    }

}
