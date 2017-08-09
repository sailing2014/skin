<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\UserModel;

class ComplexController extends ControllerBase {    
   
   
    /**
     * Get weather 
     * 
     * @param city  pinyin or Chinese. 城市名称 中文e.g 北京
     * 
     * GET /v1/user/weather?city={city}
     */
    public function weatherAction(){
        $user = $this->checkToken();
        
        $validators = array("city"=>"required");
        $city = $this->request->getQuery('city');
        $this->checkParams($validators, array("city"=>$city));
        
        $userModel = new UserModel();        
        $data = $userModel->getWeather($city);
        $userModel->setUserCity($user["uid"], $city);
        
        $this->responseJson($data);
                
    }
    
    /**
     * Get weather 
     * 
     * @param city  pinyin or Chinese. 城市名称 中文e.g 北京
     * 
     * GET /v1/article/todolist?city={city}
     */
    public function todoListAction(){
        $user = $this->checkToken();
        
        $validators = array("city"=>"required");
        $city = $this->request->getQuery('city');
        $this->checkParams($validators, array("city"=>$city));
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getIndexTodoList($user["uid"],$city);        
        $this->responseJson($data,"data",true);
    }
    
    /**
     * Get todo detail  
     * 
     * @param string doc_id  todo doc_id
     * 
     * GET /v1/article/todo/{doc_id}
     */
    public function todoAction($doc_id){
        $user = $this->checkToken();       
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getTodoComplexList($user["uid"],$doc_id);
        $this->responseJson($data);
    }
    
      /**
     * Get c_plan steps  
     * 
     * @param string $doc_id c_plan id
     * 
     * GET /v1/article/cplan/{doc_id}
     */
    public function cplanAction($doc_id){
        $user = $this->checkToken();       
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getCplanComplexList($user["uid"],$doc_id);
        $this->responseJson($data);
    }
    
    
    /**
     * punch time clock
     * 
     * POST /v1/article/clock
     */
    public function clockAction(){
        $user = $this->checkToken();
        
        $userModel = new UserModel();   
        $userModel->addClock($user["uid"]);
        $this->responseJson(0);
    }
    
    /**
     * punch time clock
     * 
     * POST /v1/article/cplan/clock/{c_plan_id}
     */
    public function clockCplanAction($doc_id){
        $user = $this->checkToken();
        
        $userModel = new UserModel();   
        $userModel->addCplanClock($user["uid"],$doc_id);
        $this->responseJson(0);
    }
   
}
