<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Exception\ServiceException;

use App\Models\Mysql\PlandbModel;

/**
 *  plan model
 *
 * @author yang.f
 *        
 */
class PlanModel extends ServiceBase {
    private $list_doc_id = "skin:plan:tags";
    private $plan_list_id = "skin:plan:list";
    public function __construct() {
        $this->serviceName = 'plan';
    }  
    
    
    public function addTag($title)
    {    
        $this->checkTitle($title);
        
        $url = $this->api['devicedata.add'];
        
        $time = time();
        $tag_id = "skin:plan:tag:".  uniqid();
        $data = array( "doc_id" => $tag_id,
                       "data"   =>  array(  
                                            "title"=>$title,
                                            "create_at"=>$time,
                                            "update_at"=>$time
                                         )
                                 
                      );
        
        $this->sendHttpRequest($url, $data);      
        
        //更新tag list       
        $this->addtags($tag_id,$title,$time,$time);       
        
        return $tag_id;
    }
   
    private function addtags($tag_id,$title,$time,$update){        
        
        $total = 0;
        $data = array();
        
        $old = $this->get($this->list_doc_id,false);  
        if($old)
        {
            $total = $old["total"];
            $data = isset($old["data"])?$old["data"]:array();
        }      
            
         ++$total;
        $data[$tag_id] =  array("tag_id"=>$tag_id,"title"=>$title,"create_at"=>  $time,"update_at"=>$update);
        $new = array("total"=>$total) + array("data"=>$data);
        $this->set($this->list_doc_id, $new);            
    }
   
    
    public function updateTags($tag_id,$title){
        
        $this->checkTitle($title,$tag_id);
        
        $update_at = time();
        try{
            $old = $this->get($tag_id);  
        }  catch (ServiceException $se){
            throw new ApiException(427111,200," This tag has not been added yet!");
        }
        $new = array("title"=>$title,"create_at"=>$old["create_at"],"update_at"=>$update_at);        
        $this->set($tag_id, $new);
        
        //更新list
        $old_list = $this->get($this->list_doc_id);        
        $old_list["data"][$tag_id] = array("tag_id"=>$tag_id) + $new;
        $this->set($this->list_doc_id, $old_list);
    } 
    
   public function getTags(){
       try{
            $ret = $this->get($this->list_doc_id);
            $data["total"] = $ret["total"];
            foreach ($ret["data"] as $val){
                $data["data"][] = $val;
            }
            return $data;
       }  catch ( ServiceException $se){
           throw  new ApiException(427111,200);
       }
   }
   
   public function getUserTags($uid){
       $data = array();
       $ret = $this->get($this->list_doc_id, false);     
       if($ret){
           foreach ($ret["data"] as $val){
               $data[] = array("tag_id"=>$val["tag_id"],"title"=>$val["title"]); 
           }
       }else{
           throw new ApiException(427111,200);
       }
       return $data;
   }


   public function getTagsById($tag_id){
       try{            
            $data = array("tag_id"=>$tag_id) + $this->get($tag_id);
            return $data;
       }  catch (ServiceException $se){
           throw new ApiException(427111,200);
       }
   }
   
   public function deleteTagsById($tag_id){
       //更新list
       $old = $this->get($this->list_doc_id);      
       if(isset($old["data"][$tag_id])){
           --$old["total"];
           unset($old["data"][$tag_id]);    
           $this->set($this->list_doc_id, $old);
       }else{
           throw new ApiException(427115,200);
       }
       
       //删除tag
       $this->delete($tag_id);
   }
   
   
   private function checkTitle($title,$tag_id=""){
       $original = $this->get($this->list_doc_id,false);
       $errcode = 427110;
       if($original){
           if($tag_id){
               unset($original["data"][$tag_id]);
               $errcode = 427113;
           }
            foreach ($original["data"] as $val) {
                if($val["title"] == $title){
                    throw new ApiException($errcode,200," This title exists!");
                }
            }
       }
   }
   
   public function addPlan($title,$tags="",$image="",$body_part="",$actions_num=0,$time=0,$actions=0){
       $plan_id = "skin:plan:".uniqid();
       
       $now = time();
       $data = array("title"=>$title,"tags"=>$tags,"image"=>$image,
                      "body_part"=>$body_part,"action_num"=>$actions_num,
                      "time"=>$time,"actions"=>$actions,
                      "create_at"=>$now,"update_at"=>$now);
       $this->set($plan_id, $data);
       
       
       $this->addToPlanList($plan_id,$now,$now);
       
       return $plan_id;
   }
   
   private function addToPlanList($plan_id,$create_at,$update_at){
       $data[$plan_id] = array("plan_id"=>$plan_id,"create_at"=>$create_at,"update_at"=>$update_at);
       $this->update($this->plan_list_id, $data);
   }
   
   public function updatePlan($plan_id,$title,$tags="",$image="",$body_part="",$actions_num=0,$time=0,$actions=0){
       try{
           $plan = $this->get($plan_id);
           $now = time();
           $create_at = $plan["create_at"];
           $data = array("title"=>$title,"tags"=>$tags,"image"=>$image,
                      "body_part"=>$body_part,"action_num"=>$actions_num,
                      "time"=>$time,"actions"=>$actions,
                      "create_at"=>$create_at,"update_at"=>$now);
           $this->set($plan_id, $data);
           $this->addToPlanList($plan_id,$create_at,$now);
           
       }catch(ServiceException $se){
           throw new ApiException(427113,200," This plan doesn't exist!");
       }
   }
   
   public function setTop($plan_id,$top=0){
       $data = array("top"=>$top,"update_at"=>  time());
       
       $id = str_replace("skin:plan:", "", $plan_id);
       $dbModel = new PlandbModel();
       $dbModel->updateTable($data, "id = '".$id."'", "plan");
   }
  
   
    public function getPlanById($plan_id,$uid=0,$done=-1){    
    
        $plan = array();

        $dbModel = new PlandbModel();
        $id = str_replace("skin:plan:","" ,$plan_id);
        $ret = $dbModel->getPlanById($id);    
        if(!$ret){
            return $plan;
        }

       $plan["plan_id"] = $plan_id;
       $plan["title"] = $ret["title"];
       $plan["image"] = $ret["image"]?$ret["image"]:"";
       if($uid){
        $plan["take"] = $this->checkUserTakePlanOrNot($plan_id, $uid);
       }
       $plan["participant_num"] = intval($ret["participant_num"]);
       $plan["circle"] = intval($ret["circle"]);
       $plan["days"] = array();
       if($done > -1){           
            ++$done;
            $ret["days"] = $dbModel->getDaysByPlanId($id,'*',"step = ".$done);
       }else{
           $ret["days"] = $dbModel->getDaysByPlanId($id);
       }

       if(!$ret["days"]){
           return $plan;
       }

       foreach($ret["days"] as $v){ 
               $day = $dbModel->getDayById($v["id"]);
               $tmp = $dbModel->getDayStepsById($v["id"]);
               $day_steps = array();
               if($tmp){               
                           foreach($tmp as $val){
                                   $day_steps[] = array(   "doc_id"=>"skin:step:".$val["id"],"title"=>$val["title"],
                                                           "thumb"=>$val["thumb"],"image"=>$val["image"],
                                                           "video"=>$val["video"],
                                                           "video_size"=> $this->getRemoteFilesize($val["video"]),
                                                           "time"=>intval($val["time"]),
                                                           "description"=>$val["description"],
                                                           "difficulty_level"=>intval($val["difficulty_level"]),
                                                           "tools"=>$val["tools"],"body_part"=>$val["body_part"],
                                                           "tips"=>$val["tips"],"step"=>intval($val["step"])
                                                       );
                               }
               }

               $plan["days"][] = array(      
                                           "doc_id"=>"skin:day:".$v["id"],"step"=>intval($day["step"]),
                                           "title"=>$day["title"],
                                           "description"=>"零基础晚间护肤简介",
                                           "time"=>intval($day["time"]),
                                           "steps"=>$day_steps
                                   );


               unset($day,$tmp,$day_steps);
       }
       
        return $plan;
   }
   
    public function getPlanTipsById($plan_id){     
     $dbModel = new PlandbModel();
     $id = str_replace("skin:plan:","" ,$plan_id);
     $ret = $dbModel->getPlanById($id);
     $plan = array();
     
     if($ret){  
         $plan["plan_id"]  = $plan_id;
        $plan["title"] = $ret["title"];
        $plan["description"] = $ret["description"];
        $plan["difficulty_level"] = intval($ret["difficulty_level"]);
        $plan["tools"] = $ret["tools"];
        $plan["circle"] = intval($ret["circle"]);
        $plan["principle"] = $ret["principle"];
        $plan["fit"] = $ret["fit"];
        $plan["tips"] = $ret["tips"];
        
     }else{
         throw new ApiException(427111,200);
     }
     
     return $plan;
   }
   
   public function getDayById($doc_id){     
     $dbModel = new PlandbModel();
     $id = str_replace("skin:day:", "", $doc_id);
     $ret = $dbModel->getDayById($id);
     $day = array();
     
     if($ret){  
        $day["doc_id"] = $doc_id;
        $day["title"] = $ret["title"];
        $day["time"] = $ret["time"];
        $day["steps"] = array();
        if(isset($ret["steps"]) && !empty($ret["steps"])  ){
            $steps = $dbModel->getDayStepsById($id);
            if($steps){
                foreach ($steps as $val){
                    $day["steps"][] = array(    "step"=>$val["step"],"title"=>$val["title"],
                                                "thumb"=>$val["thumb"],"image"=>$val["image"],
                                                "video"=>$val["video"],"time"=>intval($val["time"]),
                                                "description"=>$val["description"],
                                                "difficulty_level"=>intval($val["difficulty_level"]),
                                                "tools"=>$val["tools"], "body_part"=>$val["body_part"],
                                                "tips"=>$val["tips"]
                                            );
                }
            }
        }
        
     }else{
         throw new ApiException(427111,200);
     }
     
     return $day;
   }
   
   public function getUserPlanList($uid,$page=1,$size=10){
       $ret = array("page"=>$page,"size"=>$size);
       $plans = array();   
       $dbModel = new PlandbModel();
       $data = $dbModel->getList($page, $size);  
        if($data["total_rows"]){
            foreach($data["rows"] as $val){
                $plans[] = array(   "plan_id"=>"skin:plan:".$val["id"],
                                    "thumb"=>$val["thumb"],
                                    "title"=>$val["title"],
                                    "image"=>$val["image"],
                                    "body_part"=>$val["body_part"],
                                    "participant_num"=>  intval($val["participant_num"]),
                                    "circle"=>28,                                     
                                    "take"=>array_rand(array(0,1)),
                                    "today_done"=>  array_rand(array(0,1))
                                );
            }            
        }
        
       $ret["list"] = $plans;
       return $ret;
   }
   
   public function getUserTakePlanList($uid){
       
       $plans = array();
             
       $user_plan_doc_id = "skin:plan:".$uid;
       $user_plans = $this->get($user_plan_doc_id,false);
       if(isset($user_plans["plans"]) && (!empty($user_plans["plans"])) ){
           $dbModel = new PlandbModel();
           foreach ($user_plans["plans"] as $val){
               $id = str_replace("skin:plan:","" ,$val["plan_id"]);
               $plan = $dbModel->getPlanById($id);
               if($plan){
//                    $num = $this->getParticipantNum($val["plan_id"]);
                    $plans[] = array(
                                    "plan_id"=>$val["plan_id"],"title"=>$plan["title"],
                                    "thumb"=>$plan["thumb"],"body_part"=>$plan["body_part"],
                                    "participant_num"=>intval($plan["participant_num"]),
                                    "take"=>0,
                                    "today_done"=>  array_rand(array(0,1))
                                    );
                    unset($id,$plan);
               }               
           }
       }
           
      return $plans;           
   }
   
   protected function checkUserTakePlanOrNot($plan_id,$uid=0){
       $take = 0;
       if($uid){
            $user_plan_doc_id = "skin:plan:".$uid;
            $planlist = $this->get($user_plan_doc_id,false);   
            if(isset($planlist["plans"][$plan_id])){     
                    $take = 1;
            }
       }      
       
       return $take;
   }


   public function takePlan($uid,$plan_id){
       $user_plan_doc_id = "skin:plan:".$uid;
       
       $flag = true;
       $time = time();
       
       $old = $this->get($user_plan_doc_id,false);   
       if($old){
           $user_plans = $old;
           if(isset($user_plans["plans"][$plan_id])){
               $flag = false; 
           }
       }
              
       $expire = $this->getUserPlanExpired($plan_id,$user_plans["plans"][$plan_id]["take_at"]);
       if($flag || $expire){ //第一次参加或者以前参加了计划时间过期的
            $user_plans["uid"] = $uid;
            $user_plans["plans"][$plan_id] = array("plan_id"=>$plan_id,"take_at"=>$time);
            $this->set($user_plan_doc_id, $user_plans);
            $this->updatePlanUsers($plan_id, $uid,$time);             
       }
       
       if($flag){//第一次参加，更新计划参加人数
            $id = str_replace("skin:plan:", "", $plan_id);
            $dbModel = new PlandbModel();
            $dbModel->updatePlanParticipantNum($id);
       }       
   }
   
   private function updatePlanUsers($plan_id,$uid,$time){
       $plan_take_list_doc_id = "skin:plan:users:".$plan_id;
       $plan_users = $this->get($plan_take_list_doc_id,false);
       if($plan_users){
           $plan_users =  array("plan_id"=>$plan_id,"users"=>array($uid=>array("uid"=>intval($uid),"take_at"=>$time)) );
       }else{
           $plan_users["users"][$uid] = array("uid"=>intval($uid),"take_at"=>$time);
       }
       
       $this->set($plan_take_list_doc_id, $plan_users);
   }
   
    public function cancelPlan($uid,$plan_id){
       $user_plan_doc_id = "skin:plan:".$uid;
       $plan_take_list_doc_id = "skin:plan:users:".$plan_id;
       $user_process_id = "skin:plan:process:".$uid;
       
       $user_plans = $this->get($user_plan_doc_id,false);       
       $plan_users = $this->get($plan_take_list_doc_id,false);
       $user_process = $this->get($user_process_id,false);
       if($user_plans && isset($user_plans["plans"][$plan_id])){
           unset($user_plans["plans"][$plan_id]);
           unset($plan_users["users"][$uid]);
          
           $this->set($user_plan_doc_id, $user_plans);
           $this->set($plan_take_list_doc_id,$plan_users);
           
           
            $id = str_replace("skin:plan:", "", $plan_id);
            $dbModel = new PlandbModel();
            $dbModel->updatePlanParticipantNum($id,'-');
               
           if(isset($user_process["plans"][$plan_id])){
               unset($user_process["plans"][$plan_id]);
               $this->set($user_process_id,$user_process);          
              
           }
       }else{
           throw new ApiException(440003,200,"user hasn't taken the plan yet!");
       }
       
   }
   
   private function getParticipantNum($plan_id){
       $num = 0;
       $plan_take_list_doc_id = "skin:plan:users:".$plan_id;
       $users = $this->get($plan_take_list_doc_id,false);
       if($users){
           $num = count($users["users"]);
       }
       return $num;
   }
   
   public function updateProcess($uid,$plan_id){
       $user_process_id = "skin:plan:process:".$uid;  
       $process = array();
       
       $old = $this->get($user_process_id,false);   
       if($old){                
            $process = $old;
       }else{
           $process["uid"] = $uid;
       }
       
        $done = isset($process["plans"][$plan_id]["done"])?++$process["plans"][$plan_id]["done"]:1;          
        $circle = $this->getPlanCircle($plan_id);
        if($circle && ($done>=$circle) )
        {$done = 0;} //看完了就从头开始吧

        
        $process["plans"][$plan_id] = array("plan_id"=>$plan_id,"done"=>$done);   
        $this->set($user_process_id, $process);
        
        $data = "今天的护肤计划已完成,护肤需要您每天不断的坚持哦~";
        return $data;
   }
   
   protected function getPlanCircle($plan_id){
       $circle = 0;
       $id = str_replace("skin:plan:", "", $plan_id);
       $dbModel = new PlandbModel();
       $plan  = $dbModel->getPlanById($id);
       if($plan){
           $circle = intval($plan["circle"]);
       }
       return $circle;
   }


   public function getProcessByUid($uid,$plan_id){
        $done = 0;
        $user_process_id = "skin:plan:process:".$uid;       
        $process = $this->get($user_process_id,false);  
        if($process && isset($process["plans"][$plan_id])){
            $done = $process["plans"][$plan_id]["done"];
        }
        return array("uid"=>$uid,"plan_id"=>$plan_id,"done"=>$done);
   }   
  
   public function getRecordByUid($uid,$plan_id){    
        $days[] = array("doc_id"=>"skin:day:577f548ead05f","step"=>1,"status"=>1); 
        $days[] = array("doc_id"=>"skin:day:577f548ead05f","step"=>2,"status"=>-1); 
        $days[] = array("doc_id"=>"skin:day:577f548ead05f","step"=>3,"status"=>0); 
        return array("plan_id"=>$plan_id,"days"=>$days);
   }   
   
    public function getReason($plan_id){    
        $reason = array("plan_id"=>$plan_id,"title"=>"零基础晚间护肤课程为什么值得您参加","content"=>"小编啥都没留下~~");
        return $reason;
   } 
   public function deleteData($doc_id,$field=""){
       try{
           $data = $this->get($doc_id);
       } catch (ServiceException $ex) {
           throw new ApiException(427115,200,"This ".$field." does not exist!");
       }
       
       if($field == "step"){
            if(isset($data["day_id"]) && $data["day_id"]){
                $day = $this->get($data["day_id"],false);
                if($day && $day["steps"]){
                    foreach($day["steps"] as $key=>$val ){
                        if($val["doc_id"] == $doc_id){
                            unset($day["steps"][$key]);
                        }
                    }
                    $this->set($data["day_id"], $day);
                }
            }
       }else if($field == "day"){
           if(isset($data["plan_id"]) && $data["plan_id"]){
                $plan = $this->get($data["plan_id"],false);
                if($plan && $plan["days"]){
                   foreach($plan["days"] as $key=>$val){
                       if($val["doc_id"] == $doc_id){
                           unset($plan["days"][$key]);
                       }
                   }
                   $this->set($data["plan_id"],$plan);
                }
           }
           if($data["steps"]){
                $step_ids = "";
                foreach ($data["steps"] as $val){
                    $step_ids = $val["doc_id"].",";
                }
                if($step_ids){
                    $ids = substr($step_ids, 0,-1);                   
                    $this->delete($ids);
                }
           }
       }
       
       $this->delete($doc_id);      
     
   }
   
   public function getData($doc_id,$field=""){
       try{
           $data = $this->get($doc_id);  
       } catch (ServiceException $ex) {
           throw new ApiException(427111,200,"This ".$field." does not exist!");
       }
       
       unset($data["doc_type"]);
       return $data;
   }
   
   public function getPlanData($plan_id,$field = "plan"){
       $ret = $this->getData($plan_id,$field);
       if($ret && $ret["tags"]){
           $tags = explode(',', $ret["tags"]);
           $ret["tags"] = $this->getMultipleTitleFromData($tags,false);
       }
       
       if(!$ret){          
            throw new ApiException(427111,200);
       }
       
       if( isset($ret["days"]) && $ret["days"]){  
            foreach($ret["days"] as $day){                
                $tmp = $this->get($day["doc_id"], FALSE);
                if($tmp){
                    $days[] = array("step"=>$day["step"],"title"=>$tmp["title"],"time"=>$tmp["time"],"doc_id"=>$day["doc_id"]);
                }
                unset($tmp);
            }  
                     
            if($days){
                $ret["days"] = $days;
            }  
     }
     
     $ret["participant_num"] = $this->getParticipantNum($plan_id);
     
     return $ret;
   }  
   
   public function getDayData($doc_id,$field="day"){
       $ret = $this->getData($doc_id,$field);
       if(! $ret){        
         throw new ApiException(427111,200);    
       }
       if(isset($ret["steps"]) && $ret["steps"]){  
           foreach($ret["steps"] as $step){                
                $tmp = $this->get($step["doc_id"], FALSE);
                if($tmp){
                    $steps[] = array("step"=>$step["step"],"title"=>$tmp["title"],"time"=>$tmp["time"],"doc_id"=>$step["doc_id"]);
                }
                unset($tmp);
            }  
            
            if($steps){
                $ret["steps"] = $steps;
            }  
     }
     
     return $ret;
   }
   
   public function getPlanList($page,$size){
       $data = array();
       
       $dbModel = new PlandbModel();
       $ret = $dbModel->getList($page, $size);
       if(!$ret["total_rows"]){
           throw new ApiException(427111,200);
       }
       
       $data["total"] = $ret["total_rows"];
       $data["page"] = intval($page);
       $data["size"] = intval($size);
       $data["data"] = array();
       foreach($ret["rows"] as $val){
           $plan_id = "skin:plan:".$val["id"];
           $participant_num = $this->getParticipantNum($plan_id);
           $days = $this->getPlanDayStep($val["id"]);
           $data["data"][] = array(
                                    "doc_id"=>$plan_id,"title"=>$val["title"],"tags"=>$val["tags"],
                                    "description"=>$val["description"],
                                    "difficulty_level"=>intval($val["difficulty_level"]),
                                    "tools"=>$val["tools"],                                    
                                    "circle"=>intval($val["circle"]),
                                    "principle"=>$val["principle"],
                                    "fit"=>$val["fit"],
                                    "tips"=>$val["tips"],
                                    "image"=>$val["image"],
                                    "thumb"=>$val["thumb"],
                                    "body_part"=>$val["body_part"],
                                    "basic"=>intval($val["basic"]),
                                    "top"=>intval($val["top"]),
                                    "days"=>$days,
                                    "participant_num"=>$participant_num,
                                    "create_at"=>intval($val["create_at"]),
                                    "update_at"=>intval($val["update_at"])
                                );
           unset($plan_id,$participant_num,$days);
       }
       
       return $data;
       
   }
   
   private function getPlanDayStep($id){
       $data = array();
       $dbModel = new PlandbModel();
       $days = $dbModel->getDaysByPlanId($id,"id,step");
       if($days){
            foreach($days as $val){
                $data[] = array("doc_id"=>"skin:day:".$val["id"],"step"=>intval($val["step"]));
            }
       }
       return $data;
   }
   
   public function getDataList($page=1,$size=10,$field=""){
       $ret = array();   
         try{
            $data = $this->getList("skin_".$field."s",array("doc_type"=>"skin:".$field),$page,$size);
         }catch(ServiceException $se){
             throw new ApiException(427111,200);
         } 
         
         $ret["total"] = $data["total_rows"];
         $ret["page"] = intval($page);
         $ret["size"] = intval($size);
         if(isset($data["rows"]) && $data["rows"]){             
             foreach($data["rows"] as $val){    
                 unset($val["value"]["doc_type"]);
                 $val["value"]["participant_num"] = $this->getParticipantNum($val["doc_id"]);
                 $ret["data"][] = array("doc_id"=>$val["doc_id"]) + $val["value"];
             }
         }else{
             throw new ApiException(427111,200);
         }
         
         return $ret;
   }
   
   public function getDataByTitle($title,$page=1,$size=10,$field=""){
        $multi = array("query"=>$title,"fields"=>array("doc.title") );
        $multi_param = array("query"=>array("multi_match"=>$multi)); 
        $filter_param = array("filter"=>array("term"=>array("doc.doc_type"=>"skin:".$field)));
        $param = array("query"=>array("filtered"=>$multi_param+$filter_param));
        
        $ret = $this->queryES($param,$page,$size);
        
        $data["total"] = $ret["hits"]["total"];
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        
        foreach ($ret["hits"]["hits"] as $val) { 
            $id = str_replace("{:DEVICEDATA:}", "", $val["_id"]);
            unset($val["_source"]["doc"]["doc_type"],$val["_source"]["doc"]["_ser_type_"]);
            $data["list"][] = array("doc_id"=>$id) + $val["_source"]["doc"];
            unset($id);
        }
       
        return $data;        
   }
   
   public function addData($data,$doc_id="",$field=""){
      $time = time();
       if(empty($doc_id)){
           
           $doc_id = "skin:".$field.":".uniqid();
           $data["create_at"] = $time;    
           
       }else{   //更新           
            try{
                $old = $this->get($doc_id); //查看是否存在
            } catch (ServiceException $ex) {
                throw new ApiException(427113,200,"this ". $field ." does not exist!");
            }                        
            
            if($field == "plan"){
                $data["days"] = $old["days"];
                $participant_num = $old["participant_num"];
                $data["participant_num"] = $participant_num;
            }else if($field == "day"){
                $data["plan_id"] = $old["plan_id"];
                $data["steps"] = $old["steps"];
            }else if($field == "step"){
                $data["day_id"] = $old["day_id"];
            } 
            $create_at = $old["create_at"];
            $data["create_at"] = $create_at;                
       }
       
       if($field == "day"){
            $plan_id = $data["plan_id"];
            $step = $data["step"];
            $this->updateIntlPlanSteps($doc_id,$plan_id,$step);
       }else if($field == "step"){
           $day_id = $data["day_id"];
           $step = $data["step"];
           $this->updateIntlDaySteps($doc_id,$day_id,$step);
       }   
       $data["update_at"] = $time;      
       $data["doc_type"] = "skin:".$field;
       $this->set($doc_id, $data);
       
       return $doc_id;
   }
   
   protected function updateIntlPlanSteps($doc_id,$plan_id,$step){
       try{
            $plan = $this->get($plan_id);
       }catch(App\Exception\ServiceException $se){
           throw new ApiException(427113,200,"this plan does not exist!");
       }
       
       $exist = false;
       foreach ($plan["days"] as $key => $val) {
           if($val["doc_id"] == $doc_id){
               $plan["days"][$key] = array("step"=>$step,"doc_id"=>$doc_id);
               $exist = true;
               break;
           }
       }
       if(!$exist){
           $plan["days"][] =  array("step"=>$step,"doc_id"=>$doc_id);
       }
       
       $this->set($plan_id, $plan);       
   }
   
   protected function updateIntlDaySteps($doc_id,$day_id,$step){
       try{
            $day = $this->get($day_id);
       }catch(App\Exception\ServiceException $se){
           throw new ApiException(427113,200,"this plan does not exist!");
       }
       
       $exist = false;
      
       foreach ($day["steps"] as $key => $val) {
           if($val["doc_id"] == $doc_id){
               $day["steps"][$key] = array("step"=>$step,"doc_id"=>$doc_id);
               $exist = true;
               break;
           }
       }
       if(!$exist){
           $day["steps"][] =  array("step"=>$step,"doc_id"=>$doc_id);
       }
       
       $this->set($day_id, $day);       
   }
}
