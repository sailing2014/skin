<?php
namespace App\Html\Controllers;
use App\Models\ArticleModel;
use App\Exception\ApiException;

class ArticleController extends ControllerBase {   

    
    public function indexAction()
    {
        $token = $this->request->getReqHeader('TOKEN'); 
//        $token = "sess::bd8dd33d88aa8d002231882ecbea01be";
        $user = $this->checkToken($token);
        if($user  && isset($user["uid"]) && !empty($user["uid"])){
            $uid = $user["uid"];
        }
        
        // Returns "id" parameter
        $id = $this->dispatcher->getParam("encyclopedia_id");
        
        $model = new ArticleModel();
        try{
        $data = $model->getUserEncyclopedia($uid,$id);
        }catch(ApiException $se){
            $return = array("errorCode"=>$se->getCode(),"errorMessage"=>$se->getMessage());
            $this->jsonReturn($return);
        }        
        $this->view->setVar("article", $data);
        $this->view->setVar('token',$token);
    } 
   
    
}
