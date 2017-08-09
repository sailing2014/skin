<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Models\TestModel;

/**
 *  result model
 *
 * @author yang.f
 *        
 */
class ResultModel extends ServiceBase {

    public function __construct() {
        $this->serviceName = 'result';
    }  
    
    public function add($uid,$test_type,$result)
    {    
        $url = $this->api['devicedata.update'];
        
        $result_id = "skin:result:".  $uid;
        $data = array(
                       "doc_id" => $result_id,
                       "data"   =>  array(   
                                            "type".$test_type => array(
                                                                        "test_type"=>$test_type,
                                                                        "result"=>$result,
                                                                        "time"=>time()
                                                                       )
                                         )                                 
                      );
        
        $this->sendHttpRequest($url, $data); 
        
        $ret = $this->getResultByUid($uid);        
        return $ret;
    }    
    
  
    
    public function get($doc_id,$throwException=true)
    {
        $url = $this->api['devicedata.get'];
        
        $data = array("doc_id"=>$doc_id);
        
        $response = $this->sendHttpRequest($url, $data,$throwException);       
        return $response["data"];
    }    
    
    public function update($doc_id,$data,$throwException=true){
        $url = $this->api['devicedata.update'];
        
        $data = array("doc_id"=>$doc_id,"data"=>$data);
        $response = $this->sendHttpRequest($url, $data,$throwException); 
        return $response;
    }
    
    public function getResultByUid($uid){
        $response["uid"] = intval($uid);
        $response["skin"] = array();
        
        $result_id = "skin:result:".  $uid; 
        $ret = $this->get($result_id,false);    
        if(!$ret){
            return $response;
        }        
         
        $testModel = new TestModel();        
        foreach($ret as $val){   
            $result_title = $testModel->getResultTitle($val["test_type"],$val["result"]);
            $response["skin"][] = array("test_type"=>$val["test_type"],"result"=>$val["result"],"result_title"=>$result_title) ;
        }
        
        return $response;
    }
    
    /**
     * 
     * @param int $uid
     * @param int $test_type
     * @param type $answers
     */
   public function addUserAnswers($uid,  $test_type, $answers){
       $url = $this->api['devicedata.update'];
        
        $result_id = "skin:answers:type:". $test_type;
        $data = array(
                       "doc_id" => $result_id,
                       "data"   => array( 
                                            "uid"=>$uid,
                                            "test_type"=>$test_type,
                                            "answers"=>$answers,
                                            "time"=>time()
                                        )
                      );
        
        $this->sendHttpRequest($url, $data); 
        
        $user_result = $this->getResultByAnswers($test_type,$answers);   
        
        $result = $user_result["result"];
        $this->add($uid, $test_type, $result); 
       
        return $user_result;
   }
   
   /**
    * answer algorithm todo
    * @param int test type
    * @param type $answers
    */
   private function getResultByAnswers($test_type,$answers){
       $testModel = new TestModel();
       $score = 0;
        foreach($answers as $val){
            $score += $this->getScoreByQuestionId($val->question_id,$val->choice_index);
        }
       $result = $testModel->getAnalyticsResult($test_type,$score);      
       
       return $result;
   }
   
   private function getScoreByQuestionId($question_id,$choice){
       $score = 0;       
       $question = $this->get($question_id,false);
       if($question){
           foreach($question["choices"] as $val){
               if($val["index"] == $choice){
                   $score = $val["score"];
                   break;
               }
           }
       }
       return $score;
   }    
  
}
