<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Exception\ServiceException;

use App\Models\ProductModel;
use App\Models\Mysql\ArticledbModel;
use App\Models\Mysql\PlandbModel;
/**
 *  article model
 *
 * @author yang.f
 *        
 */
class ArticleModel extends ServiceBase {
    private $list_doc_id = "skin:article:tags";
    
    public function __construct() {
        $this->serviceName = 'article';
    }  
    
    
    public function addTag($title)
    {    
        $this->checkTagTitle($title);
        
        $url = $this->api['devicedata.add'];
        
        $time = time();
        $tag_id = "skin:article:tag:".  uniqid();
        $data = array( "doc_id" => $tag_id,
                       "data"   =>  array(  
                                            "title"=>$title,
                                            "create_at"=>$time,
                                            "update_at"=>$time
                                         )
                                 
                      );
        
        $this->sendHttpRequest($url, $data);      
        
        //更新tag list       
        $this->updateTags($tag_id,$title,$time,$time);       
        
        return $tag_id;
    }
   
    private function checkTagTitle($title,$doc_id){
        $errCode = 427110;
        $tags = $this->get($this->list_doc_id,false);
        if($tags){
            if($doc_id){
                unset($tags[$doc_id]);
                $errCode = 427113;
            }
            
            foreach ($tags as $val){
                if($val["title"] == $title){
                    throw new ApiException($errCode,200);
                }
            }
        }
    }
    
    private function updateTags($tag_id,$title,$time,$update){  
       
        $data = array();
        $old = $this->get($this->list_doc_id,false);  
        if($old)
        {           
            $data = $old;
        }                 
         
        $data[$tag_id] = array("tag_id"=>$tag_id,"title"=>$title,"create_at"=> $time,"update_at"=>$update);        
        $this->set($this->list_doc_id, $data);            
    }
   
    public function updateTag($tag_id,$title){        
        $this->checkTagTitle($title, $tag_id);
        
        $time = time();
        $data = $this->get($tag_id,false);
        if($data){
            $new = array("title"=>$title,"create_at"=>$data["create_at"],"update_at"=>$time);    
            $this->set($tag_id, $new);
            
            $this->updateTags($tag_id, $title, $data["create_at"], $time);
            
        }else{
            throw new ApiException(427113,200);
        }
        
    }
    
    public function deleteTag($tag_id){       
        $this->delete($tag_id);        
        
        $data = $this->get($this->list_doc_id,false);
        if($data){
            unset($data[$tag_id]);
            $this->set($this->list_doc_id, $data);
        }
    }
    
    public function getTags(){       
        $data = $this->get($this->list_doc_id,false);
        $tags = array();
        
        if($data){
            foreach ($data as $val) {
                $tags[] = $val;                
            }       
            return $tags;
        }else{
            throw new ApiException(427111,200);
        }
    }
    
    public function getTagsByUid($uid){
        $data = $this->get($this->list_doc_id,false);
        $tags = array();
        
        if($data){
            foreach ($data as $val) {
                $tags[] = array("tag_id"=>$val["tag_id"],"title"=>$val["title"]);
            }
            
            return $tags;
        }else{
            throw new ApiException(427111,200);
        }
    }
    
    public function getTagById($tag_id){
        $data = $this->get($tag_id,false);
                
        if($data){
            return $data;
        }else{
            throw new ApiException(427111,200);
        }
    }
      
    public function addComprenhensive($type,$title){
        $doc_id = "skin:comprehensive:types";
        $list = $this->get($doc_id,false);
        if($list){
            $data = $list;            
        }
        $data[$type] = array("type"=>$type,"title"=>$title);
        $this->set($doc_id, $data);
        return $data;
    }
    
    public function getComprenhensive(){
        $doc_id = "skin:comprehensive:types";
        $list = $this->get($doc_id,false);
        if($list){
            return $list;
        }else{
            throw new ApiException(427110,200);
        }
    }
    
   public function addCharacter($skin_comprehensive_type,$title,  $description,  $detail,  $daily,  $solutions, $code){      
       $doc_id = "skin:tips:".$skin_comprehensive_type;
       $time = time();
       $data = array(
                        "type"=>$skin_comprehensive_type,"title"=>$title,"description"=>$description,
                        "detail"=>$detail,"daily"=>$daily,"solutions"=>$solutions,"code"=>$code,
                        "create_at"=>$time, "update_at"=>$time               
                    );
       $this->set($doc_id, $data);
       
       //增加code主键记录
       $code_arr = explode(',', $code);
       foreach($code_arr as $cd){
            $data["code"] = $cd;
            $param[] = array("doc_id" => "skin:tips:code:".$cd,"data"=>$data);
       }
       $this->addMutiple($param);
       
       return $doc_id;
   }
   
   public function getCharacter($type){
       $doc_id = "skin:tips:".$type;
       $data = $this->get($doc_id, false);
       if($data){
           return $data;
       }else{
           throw new ApiException(427111,200);
       }       
   }
   
   /**
    * 
    * @param int $uid
    * @param boolean $ucode whether user his own skin code returns, the default is not returned.
    * @return array
    * @throws ApiExceptions
    */
   public function getCharacteByUid($uid,$ucode=false){
       
       $code = $this->getSkinCodeByResult($uid);
       $doc_id = "skin:tips:code:".$code;
       $data = $this->get($doc_id, false);       
       if($data){
           unset($data["code"],$data["create_at"],$data["update_at"]);
       }else{
           throw new ApiException(450004,200);
       }  
       if($ucode){
            $data["ucode"] = $code;
       }
       
        return $data;
   }
   
   protected function getSkinCodeByResult($uid){
       $result_id = "skin:result:".  $uid;      
       try{
           $ret = $this->get($result_id);
       }  catch (\App\Exception\ServiceException $se){
           throw new ApiException(437111,200);
       }       
       
       if(count($ret) >= 4){           
            $code = "";
            for($i=1;$i<=count($ret);$i++){
                $tmp = $ret["type".$i]["result"];
                $code .="$tmp";
            }
       }else{
           throw new ApiException(450003,200);
       }
       return $code;
 }
   
 


   public function addEncyclopedia($title,$tags,$image,$thumb,$content,$from_nickname,$from_image,$pageView,$favnum,$top){
       $dbModel = new ArticledbModel();
       $id = uniqid();
       $doc_id = "skin:encyclopedia:".$id;
       $time = time();
       $data = array(
                        "unique" => $id,
                        "title"=>$title,"tags"=>$tags,
                        "image"=>$image,"thumb"=>$thumb,
                        "content"=>$content,
                        "pageView"=>$pageView,"favnum"=>$favnum,
                        "from_nickname"=>$from_nickname,
                        "from_image"=>$from_image,
                        "top"=>$top,
                        "create_at"=>$time,"update_at"=>$time
                    );
      $dbModel->addEncyclopedia($data);
      
      return $doc_id;
   }
   
   public function setEncyclopedia($encyclopedia_id,$title,$tags,$image,$thumb,$content,$from_nickname,$from_image,$pageView,$favnum,$top){
       $time = time();
       $id = str_replace("skin:encyclopedia:", "", $encyclopedia_id);
       $dbModel = new ArticledbModel();
       $old = $dbModel->getEncyclopediaById($id);
      
       if($old){            
            $data = array(
                             "title"=>$title,"tags"=>$tags,
                             "image"=>$image,"thumb"=>$thumb,
                             "content"=>$content,
                             "pageView"=>$pageView,"favnum"=>$favnum,
                             "from_nickname"=>$from_nickname,
                             "from_image"=>$from_image,
                             "top"=>$top,
                             "update_at"=>$time,
                         );
            $dbModel->updateEncyclopedia($data, "`unique`='".$id."'");
            
       }else{
           throw new ApiException(427113,200,"  this article doesn't exist");
       }
   }
   
   public function setTop($encyclopedia_id,$top=0){
       
       $id = str_replace("skin:encyclopedia:", "",$encyclopedia_id);
       $dbModel = new ArticledbModel();       
       $encyclopedia = $dbModel->getEncyclopediaById($id);      
       if(!$encyclopedia){
           throw new ApiException(427113,200,"this article doesn't exist ");
       }
       
       $data = array("update_at"=>time(),"top"=>$top);
       $ret = $dbModel->updateEncyclopedia($data, "`unique`='".$id."'");
       if(!$ret){
             throw new ApiException(427113,200,"set top  failed! ");
        }
   }
   
   public function deleteEncyclopedia($encyclopedia_id){
       $id = str_replace("skin:encyclopedia:", "",$encyclopedia_id);
       $dbModel = new ArticledbModel();       
       $ret = $dbModel->deleteEncyclopedia($id);
       if(!$ret){
           throw new ApiException(427115,200," delete failed!");
       }
   }   
    
   public function getEncyclopediaById($encyclopedia_id){
       $id = str_replace("skin:encyclopedia:", "",$encyclopedia_id);
       $dbModel = new ArticledbModel();       
       $encyclopedia = $dbModel->getEncyclopediaById($id);       
       if(!$encyclopedia){
           throw new ApiException(427111,200);
       }
       
       $data = array(   
                        "title"=>$encyclopedia["title"],"tags"=>$encyclopedia["tags"],
                        "image"=>$encyclopedia["image"],"thumb"=>$encyclopedia["thumb"],
                         "content"=>  stripslashes($encyclopedia["content"]),
                        "pageView"=>intval($encyclopedia["pageView"]),
                        "favnum"=>intval($encyclopedia["favnum"]),
                        "from_nickname"=>$encyclopedia["from_nickname"],
                        "from_image"=>$encyclopedia["from_image"],
                        "top"=>intval($encyclopedia["top"]),
                        "create_at"=>intval($encyclopedia["create_at"]),
                        "update_at"=>intval($encyclopedia["update_at"]),
                        "comments"=>intval($encyclopedia["comments"])
                    );
       return $data;
   }
   
   public function getTipsByUid($uid){     
       $ret = array();
       $id_arr = array("579041d78ff0e","578c4b43e3516","5760eef6ce256");
       $key = array_rand($id_arr);
       $id = $id_arr[$key];
       $dbModel = new ArticledbModel();
       $enclyclopedia = $dbModel->getEncyclopediaById($id);
       if($enclyclopedia){
            $description = mb_substr(strip_tags($enclyclopedia["content"]),0,22,"utf-8") . "...";
            $ret = array(
                                         "encyclopedia_id"=>"skin:encyclopedia:".$id,
                                         "title" => $enclyclopedia["title"],
                                         "thumb" => $enclyclopedia["thumb"],
                                         "content" => $description,
                                         "favnum" => intval($enclyclopedia["favnum"]),
                                         "pageView" => intval($enclyclopedia["pageView"])
                         );
       }
       return $ret;
   }
   
   public function getUserEncyclopedia($uid,$encyclopedia_id){
       $dbModel = new ArticledbModel();
       $id = str_replace("skin:encyclopedia:", "", $encyclopedia_id);
       $enclyclopedia = $dbModel->getEncyclopediaById($id);
       $ret = array();
       
       if($enclyclopedia){
           $ret =  array(
                                    "encyclopedia_id"=>$encyclopedia_id,
                                    "title" => $enclyclopedia["title"],
                                    "from_nickname" => $enclyclopedia["from_nickname"],
                                    "from_image" => $enclyclopedia["from_image"],
                                    "img" => $enclyclopedia["image"],
                                    "content" => stripslashes($enclyclopedia["content"]),
                                    "favnum" => intval($enclyclopedia["favnum"]),
                                    "pageView" =>((intval($enclyclopedia["pageView"]))+1),
                                    "comments" => intval($enclyclopedia["comments"])
                        );
           
           $dbModel->updateEncyclopediaField($id);
           
          //查看用户是否收藏该文章
            $ret["collect"] = $this->checkUserCollect($uid,$encyclopedia_id);
           
           return $ret;
       }else{
           throw new ApiException(427111,200);
       }
   }   
   
   
   public function getEncyclopediaList($page=1,$size=10){
        $dbModel = new ArticledbModel();
       
        $data = $dbModel->getEncyclopediaList($page, $size);  
//        $data = $this->getList("skin_encyclopedias", array("doc_type"=>"skin:encyclopedia"), $page, $size);
        
         $ret["total"] = $data["total_rows"];
         $ret["page"] = intval($page);
         $ret["size"] = intval($size);
         $ret["data"] = array();
         
         
         if(isset($data["rows"]) && $data["rows"]){   
             
                foreach($data["rows"] as $val){ 
                    $ret["data"][] = array("encyclopedia_id"=>"skin:encyclopedia:".$val["unique"],"title"=>$val["title"],
                                           "thumb"=>$val["thumb"],"top"=>intval($val["top"]));
 
//                    $tmp["unique"] = str_replace("skin:encyclopedia:", "", $val["doc_id"]);
//                    $tmp["title"] = $val["value"]["title"];                    
//                    $tmp["tags"] = $val["value"]["tags"];
//                    $tmp["image"] = $val["value"]["image"];
//                    $tmp["thumb"] = $val["value"]["thumb"];
//                    $tmp["content"] = $val["value"]["content"];
//                    $tmp["pageView"] = intval($val["value"]["pageView"]);
//                    $tmp["favnum"] = intval($val["value"]["favnum"]);
//                    $tmp["from_nickname"] = $val["value"]["from_nickname"];
//                    $tmp["from_image"] = $val["value"]["from_image"];
//                    $tmp["create_at"] = $val["value"]["create_at"];
//                    $tmp["update_at"] = intval($val["value"]["update_at"]);
//                    $tmp["comments"] = isset($val["value"]["comments"])?intval($val["value"]["comments"]):0;
//                    $dbModel->addEncyclopedia($tmp);
                }
             
             
         }else{
             throw new ApiException(427111,200);
         }
         
         return $ret;
   }

   public function getListByUid($uid,$list_type,$page=1,$size=10){      
        $ret = array();   
        $dbModel = new ArticledbModel();
       
        $data = $dbModel->getEncyclopediaList($page, $size);    
        
         $ret["total"] = $data["total_rows"];
         $ret["page"] = intval($page);
         $ret["size"] = intval($size);
         $ret["encyclopedia_list"] = array();
         if(isset($data["rows"]) && $data["rows"]){             
             foreach($data["rows"] as $val){     
                 $description = "";
                 if($val["content"]){
                    $description = mb_substr(strip_tags(stripslashes($val["content"])),0,22,"utf-8") . "...";
                 }
                 $ret["encyclopedia_list"][] = array("encyclopedia_id"=>"skin:encyclopedia:".$val["unique"],
                                                    "title"=>$val["title"],
                                                    "content" => $description,
                                                    "thumb"=>$val["thumb"],
                                                    "favnum"=>intval($val["favnum"]),
                                                    "pageView"=>intval($val["pageView"]));
                 unset($description);
             }            
             
         }
         
         return $ret;
   }
   
   public function getListByTag($tag_id,$page,$size){
       $where = "tags like '%".$tag_id ."%'";
        $articleModel = new ArticledbModel();
        $ret = $articleModel->getEncyclopediaListBySql($where, $page, $size);
        if(!$ret["total_rows"]){
            throw new ApiException(427111,200);
        }
        
        $data["total"] = $ret["total_rows"];        
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        $data["tag_id"] = $tag_id;
        $data["encyclopedia_list"] = $this->convertRows($ret["rows"]);
       
        return $data;
   }
   
   public function searchListByKeywords($keywords,$page,$size){
        $where = "title like '%".$keywords ."%' OR content like '%".$keywords ."%'";
        $articleModel = new ArticledbModel();
        $ret = $articleModel->getEncyclopediaListBySql($where, $page, $size);
        if(!$ret["total_rows"]){
            throw new ApiException(427111,200);
        }
        
        $data["keywords"] = $keywords;
        $data["total"] = $ret["total_rows"];
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        $data["encyclopedia_list"] = $this->convertRows($ret["rows"]);   
        
        return $data;
   }
   
   private function convertRows($rows){
       $data = array();
       
       foreach ($rows as $val) { 
          $description = mb_substr(strip_tags($val["content"]),0,22,"utf-8") . "...";   
          $thumb = empty($val["thumb"])?$val["image"]:$val["thumb"];
          if(!$thumb){
              $thumb = "";
          }
          $data[] = array( 
                                    "encyclopedia_id" =>"skin:encyclopedia:".$val["unique"],                                  
                                    "title" => $val["title"],
                                    "content" => $description,
                                    "thumb" => $thumb,
                                    "pageView" => isset($val["pageView"])?intval($val["pageView"]):0,
                                    "favnum" => isset($val["favnum"])?intval($val["favnum"]):0
                        );
          unset($description,$thumb);
       }
       
       return $data;
   }
   
   public function updateFav($uid,  $encyclopedia_id){
       $id = str_replace("skin:encyclopedia:", "", $encyclopedia_id);
       
       $dbModel  =  new ArticledbModel();
       $ret = $dbModel->getEncyclopediaById($id);       
       if(!$ret){
           throw new ApiException(427113,200," this article doesn't exist! ");
       }
       
        $update_at = time();       
        $this->checkFav($uid,$encyclopedia_id,$update_at);    
        
        $dbModel->updateEncyclopediaField($id, "favnum");           
       
   }
   
   private function checkFav($uid,$encyclopedia_id,$time){
       $time = time();
       $doc_id = "skin:encyclopedia:fav:".$encyclopedia_id;
       $encyclopedia_fav = $this->get($doc_id,false);
       if($encyclopedia_fav){
           if(isset($encyclopedia_fav["users"][$uid])){
               throw new ApiException(450001,200);
           }
       }
       
       $encyclopedia_fav["encyclopedia_id"] = $encyclopedia_id;
       $encyclopedia_fav["users"][$uid] = array("uid"=>$uid,"create_at"=>$time);
       $this->set($doc_id, $encyclopedia_fav);
       
   }
   
    public function cancelFav($uid,  $encyclopedia_id){
       $id = str_replace("skin:encyclopedia:", "", $encyclopedia_id);
       
       $dbModel  =  new ArticledbModel();
       $ret = $dbModel->getEncyclopediaById($id);       
       if(!$ret){
           throw new ApiException(427113,200," this article doesn't exist! ");
       }
           
       $this->checkDeleteFav($uid,$encyclopedia_id);     
           
       $dbModel->updateEncyclopediaField($id, "favnum", "-");      
   }
   
   
   private function checkDeleteFav($uid,  $encyclopedia_id){
       $doc_id = "skin:encyclopedia:fav:".$encyclopedia_id;
       $encyclopedia_fav = $this->get($doc_id,false);
       if($encyclopedia_fav && isset($encyclopedia_fav["users"][$uid]) ){
            unset($encyclopedia_fav["users"][$uid]);
            if($encyclopedia_fav["users"]){
                 $this->set($doc_id, $encyclopedia_fav);
            }else{
                $this->delete($doc_id);
            }
       }else{
           throw new ApiException(450002,200);
       }
       
      
       
   }
   
   public function queryFav($uid,  $encyclopedia_id){
       $fav = 0;
       $collect = 0;
       
       $doc_id = "skin:encyclopedia:fav:".$encyclopedia_id;
       $encyclopedia_fav = $this->get($doc_id,false);
       if($encyclopedia_fav){
           if(isset($encyclopedia_fav["users"][$uid])){   
               $fav = 1;
           }
       }    
       
       $encyclopedia_collect = $this->checkUserCollect($uid, $encyclopedia_id);
       if($encyclopedia_collect){
           $collect = 1;
       }
       
       $data = array(   
                        "uid"=>$uid,"encyclopedia_id"=>$encyclopedia_id,
                        "praise"=>$fav,"collect"=>$collect
                    );
       return $data;
   }
   
   public function addTodo($title,$description="",$content="",$thumb="",$image="",$doc_id="",$type=""){  
       $data = array(   "title"=>$title,"description"=>$description,"content"=>$content,
                        "thumb"=>$thumb,"image"=>$image);
       $dbModel = new ArticledbModel();
       
       if(empty($doc_id)){
           $id = uniqid();
           $doc_id = "skin:todo:".$id;
           $data["id"] = $id;
           $dbModel->add($data);
       }else{
           $id = str_replace("skin:todo:", "", $doc_id);
           $dbModel->updateTodo($data, "id='".$id."'");
       }
       return $doc_id;
   }
   
   public function deleteTodo($doc_id){     
        $id = str_replace("skin:todo:", "", $doc_id);
        $dbModel = new ArticledbModel();
        $ret = $dbModel->deleteTodo($id);
        if(!$ret){
             throw new ApiException(427115,200);
         }
   }
   
   public function getTodo($doc_id){
        $id = str_replace("skin:todo:", "", $doc_id);
        $dbModel = new ArticledbModel();
        $ret = $dbModel->getTodoById($id,"*");
         if(!$ret){
             throw new ApiException(427112,200);
         }
         
         $data = array(
                        "title"=>$ret["title"],"description"=>$ret["description"],
                        "content"=>  stripslashes($ret["content"]),
                        "thumb"=>$ret["thumb"],"image"=>$ret["image"]
                       );
         return $data;
   }
   
   public function getTodoList($page=1,$size=10){
        $dbModel = new ArticledbModel();
        $data = $dbModel->getTodoListBySql("", $page, $size);
         
         $ret["total"] = $data["total_rows"];
         $ret["page"] = intval($page);
         $ret["size"] = intval($size);
         $ret["data"] = array();
         if(isset($data["rows"]) && $data["rows"]){             
             foreach($data["rows"] as $val){  
                 
                 $tmp["doc_id"] = "skin:todo:".$val["id"]; 
                 $tmp["title"] = $val["title"];
                 $tmp["thumb"] = $val["thumb"];
                 
                 $ret["data"][] = $tmp;
                 unset($tmp);       
             }            
             
         }
         
         return $ret;
   }
   
   public function getIndexTodoList($uid,$city){
       $data = array();
       $skin = "";
       try{
            $ret = $this->getCharacteByUid($uid,true);   
       }catch( ApiException $ex){
           throw new ApiException($ex->getCode(),200);
       }
        if(isset($ret)){
            $skin = $ret["type"];
        }        
        
        $doc_id = "skin:weather:".$city;    
        $weather = $this->get($doc_id,false);
        if($weather){
            $tags = $this->getTagsByComplex($skin, $weather);  
            $data = $this->getTodoListByTags($tags);            
        }
        
        if($data){
            $cplan = $this->getCplanByCode($ret["ucode"]);
            if($cplan){
                $data[] = $cplan;
            }
        }
        
       return $data;
    }
    
    /**
     * get cplan by code
     * @param string $ucode
     */
    public function getCplanByCode($ucode){
        $ret = array();
        $type = 0;     
        $title = "";   
//        $type = 2;
//        $title="晚间护理";
        $code = substr($ucode, 0, 2); 
        $date = date("H-w",time());
        $date_arr = explode('-',$date);
        $h = intval($date_arr[0]);
        $w = intval($date_arr[1]);
        
        if($h<= 10 & $h >=4){ //上午4:00-10:00 晨间护理
           $type = 1;
           $title="晨间护理";
        }else if($h>= 20 & $h <= 23 ){ //晚上20:00-23:00 夜间护理
            $type = 2;
            $title="晚间护理";
            if($w == 0){ //是周末，夜间升华护理
                $type = 3;
                $title="晚间升华护理";
            }
        }
        
        if($type==0){
            return $ret;
        }
        
       $dbModel = new ArticledbModel();
       $data = $dbModel->getCplan($code,$type);
       if($data){
           $ret = array(    "doc_id"=>"skin:cplan:".$data["id"],
                            "title"=>$title,
                            "content"=>$data["description"],
                            "thumb"=>$data["thumb"]
                        );
       }
       return $ret;
       
    }
    
    public function getBackstagePushList($uid,$city){
       $data = array();
       $skin = "";
       try{
        $ret = $this->getCharacteByUid($uid);   
       }catch( ApiException $ex){
           throw new ApiException($ex->getCode(),200);
       }
        if(isset($ret)){
            $skin = $ret["type"];
        }         
        
        $doc_id = "skin:weather:".$city;    
        $weather = $this->get($doc_id,false);        
        if($weather){
            $tags = $this->getTagsByComplex($skin, $weather);  
            $data = $this->getPushListByTags($tags);
        }
       return $data;
    }
    
    protected function getTagsByComplex($skin="",$weather){
        $tags = array();
        if($weather){            
            if($weather["uv"] =="弱"){
                if(strpos($skin, "P")!==false){
                    $tags[] = "轻度防晒";
                }
            }else if($weather["uv"] =="中等"){
                $tags[] = "中度防晒";
            }else if(strpos($weather["uv"], "强")!==false){
                $tags[] = "重度防晒";
            }               
            
            if($weather["hum"] <= 40 ){
                $tags[] = "深度补水";
                if(strpos($skin, "S")!==false){
                    $tags[] = "抗敏";
                }
            }else if($weather["hum"] > 40  && $weather["hum"] <= 60){
                if(strpos($skin, "D")!==false){
                    $tags[] = "深度补水";
                }
            }else{
                if(strpos($skin, "S")!==false){
                    $tags[] = "抗敏";
                }
            }
            
             if($weather["tmp"] >25 && (strpos($skin, "O")!==false) ){                 
                 $tags[] = "控油";
             }            
            
            if($weather["pm25"] > 75 && $weather["pm25"] <= 115){
                if(strpos($skin, "O")!==false){
                    $tags[] = "深层清洁";
                }
            }else if($weather["pm25"] > 115){
                $tags[] = "深层清洁";
            }  
        }
        
        if(!$tags){
            $tags[] = "补水";
        }

        return $tags;
    }


    protected function getTodoListByTags($tags){
       $data = array();
       $where = "";
       foreach($tags as $unique){    
           $where .= "title = '".$unique ."' OR ";
       }
       
      if($where){
          $where = substr($where, 0,-3);
      }
       $dbModel = new ArticledbModel();
       $ret = $dbModel->getTodoList("id,title,description,thumb", $where);
       if(!$ret["total_rows"]){
           throw new ApiException(427111,200);
       }
       foreach ($ret["rows"] as $val){
           $data[] = array("doc_id"=>"skin:todo:".$val["id"],"title"=>$val["title"],
                            "content"=>$val["description"],"thumb"=>$val["thumb"]);
       }
       
       if(!$data){
            throw new ApiException(427111,200);
       }       
       return $data;
   }
   
   protected function getPushListByTags($tags){
       $data = array();
       $where = "";
       foreach($tags as $unique){    
           $where .= "title = '".$unique ."' OR ";
       }
       
      if($where){
          $where = substr($where, 0,-3);
      }
       $dbModel = new ArticledbModel();
       $ret = $dbModel->getPushList("id,title,description,thumb", $where);
       if(!$ret["total_rows"]){
           throw new ApiException(427111,200);
       }
       foreach ($ret["rows"] as $val){
           $data[] = array("doc_id"=>$val["id"],"title"=>$val["title"],
                            "content"=>$val["description"],"thumb"=>$val["thumb"]);
       }
       
       if(!$data){
            throw new ApiException(427111,200);
       }       
       return $data;
   }
   
   public function getTodoComplexList($uid,$doc_id){
       $data = array(   
                        "todo"  => array("doc_id"=>$doc_id,"title"=>"","case"=>"","key_point"=>"","suggestion"=>""),                        
                        "products"=>array()
                    );
       $id = str_replace('skin:todo:', '', $doc_id);
       $dbModel = new ArticledbModel();
       $todoRet = $dbModel->getTodoById($id,"id,title,`case`,key_point,suggestion"); 
       if($todoRet){
           $data["todo"] = array(
                                    "doc_id"=>$doc_id,
                                    "title"=>$todoRet["title"],
                                    "case"=>$todoRet["case"],
                                    "key_point"=>$todoRet["key_point"],
                                    "suggestion"=> stripslashes($todoRet["suggestion"])
                                );        
           $title = $todoRet["title"];       
       }
       
       if(isset($title) && $title){
           $title = preg_replace("/(轻|中|重|深)(层|度)/u", "", $title);
           $productModel = new ProductModel();
           $products = $productModel->getSearchListByUid($uid,$title,1,0,1,5);
           $data["products"] = $products["list"];
       }       
       
       return $data;
   }
   
   /**
    * 
    * @param type $uid
    * @param type $doc_id
    * @return type
    */
    public function getCplanComplexList($uid,$doc_id){
       $data = array("doc_id"=>$doc_id,"title"=>"","tips"=>"晚安心语","description"=>"","steps"=>"","plans"=>array());
       $id = str_replace('skin:cplan:', '', $doc_id);
       $dbModel = new ArticledbModel();
       $cplanRet = $dbModel->getCplanDetailById($id); 
       if($cplanRet){
           $data["doc_id"] = $doc_id;
           $data["title"]= $cplanRet["title"];           
           $data["description"] = $cplanRet["description"];
           $title = $cplanRet["title"];     
           if(strpos($title, "晨") !== false){
               $data["tips"] = "早安心语";               
           }else{
               if(strpos($title, '升华') !== false){
                    $plan_title = "晚间升华护理";
               }else{
                   $plan_title = "晚间护理";
               }
           }
           
       }
       
       if(isset($plan_title)){
           $plandbModel = new PlandbModel();
           $plans = $plandbModel->searchPlanByWords($plan_title,"id,title,thumb"); 
        }
       if(isset($plans) && $plans){           
                $planModel = new PlanModel();
                foreach($plans as $val){
                    $plan_id = "skin:plan:".$val["id"];    //dev
//                    $plan_id = "skin:plan:577ddbe8f3cd8"; //local
                    $done = 0;
                    $user_process_id = "skin:plan:process:".$uid;       
                    $process = $this->get($user_process_id,false);  
                    if($process && isset($process["plans"][$plan_id])){
                        $done = $process["plans"][$plan_id]["done"];
                    }
                    
                    $plan = array();
                    $tmpPlan = $planModel->getPlanById($plan_id,$uid,$done);   
                    if($tmpPlan){
                            $plan = array(
                                            "plan_id"=>$tmpPlan["plan_id"],
                                            "title"=>$tmpPlan["title"],
                                            "image"=>$tmpPlan["image"],
                                            "take" => $tmpPlan["take"],
                                            "done" => $done,
                                            "circle" => $tmpPlan["circle"],
                                            "days" => $tmpPlan["days"]
                                        );
                    }
                    $data["plans"][] = $plan;
                }
        }
        
        $cplanSteps = $dbModel->getCplanStepsById($id,"id,step,title,type_id,description");
        if($cplanSteps){
            $productModel = new ProductModel();
            foreach ($cplanSteps as $step) {
                $products["list"] = array();
                $type_title = "";
                if($step["type_id"]){
                    $type_title = $productModel->getTitleById($step["type_id"]);
                    $products = $productModel->getFilterListByFilter($uid, array("type_id"=>$step["type_id"]), 1, 1, 1, 2);
                }else{
                    $step["type_id"] = "";
                }
                $data["steps"][] = array(      
                                                "id"=>"skin:cplan:step:".$step["id"],
                                                "step"=>intval($step["step"]),
                                                "title" => $step["title"],
                                                "type_id" => $step["type_id"],
                                                "type_title" => $type_title,
                                                "description" => $step["description"],
                                                "products"  =>$products["list"]
                                               );
            }
        }
       return $data;
   }
   
   
   /**
    * user collect encyclopedia
    * 
    * @param int $uid
    * @param string $encyclopedia_id
    * @throws ApiException
    */
   public function collect($uid,$encyclopedia_id){
       
       //收藏该产品的用户表
       $doc_id = "skin:encyclopedia:collect:".$encyclopedia_id;
       $ret = $this->get($doc_id,false);
       if($ret){
           $new = $ret;
           if(isset($ret["users"][$uid])){
               throw new ApiException(450013,200);
           }
       }        
       $new["users"][$uid] = $uid;
       
       //该用户收藏的产品表
       $user_collect_list_id = "user:encyclopedia:collect:".$uid;
       $user_collect_ret = $this->get($user_collect_list_id, false);
       if($user_collect_ret){
           $user_collect_data = $user_collect_ret;
       }
       $user_collect_data["encyclopedias"][$encyclopedia_id] = $encyclopedia_id;
       
       //更新
       $dd_data[] = array("doc_id"=>$doc_id,"data"=>$new);
       $dd_data[] = array("doc_id"=>$user_collect_list_id,"data"=>$user_collect_data);
       $this->write($dd_data);
   }
   
   /**
    * user cancel collection 
    * 
    * @param int $uid
    * @param string $encyclopedia_id
    * @throws ApiException
    */
   public function collectCancel($uid,$encyclopedia_id){
       
        //收藏该产品的用户表
        $doc_id = "skin:encyclopedia:collect:".$encyclopedia_id;
        $ret = $this->get($doc_id,false);
        if(! $ret){
            throw new ApiException(450014,200);
        }
       
        $new = $ret;
        if(! isset($ret["users"][$uid])){
            throw new ApiException(450014,200);
        }
        unset($new["users"][$uid]);        
        
       
       //该用户收藏的产品表
       $user_collect_list_id = "user:encyclopedia:collect:".$uid;
       $user_collect_ret = $this->get($user_collect_list_id, false);
       if( $user_collect_ret ){
           $user_collect_data = $user_collect_ret;
           unset($user_collect_data["encyclopedias"][$encyclopedia_id]);
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
   
   public function getColltionList($uid,$page,$size){
       $ids = "";
       $encyclopedias = array();
       $list = array();
       
       $user_collect_list_id = "user:encyclopedia:collect:".$uid;
       try{
           $data = $this->get($user_collect_list_id);
       } catch (ServiceException $ex) {
           return $list;
       }
       
       if($data){           
           foreach($data["encyclopedias"] as $key => $val){
               $key = str_replace("skin:encyclopedia:", "", $key);
               $ids .= "'".$key."',";
           }           
       }
       
       if($ids){
           $ids = substr($ids, 0,-1);
           $sql = "`unique` in ( ". $ids. ")";
           $dbModel = new ArticledbModel();
           $encyclopedias = $dbModel->getEncyclopediaListBySql($sql,$page,$size);
       }
       
       if($encyclopedias["total_rows"]){       
           foreach ($encyclopedias["rows"] as $encyclopedia) {                
               $content = mb_substr(strip_tags($encyclopedia["content"]),0,22,"utf-8")."...";
               $list[] = array(
                                "encyclopedia_id"=>"skin:encyclopedia:".$encyclopedia["unique"],
                                "thumb"=>$encyclopedia["thumb"]?$encyclopedia["thumb"]:$encyclopedia["image"],
                                "title"=>$encyclopedia["title"],
                                "content"=>$content
                            );
               unset($content);
           }
       }       
       
       return $list;
   }
   
   protected function checkUserCollect($uid,$encyclopedia_id){
       $ret = false;
          //该用户收藏的产品表
       $user_collect_list_id = "user:encyclopedia:collect:".$uid;
       $user_collect_ret = $this->get($user_collect_list_id, false);
       if( $user_collect_ret && isset($user_collect_ret["encyclopedias"][$encyclopedia_id])){
           $ret = true;
       }  
       
       return $ret;
   }
   
   public function htmlCollect($uid,$encyclopedia_id){
       $errcode = 0; 
       
       //用户没收藏,添加收藏
       $user_collect_list_id = "user:encyclopedia:collect:".$uid;
       try{           
            $user_collect_ret = $this->get($user_collect_list_id, false); 
       } catch (ServiceException $ex) {           
           try{               
               $ret = $this->collect($uid,$encyclopedia_id);               
           } catch (ApiException $ex) {
               $errcode = 1;
               return $errcode;
           }
       }
       
       //用户收藏过该文章，取消收藏
       if( $user_collect_ret && isset($user_collect_ret["encyclopedias"][$encyclopedia_id])){  
                try{
                    $ret = $this->collectCancel($uid, $encyclopedia_id);                    
                } catch (ApiException $ex) {
                    $errcode = 1;
                    return $errcode;
                }
        }else if( !isset($user_collect_ret["encyclopedias"][$encyclopedia_id]) ){
            try{ 
               $ret = $this->collect($uid,$encyclopedia_id);
            } catch (ApiException $ex) {
                $errcode = 1;
                return $errcode;
            }
        }
        
        return $errcode;        
   }   
   
   
   public function addPush($title,$description="",$content="",$thumb="",$image="",$id=""){  
       $data = array(   "title"=>$title,"description"=>$description,"content"=>$content,
                        "thumb"=>$thumb,"image"=>$image);
       $dbModel = new ArticledbModel();
       
       if(empty($id)){
           $id = uniqid();         
           $data["id"] = $id;
           $dbModel->addPush($data);
       }else{           
           $dbModel->updatePush($data, "id='".$id."'");
       }
       return $id;
   }
   
   public function deletePush($id){
        $dbModel = new ArticledbModel();
        $ret = $dbModel->deletePush($id);
        if(!$ret){
             throw new ApiException(427115,200);
         }
   }
   
   public function getPush($id){
        $dbModel = new ArticledbModel();
        $ret = $dbModel->getPushById($id,"*");
         if(!$ret){
             throw new ApiException(427112,200);
         }
         
         $data = array(
                        "title"=>$ret["title"],"description"=>$ret["description"],
                        "content"=>  stripslashes($ret["content"]),
                        "thumb"=>$ret["thumb"],"image"=>$ret["image"]
                       );
         return $data;
   }
   
   public function getPushList($page=1,$size=10){
        $dbModel = new ArticledbModel();
        $data = $dbModel->getPushListBySql("", $page, $size);
         
         $ret["total"] = $data["total_rows"];
         $ret["page"] = intval($page);
         $ret["size"] = intval($size);
         $ret["data"] = array();
         if(isset($data["rows"]) && $data["rows"]){             
             foreach($data["rows"] as $val){  
                 
                 $tmp["doc_id"] = $val["id"]; 
                 $tmp["title"] = $val["title"];
                 $tmp["thumb"] = $val["thumb"];
                 
                 $ret["data"][] = $tmp;
                 unset($tmp);       
             }            
             
         }
         
         return $ret;
   }
  
   /**
    * delete bbc redundant data temporarily
    *
    * @param int $type
    * @param int $page
    * @param int $size
    */
   public function deleteRawList($type=1,$page=1,$size=10){
       $view_table = "del_raw";
       $condition =  array("doc_type"=>"raw_data");
       if($type ==2){
           $view_table = "del_data";
           $condition =  array("_doc_type"=>"device_data");
       }
       $data = $this->getList($view_table, $condition, $page, $size);
       if($data){
           $doc_ids = "";
            foreach ($data["rows"] as $val) {
                $doc_ids .= $val["doc_id"].",";
            }
            if($doc_ids){
                $doc_ids = substr($doc_ids, 0, -1);
                $this->delete($doc_ids);
            }
       }
       $data["page"] = intval($page);
       $data["size"] = intval($size);
       return $data;
   }
}
