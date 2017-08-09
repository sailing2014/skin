<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ServiceException;

/**
 *  problem model
 *
 * @author yang.f
 *        
 */
class ProblemModel extends ServiceBase {

    public function __construct() {
        $this->serviceName = 'problem';
    }  
    
    public function add($testType,$title,$choices)
    {    
        $url = $this->api['devicedata.add'];
        
        $time = time();
        $question_id = "skin:test:question:".  uniqid();
        $data = array( "doc_id" => $question_id,
                       "data"   =>  array(   "test_type"=>$testType,
                                            "title"=>$title,
                                            "choices"=>$choices,
                                            "create_at"=>$time,
                                            "update_at"=>$time
                                         )
                                 
                      );
        
        $this->sendHttpRequest($url, $data);      
        
        //更新记录这种test_type类型所有题目id的文档，不用view
        $type_doc_id = "skin:test:type:".$testType;
        $this->addTypeProblems($type_doc_id,$question_id,$time);       
        
        return $question_id;
    }
   
    private function addTypeProblems($type_doc_id,$question_id,$time){     
        
        $total = 0;
        $data = array();
        
        $old = $this->get($type_doc_id,false);  
        if($old)
        {
            $total = $old["total"];
            $data = isset($old["data"])?$old["data"]:array();
        }      
            
         ++$total  ;
        $data[$question_id] =  array("question_id"=>$question_id,"create_at"=>  $time,"update_at"=>$time);
        $new = array("total"=>$total) + array("data"=>$data);
        $this->set($type_doc_id, $new);
            
    }
    
    
     
    public function delete($doc_id)
    {
         $url = $this->api['devicedata.delete'];
        
        $data = array("doc_id"=>$doc_id);
        
        $response = $this->sendHttpRequest($url, $data);       
        return $response["data"];
    }
    
    public function get($doc_id,$throwException=true)
    {
        $url = $this->api['devicedata.get'];
        
        $data = array("doc_id"=>$doc_id);
        
        $response = $this->sendHttpRequest($url, $data,$throwException);      
        if(isset($response["data"])){
            return $response["data"];
        }else{
            return false;
        }
    }
    
    public function set($doc_id,$data){
        $url = $this->api['devicedata.set'];
        $data = array("doc_id"=>$doc_id,"data"=>$data);        
        $response = $this->sendHttpRequest($url, $data);  
        return $response;
    }
    
    public function getByType($test_type){
        $type_doc_id = "skin:test:type:".$test_type;
        $ret = $this->get($type_doc_id);
        
        $response["total"] = $ret["total"];
        $response["test_type"] = intval($test_type);
        foreach($ret["data"] as $key => $val){
            //根据order排序 todo
            $tmp = $this->get($key);
            unset($tmp["test_type"],$tmp["create_at"],$tmp["update_at"]);
            foreach($tmp["choices"] as $key1 => $val1){
                unset($tmp["choices"][$key1]["order"]);
                if(isset($tmp["choices"][$key1]["score"])){
                    unset($tmp["choices"][$key1]["score"]);
                }
            }
            $response["questions"][] = array("question_id"=>$key) + $tmp ;
            unset($tmp);
        }
        return $response;
    }
    
    
    
    // -------------intl--------------//
    /**
     * 
     * @param type $test_type
     * @return type
     */
    public function getIntlByType($test_type){
        $type_doc_id = "skin:test:type:".$test_type;
        $ret = $this->get($type_doc_id);
        
        $response["total"] = $ret["total"];
        $response["test_type"] = intval($test_type);
        foreach($ret["data"] as $key => $val){
            //根据order排序 todo
            $tmp = $this->get($key);  
            $response["questions"][] = array("question_id"=>$key) + $tmp ;
            unset($tmp);
        }
        return $response;
    }
    
    public function update($question_id,$testType,$title,$choices)
    {    
        $multiple = array();
        
        $old = $this->get($question_id);    
        //更新question
        $update_at = time();
        $new_question = array(  "test_type"=>$testType,
                        "title"=>$title,
                        "choices"=>$choices,
                        "create_at"=>$old["create_at"],
                        "update_at"=>$update_at
                      );
        $multiple[] = array("doc_id"=>$question_id,"data"=>$new_question);
        
        //更新记录这种test_type类型所有题目id的文档，不用view        
       if($old["test_type"] == $testType )
       {
           //更新list          
           $new_list_data = $this->getOperationList($testType, $question_id);
           $multiple[] = $new_list_data;
       }else{           
           //删除list原来的index，再插入现在的     
           $multiple[] = $this->getOperationList($old["test_type"], $question_id,-1);           
         
           $multiple[] = $this->getOperationList($testType, $question_id,1);     
       }     
       
       $url = $this->api["devicedata.write"];     
       $this->sendHttpRequest($url,array("data"=>$multiple));        
       
    }
    
    public function deleteProblem($question_id){
        $old = $this->get($question_id);
        //1.更新list
        $testType = $old["test_type"];
        $list = $this->getOperationList($testType, $question_id, -1);
        $this->set($list["doc_id"], $list["data"]);
        
        //2.删除question
        $this->delete($question_id);
        
    }
    
    /**
     * 
     * @param type $testType
     * @param type $question_id
     * @param int  $total_plus 0-->update,-1-->delete,1-->add
     * @return array 
     */
    private function getOperationList($testType,$question_id,$total_plus=0){        
           
           $update_at = time();
           
           $list_doc_id = "skin:test:type:".$testType;    
           try{
                $old_list = $this->get($list_doc_id);  
           }  catch (ServiceException $se){ //没有list文档，创建list文档
               $old_list = array("total"=>0,"data"=>array( $question_id=>
                                                                    array("question_id" => $question_id,
                                                                          "create_at"  =>  $update_at,
                                                                          "update_at"  =>  $update_at)
                                                        ));
           } 
           
            $new_list = $old_list;            
            $new_list["total"] += $total_plus;
            if($new_list["total"] < 0){
                $new_list["total"]  = 0;
            }

            if($total_plus == -1){              
                 unset($new_list["data"][$question_id]);
            }else{
                $new_list["data"][$question_id] =  array("question_id"=>$question_id,"create_at"=>  $old_list["data"][$question_id]["create_at"],"update_at"=>$update_at);
            }

            $ret = array("doc_id"=>$list_doc_id,"data"=>$new_list);     
            return $ret;           
    }
    
    // -------------intl--------------//
    /**
     * get all problems
     * 
     * @return array
     */
    public function getIntlAll(){
        $type_doc_id = "skin:test:types";        
        $types = $this->get($type_doc_id);        
        $ret = array();
        
        foreach ($types as $key => $val) {            
       
            $type_doc_id = "skin:test:type:".$val["test_type"];
            $problems = $this->get($type_doc_id,false);            
            if($problems && isset($problems["total"]) && $problems["total"]){
                $list["total"] = $problems["total"];
                $list["test_type"] = $val["test_type"];
                foreach($problems["data"] as $k => $v){
                    //根据order排序 todo
                    $tmp = $this->get($k,false);                   
                    if($tmp){                               
                        $list["questions"][] = array("question_id"=>$k) + $tmp ;        
                    }
                }
                $ret[] = $list;
                unset($problems,$list,$tmp);
            }
            unset($type_doc_id,$problems);
        }
            return $ret;
    }
   
    
}
