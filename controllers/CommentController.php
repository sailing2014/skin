<?php

namespace App\Controllers;

use App\Exception\ApiException;
use App\Models\CommentModel;

class CommentController extends ControllerBase {
    /**
     * POST /v1/message/comments
     * add comments
     * 
     * @throws ApiException
     */
    public function addCommentAction(){
        $user = $this->checkToken();      
        
        $product_id = isset($this->_body->product_id)?$this->_body->product_id:"";
        $encyclopedia_id = isset($this->_body->encyclopedia_id)?$this->_body->encyclopedia_id:"";
        
        if( !($product_id || $encyclopedia_id) ){
            throw new ApiException(400001, 400,"Field product_id and encyclopedia_id can't be empty at the same time");
        }else if($product_id && $encyclopedia_id){
            throw new ApiException(400001, 400,"Field product_id and encyclopedia_id can't be sent at the same time");
        } 
        
        $validators = array("content"=>"required");
        $this->checkParams($validators);        
        $content = $this->_body->content;
        $commentModel = new CommentModel();
        $data = array();
       
        if($product_id){
            $comment_id = $commentModel->addProductComment($product_id, $user["uid"], $content);
        }else if($encyclopedia_id){
            $comment_id = $commentModel->addArticleComment($encyclopedia_id, $user["uid"], $content);
        }
        
        $data["comment_id"] = $comment_id;
        $this->responseJson($data);
    }
    
    /**
     * GET /v1/message/comments/{doc_id}?page={page}&size={size}
     * get comment list
     * @param string $doc_id
     */
   public function getListAction($doc_id){
       $this->checkToken();
       $page = $this->request->get('page','int',1);
       $size = $this->request->get('size','int',5);
       $commentModel = new CommentModel();
       $data = $commentModel->getComments($doc_id,0,$page,$size);
       $this->responseJson($data);
   }
   
    /**
     * html get product comments list
     * 
     * GET /v1/message/html/comments/{product_id}?page={page}&size={size}
     */
    public function getHtmlListAction($product_id){
        $ret = array("errorCode"=>400,"errorMessage"=>"succes","data"=>"");
        $token = $this->request->getReqHeader('TOKEN');
        $user = $this->checkHtmlToken($token);
        
        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',5);
        
         if(isset($user["uid"]) ){
            $commentModel = new CommentModel();
            $ret["errorCode"] = 0;
            $ret["data"] = $commentModel->getHtmlComments($user["uid"],$product_id,$page,$size);                
        }
        $this->jsonReturn($ret);
    }
    
    /**
     * fav or cancel fav comment
     * 
     * POST /v1/message/html/comment/fav/{$comment_id}
     */
    public function htmlFavAction($comment_id){
        $ret = array("errorCode"=>400,"errorMessage"=>"succes");
        $token = $this->request->getReqHeader('TOKEN');
        $user = $this->checkHtmlToken($token);
//        $user["uid"] = 10000002;
        if(isset($user["uid"]) && $user["uid"]!==1){
            $commentModel = new CommentModel();
            $ret["errorCode"] = $commentModel->htmlFav($user["uid"],$comment_id);                
        }
        $this->jsonReturn($ret);
    }    
}
