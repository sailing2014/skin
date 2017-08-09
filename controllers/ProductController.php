<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Exception\ApiException;
use App\Exception\ServiceException;

use App\Models\Mysql\ProductdbModel;
class ProductController extends ControllerBase {   

    /**
     * get tags 
     * 
     * GET /v1/product/tags/{uid}?page=1&size=10
     */
    public function getAction($uid)
    {   
        $user = $this->checkToken();
        $productModel = new ProductModel();
        
        $page = $this->request->getQuery('page','int',1);
        $size = $this->request->getQuery('size','int',10);
        $data = $productModel->getByUid($user["uid"],$page,$size);
        $this->responseJson($data);       
        
    }
    
    /**
     *  get list
     * 
     *  Get product list according users
     * 
     *  GET /v1/product/list?list_type={list_type}&page={page}&size={size}&recommend_type={recommend_type}&safety={safety}
     */
  
    public function getListAction(){
        $user = $this->checkToken();
        $uid = $user["uid"];
        $list_type = intval($this->request->get('list_type', 'int',1));        
        $page = intval($this->request->get("page","int",1));
        $size = intval($this->request->get("size","int",10));
        $recommend_type = intval($this->request->get("recommend_type","int",0));
        $safety = intval($this->request->get("safety","int",0));
        $validators = array( 
                            'list_type'=>array(
                                                array('required'),
                                                array( 'include',array( 'domain'=>array(1,2,3) )
                                                     )
                                              )
                         );
        
        $this->checkParams($validators,array("list_type"=>$list_type));
       
        $productModel = new ProductModel();
        $ret = $productModel->getProductListByUid($uid,$recommend_type,$safety,$page,$size);
        
        $data = array("uid"=>$uid,'list_type'=>$list_type,"recommend_type"=>$recommend_type,"safety"=>$safety) + $ret; 
        
        $this->responseJson($data);            
    }
    
    /**
     * search by keywords
     * 
     * /v1/product/search?keywords={keywords}&page=1&size=10
     */
    public function searchAction(){
        $user = $this->checkToken();
        
        $keywords = trim($this->request->getQuery('keywords','string',""));        
        $validators = array( 'keywords' => 'required');        
        $this->checkParams($validators,array("keywords"=>$keywords));        
       
        $page = intval($this->request->get("page","int",1));
        $size = intval($this->request->get('size',"int",10));
        $recommend_type = intval($this->request->get("recommend_type","int",0));
        $safety = intval($this->request->get("safety","int",0));
        $productModel = new ProductModel();
        $ret = $productModel->getSearchListByUid($user["uid"],$keywords,$recommend_type,$safety,$page,$size);
        $data = array("keywords"=>$keywords,"recommend_type"=>$recommend_type,"safety"=>$safety) + $ret;
        $this->responseJson($data);      
    }


    /**
     * get filters
     * 
     * GET /v1/product/filters/{fileter_type}
     */

    public function filterTypeAction($filter_type){
       
        $filter_type = $filter_type? intval($filter_type):1;
//         1-->types,2-->usage,3-->brand,4-->price
        $data["filter_type"] = $filter_type;
        $validators =array(
                         'filter_type' => array(
                                                   array('include', array('domain' => array(1,2,3,4) ) )
                                                )
                          );
        $this->checkParams($validators,array("filter_type"=>$filter_type));
        
       
        $productModel = new ProductModel();
        $data = $productModel->getFilterListByType($filter_type);
        $this->responseJson($data);  
        
    }
    
 

    /**
     * get filter  list
     * 
     * GET /v1/product/filter_list?doc_id={type_id},{usage_id},{brand_id},{price_id}
     */
    
    public function filterAction(){
        $user = $this->checkToken();
        
        $doc_id = $this->request->get('doc_id');
        $page = intval($this->request->get("page","int",1));
        $size = intval($this->request->get("size","int",10));
        $recommend_type = intval($this->request->get("recommend_type","int",0));
        $safety = intval($this->request->get("safety","int",0));
        
        $id_arr = explode(',', $doc_id);
        foreach($id_arr as $id){
            if(strpos($id, "type")){
                $param["type_id"] = $id;
            }else if(strpos($id,"usage")){
                $param["usage_id"] = $id;
            }else if(strpos($id, "brand")){
                $param["brand_id"] = $id;
            }else if(strpos($id, "price")){
                $param["price_id"] = $id;
            }
        }
        
         if( $param ){             
            $param["recommend_type"] = $recommend_type;
            $param["safety"] = $safety;
            $productModel = new ProductModel();
            $ret = $productModel->getFilterListByFilter($user["uid"],$param,$recommend_type,$safety,$page,$size);
            $data = $param +  $ret ; 
            $this->responseJson($data);
         }else{
             throw new ApiException(400001,400);
         }
    }
    
    /**
     * get product detail
     * 
     * /v1/product/{product_id} 
     * 
     */
    public function getDetailAction($product_id){
        $user = $this->checkToken();
        $productModel = new ProductModel();
        $data = $productModel->getProduct($user["uid"],$product_id);
         $this->responseJson($data);
    }
    
    /**
     * get product components
     * 
     * GET /v1/product/components/{product_id}
     */
    public function componentsAction($product_id){
       
        $productModel = new ProductModel();
        $data = $productModel->getProductComponents($product_id);
        $this->responseJson($data);
    }
    
    /**
     * get component detail
     * 
     * GET /v1/product/component_detail/{component_id}
     */
    public function componentDetailAction($component_id){
        $data = array();
        $productdbModel = new ProductdbModel();
        $id = str_replace("skin:product:component:", "", $component_id);
        $ret = $productdbModel->getComponentById($id);        
       if(!$ret){
           throw new ApiException(427111,200);
       }
       foreach($ret as $val){
           $data["component_id"] = "skin:product:component:".$val["id"];
           $data["title"] = stripslashes($val["title"]);
           $data["EN_title"] = stripslashes($val["EN_title"]);
           $data["alias"] = stripslashes($val["alias"]);
           $data["description"] = stripslashes($val["description"]);
           $data["usage"] = $val["usage"];
           $data["safety"] = intval($val["safety"]);
           $data["acne_risk"] = intval($val["acne_risk"]);          
           $data["sensitization"] = intval($val["sensitization"]);
           $data["active"] = intval($val["active"]);
           $data["time"] = intval($val["time"]);           
           $data["id"] = intval($val["id"]);
           $data["doc_type"] = "skin:component";
       }
       $this->responseJson($data,"component");
    }
     
    /**
     * update product favnum 
     * 
     * PUT /v1/product/favnum
     */
    public function favnumAction(){        
        $validators = array(    "product_id"=>"required",
                                "type"=>array(
                                                array(  "required"  ),
                                                array(  "include",
                                                        array("domain"=>array(-1,1))
                                                    )
                                             )
                            );
        $this->checkParams($validators);
        
        $user = $this->checkToken();
        
        $product_id = $this->_body->product_id;
        $type = $this->_body->type;
        
        $productModel = new ProductModel();
        $productModel->updateFavnum($user["uid"],$product_id,$type);
        $this->responseJson(0);
    }
    
    /**
     * cancel product favnum
     * 
     * DELETE /v1/product/favnum
     */
    public function cancelFavnumAction(){    
        $product_id = $this->request->get('product_id', 'string', '');
        $type = $this->request->get('type','int','');
        $validators = array(    "product_id"=>"required",
                                "type"=>array(
                                                array( "required" ),
                                                array( "include",
                                                        array( "domain"=>array(-1,1) ) 
                                                    )
                                             )
                            );
        $this->checkParams($validators,array("product_id"=>$product_id,"type"=>$type));
        
        $user = $this->checkToken();
        
        $productModel = new ProductModel();
        $productModel->cancelFavnum($user["uid"],$product_id,$type);
        $this->responseJson(0);
    }
    
    /**
     * get user  product favnum,tearnum
     * 
     * GET /v1/product/favnum/{product_id}
     */
    public function favnumCheckAction($product_id){                   
        $user = $this->checkToken();
        
        $productModel = new ProductModel();
        $data = $productModel->getFavnumCheck($user["uid"],$product_id);
        $this->responseJson($data);
    }
    
    /**
     * collect product_id
     * 
     * POST /v1/product/collect/{product_id}
     * 
     */
    public function collectAction($product_id){
        $user = $this->checkToken();
        
        $productModel = new ProductModel();
        $productModel->collect($user["uid"],$product_id);
        $this->responseJson(0);
    }
    
    /**
     * collect product_id
     * 
     * DELETE /v1/product/collect/{product_id}
     * 
     */
    public function collectCancelAction($product_id){
        $user = $this->checkToken();
        
        $productModel = new ProductModel();
        $productModel->collectCancel($user["uid"],$product_id);
        $this->responseJson(0);
    }
    
    /**
     * get collection list
     * 
     * GET /v1/product/collect/list?page=1&size=20
     */
    public function getCollectListAction(){
        $user = $this->checkToken();
        $page = intval($this->request->get("page","int",1));
        $size = intval($this->request->get("size","int",10));
        
        $productModel = new ProductModel();
        $data = $productModel->getColltionList($user["uid"],$page,$size);
        $this->responseJson($data,"data",true);
    }
    
    /**
     * collect or cancel collect
     * 
     * POST /v1/product/html/collect
     */
    public function htmlCollectAction($product_id){
        $ret = array("errorCode"=>400,"errorMessage"=>"succes");
        $token = $this->request->getReqHeader('TOKEN');
        $user = $this->checkHtmlToken($token);
        
        if(isset($user["uid"]) && $user["uid"]!==1){
            $productModel = new ProductModel();
            $ret["errorCode"] = $productModel->htmlCollect($user["uid"],$product_id);                
        }
        $this->jsonReturn($ret);
    }    

}
