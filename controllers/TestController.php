<?php

namespace App\Controllers;

use App\Models\TestModel;
use App\Exception\ServiceException;

class TestController extends ControllerBase {
    
    /**
     * update  test type
     * 
     * PUT /v1/test/intl/{test_type}
     */
    public function updateAction($test_type)
    {
        $validators =array(            
            'title'=>'required',
            'order'=>'required'
        );
        
        $this->checkParams($validators);       
        $this->checkApiToken();
        
        $testModel = new TestModel();       
        $testModel->updateTestType(intval($test_type), $this->_body->title, $this->_body->order);
        $this->responseJson(0);
        
                
    }   
    
    /**
     * add  test type
     * 
     * POST /v1/test/types/intl
     */
    public function addAction()
    {
        $validators =array(            
            'title'=>'required'
        );
        
        $this->checkParams($validators);       
        $this->checkApiToken();
        
        $testModel = new TestModel();       
        $order = isset($this->_body->order)?$this->_body->order:0;
        $test_type = $testModel->addTestType($this->_body->title,$order);
        $this->responseJson($test_type,"test_type");
    }   
    

    
    /**
     * get all test types 
     * 
     * GET /v1/test/types/intl
     */
    public function intlGetAction(){
        $this->checkApiToken();
        
        $testModel = new TestModel();
        try{
            $data = $testModel->getIntlTestType();
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
    }    
   
    
    
    /**
     * get all test types 
     * 
     * GET /v1/test/types/
     */
    public function getAction()
    {   
        $this->checkToken();
        $testModel = new TestModel();
        try{
            $data = $testModel->getTestType();        
            $this->responseJson($data);
        } catch (ServiceException $se) {
            $this->throwException($se, 7111,420000);
        }
        
    }
    
     /**
     * get  test type detail
     * 
     * GET /v1/test/types/intl/{$test_type}
     */
    public function getByTypeAction($test_type)
    {        
        $this->checkApiToken();
        $testModel =  new TestModel();       
        $data = $testModel->getIntlTestByType($test_type);        
        $this->responseJson($data);
    }    
  
    /**
     * get  test type detail
     * 
     * DELETE /v1/test/types/intl/{$test_type}
     */
    public function deleteAction($test_type)
    {
        $this->checkApiToken();
        $testModel =  new TestModel();
        $testModel->deleteIntlTestByType($test_type); 
        $this->responseJson(0);
    }
    
    /**
     * set result
     * POST /v1/test/type/results/intl/{$test_type}
     * @param type $test_type
     */
    public function setResultAction($test_type){
        $this->checkApiToken();
        $validators = array("results"=>"required");
        $this->checkParams($validators);
        
        $results = $this->_body->results;
        $testModel = new TestModel();
        $doc_id = $testModel->addResults(intval($test_type),$results);
        $this->responseJson($doc_id);
    }
    
    /**
     * delete result
     * DELETE /v1/test/type/results/intl/{$test_type}
     * @param type $test_type
     */
    public function deleteResultAction($test_type){
        $this->checkApiToken(); 
        
        $testModel = new TestModel();
        $testModel->deleteResults(intval($test_type));
        $this->responseJson(0);
    }
    
    /**
     * get result
     * GET /v1/test/type/results/{$test_type}
     * @param type $test_type
     */
    public function getResultAction($test_type){
//        $this->checkToken();
        $testModel = new TestModel();
        $data = $testModel->getResult($test_type);
        $this->responseJson($data);
    }
    
    /**
     * add test type config,including result,max,min,title,and content
     * POST /v1/test/type/config/intl/{$test_type}
     * @param int $test_type
     */
    public function intlAddTestTypeConfigAction($test_type){
        $this->checkApiToken();
        // 'client' => array(  array( 'required' ),array( 'include', array( 'domain' => array( 'ios', 'android' )  ) ) )
        $validators = array(
                            "max"=>"required",
                            "min"=>"required",
                            "result"=>array( array("required"),array("include",array("domain"=>array(1,2,3,4,5) ) ) ) 
                           );
        $this->checkParams($validators);
        $max = $this->_body->max;
        $min = $this->_body->min;
        $result = $this->_body->result;
        $title = $this->_body->title;
        $content = $this->_body->content;
        
        $testModel = new TestModel();
        $doc_id = $testModel->addTestConfig(intval($test_type),$result,$max,$min,$title,$content);
        $this->responseJson($doc_id,"doc_id");
    }

    /**
     * update test type config,test_type and result cann't be modified.
     * PUT /v1/test/type/config/{$doc_id}
     * @param string $doc_id
     */
    public function intlUpdateTestTypeConfigAction($doc_id){
        $this->checkApiToken();
        // 'client' => array(  array( 'required' ),array( 'include', array( 'domain' => array( 'ios', 'android' )  ) ) )
        $validators = array(
                            "test_type"=>"required",
                            "max"=>"required",
                            "min"=>"required",
                            "result"=>array( array("required"),array("include",array("domain"=>array(1,2,3,4,5) ) ) ) 
                           );
        $this->checkParams($validators);
        $test_type = $this->_body->test_type;
        $max = $this->_body->max;
        $min = $this->_body->min;
        $result = $this->_body->result;
        $title = $this->_body->title;
        $content = $this->_body->content;
        
        $testModel = new TestModel();
        $testModel->updateTestConfig($doc_id,$test_type,$result,$max,$min,$title,$content);
        $this->responseJson(0);
    }
    
     /**
     * get test type config,including result,max,min,title,and content
     * GET /v1/test/type/config/intl/{$test_type}
     * @param int $test_type
     */
    public function intlGetTestTypeConfigAction($test_type){
        $this->checkApiToken();       
        
        $testModel = new TestModel();
        $data = $testModel->getTestTypeConfig($test_type);
        $this->responseJson($data);
    }
    
    /**
     * delete test type config by doc_id
     * DELETE /v1/test/type/config/{$doc_id}
     * @param string $doc_id
     */
    public function intlDeleteTestTypeConfigAction($doc_id){
        $this->checkApiToken();    
        $testModel = new TestModel();
        $testModel->deleteTestTypeConfig($doc_id);
        $this->responseJson(0);
    }
    
    /**
     * get test config by doc_id
     * GET /v1/test/type/config/{$doc_id}
     * @param string  $doc_id
     */
    public function getTestTypeConfigAction($doc_id){
        $testModel = new TestModel();
        $data = $testModel->getTestConfig($doc_id);
        $this->responseJson($data);
    }
    
    /**
     * get all test config 
     * GET /v1/test/type/config
     */
    public function getAllTestTypeConfigAction(){
        $testModel = new TestModel();
        $data = $testModel->getTestConfigList();
        $this->responseJson($data);
    }
 
}
