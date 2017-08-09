<?php
namespace App\Controllers;

use App\Models\FeedbackModel;
use App\Models\UserModel;
use App\Exception\ApiException;
use App\Exception\ServiceException;

/**
 * 用户反馈接口
 *
 * @author yang.f
 *        
 */
class FeedbackController extends ControllerBase
{  

    /**
     * 意见反馈
     * POST /v1/feedback
     */
    public function addAction()
    {
        $validators = array("content"=>"required");
        $this->checkParams($validators);
        
        $user1 = $this->checkToken();        
        $userModel = new UserModel();
        try{
            $user = $userModel->getUserByUid($user1["uid"]);  
        }  catch (ServiceException $se){
            throw new ApiException(470001,400,"Query this user info failed!");
        }        
       
        $feedbackModel = new FeedbackModel();
        $ret = $feedbackModel->addIssue($user["uid"], $user["phone"], trim($this->_body->content));
        if($ret){
         $this->responseJson(0);   
        }else{
            throw new ApiException(470001,400);
        }
    }
}
