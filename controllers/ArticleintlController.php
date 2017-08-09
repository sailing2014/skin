<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Exception\ServiceException;

class ArticleintlController extends ControllerBase {
    
    public function beforeExecuteRoute($dispatcher) {
        parent::beforeExecuteRoute($dispatcher);
         
        $this->checkApiToken();        
    }


    /**
     * get tags 
     * 
     * GET /v1/article/tags/intl
     */
    public function getTagsAction(){
        $articleModel = new ArticleModel();
        $data = $articleModel->getTags();
        $this->responseJson($data,"tags");
    }
    
    /**
     * add tag
     * 
     * POST /v1/article/tags/intl
     */
    public function addTagAction(){    
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $articleModel = new ArticleModel();
        $data = $articleModel->addTag($this->_body->title);
        $this->responseJson($data,"tag_id");
    }
    
    /**
     * 
     * get tag by tag_id
     * 
     * @param string $tag_id
     */
    public function getTagAction($tag_id){
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getTagById($tag_id);
        $this->responseJson($data);
    }
    
    
    /**
     * update tag
     * 
     * PUT /v1/article/tags/intl/{$tag_id}
     */
    public function updateTagAction($tag_id){
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $articleModel = new ArticleModel();
        $articleModel->updateTag($tag_id,$this->_body->title);
        $this->responseJson(0);
    }
    
    /**
     * delete tag
     * 
     * DELETE /v1/article/tags/intl/{$tag_id}
     */
    public function deleteTagAction($tag_id){
        $articleModel = new ArticleModel();
        try{
            $articleModel->deleteTag($tag_id);
            $this->responseJson(0);       
        }catch( ServiceException $se){
            $this->throwException($se, 7115,420000);
        }
    }
    
    public function addComprenhensiveAction(){
        $validators = array("type"=>"required","title"=>"required");
        $this->checkParams($validators);
        
         $articleModel = new ArticleModel();
         $data = $articleModel->addComprenhensive($this->_body->type,  $this->_body->title);
         $this->responseJson($data);
    }
    
    public function getComprehensiveAction(){
         $articleModel = new ArticleModel();
         $data = $articleModel->getComprenhensive();
         $this->responseJson($data);
    }

    /**
     * add character daily care and solutions 
     * 
     * POST /v1/article/character/intl/{type}
     * 
     * @param string $skin_comprehensive_type
    */
    public function addCharacterAction($skin_comprehensive_type){
        $validators = array("title"=>"required","description"=>"required","detail"=>"required","daily"=>"required","solutions"=>"required","code"=>"required");
        $this->checkParams($validators);
                
        $articleModel = new ArticleModel();
        $tip_id = $articleModel->addCharacter($skin_comprehensive_type,$this->_body->title,  
                                              $this->_body->description,  $this->_body->detail,  
                                              $this->_body->daily,  $this->_body->solutions,  
                                              $this->_body->code);
        $this->responseJson($tip_id, 'tip_id');
    }
    
    /**
     * get character by skin type
     * 
     * @param string $skin_comprehensive_type
     */
    public function getCharacterAction($skin_comprehensive_type){
                       
        $articleModel = new ArticleModel();
        $data = $articleModel->getCharacter($skin_comprehensive_type);
        $this->responseJson($data, 'character');
    }
    
    /**
     * get character by skin type
     * 
     * @param int $code
     */
    public function getCharacterbyCodeAction($code){
                       
        $articleModel = new ArticleModel();
        try{
            $doc_id= "skin:tips:code:".intval($code);
        $data = $articleModel->get($doc_id);
        }catch(\App\Exception\ServiceException $se){
            throw new \App\Exception\ApiException(400,400);
        }
        $this->responseJson($data, 'character');
    }
    
    /**
     * delete character by skin type
     * 
     * @param int $code
     */
    public function delCharacterbyCodeAction($code){
                       
        $articleModel = new ArticleModel();
        $doc_id= "skin:tips:code:".intval($code);
        try{            
            $articleModel->delete($doc_id);
        }catch(\App\Exception\ServiceException $se){
            throw new \App\Exception\ApiException(400,400);
        }
        $this->responseJson(0);
    }
    
    
    public function addEncyclopediaAction(){
         $validators = array("title"=>"required");
         $this->checkParams($validators);
         
         $title = $this->_body->title;
         $content = $this->_body->content; 
         $tags = isset($this->_body->tags)?$this->_body->tags:"";
         $from_nickname = isset($this->_body->from_nickname)?$this->_body->from_nickname:"";
         $from_image = isset($this->_body->from_image)?$this->_body->from_image:"";
         $image = isset($this->_body->img)?$this->_body->img:"";
         $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";  
         $pageView = isset($this->_body->pageView)?intval($this->_body->pageView):0;  
         $favnum = isset($this->_body->favnum)?intval($this->_body->favnum):0;  
         $top = isset($this->_body->top)?intval($this->_body->top):0;  
                 
         $articleModel = new ArticleModel();
         $encyclopedia_id = $articleModel->addEncyclopedia($title,$tags,$image,$thumb,$content,$from_nickname,$from_image,$pageView,$favnum,$top);
         $this->responseJson($encyclopedia_id,"encyclopedia_id");
    }
    
    public function setEncyclopediaAction($encyclopedia_id){
         
         $title = $this->_body->title;
         $content = $this->_body->content; 
         $tags = isset($this->_body->tags)?$this->_body->tags:"";
         $from_nickname = isset($this->_body->from_nickname)?$this->_body->from_nickname:"";
         $from_image = isset($this->_body->from_image)?$this->_body->from_image:"";
         $image = isset($this->_body->img)?$this->_body->img:"";
         $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";  
         $pageView = isset($this->_body->pageView)?intval($this->_body->pageView):0;  
         $favnum = isset($this->_body->favnum)?intval($this->_body->favnum):0;  
         $top = isset($this->_body->top)?intval($this->_body->top):0;         
         
         $articleModel = new ArticleModel();
         $articleModel->setEncyclopedia($encyclopedia_id,$title,$tags,$image,$thumb,$content,$from_nickname,$from_image,$pageView,$favnum,$top);
         $this->responseJson(0);
    }
    
    public function setTopAction($encyclopedia_id){
         $top = isset($this->_body->top)?$this->_body->top:0;         
         $articleModel = new ArticleModel();
         $articleModel->setTop($encyclopedia_id,$top);
         $this->responseJson(0);
    } 
   
    
    public function deleteEncyclopediaAction($encyclopedia_id){
        $articleModel = new ArticleModel();
        $articleModel->deleteEncyclopedia($encyclopedia_id);
        $this->responseJson(0);
    }
    
    public function getEncyclopediaAction($encyclopedia_id){
        $articleModel = new ArticleModel();
        $data = $articleModel->getEncyclopediaById($encyclopedia_id);
        $this->responseJson($data);        
    }
    
    public function getEncyclopediaListAction(){
        $articleModel = new ArticleModel();
        $page = $this->request->get('page', 'int', 1);
        $size = $this->request->get('size','int',10);
        $data = $articleModel->getEncyclopediaList($page,$size);
        $this->responseJson($data); 
    }
    
    /**
     * add todo article
     * POST /v1/article/todo/intl
     */   
    public function addTodoAction(){        
         $validators = array("title"=>"required");
         $this->checkParams($validators);
         
         $title = $this->_body->title;
         $description = isset($this->_body->description)?$this->_body->description:"";
         $content = isset($this->_body->content)?$this->_body->content:"";
         $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
         $image = isset($this->_body->image)?$this->_body->image:"";
                 
         $articleModel = new ArticleModel();
         $todo_id = $articleModel->addTodo($title,$description,$content,$thumb,$image);
         $this->responseJson($todo_id,"doc_id");
    }
    
    /**
     * delete todo by docid
     * DELETE /v1/article/todo/intl/{doc_id}
     * @param string $doc_id
     */
    public function deleteTodoAction($doc_id){
         $articleModel = new ArticleModel();
         $articleModel->deleteTodo($doc_id);
         $this->responseJson(0);
    }
    
    /**
     * update todo by docid
     * PUT /v1/article/todo/intl/{doc_id}
     * @param string $doc_id
     */
    public function updateTodoAction($doc_id){         
         $validators = array("title"=>"required");
         $this->checkParams($validators);
         
         $title = $this->_body->title;
         $description = isset($this->_body->description)?$this->_body->description:"";
         $content = isset($this->_body->content)?$this->_body->content:"";
         $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
         $image = isset($this->_body->image)?$this->_body->image:"";
                 
         $articleModel = new ArticleModel();
         $articleModel->addTodo($title,$description,$content,$thumb,$image,$doc_id);
         $this->responseJson(0);
    }
    
    /**
     * get todo article by docid
     * 
     * GET　/v1/article/todo/intl/{doc_id}
     * @param string $doc_id
     */
    public function getTodoAction($doc_id){
        $articleModel = new ArticleModel();
        $data = $articleModel->getTodo($doc_id);
        $this->responseJson($data);
    }
    
     /**
     * get todo article list 
     * 
     * GET　/v1/article/todo/intl?page={page}&size={size}
     */
    public function getTodoListAction(){
        
        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',10);
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getTodoList($page,$size);
        $this->responseJson($data);
    }
    
    
    // ~~~~~~~~~~~~~~~~~~~push华丽妖娆的分割线----------------
    //--------------------------------------------------------
    /**
     * 
     * add push article
     * 
     * POST /v1/article/push/intl
     * 
     */   
    public function addPushAction(){        
         $validators = array("title"=>"required");
         $this->checkParams($validators);
         
         $title = $this->_body->title;
         $description = isset($this->_body->description)?$this->_body->description:"";
         $content = isset($this->_body->content)?$this->_body->content:"";
         $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
         $image = isset($this->_body->image)?$this->_body->image:"";
                 
         $articleModel = new ArticleModel();
         $push_id = $articleModel->addPush($title,$description,$content,$thumb,$image);
         $this->responseJson($push_id,"doc_id");
    }
    
    /**
     * delete push article by docid
     * DELETE /v1/article/push/intl/{doc_id}
     * @param string $doc_id
     */
    public function deletePushAction($doc_id){
         $articleModel = new ArticleModel();
         $articleModel->deletePush($doc_id);
         $this->responseJson(0);
    }
    
    /**
     * update push article by docid
     * PUT /v1/article/push/intl/{doc_id}
     * @param string $doc_id
     */
    public function updatePushAction($doc_id){         
         $validators = array("title"=>"required");
         $this->checkParams($validators);
         
         $title = $this->_body->title;
         $description = isset($this->_body->description)?$this->_body->description:"";
         $content = isset($this->_body->content)?$this->_body->content:"";
         $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
         $image = isset($this->_body->image)?$this->_body->image:"";
                 
         $articleModel = new ArticleModel();
         $articleModel->addPush($title,$description,$content,$thumb,$image,$doc_id);
         $this->responseJson(0);
    }
    
    /**
     * get push article by docid
     * 
     * GET　/v1/article/push/intl/{doc_id}
     * @param string $doc_id
     */
    public function getPushAction($doc_id){
        $articleModel = new ArticleModel();
        $data = $articleModel->getPush($doc_id);
        $this->responseJson($data);
    }
    
     /**
     * get push article list 
     * 
     * GET　/v1/article/push/intl?page={page}&size={size}
     */
    public function getPushListAction(){
        
        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',10);
        
        $articleModel = new ArticleModel();
        $data = $articleModel->getPushList($page,$size);
        $this->responseJson($data);
    }
    
    /**
     * get push article list
     *
     * DELETE　/v1/article/bbc_raw/intl?page={page}&size={size}
     */
    public function deleteRawListAction(){

        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',10);

        $articleModel = new ArticleModel();
        $data = $articleModel->deleteRawList(1,$page,$size);
        $this->responseJson($data);
    }
}
