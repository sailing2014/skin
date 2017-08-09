<?php

namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\Mysql\PlandbModel;
use App\Exception\ApiException;

class PlanintlController extends ControllerBase {   
    
    public function beforeExecuteRoute($dispatcher) {
        parent::beforeExecuteRoute($dispatcher);
        
        $this->checkApiToken();
    }

    /**
     * add  plan tags
     * 
     * POST /v1/plan/tags/intl
     */
    public function addTagsAction()
    {
        $validators =array(
            'title' => "required"
        );
        
        $this->checkParams($validators);       
        
        $planModel = new PlanModel();
        $doc_id = $planModel->addTag($this->_body->title);
        $this->responseJson(array("tag_id"=>$doc_id)); 
    } 
    
    /**
     *  update tag by tag_id
     * 
     * PUT /v1/plan/tags/intl/{tag_id}
     */
     public function updateTagsAction($tag_id)
     {
         $validators =array(           
            'title' => "required"
        );
        
        $this->checkParams($validators);       
        
        $planModel = new PlanModel();
        $planModel->updateTags($tag_id,$this->_body->title);
        $this->responseJson(0);
    }
    
    /**
     *  get all tags
     * 
     * GET /v1/plan/tags/intl
     */
    public function getTagsAction(){

        $planModel = new PlanModel(); 
        $data = $planModel->getTags();
        $this->responseJson($data);       
    }
 
    /**
     * get tag by tag_id
     * 
     * GET　/v1/plan/tags/intl/{tag_id}
     * 
     * @param string $tag_id
     */
    public function getByIdAction($tag_id){   
        
        $planModel = new PlanModel();        
        $data = $planModel->getTagsById($tag_id);
        $this->responseJson($data);          
    }
   
    /**
     * delete tag by tag_id
     * DELETE /v1/plan/tags/intl/{tag_id}
     * 
     * @param string $tag_id
     */
    public function deleteAction($tag_id){
        
        $planModel = new PlanModel();
        $planModel->deleteTagsById($tag_id);
        $this->responseJson(0);
           
    }
    
    
    /**
     * add plan
     * 
     * POST /v1/plan/intl
     */
    public function addPlanAction(){
        
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $title = $this->_body->title;
        $tags = isset($this->_body->tags)?$this->_body->tags:"";
        $description = isset($this->_body->description)?$this->_body->description:"";
        $difficulty_level = isset($this->_body->difficulty_level)?intval($this->_body->difficulty_level):0;
        $tools = isset($this->_body->tools)?$this->_body->tools:"";
        $circle = isset($this->_body->circle)?intval($this->_body->circle):0;
        $principle = isset($this->_body->principle)?$this->_body->principle:"";
        $fit = isset($this->_body->fit)?$this->_body->fit:"";
        $tips = isset($this->_body->tips)?$this->_body->tips:"";
        $image = isset($this->_body->image)?$this->_body->image:"";
        $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
        $body_part = isset($this->_body->body_part)?$this->_body->body_part:"";     
        $basic = isset($this->_body->basic)?intval($this->_body->basic):0;        
        $top = isset($this->_body->top)?intval($this->_body->top):0;        
        
        $data = array(
                       "title"=>$title,"tags"=>$tags,"description"=>$description,"difficulty_level"=>$difficulty_level,
                       "tools"=>$tools,"circle"=>$circle,"principle"=>$principle,"fit"=>$fit,"tips"=>$tips,
                        "image"=>$image,"thumb"=>$thumb,"body_part"=>$body_part,
                        "basic"=>$basic,"participant_num"=>0,
                        "top"=>$top
                     );
        $data["create_at"] = $data["update_at"] = time();
        $data["id"] = uniqid();
        $plandbModel = new PlandbModel();
        $plandbModel->addToTable($data, 'plan');
        
        $plan_id = "skin:plan:".$data["id"];
        $this->responseJson($plan_id);
    }
    
    /**
     * update plan
     * 
     * PUT /v1/plan/intl/{plan_id}
     */
    public function updatePlanAction($plan_id){
        
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $title = $this->_body->title;
        $tags = isset($this->_body->tags)?$this->_body->tags:"";
        $description = isset($this->_body->description)?$this->_body->description:"";
        $difficulty_level = isset($this->_body->difficulty_level)?intval($this->_body->difficulty_level):0;
        $tools = isset($this->_body->tools)?$this->_body->tools:"";
        $circle = isset($this->_body->circle)?intval($this->_body->circle):0;
        $principle = isset($this->_body->principle)?$this->_body->principle:"";
        $fit = isset($this->_body->fit)?$this->_body->fit:"";
        $tips = isset($this->_body->tips)?$this->_body->tips:"";
        $image = isset($this->_body->image)?$this->_body->image:"";
        $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
        $body_part = isset($this->_body->body_part)?$this->_body->body_part:"";     
        $basic = isset($this->_body->basic)?intval($this->_body->basic):0; 
        $top = isset($this->_body->top)?intval($this->_body->top):0;
        
        $data = array(
                       "title"=>$title,"tags"=>$tags,"description"=>$description,"difficulty_level"=>$difficulty_level,
                       "tools"=>$tools,"circle"=>$circle,"principle"=>$principle,"fit"=>$fit,"tips"=>$tips,
                        "image"=>$image,"thumb"=>$thumb,"body_part"=>$body_part,
                        "basic"=>$basic,"top"=>$top
                    );
        $data["update_at"] = time();
        
        $id = str_replace("skin:plan:", "", $plan_id);
        $plandbModel = new PlandbModel();
        $plandbModel->updateTable($data, "id='".$id."'", "plan");
        $this->responseJson(0);
    }
    
   
    
    /**
     * 
     * get plan by id
     * 
     * GET /v1/plan/intl/{plan_id}
     * @param string $plan_id
     */
    public function getPlanAction($plan_id){
        $ret = $day_arr = array();
        
        $id = str_replace("skin:plan:", "", $plan_id);
        $plandbModel = new PlandbModel();
        $data = $plandbModel->getPlanById($id);
        if($data){
            unset($data["id"]);
        }else{
            throw new ApiException(427111,200,"This plan does not exist!");
        }
        $days = $plandbModel->getDaysByPlanId($id,"id,title,step,time");
        if($days){
            foreach ($days as $day){
                $day_arr[] = array("step"=>intval($day["step"]),"title"=>$day["title"],
                                    "time"=>intval($day["time"]),"doc_id"=>"skin:day:".$day["id"]
                                    );
            }
        }
        
        $ret = array(
                        "title"=>$data["title"],"tags"=>$data["tags"],"descirption"=>$data["description"],
                        "difficulty_level"=>intval($data["difficulty_level"]),"tools"=>$data["tools"],
                         "circle"=>intval($data["circle"]),"principle"=>$data["principle"],
                         "fit"=>$data["fit"],"tips"=>$data["tips"],"image"=>$data["image"],            
                        "thumb"=>$data["thumb"],"body_part"=>$data["body_part"],
                        "basic"=>intval($data["basic"]),"top"=>intval($data["top"]),
                        "days"=>$day_arr,"participant_num"=>intval($data["participant_num"]),
                        "create_at"=>intval($data["create_at"]),"update_at"=>intval($data["update_at"])
                );
       
        $this->responseJson($ret,"plan");
    }
    
    
    
     /**
     * 
     * get plan list
     * 
     * GET /v1/plan/intl
     * 
     */
    public function getPlanListAction(){
        
        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',10);
        
        $planModel = new PlanModel();
        $data = $planModel->getPlanList($page,$size);
        $this->responseJson($data);
    }
    
    /**
     * set top
     * 
     * @param string $plan_id
     */
    public function setTopAction($plan_id){
        $top = isset($this->_body->top)?$this->_body->top:0;
        
        $planModel = new PlanModel();
        $planModel->setTop($plan_id,$top);
        $this->responseJson(0);
    }
    
    /**
     * delete plan
     * 
     * DELETE /v1/plan/intl/{plan_id}
     * @param string $plan_id
     */
    public function deletePlanAction($plan_id){
        $id = str_replace("skin:plan:", "",$plan_id);
        $plandbModel = new PlandbModel();
        $plandbModel->deletePlan($id);
        $this->responseJson(0);
    }
    
    /**
     * add plan step 
     * 
     * POST /v1/plan/step/intl
     */
    public function addStepAction(){
        $validators = array("title"=>"required","day_id"=>"required");
        $this->checkParams($validators);
        
        $title = $this->_body->title;
        $day_id = str_replace("skin:day:","",$this->_body->day_id);
        $step = isset($this->_body->step)?intval($this->_body->step):0;
        $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
        $image = isset($this->_body->image)?$this->_body->image:"";
        $video = isset($this->_body->video)?$this->_body->video:"";
        $time = isset($this->_body->time)?intval($this->_body->time):0;
        $description = isset($this->_body->description)?$this->_body->description:"";
        $difficulty_level = isset($this->_body->difficulty_level)?intval($this->_body->difficulty_level):0;
        $tools = isset($this->_body->tools)?$this->_body->tools:"";
        $body_part = isset($this->_body->body_part)?$this->_body->body_part:"";
        $tips = isset($this->_body->tips)?$this->_body->tips:"";
        
        $id = uniqid();
        $data = array(
                        "id"=>  $id,
                        "day_id"=>$day_id,"step"=>$step,"title"=>$title,"thumb"=>$thumb,"image"=>$image,"video"=>$video,"time"=>$time,
                        "description"=>$description,"difficulty_level"=>$difficulty_level,"tools"=>$tools,
                        "body_part"=>$body_part,"tips"=>$tips
                      );
        $data["create_at"] = $data["update_at"] = time();
        
        $plandbModel = new PlandbModel();
        $plandbModel->addToTable($data, 'step');
        $doc_id = "skin:step:".$id;
        $this->responseJson($doc_id,"doc_id");
    }
    
    /**
     * update step 
     * PUT /v1/plan/step/intl/{doc_id}
     * @param string $doc_id
     */
    public function updateStepAction($doc_id){
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $title = $this->_body->title;
        $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
        $image = isset($this->_body->image)?$this->_body->image:"";
        $video = isset($this->_body->video)?$this->_body->video:"";
        $time = isset($this->_body->time)?intval($this->_body->time):0;
        $description = isset($this->_body->description)?$this->_body->description:"";
        $difficulty_level = isset($this->_body->difficulty_level)?intval($this->_body->difficulty_level):0;
        $tools = isset($this->_body->tools)?$this->_body->tools:"";
        $body_part = isset($this->_body->body_part)?$this->_body->body_part:"";
        $tips = isset($this->_body->tips)?$this->_body->tips:"";
        $step = isset($this->_body->step)?intval($this->_body->step):0;
        
        $data = array(
                        "title"=>$title,"thumb"=>$thumb,"image"=>$image,"video"=>$video,"time"=>$time,
                        "description"=>$description,"difficulty_level"=>$difficulty_level,"tools"=>$tools,
                        "body_part"=>$body_part,"tips"=>$tips,"step"=>$step,"update_at"=>time()
                      );
        
        $id = str_replace("skin:step:", "", $doc_id);
        $plandbModel = new PlandbModel();
        $plandbModel->updateTable($data, "id = '". $id. "'", 'step');
        $this->responseJson(0);
    }
    
    /**
     * delete plan step
     * 
     * DELETE /v1/plan/step/intl/{doc_id}
     * @param string $doc_id
     */
    public function deleteStepAction($doc_id){
        $id = str_replace("skin:step:", "", $doc_id);
        $plandbModel = new PlandbModel();
        $plandbModel->deleteSteps("'".$id."'");
        $this->responseJson(0);
    }
    
    /**
     * get step by id
     * 
     * GET /v1/plan/step/intl/{doc_id}
     * @param string $doc_id
     */
    public function getStepByIdAction($doc_id){
        $id = str_replace("skin:step:", "", $doc_id);
        $plandbModel = new PlandbModel();
        $data = $plandbModel->getStepById($id);
        if(isset($data["day_id"])){
            unset($data["id"]);
            $data["day_id"] = "skin:day:".$data["day_id"];
        }
        $this->responseJson($data);
    }  

    
    /**
     * add plan day action
     * 
     * POST /v1/plan/day/intl
     */
    public function addDayAction(){
        $validators = array("title"=>"required","plan_id"=>"required");
        $this->checkParams($validators);
        
        $title = $this->_body->title;
        $plan_id = str_replace("skin:plan:", "", $this->_body->plan_id);
        $step = isset($this->_body->step)?intval($this->_body->step):0;
        $time = isset($this->_body->time)?intval($this->_body->time):0;   
        
        $id = uniqid();
        $data = array("id"=>$id,"title"=>$title,"time"=>$time,"plan_id"=>$plan_id,"step"=>$step);
        $data["create_at"] = $data["update_at"] = time();
        $plandbModel = new PlandbModel();
        $plandbModel->addToTable($data, "day");
        $doc_id = "skin:day:".$id;
        $this->responseJson($doc_id);
    }
    
    /**
     * update day by id
     * 
     * PUT /v1/plan/day/intl
     * @param string $doc_id
     */
    public function updateDayAction($doc_id){
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $id = str_replace("skin:day:", "",$doc_id);
        $title = $this->_body->title;        
        $time = isset($this->_body->time)?intval($this->_body->time):0;
        $step = isset($this->_body->step)?intval($this->_body->step):0;
        
        $data = array("title"=>$title,"time"=>$time,"step"=>$step,"update_at"=>time());
        
        $plandbModel = new PlandbModel();        
        $plandbModel->updateTable($data, "id = '". $id. "'", "day");
        $this->responseJson(0);
    }
   
    /**
     * delete day by id
     * 
     * DELETE /v1/plan/day/intl/{doc_id}
     * @param string $doc_id
     */
    
    public function deleteDayAction($doc_id){
        
        $id = str_replace("skin:day:", "",$doc_id);
        
        $plandbModel = new PlandbModel();        
        $plandbModel->deleteDay("'".$id."'");
        $this->responseJson(0);        
    }
    
    /**
     * get day by id
     * 
     * GET /v1/plan/intl/{doc_id}
     * @param string $doc_id
     */
    public function getDayByIdAction($doc_id){
        $id = str_replace("skin:day:", "",$doc_id);
        
        $plandbModel = new PlandbModel();        
        $day = $plandbModel->getDayById($id);
        if(! $day){        
         throw new ApiException(427111,200);    
       }       
       
       $step_arr = array();
       $steps = $plandbModel->getDayStepsById($id);
       if($steps){
           foreach($steps as $step){
               $step_arr[] = array(
                                    "step"=>intval($step["step"]),"title"=>$step["title"],
                                    "time"=>intval($step["time"]),"doc_id"=>"skin:step:".$step["id"]
                                    );
           }
       }
       
       $data = array(   "title" => $day["title"],    
                        "time" => intval($day["time"]),    
                        "step" => intval($day["step"]),   
                        "plan_id" => "skin:plan:".$day["plan_id"],
                        "steps"=> $step_arr,
                        "create_at" => intval($day["create_at"]),
                        "update_at" => intval($day["update_at"])
                    );
       
        $this->responseJson($data);
    }     
    
  
}
