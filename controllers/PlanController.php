<?php

namespace App\Controllers;

use App\Models\PlanModel;

class PlanController extends ControllerBase {
  
    /**
     * get tags 
     * 
     * GET /v1/plan/tags
     */
    public function getTagsAction(){
        $user = $this->checkToken();     
        
        $planModel = new PlanModel();
        $tag = $planModel->getUserTags($user["uid"]);
        $data = array("uid"=>$user["uid"],"tags"=>$tag);
        $this->responseJson($data);
    }
    
 
    /**
     * get encyclopedia list
     * 
     * GET /v1/plan/list?list_type={list_type}&page={page}&size={size}
     */
    public function getListAction(){     
        $user = $this->checkToken();       
        $list_type = $this->request->get('list_type','int',1);
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size','int',10);
        $validators = array(                        
                         'list_type' => array(
                                                   array('include', array('domain' => array(1,2,3) ) )
                                               )
                          );
        
        $this->checkParams($validators, array("uid"=>$user["uid"],"list_type"=>$list_type));
        
        $planModel = new PlanModel();
        $plan_list = $planModel->getUserPlanList($user["uid"],intval($page),intval($size));
        $data = array("uid"=>$user["uid"],"list_type"=>intval($list_type)) + $plan_list;
        $this->responseJson($data);
        
    }
    
    
    /**
     * search plan 
     * 
     * GET /v1/plan/search?keywords={keywords}
     */
    public function searchAction(){
        $user = $this->checkToken();
        $keywords = $this->request->getQuery('keywords');
        $page = intval($this->request->getQuery('page', 'int', 1));
        $size = intval($this->request->getQuery('size','int',10));
        
        $validators = array(
                            'keywords' => 'required'
                          );
        
        $this->checkParams($validators, array("keywords"=>$keywords));
        
        $planModel = new PlanModel();
        $plan_list = $planModel->getUserPlanList($user["uid"],$page,$size);
        $data = array("keywords"=>$keywords,"plans"=>$plan_list);
        $this->responseJson($data);
        
    }
    
    /**
     * add a plan
     * 
     * POST /v1/plan/{plan_id}
     */    
    public function addAction($plan_id){
        $user = $this->checkToken();        
        $planModel = new PlanModel();
        $planModel->takePlan($user["uid"], $plan_id);
        $this->responseJson(0);
    }
    
    /**
     * cancel a plan
     * 
     * DELETE /v1/plan/{plan_id}
     */
    public function cancelAction($plan_id){
        $user = $this->checkToken();
//        $user["uid"] = 10000014;
        $planModel = new PlanModel();
        $planModel->cancelPlan($user["uid"],  $plan_id);
        $this->responseJson(0);
    }
    
    /**
     * 
     * user plan list
     * 
     * GET　/v1/plan/take_list
     */
    public function takeListAction(){
        $user = $this->checkToken();      
        
        $planModel = new PlanModel();
        $plan_list = $planModel->getUserTakePlanList($user["uid"]);
        $data = array("uid"=>$user["uid"],"plans"=>$plan_list);
        $this->responseJson($data);
        
    }
    
    
    
    /**
     *  get plan detail
     * 
     *  GET /v1/plan/{plan_id}
     */
    public function getDetailAction($plan_id){
        $user = $this->checkToken();
        
        $planModel = new PlanModel();
        $data = $planModel->getPlanById($plan_id,$user["uid"]);                       
        $this->responseJson($data, "plan");
    }
    
    /**
     *  get plan tips
     * 
     *  GET /v1/plan/tips/{plan_id}
     */
    public function getDetailTipsAction($plan_id){
                
        $planModel = new PlanModel();
        $data = $planModel->getPlanTipsById($plan_id);                       
        $this->responseJson($data);
    }
    
    /**
     *  get day detail
     * 
     *  GET /v1/plan/day/{doc_id}
     */
    public function getDayAction($doc_id){
                
        $planModel = new PlanModel();
        $data = $planModel->getDayById($doc_id); 
        $this->responseJson($data);
    }
    
    
    /**
     * update process 
     * 
     * PUT /v1/plan/process/{plan_id}
     */
    
    public function updateProcessAction($plan_id){
        $user = $this->checkToken();       
        
        $planModel = new PlanModel();       
        $data = $planModel->updateProcess($user["uid"],$plan_id); 
        $this->responseJson($data);
    }
    
    /**
     * get process
     * 
     * GET /v1/plan/process/{plan_id}
     */
    
    public function getProcessAction($plan_id){
        $user = $this->checkToken();
        
        $planModel = new PlanModel();   
        $data = $planModel->getProcessByUid($user["uid"],$plan_id);   
        $this->responseJson($data,"process");
    }
    
    /**
     * get plan record
     * 
     * GET /v1/plan/record/{plan_id}
     */
    
    public function getRecordAction($plan_id){
        $user = $this->checkToken();
        
        $planModel = new PlanModel();   
        $data = $planModel->getRecordByUid($user["uid"],$plan_id);   
        $this->responseJson($data);
    }
    
    /**
     * get plan taken reason 
     * 
     * GET /v1/plan/reason/{plan_id}
     */
    
    public function getReasonAction($plan_id){
        $this->checkToken();
        
        $planModel = new PlanModel();   
        $data = $planModel->getReason($plan_id);   
        $this->responseJson($data);
    }
}
