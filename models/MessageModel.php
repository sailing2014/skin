<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Exception\ServiceException;

/**
 *  message model
 *
 * @author yang.f
 *        
 */
class MessageModel extends ServiceBase {

    public function __construct() {
        $this->serviceName = 'message';
    }  
    
   public function addUserMessage($uid,$content,$title){
            $create_time = time();
            $msg_id = "skin:message:" . $uid . ":" . $create_time;
            
            $data['uid']         = $uid;
            $data['title']       = $title;        
            $data['content']     = $content;
            $data['created_at']  = $create_time;
            $data['from_type']  = 1;            
            $data['have_read'] = 0;
            $data['doc_type']    = "skin_message";
            
            $this->set($msg_id, $data);            
            $this->updateUserMessageList($uid,$msg_id,$create_time);
            
            return $msg_id;
   }
   
   private function updateUserMessageList($uid,$msg_id,$create_time){
       $doc_id = "skin:message:list:".$uid;
       $list = $this->get($doc_id,false);
       if($list){
           $data = $list;
       }else{
           $data["uid"] = $uid;
       } 
       $data["messages"][$msg_id] = array("message_id"=>$msg_id,"create_at"=>$create_time);
       
       $this->set($doc_id, $data);
   }
   
   public function getIntlUserMessageList($uid){   
       $doc_id = "skin:message:list:".$uid;
       $list = $this->get($doc_id,false);      
       $data = array();
       if($list && isset($list["messages"])){
           foreach($list["messages"] as $val){
               $message = $this->get($val["message_id"], false);
               if($message){
                   $data[] = array("message_id"=>$val["message_id"]) + $message;
               }
               unset($message);
           }
       }
       
       return $data;
   }
   
   public function getUserMessageList($uid){
       $list = $this->getIntlUserMessageList($uid);
       $data = array();
       
        if($list){
           foreach($list as $val){
               if( isset($val['deleted_by_user']) && $val['deleted_by_user'] == 1){
               }else{
//                   $convertTime = $this->convertTime($val["created_at"]);
                   array_unshift($data, array("message_id"=>$val["message_id"],
                                    "type" => $val["from_type"],
                                    "title" => $val["title"],
                                    "content" => $val["content"],
                                    "have_read" => $val["have_read"],
                                    "create_at" => $val["created_at"]
                                  ) );
               }
           }
       }
       
       return $data;
   }
   
   public function getUserMessage($message_id){
       $data = array();
       $message = $this->get($message_id, false);
       if($message){
           $data = array("message_id"=>$message_id,"type"=>$message["from_type"],
                          "title"=>$message["title"],
                          "content"=>$message["content"],
                          "have_read"=>$message["have_read"],
                          "create_at"=>$message["created_at"]
                        ); 
          
       } 
    
       return $data;
   }
   
    public function deleteUserMessage($message_id,$uid){
        $ret = $this->checkOwner($message_id, $uid,427115);
        if($ret){
            try{
                $this->delete($message_id);
                $doc_id = "skin:message:list:".$uid;
                $list = $this->get($doc_id);
                if($list){
                    unset($list["messages"][$message_id]);
                    $this->set($doc_id, $list);
                }
               
            }  catch (ServiceException $se){
                throw new ApiException(427115,400);
            }
            
        }else{
            throw new ApiException(427115,400,"This message is not exist");
        }
        
   }
   
    public function deleteUserMessageList($uid){
        $doc_id = "skin:message:list:".$uid;
        $list = $this->get($doc_id,false);      
        $message_ids ="";
        if($list && isset($list["messages"])){
            foreach($list["messages"] as $val){
                $message_ids .= $val["message_id"].",";
            }
        }
        if($message_ids){
            $message_ids .=$doc_id; 
        }
        $this->delete($message_ids);
    }
   
   public function setMessageRead($message_id, $uid){
       $ret = $this->checkOwner($message_id, $uid);
       if($ret){           
           try{
               $this->update($message_id, array("have_read"=>1));
           }catch(ServiceException $se){
               throw new ApiException(427112,400,$se->getMessage());
           }
       }
   }
   
   private function checkOwner($message_id,$uid,$errcode=427113){      
      $ret = false;
       
       $message = $this->get($message_id,false);
       if($message){
           if($message["uid"] == $uid){
               $ret = true;
           }else{
               throw new ApiException(460007,400);
           }
       }else{
           throw new ApiException($errcode,400,"This message is not exist");
       }
      
       return $ret;
       
   }
   
   public function addUserPush($uid,$content,$title,$custom=array("type"=>3),$message_type=1){
       $push_account= $this->getPushAccount($uid);
        if($push_account){
                $push_param=$this->format_push_param($push_account,$message_type,$custom,$title,$content);
                $url = $this->api["message.push"];
                try{
                    $this->sendHttpRequest($url,$push_param,false,'POST',true,array(),0);
                }catch(ServiceException $se){
                    throw new ApiException(460006,400,$se->getMessage());
                }
        }else{
            throw new ApiException(460002,400);
        }
   }   

  
    
    private function format_push_param($push_account,$message_type=1,$custom_content=array("type"=>3),$title="",$content=""){
            
            $push_param=array('message_type'=>$message_type,'expire'=>24*3600);            
            
            if(isset($push_account["plat"])&&$push_account['push_id']&&$push_account['push_id']!='null'){
                if($push_account["plat"]=='ios'){                     
                            $push_param['apn_push_entity']['device_tokens']=$push_account["push_id"];
                            $push_param['apn_push_entity']['env']= APNS_MODE;
                            $push_param['apn_push_entity']['custom_content']=$custom_content;
                            if($message_type==1){
                                 $push_param['apn_push_entity']['body']=$content;
                            }

                }else if($push_account["plat"]=='android'){
                        
                            $push_param['baidu_push_entity']['device_tokens']=$push_account["push_id"];                            
                            $push_param['baidu_push_entity']['title']=$title;
                            $push_param['baidu_push_entity']['description']=$content;
                            $push_param['baidu_push_entity']['custom_content']=$custom_content;
                            if($message_type==0){
                                 $push_param['baidu_push_entity']['description']=json_encode($custom_content); 
                            }
                }
            }
               
                
            return $push_param;
        }
    
   
   public function getPushAccount($uid){
       $doc_id="skin:app_push:".$uid;
       $data = $this->get($doc_id,false);
       if($data){
           unset($data["type"]);
           return $data;
       }else{
           throw new ApiException(460002,400);
       }
                
   }
   
    /**
     * 
     * @param int $uid
     * @param array $push_account
     * @return string doc_id
     * @throws ApiException
     */
   public function setPushAccount($uid,$push_account){
        $doc_id="skin:app_push:".$uid;
        try{
            $this->set($doc_id, $push_account);
        }catch(ServiceException $se){
            throw new ApiException(460003,400);
        }
        
        return $doc_id;
   }
    public function deletePushAccount($uid)
    {
       $doc_id="skin:app_push:".$uid;
       try{
            $this->delete($doc_id);
        }catch(ServiceException $se){
            throw new ApiException(460004,400);
        }
    }
    
    /**
     * send todo list arr
     * 
     * @param int uid
     * @param array $data
     * @param boolean $push push flag. default is false
     */
    public function sendTodo($uid,$data,$push=false){
        $keyPre = "skin:backstage:push:";
        $cacheData = array("time"=>time());
        foreach($data as $val){            
            $cacheDocId = $keyPre . md5($uid . $val["title"]);
            $pushCacheResponse = $this->getCache($cacheDocId,false);
            if(!$pushCacheResponse){
                $message_id = $this->addUserMessage($uid,$val["content"],$val["title"]);
                $this->logger->log('UID : ' . $uid . ', content :" ' . $val["content"] . 
                                    '",title: "' . $val["title"].'",message_id:"'. $message_id.'"');
                if($message_id && $push){
                        $custom = array("message_id"=>$message_id);
                        $this->addUserPush($uid,$val["content"],$val["title"],$custom);
                        unset($custom);
                }
                unset($message_id);
            }else{
                $this->setCache($cacheDocId, $cacheData, 120);
            }
            unset($cacheDocId,$pushCacheResponse);
        }
    }
}
