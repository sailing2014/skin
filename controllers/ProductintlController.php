<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Exception\ServiceException;
use App\Exception\ApiException;

class ProductintlController extends ControllerBase {    
   /**
     * add  product tags
     * 
     * POST /v1/product/tags/intl
     */
    public function addTagsAction()
    {
        $validators =array(
            'title' => "required"
        );
        
        $this->checkParams($validators);       
        $this->checkApiToken();
        
        $productModel = new ProductModel();        
        $doc_id = $productModel->addTag($this->_body->title);
        $this->responseJson($doc_id,"tag_id");
    }   
    
    /**
     * update  product tags
     * 
     * PUT /v1/product/tags/intl/{tag_id}
     */
    public function updateTagsAction($tag_id)
    {
        $validators =array(
            'title' => "required"
        );
        
        $this->checkParams($validators);       
        $this->checkApiToken();
        
        $productModel = new ProductModel();
        $productModel->updateTag($tag_id,$this->_body->title);
        $this->responseJson(0);
                
    }   
    
    /**
     * add  product tags
     * 
     * GET /v1/product/tags/intl
     */
    public function getTagsAction()
    {       
        $this->checkApiToken();
        
        $productModel = new ProductModel();        
        $data = $productModel->getTagList();
        $this->responseJson($data,"tags");   
    }  
    
     /**
     * add  product 
     * 
     * POST /v1/product/intl
     */
    public function addProductAction()
    {         
        $this->checkApiToken();
        
        $productModel = new ProductModel();
        
        $id = isset($this->_body->id)?intval($this->_body->id):$productModel->getIncreseId();
        $title = isset($this->_body->title)?addslashes($this->_body->title):"";
        $alias = isset($this->_body->alias)?addslashes($this->_body->alias):"";
        $type_id = isset($this->_body->type_id)?$this->_body->type_id:"";
        $brand_id = isset($this->_body->brand_id)?$this->_body->brand_id:"";
        $usage_id = isset($this->_body->usage_id)?$this->_body->usage_id:"";      
        $price = isset($this->_body->price)?$this->_body->price:"";
        $image = isset($this->_body->image)?$this->_body->image:"";
        $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
        $shape = isset($this->_body->shape)?$this->_body->shape:"";
        $description = isset($this->_body->description)?addslashes($this->_body->description):"";
        $sys_description = isset($this->_body->sys_description)?addslashes($this->_body->sys_description):"";
        $component_id = isset($this->_body->component_id)?$this->_body->component_id:"";
        $made_in =  isset($this->_body->made_in)?$this->_body->made_in:"";
        $shelflife = isset($this->_body->shelflife)?$this->_body->shelflife:"";
        $scale = isset($this->_body->scale)?$this->_body->scale:"";
        $unfit = isset($this->_body->unfit)?$this->_body->unfit:"";
        $url = isset($this->_body->url)?$this->_body->url:"";     
        $top = isset($this->_body->top)?$this->_body->top:0;

        $data = array(  "id"=>$id,"title"=>$title,"alias"=>$alias,
                        "type_id"=>$type_id,"brand_id"=>$brand_id,
                       "usage_id"=>$usage_id,"price"=>$price,
                       "image" => $image,"thumb" => $thumb,"shape"=>$shape,
                        "description"=>$description,"sys_description"=>$sys_description,
                        "component_id"=>$component_id,"made_in"=>$made_in,
                        "shelflife"=>$shelflife,"scale"=>$scale,
                        "unfit"=>$unfit,"url"=>$url,
                        "top"=>$top
                    );
        
        $doc_id = $productModel->addProduct($data);
        $this->responseJson(array("product_id"=>$doc_id));        
    } 
  
   
    
    
    /**
     * update  product 
     * 
     * PUT /v1/product/intl/{product_id}
     */
    public function updateProductAction($product_id)
    {         
        $this->checkApiToken(); 
      
        $productModel = new ProductModel();
                  
        $title = isset($this->_body->title)?addslashes($this->_body->title):"";
        $alias = isset($this->_body->alias)?addslashes($this->_body->alias):"";
        $type_id = isset($this->_body->type_id)?$this->_body->type_id:"";
        $brand_id = isset($this->_body->brand_id)?$this->_body->brand_id:"";
        $usage_id = isset($this->_body->usage_id)?$this->_body->usage_id:"";      
        $price = isset($this->_body->price)?$this->_body->price:"";
        $image = isset($this->_body->image)?$this->_body->image:"";
        $thumb = isset($this->_body->thumb)?$this->_body->thumb:"";
        $shape = isset($this->_body->shape)?$this->_body->shape:"";
        $description = isset($this->_body->description)?addslashes($this->_body->description):"";
        $sys_description = isset($this->_body->sys_description)?addslashes($this->_body->sys_description):"";
        $component_id = isset($this->_body->component_id)?$this->_body->component_id:"";
        $made_in =  isset($this->_body->made_in)?$this->_body->made_in:"";
        $shelflife = isset($this->_body->shelflife)?$this->_body->shelflife:"";
        $scale = isset($this->_body->scale)?$this->_body->scale:"";
        $unfit = isset($this->_body->unfit)?$this->_body->unfit:"";
        $url = isset($this->_body->url)?$this->_body->url:"";
        $top = isset($this->_body->top)?$this->_body->top:0;

        $data = array(  "title"=>$title,"alias"=>$alias,
                        "type_id"=>$type_id,"brand_id"=>$brand_id,
                       "usage_id"=>$usage_id,"price"=>$price,
                       "image" => $image,"thumb" => $thumb,"shape"=>$shape,
                        "description"=>$description,"sys_description"=>$sys_description,
                        "component_id"=>$component_id,"made_in"=>$made_in,
                        "shelflife"=>$shelflife,"scale"=>$scale,
                        "unfit"=>$unfit,"url"=>$url,
                        "top"=>$top
                    );
        $productModel->updateProduct($product_id,$data);
        $this->responseJson(0);
              
                
    }   
    
    /**
     * set top
     * 
     * PUT /v1/product/top/intl/{product_id}
     * @param string $product_id
     */
    public function setTopAction($product_id){
        $top = isset($this->_body->top)?$this->_body->top:0;
        
        $productModel = new ProductModel();         
        $productModel->setTop($product_id,$top);
        $this->responseJson(0);
    }
    
    /**
     * get product detail
     * 
     * GET /v1/product/intl/{product_id}
     * 
     * @param string $product_id
     */
    public function getProductAction($product_id){ 
        $this->checkApiToken();
        
         $productModel = new ProductModel();         
         $data = $productModel->getIntlProduct($product_id);        
         $this->responseJson($data,"product");
    }
    
       /**
     * get product detail
     * 
     * GET /v1/product/components/intl/{product_id}
     * 
     * @param string $product_id
     */
    public function getProductCompAction($product_id){ 
        $this->checkApiToken();
        
         $productModel = new ProductModel();         
         $data = $productModel->getIntlProductComp($product_id); 
         $this->responseJson($data,"data");
    }
    
     /**
     * get product detail
     * 
     * DELETE /v1/product/intl/{product_id1,product_id2}
     * 
     * @param string $product_id separated by ','
     */
    public function deleteProductAction($product_id){ 
        $this->checkApiToken();
        
         $productModel = new ProductModel();
         $productModel->deleteProduct($product_id);
         $this->responseJson(0);
    }
    
    /**
     * get  product list
     * 
     * GET /v1/product/intl?page=1&size=10
     */
    public function productListAction()
    {         
        $this->checkApiToken();       
      
        $page = $this->request->getQuery('page','int',1);
        $size = $this->request->getQuery('size','int',10);
        $productModel = new ProductModel();              
        $data = $productModel->getProductList($page,$size);       
        $this->responseJson($data);                     
    }   
    
    /**
     * get usage id list in products
     */
    public function getUsageIdListAction(){
        $this->checkApiToken();
        
        $productModel = new ProductModel();
        $data = $productModel->getUsageList();
        $this->responseJson($data);
    }

  
    
    /**
     * add filter
     * 
     * POST  /v1/product/filters/intl
     */
    public function addFilterTypeAction(){
        $this->checkApiToken();
        $validators =array(
                         'filter_type' => array(
                                                   array('include', array('domain' => array(1,2,3,4) ) )
                                                )
                          );               
        $this->checkParams($validators);
        
        if($this->_body->filter_type == 3){
            $this->checkParams(array("EN_title"=>"required"));            
        }
        
        $productModel = new ProductModel();
        
        $filter_type = $this->_body->filter_type;     
        $title = isset($this->_body->title)?$this->_body->title:"";   
        $section_array = array("A","B","C","D","E","F","G","H","I","J","K","L");
        $key = array_rand($section_array);        
        $section = isset($this->_body->section)?$this->_body->section:$section_array[$key];
        $image = isset($this->_body->image)?$this->_body->image:"";
        $EN_title = isset($this->_body->EN_title)?$this->_body->EN_title:"";
        $order = isset($this->_body->order)?$this->_body->order:0;
        $doc_id = $productModel->addFilter($filter_type,$title,$EN_title,$section,$image,$order);
        $this->responseJson($doc_id);
       
        
    }
    
    /**
     * GET filter type
     * @param string $id
     */
    public function getFilterTypeAction($id){
        $this->checkApiToken();
        
        $productModel = new ProductModel();
        $data = $productModel->getIntlFilterType($id);
        $this->responseJson($data);
    }
    
    /**
     * DELETE delete filter type
     * @param string $id
     */
    public function deleteFilterTypeAction($id){
        $this->checkApiToken();
        
        $productModel = new ProductModel();
        $productModel->deleteFilter($id);
        $this->responseJson(0);
    }
    
    /**
     * update filter
     * 
     * PUT  /v1/product/filters/intl
     */
    public function updateFilterTypeAction(){           
        $this->checkApiToken();
        $validators =array(
                         'filter_type' => array(
                                                   array('include', array('domain' => array(1,2,3,4) ) )
                                                )
                          );
        $this->checkParams($validators);
        
        $filter_type = $this->_body->filter_type;
        switch ($filter_type) {
            case 1:
                $this->checkParams(array("type_id"=>"required"));
                $doc_id = $this->_body->type_id;
                break;
            case 2:
                $this->checkParams(array("usage_id"=>"required"));
                $doc_id = $this->_body->usage_id;
                break;
            case 3:
                $this->checkParams(array("brand_id"=>"required"));
                $doc_id = $this->_body->brand_id;
                break;
            case 4:
                $this->checkParams(array("price_id"=>"required"));
                $doc_id = $this->_body->price_id;
                break;
            default:
                break;
        }
        
        $productModel = new ProductModel();                           
        $title = isset($this->_body->title)?trim($this->_body->title):"";   
        $section_array = array("A","B","C","D","E","F","G","H","I","J","K","L");
        $key = array_rand($section_array);        
        $section = isset($this->_body->section)?$this->_body->section:$section_array[$key];
        $EN_title = isset($this->_body->EN_title)?$this->_body->EN_title:"";  
        $image = isset($this->_body->image)?$this->_body->image:"";  
        $order = isset($this->_body->order)?$this->_body->order:0;
        $productModel->addFilter($filter_type,$title,$EN_title,$section,$image,$order,$doc_id);
        $this->responseJson(0);       
        
    }
      
    /**
     * get filters
     * 
     * GET /v1/product/filters/intl/{fileter_type}
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
        $data = $productModel->getIntlFilterListByType($filter_type);
        $this->responseJson($data);
    }
        
    /**
     * 
     * get component usage list
     * GET /v1/product/component/usage/intl
     */
    public function getCompUsageAction(){
        $this->checkApiToken();
        $productModel = new ProductModel();
        $data = $productModel->getComponentUsage();
        $this->responseJson($data);
    }
    
     /**
     * 
     * add component usage 
     * POST /v1/product/component/usage/intl
     */
    public function addCompUsageAction(){
        $this->checkApiToken();
        
        $validators = array("title"=>"required");
        $this->checkParams($validators);
               
        $title = $this->_body->title;
        
        $productModel = new ProductModel();
        $data = $productModel->addComponentUsage($title);
        $this->responseJson($data,"usage_id");
    }
    
    /**
     * 
     * get component usage list
     * GET /v1/product/component/usage/intl/{usage_id}
     */
    public function getCompUsageByIdAction($usage_id){
        $this->checkApiToken();
        $productModel = new ProductModel();
        $data = $productModel->getComponentUsageById($usage_id);
        $this->responseJson($data);
    }
    /**
     * 
     * update component usage list
     * PUT /v1/product/component/usage/intl/{usage_id}
     */
    public function updateCompUsageAction($usage_id){
        $this->checkApiToken();
        $validators = array("title"=>"required");
        $this->checkParams($validators);
        
        $productModel = new ProductModel();
        $title = $this->_body->title;
        $productModel->updateComponentUsage($usage_id,$title);
        $this->responseJson(0);
    }
    
    /**
     * 
     * delete component usage list
     * DELETE /v1/product/component/usage/intl/{usage_id}
     */
    public function deleteCompUsageAction($usage_id){
         $this->checkApiToken();
        $productModel = new ProductModel();
        $productModel->deleteComponentUsage($usage_id);
        $this->responseJson(0);
    }
    
    /**
     * get filters
     * 
     * DELETE /v1/product/filters/intl/{filter_type}
     */
    public function deleteFilterAction($filter_type){
        $this->checkApiToken();
        $productModel = new ProductModel();
        $productModel->deleteFilterListByType($filter_type);
        $this->responseJson(0);
    }
   
    
  
      
    
    /**
     * get component detail
     * 
     * GET /v1/product/component/intl/{component_id}
     */
    public function componentDetailAction($component_id){
        $this->checkApiToken();
        
        $productModel = new ProductModel();
        $data = $productModel->getFormatComponentList($component_id);
        if(!$data){
            throw new ApiException(427111,200);
        }               
        $this->responseJson($data[0],"component");
    }
    

        
    /**
     * add component
     * 
     * POST /v1/product/component/intl
     */
    public function addComponentAction(){
        $this->checkApiToken();        
        $validators = array("title"=>"required");
        $this->checkParams($validators);
    
        $productModel = new ProductModel();
        $id = isset($this->_body->id)?intval($this->_body->id):$productModel->getIncreseId();
        $title = $this->_body->title;
        $EN_title = isset($this->_body->EN_title)?$this->_body->EN_title:"";
        $description = isset($this->_body->description)?$this->_body->description:"";
        $alias = isset($this->_body->alias)?$this->_body->alias:"";
        $usage = isset($this->_body->usage)?$this->_body->usage:"";
        $acne_risk = isset($this->_body->acne_risk)?intval($this->_body->acne_risk):0;
        $sensitization = isset($this->_body->sensitization)?intval($this->_body->sensitization):0;
        $safety = isset($this->_body->safety)?intval($this->_body->safety):0;
        $active = isset($this->_body->active)?intval($this->_body->active):0;
        $component_id = $productModel->addComponent($id,$title, $EN_title  ,  $description,
                                                       $alias,$usage,
                                                       $acne_risk,$sensitization,$safety,$active);
        $this->responseJson($component_id,"component_id");        
    }
    
    /**
     * add component
     * 
     * POST /v1/product/components/intl
     */
    public function addMultipleComponentAction(){
        $this->checkApiToken();
        $validators1 = array("data"=>"required");
        $this->checkParams($validators1);
        
        $validators2 = array("id"=>"required","title"=>"required");
        foreach($this->_body->data as $val){
            $this->checkParams($validators2,$val);
        }
        
        $productModel = new ProductModel();
        $ids = $productModel->addMultipleComponent($this->_body->data);
        $this->responseJson($ids);
    }
    
    /**
     * update component
     * 
     * PUT /v1/product/component/intl/{component_id}
     */
    public function updateComponentAction($component_id){
        $this->checkApiToken();
        
        $validators = array("id"=>"required","title"=>"required");
        $this->checkParams($validators);
       
        $productModel = new ProductModel();
        $id = intval($this->_body->id);
        $title = $this->_body->title;
        $EN_title = isset($this->_body->EN_title)?$this->_body->EN_title:"";
        $description = isset($this->_body->description)?$this->_body->description:"";        
        $alias = isset($this->_body->alias)?$this->_body->alias:"";
        $usage = isset($this->_body->usage)?$this->_body->usage:"";
        $acne_risk = isset($this->_body->acne_risk)?$this->_body->acne_risk:0;
        $sensitization = isset($this->_body->sensitization)?$this->_body->sensitization:0;
        $safety = isset($this->_body->safety)?$this->_body->safety:0;
        $active = isset($this->_body->active)?$this->_body->active:0;
        
        $productModel->updateComponent(  $id, $title, $EN_title  ,  $description,
                                        $alias,$usage,
                                        $acne_risk,$sensitization, $safety,$active,
                                        $component_id);
        $this->responseJson(0);
       
    }
    
    /**
     * 
     * @param string $component_id separated by ','
     */
    public function deleteComponentAction($component_id){
        $this->checkApiToken();
    
        $productModel = new ProductModel();
        $productModel->deleteComponent($component_id);
        $this->responseJson(0);
    }
    


    /**
     * query component list
     * 
     * GET /v1/product/component_list/intl?page=1&size=10
     */
    public function componentListAction(){
        $this->checkApiToken();
    
        $page = intval($this->request->get('page','int',1));
        $size = intval($this->request->get('size','int',10));
        $productModel = new ProductModel();
        $ret = $productModel->getIntlComponentList($page,$size);
        $this->responseJson($ret);       
    }   
     
    /**
     * query component list
     * 
     * GET /v1/product/usages/intl?page=1&size=10
     */
    public function usageListAction(){
        $this->checkApiToken();
    
        $page = intval($this->request->get('page','int',1));
        $size = intval($this->request->get('size','int',10));
        $productModel = new ProductModel();
        $ret = $productModel->getIntlUsageList($page,$size);
        $this->responseJson($ret);      
    }

    /**
     *  search component id by component title or EN_title
     * GET /v1/product/component_list/search/intl?keywords={keywords}&page=1&size=10
     */
    public function searchComponentAction(){
        $this->checkApiToken();
        $keywords = $this->request->get('keywords');
        $validators = array("keywords"=>"required");
        $this->checkParams($validators,array("keywords"=>$keywords));
        
        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',10);
        
        $productModel = new ProductModel();
        $ret = $productModel->getIntlSerachComponentList($keywords,$page,$size);
        $this->responseJson($ret);       
    } 
    
     /**
     *  search product by product title 
     * GET /v1/product/search/intl?keywords={keywords}&page=1&size=10
     */
    public function searchProductAction(){
        $this->checkApiToken();
        $keywords = $this->request->get('keywords');
        $validators = array("keywords"=>"required");
        $this->checkParams($validators,array("keywords"=>$keywords));
        
        $page = $this->request->get('page','int',1);
        $size = $this->request->get('size','int',10);
        
        $productModel = new ProductModel();
        $ret = $productModel->getIntlSerachProductList($keywords,$page,$size);
        $this->responseJson($ret);       
    } 
}
