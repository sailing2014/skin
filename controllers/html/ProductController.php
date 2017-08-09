<?php
namespace App\Html\Controllers;

use App\Models\ProductModel;
use App\Exception\ApiException;

class ProductController extends ControllerBase {   

    
    public function indexAction()
    {       
        $id = $this->dispatcher->getParam("id");             
        $model = new ProductModel();
    
        $token = $this->request->getReqHeader('TOKEN');
        if(!$token){
            $token = $this->request->get('token');
        }
        $user = $this->checkToken($token);
        if($user  && isset($user["uid"]) && !empty($user["uid"])){
            $uid = $user["uid"];
        }
        
        $f = intval($this->request->get('f',"int",0));
        try{
            $data = $model->getProduct($uid,$id,$f);  
        }  catch (ApiException $se){
            $return = array("errorCode"=>$se->getCode(),"errorMessage"=>$se->getMessage());
            $this->jsonReturn($return);
        }   
        $this->view->setVar('token', $token);  
        $this->view->setVar("product", $data);
        $this->view->setVar('f',$f);
    }
    
    public function elementListAction(){
        $this->view->pick('product/elementList');
        $pid = $this->dispatcher->getParam("id");    
        $token = $this->request->get('token','string','');
        
        $user = $this->checkToken($token);
        if($user  && isset($user["uid"]) && !empty($user["uid"])){
            $uid = $user["uid"];
        }        
        $model = new ProductModel();
        $collect = $model->checkUserCollect($uid, $pid);
        
        try{
            $data = $model->getProductComponents($pid);
        }catch (ApiException $se){
            $return = array("errorCode"=>$se->getCode(),"errorMessage"=>$se->getMessage());
            $this->jsonReturn($return);
        }
        
        $this->view->setVar("product", $data);
        $this->view->setVar("collect",$collect);
        $this->view->setVar("token",$token);
    }

    public function elementAction(){
        $this->view->pick('product/elementTxt');
        $id = $this->dispatcher->getParam("id");  
        $pid = $this->request->get("pid","string","");
        $token = $this->request->get("token","string","");
                
        $user = $this->checkToken($token);
        if($user  && isset($user["uid"]) && !empty($user["uid"])){
            $uid = $user["uid"];
        }        
        $model = new ProductModel();
        $collect = $model->checkUserCollect($uid, $pid);
        
        try{
            $data = $model->getIntlUsageById($id);            
        }catch (ApiException $se){
            $return = array("errorCode"=>$se->getCode(),"errorMessage"=>$se->getMessage());
            $this->jsonReturn($return);
        }        
        $this->view->setVar("component", $data);
        $this->view->setVar('pid', $pid);
        $this->view->setVar("collect",$collect);
        $this->view->setVar("token",$token);
    }
    
    public function riskListAction(){
        $this->view->pick('product/riskList');
        
        $id = $this->request->get("id");        
        $type = $this->request->get("type");
        $token = $this->request->get('token','string','');
        
        $user = $this->checkToken($token);
        if($user  && isset($user["uid"]) && !empty($user["uid"])){
            $uid = $user["uid"];
        }        
        $model = new ProductModel();
        $collect = $model->checkUserCollect($uid, $id);
        
        try{
            $data = $model->getCompRiskListByProductId($id,$type);  
        }catch (ApiException $se){
            $return = array("errorCode"=>$se->getCode(),"errorMessage"=>$se->getMessage());
            $this->jsonReturn($return);
        }
        $this->view->setVar("components", $data["components"]);      
        $this->view->setVar("title", $data["title"]);
        $this->view->setVar("pid",$id);
        $this->view->setVar("collect",$collect);
        $this->view->setVar("token",$token);    
    }       
    
}
