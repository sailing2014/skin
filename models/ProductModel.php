<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Models\Mysql\ProductdbModel;
use App\Exception\ServiceException;

/**
 *  product model
 *
 *  @author yang.f
 *        
 */
class ProductModel extends ServiceBase {

    public function __construct() {
        $this->serviceName = 'product';
    }  
    
    public function addTag($title)
    {    
        $this->checkTagTitle($title);
        
        $url = $this->api['devicedata.add'];
        $time = time();
        
        $tag_id = 'skin:product:tag:'. uniqid();
        $data = array( "doc_id" => $tag_id,
                       "data"   =>  array(  
                                            "title"=>$title,                                            
                                            "create_at"=>$time,
                                            "update_at"=>$time
                                         )                                 
                      );
        
        $this->sendHttpRequest($url, $data);      
        
        //更新记录产品的tag
        
        $this->updateTags($tag_id,$title,$time,$time);       
        
        return $tag_id;
    }
    
    public function updateTag($tag_id,$title){
        $this->checkTagTitle($title,$tag_id);
        
        $old = $this->get($tag_id,false);
        $time = time();
        
        if($old){
            $data = array("title"=>$title,"create_at"=>$old["create_at"],"update_at"=>$time);
            $this->set($tag_id, $data);
            
            $this->updateTags($tag_id,$title,$old["create_at"],$time);
        }else{
            throw new ApiException(427113,200);
        }
    }
   
    private function checkTagTitle($title,$tag_id,$tag_doc_id="skin:product:tags"){
        $data = $this->get($tag_doc_id,false);  
        $errCode = 427110;
        if($tag_id){
            $errCode = 427113;            
        }
        
        if($data){
            if( $tag_id && isset($data[$tag_id])){
                unset($data[$tag_id]);
            }
           
            foreach ($data as $val){
                if($val["title"] == $title){
                    throw new ApiException($errCode,200,"This title exists!");
                }
            }
        }
    }
    
    private function updateTags($tag_id,$title,$create_at,$update_at){  
       $tag_doc_id = "skin:product:tags";
        $data = array();        
        
        $old = $this->get($tag_doc_id,false);  
        if($old){
            $data = $old;
        }
        
        $data[$tag_id] =  array("tag_id" => $tag_id, "title" => $title,"create_at"=>$create_at,"update_at"=>$update_at);        
        $this->set($tag_doc_id, $data);
            
    }
    
    public function getTagList(){
        $tag_doc_id = "skin:product:tags";
        $ret = $this->get($tag_doc_id);  
        $data = array();
        foreach ($ret as $val){
            $data[] = $val;
        }
        return $data;
    }
   
    
    public function addDeviceData($doc_id,$data){
        $url = $this->api['devicedata.add'];
        $data = array("doc_id"=>$doc_id,"data"=>$data);        
        $response = $this->sendHttpRequest($url, $data);  
        return $response;
    }
    
    public function getByUid($uid,$page=1,$size=10){
        $list_doc_id = "skin:product:filter:1";
        $ret = $this->get($list_doc_id,false);    
        $list = array();
        if($ret){         
            foreach($ret as $val){  
                    $order[] = $val["order"];
                    $data[] = $val;                         
            }
            array_multisort($order,SORT_ASC,$data);
            
            foreach ($data as $key => $value) {
                if($key < $size){
                    $list[] = array("tag_id"=>$value["type_id"],"title"=>$value["title"],"image"=>$value["image"]);
                }
            }
            
        }else{
            throw new ApiException(427111,200);
        }
        return $list;
    }
    
    /**
     * add product
     *     
     * @param array $data
     */
    public function addProduct($data){
        $time = time(); 
        $product_id = "skin:product:".$data["id"];
        
        $data["pageView"] = 0;
        $data["create_at"] = $time;
        $data["update_at"] = $time;                

        $dbModel = new ProductdbModel();
        $ret = $dbModel->addProduct($data);
       if(!$ret){
            throw new ApiException(427110,200);
        }        
        
        //添加到已录入产品存在的usage_id列表
        if($data["usage_id"]){
            $this->addProductUsageIdList($data["usage_id"]);
        }
        return $product_id;
    }
    
    /**
     * 
     * @param string $usage_id separated by ','
     */
    protected function addProductUsageIdList($usage_id){
        $list_doc_id = "skin:product:usage:id:list";
       
        $data = array();
        $old = $this->get($list_doc_id, false);
        if($old){
            $data = $old;
        }
        
        $usage_id_arr = explode(',', $usage_id);
        foreach($usage_id_arr as $val){            
            $data["list"][$val] = $val;
        }        
        $this->set($list_doc_id, $data);        
    }


    public function getUsageList(){
        $doc_id= "skin:product:usage:id:list";
       
        try{
            $ret = $this->get($doc_id);
        }catch(App\Exception\ServiceException $se){
            throw new ApiException(427111,200);
        }
       
        if($ret["list"]){
            foreach ($ret["list"] as $val) {
                $data[] = $val;
            }
        }else{
            throw new ApiException(427111,200);
        }
        return $data;
    }
    /**
     * 
     * add multiple products
     * @param array $data
     */
    public function addProducts($data){
        $data = $this->object_to_array($data);
        $param = array();
        foreach($data as $val){
            $time = time();
            $product_id = "skin:product:".uniqid();
            $val = $this->initValue(  $val,   array("title","EN_title","type","brand","usage","price",
                                            "tags","img","thumb","description","sys_description",
                                            "how_to_use","components")
                                  );
            $val["product_id"] = $product_id;
            $val["create_at"] = $time;
            $val["update_at"] = $time;
            $val["doc_type"] = "skin:product";
            $param[] = array("doc_id"=>$product_id,"data"=>$val);
            unset($product_id,$time);
        }
        
        try{            
            $ret = $this->addMutiple($param);
        } catch (ServiceException $ex) {
            throw new ApiException(427110,200);
        }
        return $ret;
    }

     /**
     * get product
     *     
     * @param int $uid user id
     * @param string $product_id
      * @param flag unchange pageview flag 0 --> pageview changes, plus 1.1-->pageview unchanges
     */
    public function getProduct($uid,$product_id,$flag=0){
        $product = array();
        
        $id = str_replace("skin:product:", "", $product_id);
        
        $dbModel = new ProductdbModel();
        $data = $dbModel->getProductById($id);
        if($data){
            $brand["title"] = "";
            $brand["EN_title"] = "";
            if($data["brand_id"]){
                $brand_id = str_replace("skin:product:brand:", "", $data["brand_id"]);
                $brand_data = $dbModel->getBrandById("'".$brand_id."'",'title,EN_title');    
                $brand["title"] = isset($brand_data[0]["title"])?stripslashes($brand_data[0]["title"]):"";
                $brand["EN_title"] = isset($brand_data[0]["EN_title"])?stripslashes($brand_data[0]["EN_title"]):"";
            }
            
            $usage_arr = array();
            if($data["usage_id"]){
                $usage = $this->getMultipleTitle($data["usage_id"]);               
            }
            if(isset($usage) && $usage){
                   $usage_arr = explode(',', $usage);
            }
            
            $product = array("product_id"=>$product_id,
                            "brand_title"=>$brand["title"],
                            "brand_EN_title"=>$brand["EN_title"],
                            "title"=> $data["title"],                          
                            "alias"=> $data["alias"],
                            "image"=> $data["image"],
                             "description"=> $data["description"],        
                             "usage"=>$usage_arr,
                             "price"=> $data["price"],
                             "scale"=> strtoupper($data["scale"]),
                             "shape"=>$data["shape"],
                             "made_in"=>$data["made_in"],
                             "shelflife"=>$data["shelflife"],
                             "sys_description"=>$data["sys_description"],
                              "made_in" => $data["made_in"],                              
                              "pageView" => $flag?$data["pageView"]:++$data["pageView"],
                              "favnum" => intval($data["favnum"]),
                              "tearnum"=> intval($data["tearnum"])     
                        );
            $fitlist = $this->getFitList($data["unfit"]);
            $product["unfitlist"] = $fitlist;
            
            $risk_list = $this->getRiskList($data["component_id"]);            
            $product += $risk_list;           
            
            $recommend_type = $this->getLabelByUid($uid,$data["unfit"]);
            $product["recommend_type"] = $recommend_type["recommend_type"];
                      
            if(!$flag){
                $dbModel->updateProductField($id);
            }            
            
            //查看用户是否收藏该产品
            $product["collect"] = $this->checkUserCollect($uid,$product_id);
        }else{
            throw new ApiException(427111,200);
        }
        return $product;
    }
    
    public function checkUserCollect($uid,$product_id){
        $ret = false;
         //该用户收藏的产品表
       $user_collect_list_id = "user:collect:".$uid;
       $user_collect_ret = $this->get($user_collect_list_id, false);
       if( $user_collect_ret && isset($user_collect_ret["products"][$product_id])){    
           $ret = true;
       } 
       return $ret;
    }


    protected function getRecommendType($unfitlist,$risk_list){
        //type 0-->推荐使用，1--->适合使用，2-->慎用
        //规则 unfitlist 和 risk_list元素数值之和为0，推荐使用 <=3 适合使用 >3慎用
        $type = 0;
        $sum = 0;
        
        foreach($unfitlist as $val){
            $sum += $val;
        }
        
        foreach($risk_list as $val){
                $sum += $val;
        }
            
       
        if($sum<=3 && $sum >=1){
            $type = 1;
        }else  if($sum > 3){
            $type = 2;
        }
        
        return $type;
    }


    protected function getFitList($unfit){
        $unift_arr = explode(',', $unfit);
        $arr = array("重干","轻干","轻油","重油","重敏","轻敏","轻耐","重耐");        
        
        //0-->fit,1-->unfit
        $result = array();
        foreach($arr as $val){
            $result[$val] = 0;
            if(in_array($val, $unift_arr)){
                $result[$val] = 1;   
            }
        }
        
        return $result;
    }
    public function getIntlProduct($product_id){
        
        $dbModel = new ProductdbModel();
        $id = str_replace("skin:product:", "",$product_id);
        $ret = $dbModel->getProductById($id);
        
        if(!$ret){
            throw new ApiException(427111,200);
        }   
   
        $data = array(
                        "product_id" => "skin:product:".$ret["id"],
                        "title" => stripslashes($ret["title"]),
                        "alias" => stripslashes($ret["alias"]),
                        "type_id" => $ret["type_id"],
                        "brand_id"=> $ret["brand_id"],
                        "usage_id"=> $ret["usage_id"],
                        "price"=> $ret["price"],
                        "image"=> $ret["image"],
                        "thumb"=> $ret["thumb"],
                        "shape"=> $ret["shape"],
                        "description"=> stripslashes($ret["description"]),
                        "sys_description"=> stripslashes($ret["sys_description"]),
                        "component_id"=> $ret["component_id"],
                        "made_in"=> $ret["made_in"],
                        "shelflife"=> $ret["shelflife"],
                        "scale"=> $ret["scale"],
                        "unfit"=> $ret["unfit"],
                        "url"=> $ret["url"],
                        "top"=> intval($ret["top"]),
                        "id"=> intval($ret["id"]),
                        "create_at"=> intval($ret["create_at"]),
                        "update_at"=> intval($ret["update_at"])
        );
        
        return $data;
    }
    
    public function getIntlProductComp($product_id){
        $id = str_replace("skin:product:", "",$product_id);   
        $dbModel = new ProductdbModel();
        $data = $dbModel->getProductById($id);         
       
        if(!$data) {
            throw new ApiException(427111,200);
        }
        
       $ret = array();
       if($data["component_id"]){
           $dbModel = new ProductdbModel();
           $id = str_replace("skin:product:component:", "", $data["component_id"]);
           $tmp = $dbModel->getComponentById($id,"id,title");
           if($tmp){
                foreach( $tmp as $val){
                   $ret[] = array("component_id"=>"skin:product:component:".$val["id"],"title"=>$val["title"]);
                }
           }
       }
       if($ret){
            return $ret;
       }else{
           throw new ApiException(427111,200,"query failed!");
       }
    }
    
    public function deleteProduct($product_id){   
        $id = str_replace("skin:product:", "", $product_id);
        $dbModel = new ProductdbModel();
        $dbModel->deleteProductById($id);
//        $old = $data["usage_id"];            
//        $this->updateUsageList($old, "");
    }   
    

    
    public function getProductList($page=1,$size=10){
        $ret = array();  
        $dbModel = new ProductdbModel();  
        $data = $dbModel->getIntlProductList("*","",$page,$size);
         if(!$data){
             throw new ApiException(427111,200);
         }         
         
         $ret["total"] = $data["total_rows"];
         $ret["page"] = intval($page);
         $ret["size"] = intval($size);
         if(isset($data["rows"]) && $data["rows"]){ 
           
             foreach($data["rows"] as $val){                   
                 
                 $tmp["product_id"] = "skin:product:".$val["id"]; 
                 $tmp["id"] = intval($val["id"]);                 
                 $tmp["title"] = stripslashes($val["title"]);
                 
                 $brand_id = str_replace("skin:product:brand:", "", $val["brand_id"]);
                 $brand_data = $dbModel->getBrandById("'".$brand_id."'",'title');    
                 $tmp["brand_title"] = isset($brand_data[0]["title"])?stripslashes($brand_data[0]["title"]):"";
                 
                 $tmp["type_title"] = $this->getMultipleTitle($val["type_id"]);   
                 $tmp["usage_title"]  = $this->getMultipleTitle($val["usage_id"]);   
                 
                 $tmp["top"] = intval($val["top"]);
                 $tmp["url"] = intval($val["url"]);
                 
                 $ret["data"][] = $tmp;
                 unset($tmp,$brand_id,$brand_data);                
                    
             }   
             
         }else{
             throw new ApiException(427111,200);
         }
         
         return $ret;
         
    }
    
    /**
     * 
     * get component title
     * @param type $id_str
     * @return type
     */
    protected function getMultipleTitle($id_str=""){
        $title_str = "";
        if($id_str){
            $id_arr = explode(',', $id_str);            
            $data = $this->getMultiple($id_arr, false);
            if($data){          
                foreach($data as $val){
                    if($val["title"]){
                         $title_str .= $val["title"].",";
                    }
                }                
                if($title_str){
                    $title_str = substr($title_str, 0,-1);
                }
            }            
        }
        
        return $title_str;
    }
    
    /**
     * get component risk list
     * @param string $id_str component_id string,separated by ','
     * @return array
     */
    protected function getRiskList($id_str){
        $risk_arr = array("sensitization"=>0,"acne_risk"=>0,"safety"=>0,"active"=>0);
        if($id_str){
            $id_arr = explode(',', $id_str);            
            $data = $this->getMultiple($id_arr, false);
            if($data){          
                foreach($data as $val){
                    $risk_arr["sensitization"] += $val["sensitization"];
                    $risk_arr["acne_risk"] += $val["acne_risk"];
                    $risk_arr["safety"] += $val["safety"];
                    $risk_arr["active"] += $val["active"];
                }
            }            
        }
        
        return $risk_arr;
    }

    
    public function getProductListByUid($uid,$recommend_type=0,$safety=0,$page=1,$size=10){
        
        if($recommend_type == 0 && $safety ==0 ){
            $data = $this->getProductAllListByUid($uid, 0,0,$page, $size);
            return $data;
        }
        
        $result_id = "skin:result:".  $uid;        
        $result = $this->get($result_id,false);
        if(!$result || count($result) < 4 ){
            $data = $this->getProductAllListByUid($uid, $recommend_type,$safety,$page, $size);
            return $data;
        }
        
        $sql = $this->getSqlStr($result, $recommend_type,$safety);        
        
        $data = $this->queryLike($uid,$sql,$page,$size);        
    
        return $data;
    }
    
private function getSqlStr($result,$recommend_type=0,$safety=0){
        $sql = $where ="";    
        
        $skin = $this->getSkinWords($result);  
        
        if( !$skin || $recommend_type == 0   ){
            if($safety == 1){
                $sql = "( unfit NOT LIKE '%孕期%' AND unfit NOT LIKE '%哺乳%' ) OR ( unfit = '' )" ;
            }else if($safety == 2){
                $sql = "( unfit LIKE '%孕期%' OR unfit LIKE '%哺乳%' )" ;
            }
            
        }else if($recommend_type == 1 ){
            $sql = "( ";
            foreach( $skin as $val ) {
                $sql .= "unfit NOT LIKE '%". $val. "%' AND ";
            }
            $sql = substr($sql,0,-4);
            $sql .= ") ";
            
            if($safety == 1){
                $sql .= "AND ( unfit NOT LIKE '%孕期%' AND unfit NOT LIKE '%哺乳%' ) OR ( unfit = '' ) ";
            }else if($safety == 2){
                $sql .= "AND ( unfit LIKE '%孕期%' OR unfit LIKE '%哺乳%' )";
            }else if($safety == 0){                
                $sql .= "OR (unfit = '')";
            }
            
        }else if($recommend_type == 2){
            $sql = "( ";
            foreach( $skin as $val ) {
                $sql .= "unfit LIKE '%". $val. "%' OR ";
            }
            $sql = substr($sql,0,-3);
            $sql .= ") ";
            
            if($safety == 1){                
                $sql .= "AND ( unfit NOT LIKE '%孕期%' AND unfit NOT LIKE '%哺乳%' )";
            }else if($safety == 2){
                $sql .= "AND ( unfit LIKE '%孕期%' OR unfit LIKE '%哺乳%' )";
            }
            
        }
        
        return $sql;
}
    
private function getSkinWords($ret){
        $skin = array();
        foreach ($ret as $val) {
            if($val["test_type"] ==1){               
                    if($val["result"] ==1){
                        $skin[] = "重油";
                    }else if($val["result"] == 2){
                        $skin[] = "轻油";
                    }else if($val["result"] == 3){
                        $skin[] = "轻干";
                    }else if($val["result"] == 4){
                        $skin[] = "重干";
                    }
            }else if($val["test_type"] == 2){
                    if($val["result"] ==1){
                        $skin[] = "重敏";
                    }else if($val["result"] == 2){
                        $skin[] = "轻敏";
                    }
            }
        }       

        return $skin;
}



    private function getProductAllListByUid($uid,$recommend_type=0,$safety=0,$page=1,$size=10){       
             
         $data = array();   
         $dbModel = new ProductdbModel();
        
         $ret = $dbModel->getList($page,$size);   
         if(!$ret["total_rows"]){
             throw new ApiException(427111,200);
         } 
         
        $data["total"] = $ret["total_rows"];       
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        $data["list"] = $this->convertRows($uid,$ret["rows"]);
        
        return $data;
    }
    
    private function queryLike($uid,$sql="",$page=1,$size=10){
        $data = array("total"=>0,"page"=>$page,"size"=>$size,"list"=>array());   
        
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getListBySql($sql,$page,$size);        
        if($ret["total_rows"]){
            $data["total"] = $ret["total_rows"];
            $data["list"] = $this->convertRows($uid, $ret["rows"]);      
        }
        
       return $data;
    }
    
    private function convertRows($uid,$rows){
        $data = array();
        $dbModel = new ProductdbModel();
        foreach ($rows as $val) {            
            $brand["title"] = "";
            $brand["EN_title"] = "";
            if($val["brand_id"]){
                $brand_id = str_replace("skin:product:brand:", "", $val["brand_id"]);
                $brand_data = $dbModel->getBrandById("'".$brand_id."'",'title,EN_title');    
                $brand["title"] = isset($brand_data[0]["title"])?stripslashes($brand_data[0]["title"]):"";
                $brand["EN_title"] = isset($brand_data[0]["EN_title"])?stripslashes($brand_data[0]["EN_title"]):"";
            }
            
            $label = $this->getLabelByUid($uid,$val["unfit"]);
            
            $thumb = empty($val["image"])?$val["thumb"]:$val["image"];
            if(!$thumb){
                $thumb = "";
            }
            
            $data[] = array( 
                                    "product_id"=>"skin:product:".intval($val["id"]),
                                    "brand_title"=>$brand["title"],
                                    "brand_EN_title"=>$brand["EN_title"],
                                    "title"=>$val["title"],
                                    "description"=> "",
                                    "thumb"=>$thumb,
                                    "tags"=>array(),
                                    "pageView"=>intval($val["pageView"]),
                                    "favnum"=>intval($val["favnum"]),
                                    "tearnum"=>intval($val["tearnum"]),
                                    "recommend_type"=>$label["recommend_type"],
                                    "safety"=>$label["safety"]
                                    );
            unset($brand_id,$brand_data,$brand,$label,$thumb);
        }
        
        return $data;
    }
       
    private function getLabelByUid($uid,$unfit){
        $data = array("recommend_type"=>-1,"safety"=>-1);
        $result_id = "skin:result:".  $uid;        
        $ret = $this->get($result_id,false);
        if($ret && count($ret)>=4){
            if(strpos($unfit, "孕期,哺乳")===FALSE){
                $data["safety"] = 1;
            }else{
                $data["safety"] = 2;
            }
            
            if(empty($unfit)){
                $data["recommend_type"] = 1;
            }else{
                $data["recommend_type"] = $this->getRecommendTypeByResult($ret,$unfit);
            }
        }
        return $data;
    }
    
    
    private function getRecommendTypeByResult($ret,$unfit){
        $recommend_type = 1;
        foreach($ret as $val){
            if($recommend_type == 1){
                if($val["test_type"] == 1){
                    if($val["result"] ==1 && strpos($unfit, "重油") !==false){
                        $recommend_type = 2;   
                    }else if($val["result"] ==2 && strpos($unfit, "轻油") !==false){
                        $recommend_type = 2;
                    }else if($val["result"] ==3 && strpos($unfit, "轻干") !==false){
                        $recommend_type = 2;
                    }else if($val["result"] ==4 && strpos($unfit, "重干") !==false){
                        $recommend_type = 2;
                    }
                }else if($val["test_type"] == 2){
                     if($val["result"] ==1 && strpos($unfit, "重敏") !==false){
                        $recommend_type = 2;    
                    }else if($val["result"] ==2 && strpos($unfit, "轻敏") !==false){
                        $recommend_type = 2;
                    }
                }
            }
        }
        return $recommend_type;
    }
    
   

    public function getSearchListByUid($uid,$keywords,$recommend_type,$safety,$page=1,$size=10){   
        $sql = "( title LIKE '%".$keywords."%' OR alias LIKE '%".$keywords."%' )";
       
        $brand_id = $this->getBrandIdByTitle($keywords);
        if($brand_id){
            $sql .= " OR (brand_id IN (". $brand_id. "))";
        }      
        
        $sql_skin = "";
        $result_id = "skin:result:".  $uid;     
        $result = $this->get($result_id,false);
        if( $result && count($result) >= 4 ){
            $sql_skin = $this->getSqlStr($result, $recommend_type,$safety);        
        }
       
       if($sql_skin){
           $sql .= "AND ( ".$sql_skin." )";
       }    
       
        $data = $this->queryLike($uid,$sql,$page,$size);      
        
        
        return $data;
    }
   
    private function getBrandIdByTitle($keywords){
        $brand_id = "";
        $dbModel = new ProductdbModel();
        $brand_arr = $dbModel->getBrandId($keywords);
        if($brand_arr){
            foreach($brand_arr as $val){
                $brand_id .= "'skin:product:brand:".$val["id"] ."',";
            }
            $brand_id = substr($brand_id, 0,-1);
        }
        return $brand_id;
    }


    public function getFilterListByFilter($uid,$param,$recommend_type=0,$safety=0,$page=1,$size=10){
        $sql = "( ";
        if(isset($param["type_id"]) ) {
         
            $sql .= "type_id like '%".  $param["type_id"]."%' ";
        }
        if(isset($param["usage_id"])){
            if(strlen($sql) >2){
                $sql .= "AND ";
            }
             $sql .= "usage_id like '%".  $param["usage_id"]."%' ";
        }
        if(isset($param["brand_id"])){
            if(strlen($sql) >2){
                $sql .= "AND ";
            }
             $sql .= "brand_id like '%".  $param["brand_id"]."%' ";
        }
        if(isset($param["price_id"])){
           
            try{
              $price = $this->getIntlFilterType($param["price_id"]);
               if(strlen($sql) >2){
                    $sql .= "AND ";
                }
              if($price){
                    $price_arr = explode('~', $price["title"]);
                    if(count($price_arr) == 2){
                        $sql .= "price between ".$price_arr["0"]  ." and ". $price_arr["1"];
                    }else{
                        $max = str_replace("+", "", $price_arr[0]);
                        $sql .= "price >= ".$max;
                    }
              }
            }catch(\App\Exception\ApiException $se){
                
            }
        }
        
        if(strlen($sql) >2){
            $sql .= ")";
        }else{
            $sql = "";
        }
       
        $sql_skin = "";
        $result_id = "skin:result:".  $uid;        
        $result = $this->get($result_id,false);
        if( $result && count($result) >= 4 ){
            $sql_skin = $this->getSqlStr($result, $recommend_type,$safety);        
        }
       
       if($sql_skin){
           $sql .= " AND ( ".$sql_skin." )";
       }       
        
        $data = $this->queryLike($uid,$sql,$page,$size);        
    
        return $data;
    }    
    
    /**
     * get type,usage,brand title      
     * @param string $id
     */
    public function getTitleById($id){
        $data = $this->get($id, false);
        $title = isset($data["title"])?$data["title"]:"";
        return $title;
    }
    
    /**
     * set product top
     * 
     * @param string $product_id
     * @param int $top
     * @throws ApiException
     */    
    public function setTop($product_id,$top=0){
        $id = str_replace("skin:product:", "",$product_id);
        $data = array("top"=>$top,"update_at"=>time());
        $dbModel = new ProductdbModel();
        $ret = $dbModel->updateProduct($data, "id = ".$id);
        if(!$ret){
            throw new ApiException(427113,200," Set product top exist!");
        }
   }
    
    /**
     * update product
     *     
     * @param string $product_id
     * @param array $data
     */
    public function updateProduct($product_id,$data){     
        
        $id= str_replace("skin:product:", "", $product_id);
        $data["update_at"] = time();
        
        $dbModel = new ProductdbModel();
        $ret = $dbModel->updateProduct($data, "id = ".$id);
        
       if(!$ret){ 
            throw new ApiException(427113,200);
        }    
        
//        $this->updateUsageList($old["usage_id"],$data["usage_id"]);   
    }    
  
    protected function updateUsageList($old,$new=""){
        $delete = explode(",",$old);
        $add = explode(",",$new);
        
        $old_list = $this->get("skin:product:usage:id:list",false);
        if(!$old_list){
            $list = array();
        }
        
        if( $delete ){            
            foreach ($delete as $val){
                if(isset($list[$val])){
                    unset($list[$val]);
                }
            } 
        }
        
        if( $add ){
            foreach ($add as $val){
                if($val){
                    $list[$val] = $val;
                }
            } 
        }        
        
        $doc_id = "skin:product:usage:id:list";        
        $this->set($doc_id, array("list"=>$list) );       
    }


    /**
     * 
     * @param type $filter_type
     * @param type $title
     * @param type $EN_title
     * @param type $section
     * @param string $image
     * @param int $order default is 0
     * @param string $doc_id
     * @return string
     */
    public function addFilter($filter_type,$title="",$EN_title="",$section="",$image="",$order=0,$doc_id=""){      
        
//        $this->checkFilterTitle($filter_type,$title, $section,$doc_id);
        
        $type = ""; 
        $id = uniqid();
        $data = array("title"=>$title);
        switch ($filter_type) {
            case 1:
                $type = "type";    
                $data["image"] = $image;
                break;
            case 2:
                $type = "usage";
                break;
            case 3:
                $type = "brand";    
                $data["section"] = $section;               
                $data["EN_title"] = $EN_title;
                $data["image"] = $image;
                break;
            case 4:
                $type = "price";
                break;
            default:
                break;
        }
       
        $data["order"] = $order;
        
        
        //mysql表中添加或删除该条品牌记录
        if($filter_type == 3){
            $ret_id = $doc_id;
            $dbModel = new ProductdbModel();
            $brand = $data;
            if(empty($doc_id)){          
                $brand["id"] = $id;
                $dbModel->addBrand($brand);
                $ret_id = "skin:product:brand:".$id;
            }else{
                $brand_id = str_replace("skin:product:brand:", "", $doc_id);
                $dbModel->updateBrand($brand, "id = '". $brand_id. "'");
            }
            return $ret_id;
        }
        
        if(empty($doc_id)){
            $doc_id ="skin:product:".$type.":".$id;
        }
        $data[$type."_id"] = $doc_id;
        $this->set($doc_id, $data);                
        
        $this->updateFilterType($filter_type,$type,$doc_id,$title,$EN_title,$section,$image,$order);
        return $doc_id;
    }
    
    private function checkFilterTitle($filter_type,$title, $section="",$doc_id=""){
        $errCode = 427110;
        $list_doc_id = "skin:product:filter:".$filter_type;
        $data = $this->get($list_doc_id, false);
        if($doc_id){
            $errCode = 427113;
            if(isset($data[$doc_id])){
                unset($data[$doc_id]);
            }else{
                unset($data[$section][$doc_id]);
            }
        }
        
        if($data){
            if($filter_type == 3){     
                $tmp = $data[$section];
            }else{
                $tmp = $data;
            }          

            foreach ($tmp as $val){              
                 if($val["title"] == $title){
                     throw new ApiException($errCode,200);
                 }
            }                
        }
        
    }
    
    private function updateFilterType($filter_type,$type,$doc_id,$title,$EN_title,$section,$image,$order){
        $list_doc_id = "skin:product:filter:".$filter_type;
        $data = array();        
        
        $old = $this->get($list_doc_id,false);  
        if($old){
            $data = $old;
        }
        
        if($filter_type == 3){
            $data[$section][$doc_id] = array("section"=>$section, "brand_id" => $doc_id, "title" => $title,"EN_title"=>$EN_title,"image"=>$image,"order"=>$order);
        }else{
            $data[$doc_id] =  array($type."_id" => $doc_id, "title" => $title,"order"=>$order);
            if($filter_type == 1){
                $data[$doc_id] =  array($type."_id" => $doc_id, "title" => $title,"image"=>$image,"order"=>$order);
            }
        }
        $this->set($list_doc_id, $data);
    }
    
    private function deleteFilterType($filter_type,$doc_id,$section=""){
        $list_doc_id = "skin:product:filter:".$filter_type;
        $data = array();        
        
        $old = $this->get($list_doc_id,false);  
        if($old){
            $data = $old;
        }
        
        if(($filter_type == 3) && $section){
           unset($data[$section][$doc_id]);
           if(!$data[$section]){
               unset($data[$section]);
           }
        }else{
           unset($data[$doc_id]);
        }
        if($data){
            $this->set($list_doc_id, $data);
        }else{
            $this->delete($list_doc_id);
        }
    }
    
    public function deleteFilter($id){
         //从mysql表中删除该条记录
        if(strpos($id,"brand")!==false){
            $brand_id = str_replace("skin:product:brand:", "", $id);
            $dbModel = new ProductdbModel();
            $ret = $dbModel->deleteBrandById("'".$brand_id."'");
            return $ret;
        }
        
        $section = "";
        $type = $this->getTypeByFilterId($id);        
        $data = $this->get($id,false);
        if(!$data){
            throw new ApiException(427115,200);
        }
        if($type == 3){
            $section = $data["section"];
        }
        
        $this->delete($id);
        $this->deleteFilterType($type, $id, $section);
        
       
    }
    
    private function getTypeByFilterId($id){
        if(strpos($id, "type")!==false){
            $type = 1;
        }else if(strpos($id, "usage")!==false){
            $type = 2;
        }else if(strpos($id, "brand")!==false){
            $type = 3;
        }else if(strpos($id, "price")!==false){
            $type = 4;
        }else{
            throw new ApiException(400001,200);
        }
        
        return $type;
    }

     public function getFilterListByType($filter_type){
         $data = array("filter_type"=>$filter_type);
         if($filter_type == 3){
             $data["brands"] = $this->getBrandList();
             return $data;
         }
         
        $list_doc_id = "skin:product:filter:".$filter_type;
        $ret = $this->get($list_doc_id,false);   
        $index = "";
        
        switch ($filter_type) {
             case 1:                
                 $index = "types";
                 break;             
             case 2:                
                 $index = "usage";
                 break;
             case 3:      
                 $index = "brands";
                 break;
             case 4:
                 $index = "price";
                 break;
             default:
                 break;
         }

        if($ret){
            foreach($ret as $val){
                if(isset($val["image"])){
                    unset($val["image"]);
                }
                $data[$index][] = $val;
            }
        }else{
            throw new ApiException(427111,200);
        }
       
        return $data;
    }

       public function getIntlFilterListByType($filter_type){
        $data = array("filter_type"=>$filter_type);        
        if($filter_type == 3){
            $data["brands"] = $this->getIntlBrandList();
            return $data;
        }
        
        $list_doc_id = "skin:product:filter:".$filter_type;
        $ret = $this->get($list_doc_id,false);   
        $index = "";        
        
        switch ($filter_type) {
             case 1:                
                 $index = "types";
                 break;             
             case 2:                
                 $index = "usage";
                 break;
             case 3:      
                 $index = "brands";
                 break;
             case 4:
                 $index = "price";
                 break;
             default:
                 break;
         }

        if($ret){
            foreach($ret as $val){
                $data[$index][] = $val;                
            }
        }else{
            $data[$index] = array();
        }        
        
        return $data;
    }
    
    /**
     * get internal brand list from mysql
     * 
     */
    public function getIntlBrandList(){
        $data = array();
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getBrandList();
        if($ret){
            foreach ($ret as $val){ 
                if($val["section"] == '【'){
                    $val["section"] = '#';
                }
                $data[] = array(
                                "section"   => $val["section"],
                                "brand_id"  => "skin:product:brand:".$val["id"],
                                "title"     => $val["title"],
                                "EN_title"  => $val["EN_title"],
                                "image"     => $val["image"],
                                "order"     => intval($val["order"])
                                );
            }
        }
        return $data;
    }
    
    /**
     * get brand list
     * 
     * @return type
     */
    public function getBrandList(){
        $data = array();
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getBrandList();
        if($ret){
            foreach ($ret as $val){               
                if($val["section"] == "【")
                {
                    $val["section"] = '#';
                }
                $data[$val["section"]][] = array(
                                "section"   => $val["section"],
                                "brand_id"  => "skin:product:brand:".$val["id"],
                                "title"     => strtoupper($val["title"]),
                                "EN_title"  => $val["EN_title"]
                                );
            }
        }
        return $data;
    }
    
    public function deleteFilterListByType($filter_type){
        $list_doc_id = "skin:product:filter:".$filter_type;
        $ret = $this->get($list_doc_id,false); 
        
        foreach($ret as $val){  
                         
                             foreach($val as $brandl_val){
                               $doc_ids .= $brandl_val["brand_id"].",";
                             }
                        
            }
     
        $doc_ids = substr($doc_ids, 0,-1);
        $this->delete($doc_ids);
        $this->delete($list_doc_id);
    }
    
    public function getIntlFilterType($id){
      
        //brand从mysql中获取
        if(strpos($id,"brand")!==false){
            $brand_id = str_replace("skin:product:brand:", "", $id);
            $dbModel = new ProductdbModel();
            $ret = $dbModel->getBrandById("'".$brand_id."'");
            if($ret){   
                $data = array(                                
                                "section" => $ret[0]["section"],
                                "brand_id"=>"skin:product:brand:".$ret[0]["id"],
                                "title" => stripslashes($ret[0]["title"]),
                                "EN_title" => stripslashes($ret[0]["title"]),
                                "image" => $ret[0]["image"],
                                "order" => intval($ret[0]["order"])
                            );
                $data["filter_type"] = 3;
                return $data;
            }else{
                 throw new ApiException(427111,200,"This filter type document according your id doesn't exist!");
            }
        }
        
        //其他从couchbase中获取
        $ret = $this->get($id,false);
        if($ret){           
            $data = $ret;
            $filter_type = $this->getTypeByFilterId($id);
            $data["filter_type"] = $filter_type;
        }else{
            throw new ApiException(427111,200,"This filter type document according your id doesn't exist!");
        }
        return $data;
    }

    /**
     * update component by one id 
     * 
     * @param int $id
     * @param string $title
     * @param string $EN_title
     * @param string $description
     * @param string $alias
     * @param string $usage
     * @param int $acne_risk 
     * @param int $sensitization
     * @param int $safety
     * @param int $active
     */
    public function updateComponent(  $id, $title="", $EN_title="",  $description="",$alias="",$usage="",
                                        $acne_risk=0,$sensitization=0, $safety=0,$active=0){
        $data = array(  "id" =>  intval($id),
                        "title" =>  addslashes($title),
                        "EN_title" =>  addslashes($EN_title),
                        "alias" => addslashes($alias),
                        "description" => addslashes($description),
                        "usage" => $usage,
                        "acne_risk" => intval($acne_risk),
                        "sensitization" => intval($sensitization),
                        "safety" =>intval($safety),
                        "active" => intval($active),
                        "time"=> time()
                    );
        
        $dbModel = new ProductdbModel();
        $ret = $dbModel->updateComponent($data,"id = ".$id);
        return $ret;
    }
     
  /**
   * 
   * @param type $id
   * @param type $title
   * @param type $EN_title
   * @param type $description
   * @param type $alias
   * @param type $usage
   * @param type $acne_risk   
   * @param type $sensitization
   * @param type $safety
   * @param int $active
   * @return string
   */
    
    public function addComponent($id,$title, $EN_title="" , $description="", $alias="",$usage="",$acne_risk=0,$sensitization=0,$safety=0,$active=0,$component_id=""){
         
        if(empty($component_id)){
            $component_id = "skin:product:component:".$id;
        }
        $data = array(  "id" =>  intval($id),
                        "title" =>  addslashes($title),
                        "EN_title" =>  addslashes($EN_title),
                        "alias" => addslashes($alias),
                        "description" => addslashes($description),
                        "usage" => $usage,
                        "acne_risk" => intval($acne_risk),
                        "sensitization" => intval($sensitization),
                        "safety" =>intval($safety),
                        "active" => intval($active),
                        "time"=> time()
                    );
        
        $dbModel = new ProductdbModel();
        $dbModel->addComponent($data);
        return $component_id;        
    }
    
    public function addMultipleComponent($dataObj){
        $data = $this->object_to_array($dataObj);
        $multi = array();
        
        $dbModel = new ProductdbModel();
        $time  = time();
        foreach($data as $v){
            $component_id = "skin:product:component:".$v["id"];
            $v = $this->initValue($v);
            $v["id"] = intval($v["id"]);
            $v["time"] = time();
            $v["doc_type"] = "skin:component";
            $one = array("component_id"=>$component_id) + $v;
            $multi[] = array("doc_id"=>$component_id) + array("data"=>$one);
            
            $tmp = array(   "id" =>  $v["id"],
                            "title" =>   $v["title"],
                            "EN_title" => addslashes($v["EN_title"]),
                            "description" => addslashes($v["description"]),
                            "alias"=>  addslashes($v["alias"]),
                            "usage" => $v["usage"],
                            "acne_risk" =>  intval($v["acne_risk"]),
                            "sensitization" => intval($v["sensitization"]),
                            "safety" => intval($v["safety"]),
                            "active" => isset($v["active"])?intval($v["active"]):0,
                            "time" => $time
                            );
            $dbModel->addComponent($tmp);
            unset($one,$tmp);
        }
        
        return $multi;
    }

    public function initValue($data,$field_arr=array("EN_title","description","alias","usage","acne_risk","sensitization","safety"),$default=""){
        foreach($field_arr as $field){
            if(!isset($data[$field])){
                $data[$field] = $default;
            }
        }
        return $data;
    }   
    
    
 /**
  * @author 脚本之家
  * @date 2013-6-21
  * @todo 将对象转换成数组
  * @param unknown_type $obj
  * @return unknown
  */
 public function object_to_array($obj){
  $_arr = is_object($obj) ? get_object_vars($obj) :$obj;
  foreach ($_arr as $key=>$val){
   $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val):$val;
   $arr[$key] = $val;
  }
  return $arr;
 }

    /**
     * 
     * @param string $component_id
     * @throws ApiException
     */
     public function deleteComponent($component_id){     
       $id = str_replace("skin:product:component:", "", $component_id);
       $dbModel = new ProductdbModel();
       $ret = $dbModel->deleteComponentById($id);       
       return $ret;
    }

    
    public function getComponentList(){
         $ret = array();
        
         $list_doc_id = "skin:product:component";
         $data = $this->get($list_doc_id,false);
         if($data){         
            foreach($data as $val){
                $ret[] = $val;
            }
         }else{
             throw new ApiException(427111,200);
         }
         return $ret;
    }   

    
     public function getIntlComponentList($page=1,$size=10){
         
        
         $dbModel = new ProductdbModel();
         $data = $dbModel->getComponentListBySql("*","",$page,$size);
         if(!$data["total_rows"])
         {
             throw new ApiException(427111,200);
         }
         
        $ret["total_rows"] = $data["total_rows"];
        $ret["page"] = intval($page);
        $ret["size"] = intval($size);
 
        foreach($data["rows"] as $val){ 
           $ret["components"][] = array(
                                            "component_id"=>"skin:product:component:".$val["id"],
                                            "id"=> intval($val["id"]),
                                            "title"=>  stripslashes($val["title"]),
                                            "EN_title" => stripslashes($val["EN_title"]),
                                            "alias"=>  stripslashes($val["alias"]),                  
                                            "usage" => $val["usage"],
                                            "description" => stripslashes($val["description"]),
                                            "acne_risk" => intval($val["acne_risk"]),
                                            "safety" => intval($val["safety"]),
                                            "sensitization" => intval($val["sensitization"]),                                            
                                            "time" => intval($val["time"]),
                                            "active" => intval($val["active"]),
                                            "doc_type" => "skin:component"
                                        );
        }
         
         return $ret;
    }
    
  


    public function getIntlUsageList($page,$size){
         $ret = array("page"=>$page,"size"=>$size);   
         try{
            $data = $this->getList("component_usage_list",array("doc_type"=>"component_usage"),$page,$size);        
         }catch(ServiceException $se){
             throw new ApiException(427111,200);
         }
         if($data){  
             $ret["total_rows"] = $data["total_rows"];
             foreach($data["rows"] as $val){ 
                $ret["data"][] = array("doc_id"=>$val["doc_id"],"data"=>$val["value"]);                
             }             
         }
         return $ret;
    }
    
    public function getProductComponents($product_id){
        $id = str_replace("skin:product:", "", $product_id);        
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getProductById($id);
     
        if(!$ret){
            throw new ApiException(427111,200);
        }
        $product["product_id"] = $product_id;
        $product["components"] = array();
        if($ret["component_id"]){
            $dbModel = new ProductdbModel();
            $cid = str_replace("skin:product:component:", "", $ret["component_id"]);
            $list = $dbModel->getComponentById($cid);
        }
        if(isset($list) && $list){
            foreach($list as $val){
                $product["components"][] = array("component_id" =>  "skin:product:component:".$val["id"],
                                                "title"     =>  stripslashes($val["title"]),
                                                "alias"     =>  stripslashes($val["alias"]),
                                                "safety"    =>  intval($val["safety"]),
                                                "acne_risk" => intval($val["acne_risk"]),
                                                "active"    =>  intval($val["active"]),
                                                "sensitization"=>   intval($val["sensitization"]));
            }
        }
        return $product;
            
       
    }
    
    /**
     * get product acne_risk,sensitization or safety component list
     * 
     * @param string $product_id
     * @param int $type 1-->active,2-->acne_risk,3-->sensitization,4-->safety
     */
    public function getCompRiskListByProductId($product_id,$type){
        $title = "";
        $components = array();
        $field_arr = $this->getFieldByType($type);
            $field = $field_arr["field"];
            $title = $field_arr["title"];
                
        $id = str_replace("skin:product:", "", $product_id);
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getProductById($id);       
        if(!$ret){             
            throw new ApiException(427111,200);       
        }
        if($ret["component_id"]){
            $dbModel = new ProductdbModel();
            $cid = str_replace("skin:product:component:", "", $ret["component_id"]);
            $list = $dbModel->getComponentById($cid);             

            foreach($list as $val){   
                if($val[$field]){
                    $components[] = array(
                                                "component_id"=>"skin:product:component:".$val["id"],
                                                "title"         =>      stripslashes($val["title"]),
                                                "alias"         =>      stripslashes($val["alias"]),
                                                "safety"        =>      intval($val["safety"]),
                                                "acne_risk"     =>      intval($val["acne_risk"]),
                                                "active"        =>      intval($val["active"]),
                                                "sensitization" =>      intval($val["sensitization"])
                                            );
                }
            }
        }
            
        return array("title"=>$title,"components"=>$components);
    }
    
    public function getFormatComponentList($component_id){
        $components = array();
        
        $dbModel = new ProductdbModel();
        $cid = str_replace("skin:product:component:", "", $component_id);
        $list = $dbModel->getComponentById($cid);
        if($list){
            foreach($list as $val){
                $components[] = array(           
                                            "component_id"=>"skin:product:component:".$val["id"],
                                            "id"=> intval($val["id"]),
                                            "title"=>  stripslashes($val["title"]),
                                            "EN_title" => stripslashes($val["EN_title"]),
                                            "alias"=>  stripslashes($val["alias"]),                  
                                            "usage" => $val["usage"],
                                            "description" => stripslashes($val["description"]),
                                            "acne_risk" => intval($val["acne_risk"]),
                                            "safety" => intval($val["safety"]),
                                            "sensitization" => intval($val["sensitization"]),                                            
                                            "time" => intval($val["time"]),
                                            "active" => intval($val["active"]),
                                            "doc_type" => "skin:component"
                                    );
            }
        }
        
        return $components;
}
    
    protected function getFieldByType($type){
        $ret = array("field"=>"","title"=>"");
        switch ($type) {
            case 1:
                $ret["field"]  = "active";
                $ret["title"] = "功效成分";
                break;
            case 2:
                $ret["field"]   = "acne_risk";
                $ret["title"]  = "易致痘成分";
                break;
            case 3:
                $ret["field"]  = "sensitization";
                $ret["title"]  = "易致敏成分";
                break;
            case 4:
                $ret["field"]  = "safety";
                $ret["title"]  = "孕期哺乳慎用成分";
                break;
            default:
                break;
        }
        return $ret;
    }


    public function getIntlSerachComponentList($keywords,$page,$size){
        $type = "title";
        
        $str_len = strlen($keywords);
        $mbstr_len = mb_strlen($keywords,'utf-8');
        if($str_len == $mbstr_len){ //全英文
            $type = "EN_title";
        }
        
        $where = "`". $type. "` LIKE '". $keywords. "%'"; 
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getComponentListBySql("id,".$type,$where,$page,$size);
        
        $data["total"] = $ret["total_rows"];
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        $data["list"] = array();
        
        foreach ($ret["rows"] as $val) { 
            $data["list"][] = array( 
                                    "component_id"=>"skin:product:component:".$val["id"],
                                    "title"=>$val[$type]
                                    );
        }
       
        return $data;        
    }
    
    public function getIntlSerachProductList($keywords,$page,$size){
        $type = "title";
        
        $str_len = strlen($keywords);
        $mbstr_len = mb_strlen($keywords,'utf-8');
        if($str_len == $mbstr_len){ //全英文
            $type = "alias";
        }
        
        $where = $type ." LIKE '%".$keywords."%' ";
        $dbModel = new ProductdbModel();
        $ret = $dbModel->getIntlFieldList("id,title,type_id,brand_id,usage_id,top",$where,$page,$size);       
        $data["total"] = $ret["total_rows"];
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        $data["list"] = array();
        
        if($ret["total_rows"]){
            foreach ($ret["rows"] as $val) { 
                     $tmp["product_id"] = "skin:product:".$val["id"]; 

                     $tmp["id"] = intval($val["id"]);                 
                     $tmp["title"] = $val["title"];

                     $brand_id = str_replace("skin:product:brand:", "", $val["brand_id"]);
                     $brand = $dbModel->getBrandById("'".$brand_id."'","title");
                     $tmp["brand_title"] = isset($brand[0]["title"])?stripslashes($brand[0]["title"]):"";
                     $tmp["type_title"] = $this->getMultipleTitle($val["type_id"]);         
                     $tmp["usage_title"]  = $this->getMultipleTitle($val["usage_id"]);   

                     $tmp["top"] = intval($val["top"]);                 

                     $data["list"][] = $tmp;
                     unset($tmp,$brand_id,$brand);            
            }
        }
        return $data;        
    }
        
    public function getComponentUsage(){
       $list_doc_id = "skin:component:usage:list";     
       $ret = $this->get($list_doc_id,false);   
       if($ret){
           foreach($ret as $key => $val){
               $data[] = array("usage_id"=>$key)+$val;               
           }         
            return $data;
       }else{
           throw new ApiException(427111,200);
       }
    }
    
    public function addComponentUsage($title){
        $list_doc_id = "skin:component:usage:list";        
        $this->checkTagTitle($title, "", $list_doc_id);
        
        $doc_id = "skin:component:usage:".uniqid();
        $multiple[] = array("doc_id"=>$doc_id,"data"=>array("title"=>$title));
        
        $new = array();
        $old = $this->get($list_doc_id,false);
        if($old){
            $new = $old;
        }
        $new[$doc_id] = array("title"=>$title);
        
        $multiple[] = array("doc_id"=>$list_doc_id,"data"=>$new);
        $this->write($multiple);
        
        return $doc_id;
    }
    
    public function updateComponentUsage($usage_id,$title){
        try{
            $this->get($usage_id);
        } catch (ServiceException $ex) {
            throw new ApiException(427113,200,"This component usage id doesn't exist!");
        }
        
        $list_doc_id = "skin:component:usage:list"; 
        $this->checkTagTitle($title, $usage_id, $list_doc_id);
        
        $multiple[] = array("doc_id"=>$usage_id,"data"=>array("title"=>$title));
        
        $new = array();
        $old = $this->get($list_doc_id,false);
        if($old){
            $new = $old;
        }
        $new[$usage_id] = array("title"=>$title);
        
        $multiple[] = array("doc_id"=>$list_doc_id,"data"=>$new);
        $this->write($multiple);
    }
   
    public function deleteComponentUsage($usage_id){
        try{
            $this->get($usage_id);
        } catch (ServiceException $ex) {
            throw new ApiException(427113,200,"This component usage id doesn't exist!");
        }        
        
        $list_doc_id = "skin:component:usage:list";
        $old = $this->get($list_doc_id,false);
        if(isset($old[$usage_id])){
            unset($old[$usage_id]);
        }
        
        $this->delete($usage_id);
        if($old){
            $this->set($list_doc_id, $old);
        }else{
            $this->delete($list_doc_id);
        }
    }
    
    public function getIntlUsageById($usage_id){
        
        $id = str_replace("skin:product:component:", "", $usage_id);
        $dbModel = new ProductdbModel();
       
        $data_arr = $dbModel->getComponentById($id);     
        if($data_arr){
            $data = $data_arr[0];
        }
        if(isset($data["usage"]) && $data["usage"]){            
                $usage = $this->getMultipleTitle($data["usage"]);
                $data["usage"] = $usage;
        }else{
            throw new ApiException(427111,200,"This component usage id doesn't exist!");
        }
        
        return $data;
    }
    
    public function getComponentUsageById($usage_id){
        try{
            $data = $this->get($usage_id);
        } catch (ServiceException $ex) {
            throw new ApiException(427111,200,"This component usage id doesn't exist!");
        }
        
        $data["usage_id"] = $usage_id;
        return $data;
    }
    
    public function updateFavnum($uid,$product_id,$type){
        $id = str_replace("skin:product:", "", $product_id);
        $dbModel = new ProductdbModel();
        $product = $dbModel->getProductById($id);
        $data = array();
        
        if(!$product){
             throw new ApiException(427113,200," this product doesn't exist! ");
        }
                        
        $orig = $this->checkFav($uid,$product_id,$type);     

        $favnum = intval($product["favnum"]);
        $tearnum = intval($product["tearnum"]);
        
        if($type==1){ //点赞
            
            $data["favnum"] = ++$favnum;         
            //点赞之前心碎过，要减去一个心碎
            if($orig == -1 && $product["tearnum"]){ 
                $data["tearnum"] = --$tearnum; 
            }  
            
        }else{ //心碎
            
            $data["tearnum"] = ++$tearnum;  
            //心碎之前点赞了，减去一个点赞数量
            if($orig == 1 && $product["favnum"]){
                $data["favnum"] = --$favnum; 
            }
        }

        $dbModel->updateProduct($data, "id = ".$id);         
     
    }
    
     private function checkFav($uid,$product_id,$type){
       $orig = 0;
       $time = time();
       $doc_id = "skin:product:fav:".$product_id;
       $product_fav = $this->get($doc_id,false);
       if($product_fav){
           if(isset($product_fav["users"]["type".$type][$uid])){
               throw new ApiException(490001,200);
           }
       }
       
       if($type == 1){
           //点赞之前心碎过，删除心碎记录
           if(isset( $product_fav["users"]["type-1"][$uid] ) ){
               unset($product_fav["users"]["type-1"][$uid]);
               $orig = -1;
           }
       }else{
           //心碎之前点赞了，删除点赞记录
           if(isset( $product_fav["users"]["type1"][$uid] ) ){
               unset($product_fav["users"]["type1"][$uid]);
               $orig = 1;
           }
       }
       
       $product_fav["product_id"] = $product_id;
       $product_fav["users"]["type".$type][$uid] = array("uid"=>$uid,"create_at"=>$time);
       $this->set($doc_id, $product_fav);
       
       return $orig;
   }
   
    public function cancelFavnum($uid,$product_id,$type){  
        $data = array();
        
        $id = str_replace("skin:product:", "", $product_id);
        $dbModel = new ProductdbModel();
        $product = $dbModel->getProductById($id);
        if(!$product){
            throw new ApiException(427113,200," this product doesn't exist! ");
        }

       $this->checkDeleteFav($uid,$product_id,$type);     

       $favnum = isset($product["favnum"])?$product["favnum"]:0;
       $tearnum = isset($product["tearnum"])?$product["tearnum"]:0;
       if($type == 1 && $favnum){
           $data["favnum"] = -- $favnum;
       }else if($type == -1 && $tearnum){
           $data["tearnum"] = -- $tearnum;
       }

       if($data){
           $dbModel->updateProduct($data, 'id = '.$id);
       }
    }
   
   
   private function checkDeleteFav($uid,  $product_id,$type){
       $doc_id = "skin:product:fav:".$product_id;
       try{
            $encyclopedia_fav = $this->get($doc_id);
       }catch(App\Exception\ServiceException $se){
           throw new ApiException(427113,200," this product doesn't exist! ");
       }
       
       if($encyclopedia_fav && isset($encyclopedia_fav["users"]["type".$type][$uid]) ){
            unset($encyclopedia_fav["users"]["type".$type][$uid]);
            if($encyclopedia_fav["users"]){
                 $this->set($doc_id, $encyclopedia_fav);
            }else{
                $this->delete($doc_id);
            }
       }else{
           throw new ApiException(490002,200);
       }
   }
   
   public function getFavnumCheck($uid,$product_id){
       $data = array("fav"=>0,"tear"=>0,"collect"=>0);
       $doc_id = "skin:product:fav:".$product_id;
       $product_fav = $this->get($doc_id,false);
       if($product_fav){
           if(isset($product_fav["users"]["type1"][$uid])){
               $data["fav"] = 1;
           }else if(isset($product_fav["users"]["type-1"][$uid])){
               $data["tear"] = 1;
           }
       }
       
       $collect = $this->checkUserCollect($uid,$product_id);
       if($collect){
           $data["collect"] = 1;
       }
       return $data;
   }
   
   /**
    * user collect products
    * 
    * @param int $uid
    * @param string $product_id
    * @throws ApiException
    */
   public function collect($uid,$product_id){
       
       //收藏该产品的用户表
       $doc_id = "skin:product:collect:".$product_id;
       $ret = $this->get($doc_id,false);
       if($ret){
           $new = $ret;
           if(isset($ret["users"][$uid])){
               throw new ApiException(490003,200);
           }
       }        
       $new["users"][$uid] = $uid;
       
       //该用户收藏的产品表
       $user_collect_list_id = "user:collect:".$uid;
       $user_collect_ret = $this->get($user_collect_list_id, false);
       if($user_collect_ret){
           $user_collect_data = $user_collect_ret;
       }
       $user_collect_data["products"][$product_id] = $product_id;
       
       //更新
       $dd_data[] = array("doc_id"=>$doc_id,"data"=>$new);
       $dd_data[] = array("doc_id"=>$user_collect_list_id,"data"=>$user_collect_data);       
       $this->write($dd_data);
   }
   
   /**
    * user cancel collection 
    * 
    * @param int $uid
    * @param string $product_id
    * @throws ApiException
    */
   public function collectCancel($uid,$product_id){
       
        //收藏该产品的用户表
        $doc_id = "skin:product:collect:".$product_id;
        $ret = $this->get($doc_id,false);
        if(! $ret){
            throw new ApiException(490004,200);
        }
       
        $new = $ret;
        if(! isset($ret["users"][$uid])){
            throw new ApiException(490004,200);
        }
        unset($new["users"][$uid]);        
        
       
       //该用户收藏的产品表
       $user_collect_list_id = "user:collect:".$uid;
       $user_collect_ret = $this->get($user_collect_list_id, false);
       if( $user_collect_ret ){
           $user_collect_data = $user_collect_ret;
           unset($user_collect_data["products"][$product_id]);
       }        
       
       //更新
       if(empty($new)){
            $this->delete($doc_id);
       }else{
            $dd_data[] = array("doc_id"=>$doc_id,"data"=>$new);
       }
       if(empty($user_collect_data)){
            $this->delete($user_collect_list_id);
       }else{
           $dd_data[] = array("doc_id"=>$user_collect_list_id,"data"=>$user_collect_data);
       }
       
       if(isset($dd_data)){
            $this->write($dd_data);
       }
   }
   
   public function getColltionList($uid,$page=1,$size=10){
       $ids = "";
       $products = array();
       $list = array();
       $dbModel = new ProductdbModel();
       
       $user_collect_list_id = "user:collect:".$uid;
       try{
           $data = $this->get($user_collect_list_id);
       } catch (ServiceException $ex) {
           return $list;
       }
       
       if($data){           
           foreach($data["products"] as $key => $val){
               $key = str_replace("skin:product:", "", $key);
               $ids .= $key.",";
           }           
       }
       
       if($ids){
           $ids = substr($ids, 0,-1);
           $where = "id IN ( ".$ids. ")";             
           $products = $dbModel->getListBySql($where, $page, $size);           
       }
       
       if($products["total_rows"]){       
           foreach ($products["rows"] as $key => $product) {    
               $brand_id = "";
               $brand["title"] = "";
               $brand["EN_title"] = "";
               if($product["brand_id"]){
                    $brand_id = str_replace("skin:product:brand:","", $product["brand_id"]);
                    $brand_data = $dbModel->getBrandById("'".$brand_id."'", 'title,EN_title');
                    $brand["title"] = isset($brand_data[0]["title"])?stripslashes($brand_data[0]["title"]):"";
                    $brand["EN_title"] = isset($brand_data[0]["EN_title"])?stripslashes($brand_data[0]["EN_title"]):"";
                }
               $label = $this->getLabelByUid($uid,$product["unfit"]); 
               $list[] = array(
                                "product_id"=>"skin:product:".$product["id"],
                                "thumb"=>$product["thumb"]?$product["thumb"]:$product["image"],
                                "title"=>$product["title"],
                                "brand_title"=>$brand["title"],
                                "brand_EN_title"=>$brand["EN_title"],
                                "pageView"=>intval($product["pageView"]),
                                "favnum"=>intval($product["favnum"]),
                                "tearnum"=>intval($product["tearnum"]),
                                "recommend_type"=>$label["recommend_type"],
                                "safety"=>$label["safety"]
                            );
           }
       }       
       
       
        return $list;
       
   }
   
   public function htmlCollect($uid,$product_id){
       $errcode = 0; 
       
       //用户没收藏,添加收藏
       $user_collect_list_id = "user:collect:".$uid;
       try{           
            $user_collect_ret = $this->get($user_collect_list_id, false); 
       } catch (ServiceException $ex) {           
           try{               
               $ret = $this->collect($uid,$product_id);               
           } catch (ApiException $ex) {
               $errcode = 1;
               return $errcode;
           }
       }
       
       //用户收藏过该文章，取消收藏
       if( $user_collect_ret && isset($user_collect_ret["products"][$product_id])){  
                try{
                    $ret = $this->collectCancel($uid, $product_id);                    
                } catch (ApiException $ex) {
                    $errcode = 1;
                    return $errcode;
                }
        }else if( !isset($user_collect_ret["products"][$product_id]) ){
            try{ 
               $ret = $this->collect($uid,$product_id);
            } catch (ApiException $ex) {
                $errcode = 1;
                return $errcode;
            }
        }
        
        return $errcode;
        
   }
}
