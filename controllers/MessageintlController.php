<?php

namespace App\Controllers;

use App\Models\MessageModel;
use App\Exception\ApiException;

class MessageintlController extends ControllerBase {
  
        public function addMessageAction(){
            
            $this->checkApiToken();
            
            $validators = array("uid"=>"required","content"=>"required");
            $this->checkParams($validators);
            
            $uid = $this->_body->uid;
            $title = isset($this->_body->title)?$this->_body->title:"";
            $content = $this->_body->content;
            
            $messageModel = new MessageModel();
            $message_id = $messageModel->addUserMessage($uid,$content,$title);
            
            $this->responseJson($message_id,"message_id");
        }
        
        
        public function addPushAction(){
            $this->checkApiToken();
            
            $validators = array("uid"=>"required","content"=>"required","title"=>"required");
            $this->checkParams($validators);
            
            $uid = $this->_body->uid;
            $title = $this->_body->title;
            $content = $this->_body->content;
            
            $messageModel = new MessageModel();
            $messageModel->addUserPush($uid,$content,$title);
            
            $this->responseJson(0);
        }
	
        
        public function senderAction(){
            $this->checkApiToken();
            
            $validators = array("uid"=>"required","content"=>"required");
            $this->checkParams($validators);
            
            $uid = $this->_body->uid;
            $title = $this->_body->title;
            $content = $this->_body->content;
            
            $messageModel = new MessageModel();
            $message_id = $messageModel->addUserMessage($uid,$content,$title);
            
            if($message_id){
                $custom = array("message_id"=>$message_id);
                $messageModel->addUserPush($uid,$content,$title,$custom);
            }else{
                throw new ApiException();
            }
            
            $this->responseJson(0);
        }
    
   
        public function getMessageListAction($uid){
            $this->checkApiToken();      
            
            $messageModel = new MessageModel();
            $data = $messageModel->getIntlUserMessageList($uid);   
            
            $this->responseJson($data);
        }
}
