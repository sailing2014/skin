<?php

namespace App\Controllers;

use App\Models\MessageModel;
use App\Exception\ApiException;


class MessageController extends ControllerBase {
  
    /**
     * get message list
     * 
     * GET /v1/message/list
     */
    public function getListAction(){
        $user = $this->checkToken();
        
        $messageModel = new MessageModel();
        $data = $messageModel->getUserMessageList($user["uid"]);
//        $data = array(  array("type"=>1,"message_id"=>"skin:message:1:1458119189","title"=>"关爱贴士","content"=>"亲爱的,明天有雨，记得带伞哟~","have_read"=>0,"create_at"=>1458119189),
//                        array("type"=>1,"message_id"=>"skin:message:1:1458109189","title"=>"充电提醒","content"=>"小肤电量仅剩10%,主人记得给我充电哈","have_read"=>1,"create_at"=>1458109189)
//                      );
        $this->responseJson($data,"data",true);
    }
    
    /**
     * get message detail 
     * 
     * GET　/v1/message/{message_id}
     * @param string $message_id
     */
    public function getDetailAction($message_id){
        $this->checkToken();
        
        $messageModel = new MessageModel();
        $data = $messageModel->getUserMessage($message_id);
//        $data = array("message_id"=>"skin:message:1:1428636899","type"=>1,"title"=>"关爱贴士","content"=>"亲爱的,明天有雨，记得带伞哟~","have_read"=>0,"create_at"=>1458119189);
        $this->responseJson($data,"message",true);
    }
  
    /**
     * delete message
     * 
     * DELETE /v1/message/{message_id}
     * @param string $message_id
     */
    public function deleteAction($message_id){
        $user = $this->checkToken();                
        
        $messageModel = new MessageModel();
        $messageModel->deleteUserMessage($message_id,$user["uid"]);
        
        $this->responseJson(0);
    }
    
      /**
     * get message list
     * 
     * DELETE /v1/message/list
     */
    public function removeListAction(){
        $user = $this->checkToken();                
        
        $messageModel = new MessageModel();
        $messageModel->deleteUserMessageList($user["uid"]);
        $this->responseJson(0);
    }
    
    /**
     * set message status read
     * 
     * PUT /v1/message/{message_id}
     * @param string $message_id
     */
     public function updateAction($message_id){
        $user = $this->checkToken();
       
        $messageModel = new MessageModel();
        $messageModel->setMessageRead($message_id,  $user["uid"]);
        
        $this->responseJson(0);
    }

    /**
     * add push account
     * 
     * POST /v1/push_account
     */
    public function addPushAccountAction(){
        $user = $this->checkToken();
        
        $validators = array(
                            "push_id"=>"required",
                            'plat' => array(
                                                   array('include', array('domain' => array("ios","android") ) )
                                            ));
        $this->checkParams($validators);
        
        $uid = $user["uid"];
        $plat = $this->_body->plat;
        $push_id = $this->_body->push_id;		
                
        if($plat=="android"&&strpos($push_id,":")){
            $channel_id_user_id=explode(':',$push_id);
            $push_id= $channel_id_user_id[0];
        }
        
        if(strtolower($push_id)=='null'){
            throw new ApiException(460001,400);
        }
             
        $push_account=array("uid"=>$uid,"push_id"=>$push_id,"plat"=>$plat,'type'=>'push_account');
        $messagModel = new MessageModel();
        $messagModel->setPushAccount($uid,$push_account);
        
        $this->responseJson(0);
    }
    
    /**
     * get push account
     * 
     * GET /v1/push_account/{uid}
     * @param int $uid
     */
    public function getPushAccountAction($uid){
        $user = $this->checkToken();       
      
        $messageModel = new MessageModel();
        $data = $messageModel->getPushAccount($user["uid"]);
        
        $this->responseJson($data);
    }
    
    /**
     * delete push account
     * 
     * DELETE /v1/push_account/{uid}
     * @param int $uid
     */
    public function deletePushAccountAction($uid){
        $user = $this->checkToken();
        
        $messageModel = new MessageModel();
        $messageModel->deletePushAccount($user["uid"]);
        $this->responseJson(0);
    }
}
