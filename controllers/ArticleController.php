<?php

namespace App\Controllers;

use App\Models\ArticleModel;

class ArticleController extends ControllerBase {
  
    /**
     * get tags 
     * 
     * GET /v1/article/tags
     */
    public function getTagsAction(){
        $user = $this->checkToken();
        
        $articleModel = new ArticleModel();
        $tag = $articleModel->getTagsByUid($user["uid"]);

        $data = array("uid"=>$user["uid"],"tags"=>$tag);
        $this->responseJson($data);
    }
    
        /**
     * get tags 
     * 
     * GET /v1/article/tags/list/{$tag_id}?page={page}&size={size}
     */
    public function getListByTagAction($tag_id){
        $this->checkToken();
        $page = intval($this->request->get('page', 'int', 1));
        $size = intval($this->request->get('size', 'int', 10));
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getListByTag($tag_id,$page,$size);
        $this->responseJson($data);
    }
    
    
    /**
     * get characteristics
     * 
     * GET  /v1/article/characteristics
     * 
     */
    public function getCharacterAction(){
        $user = $this->checkToken();            
        $articleModel = new ArticleModel();
        $characteristics = $articleModel->getCharacteByUid($user["uid"]);
        
        $data = array("uid"=>$user["uid"],"characteristics"=>$characteristics);
        $this->responseJson($data);
    }
    
     /**
     * get tips
     * 
     * GET /v1/article/tips
     * 
     */
    public function getTipsAction(){
        $user = $this->checkToken();       

        $articleModel = new ArticleModel();
        $tips = $articleModel->getTipsByUid($user["uid"]);
        
        $data = array("uid"=>$user["uid"],"tips"=>$tips);
        $this->responseJson($data);
    }
        
   
    /**
     * get encyclopedia list
     * 
     * GET /v1/article/encyclopedia_list/{list_type}
     */
    public function getEncyclopediaListAction($list_type){
        $user = $this->checkToken();       
        $validators = array(
                         'list_type' => array(
                                                   array('include', array('domain' => array(1,2,3) ) )
                                                )
                          );
        
        $this->checkParams($validators, array("list_type"=>$list_type));
        
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size', 'int', 10);
        $articleModel = new ArticleModel();
        $encyclopedia_list = $articleModel->getListByUid($user["uid"],$list_type,$page,$size);
        
        $data = array("uid"=>$user["uid"],"list_type"=>intval($list_type)) + $encyclopedia_list;
        $this->responseJson($data);
        
    }
    
    /**
     * search encyclopedia list
     * 
     * GET /v1/article/search?keywords={keywords}&page=1&size=10
     */
    public function searchAction(){
        $this->checkToken();
        $keywords = trim($this->request->getQuery('keywords',"string",""));
        $validators = array(
                            "keywords" => "required"
                          );
        
        $this->checkParams($validators, array("keywords"=>$keywords));
        
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size', 'int', 10);
        $articleModel = new ArticleModel();
        $data = $articleModel->searchListByKeywords($keywords,$page,$size);
        $this->responseJson($data);
        
    }
    
    /**
     *  get encyclopedia detail
     * 
     *  GET /v1/article/encyclopedia/{encyclopedia_id}
     */
    public function getEncyclopediaAction($encyclopedia_id){
        $user = $this->checkToken();

        $articleModel = new ArticleModel();
        $data = $articleModel->getUserEncyclopedia($user["uid"],$encyclopedia_id);
        
                       
        $this->responseJson($data, "encyclopedia");
    }
    
    /**
     * add fav num
     * 
     * POST /v1/article/encyclopedia/favnum
     */    
    public function addFavAction(){
        $user = $this->checkToken();
        
        $validators = array("encyclopedia_id"=>'required');
        $this->checkParams($validators);
        
        $articleModel = new ArticleModel();
        $articleModel->updateFav($user["uid"],  $this->_body->encyclopedia_id);
        
        $this->responseJson(0);
    }
    
    /**
     * delete fav num
     * 
     * DELETE /v1/article/encyclopedia/favnum/cancel?encyclopedia_id={encyclopedia_id}
     */ 
    public function deleteFavAction(){
        $user = $this->checkToken();
        
        $articleModel = new ArticleModel();
        $encyclopedia_id = $this->request->getQuery("encyclopedia_id");
        $articleModel->cancelFav($user["uid"],  $encyclopedia_id);
        
        $this->responseJson(0);
    }
    
     /**
     * delete fav num
     * 
     * GET /v1/article/encyclopedia/favnum/check?encyclopedia_id={encyclopedia_id}
     */ 
    public function queryFavAction(){
        $user = $this->checkToken();

        $articleModel = new ArticleModel();
        $encyclopedia_id = $this->request->getQuery('encyclopedia_id');
        $data = $articleModel->queryFav($user["uid"],  $encyclopedia_id);        
        $this->responseJson($data);
    }
    
     /**
     * collect product_id
     * 
     * POST /v1/article/encyclopedia/collect/{encyclopedia_id}
     * 
     */
    public function collectAction($encyclopedia_id){
        $user = $this->checkToken();
        
        $articleModel = new ArticleModel();
        $articleModel->collect($user["uid"],$encyclopedia_id);
        $this->responseJson(0);
    }
    
    /**
     * collect product_id
     * 
     * DELETE /v1/article/encyclopedia/collect/{encyclopedia_id}
     * 
     */
    public function collectCancelAction($encyclopedia_id){
        $user = $this->checkToken();
        
        $articleModel = new ArticleModel();
        $articleModel->collectCancel($user["uid"],$encyclopedia_id);
        $this->responseJson(0);
    }
    
    /**
     * get collection list
     * 
     * GET /v1/article/encyclopedia/collect/list?page=1&size=10
     */
    public function getCollectListAction(){
        $user = $this->checkToken();
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size', 'int', 20);
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getColltionList($user["uid"],$page,$size);
        $this->responseJson($data,"data",TRUE);
    }
    
    /**
     * collect or cancel collect
     * 
     * POST /v1/article/encyclopedia/html/collect/{encyclopedia_id}
     */
    public function htmlCollectAction($encyclopedia_id){
        $ret = array("errorCode"=>400,"errorMessage"=>"succes");
        $token = $this->request->getReqHeader('TOKEN');
        $user = $this->checkHtmlToken($token);
        
        if(isset($user["uid"]) && $user["uid"]!==1){
            $articleModel = new ArticleModel();
            $ret["errorCode"] = $articleModel->htmlCollect($user["uid"],$encyclopedia_id);                
        }
        $this->jsonReturn($ret);
    }
}
