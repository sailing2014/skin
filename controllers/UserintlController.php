<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserintlController extends ControllerBase {    
    
    /**
     * 获取用户信息
     * GET /v1/user/intl/{uid}
     */
    public function getAction($uid)
    {   
        $this->checkApiToken();
        $userModel = new UserModel(); 
        $user = $userModel->getIntlUserByUid($uid);   
        $this->responseJson($user, "user");           
    }
    
    /**
     * get user list
     * 
     * GET /v1/user/intl
     */
    public function getListAction(){
        $this->checkApiToken();
        
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size', 'int', 10);
        $userModel = new UserModel();
        $data = $userModel->getUserList($page,$size);
        $this->responseJson($data);
    }

    /**
     * push rule 
     * 
     * POST /v1/user/todo/push/intl
     * 7:30 am 防晒、控油、控敏
     * 12:30 am 补水
     * 9:00 pm 深层清洁、肌肤更新、深度补水
     */
    public function pushTodoAction(){
        $this->checkApiToken();
        
        $userModel = new UserModel();
        
        sleep(rand(10,30));
        $one_push_id = "skin:backstage:push";
        $push = $userModel->getCache($one_push_id, false);
        
        if(!$push){
            $this->logger->log('enter push..');
            $data = array("time"=>time());
            $userModel->setCache($one_push_id, $data, 5*60);                    
            $userModel->getCompleteTestUid();
            $this->logger->log('outer push..');
        }else{
            $this->logger->log(' Only 1 push within 5 minutes..');
        }       
        
        $this->responseJson(0);
    }
 
     /**
     * get user uploaded image analytics list
     * 
     * GET /v1/user/analytics/intl
     */
    public function getAnalyticsAction(){
        $this->checkApiToken();
        
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size', 'int', 10);
        $userModel = new UserModel();
        $data = $userModel->getImageList($page,$size);
        $this->responseJson($data);
    }
    
    
      /**
     * get user uploaded image analytics list
     * 
     * GET /v1/user/analytics/intl/{uid}?page=1&size=10&start=0&end=1471934039
     */
    public function getAnalyticsByUidAction($uid){
//        $user = $this->checkToken();
        
        $uid = intval($uid);
        $page = intval($this->request->get('page', 'int', 1));
        $size = intval($this->request->get('size', 'int', 10));
        $start = intval($this->request->get('start', 'int', 0));
        $end = intval($this->request->get('end', 'int', 0));
        if(!$end){
            $end = time();
        }        
        $userModel = new UserModel();
        $data = $userModel->getUserImageList($uid,$page,$size,$start,$end);
        $this->jsonReturn($data);
    }
    
    
    /**
     * get user clocks
     * 
     * GET /v1/user/clocks/intl
     */
    public function getClocksAction(){
        $this->checkApiToken();
        
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size', 'int', 10);
        $userModel = new UserModel();
        $data = $userModel->getClockList($page,$size);
        $this->responseJson($data);
    }       
}
