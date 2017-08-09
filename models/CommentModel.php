<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Models\UserModel;
use App\Models\ArticleModel;
use App\Models\Mysql\ArticledbModel;
use App\Models\Mysql\ProductdbModel;

use App\Exception\ApiException;
use App\Exception\ServiceException;
/**
 *  comment model
 *
 * @author yang.f
 *        
 */
class CommentModel extends ServiceBase { 
    public function addArticleComment($encyclopedia_id,$uid,$content){        
        //更新文章comments数量
        $id = str_replace("skin:encyclopedia:", "", $encyclopedia_id);
       
        $dbModel  =  new ArticledbModel();
        $ret = $dbModel->getEncyclopediaById($id);       
        if(!$ret){
            throw new ApiException(427113,200," this article doesn't exist! ");
        } 
        $dbModel->updateEncyclopediaField($id, "comments");
        
        //存评论内容
        $comment_id = "skin:comment:".uniqid();
        $comment_array = array("comment_id"=>$comment_id,"uid"=>$uid,"content"=>$content,"time"=>time());
        $this->set($comment_id, $comment_array);
        
        //更新评论列表
        $comments_id = "skin:commemts:".$encyclopedia_id;
        $old = $this->get($comments_id,false);
        $list = array();
        if($old){
            $list = $old;
        }else{
            $list["encyclopedia_id"] = $encyclopedia_id;
        }        
        $list["comment_ids"][] = $comment_id;
        $this->set($comments_id, $list); 
        
        return $comment_id;
    }
    
    public function getComments($doc_id,$uid=0,$page=1,$size=5){
        $comments_id = "skin:commemts:".$doc_id;
        $list = $this->get($comments_id,false);       
        $data = array();
        
        if(!$list){            
            throw new ApiException(465001,400);
        }        
        
        $list["comment_ids"] = array_reverse($list["comment_ids"]);       
        
        $count = count($list["comment_ids"]);
        
        $tmplist = array();
        if( $page*$size <= $count ){
            for( $i= ($page-1)*$size ; $i< ($page * $size); $i++){
                $tmplist[] = $list["comment_ids"][$i];
            }
        }else if( ($page-1)*$size <= $count){
            for( $i= ($page-1)*$size ; $i < $count; $i++){
                $tmplist[] = $list["comment_ids"][$i];
            }
        }
        
        if(!$tmplist){            
            throw new ApiException(465001,400);
        } 
        
        $userModel = new UserModel();
        foreach($tmplist as $val){
                $tmp = $this->get($val,false);
                if($tmp){
                    try{
                        $user = $userModel->getUserByUid($tmp["uid"]);                     
                    }  catch (ServiceException $se){
                        $user = array("nickname"=>"匿名","image"=>"");                        
                    }
                    $convertTime = $this->convertTime($tmp["time"]);       
                    
                    $shift = array("comment_id"=>$tmp["comment_id"],"uid"=>$user["uid"],
                                    "nickname"=>$user["nickname"],"image"=>$user["image"],
                                    "content"=>$tmp["content"],"time"=>$convertTime
                                  );
                    
                    if($uid){
                       $shift["favnum"] =  isset($tmp["favnum"])?$tmp["favnum"]:0;
                       $shift["fav"] = $this->favCheck($uid, $val);
                       
                       $articleModel = new ArticleModel();
                       $description = "";
                        try{                            
                            $user2 = $articleModel->getCharacteByUid($user["uid"]);
                            $description = $user2["description"];
                        } catch (ApiException $ex) {
                            
                        }
                        $shift["skin"] = str_replace(',', '.', $description);
                    }
                    
                     $data[] = $shift;
                     
                     unset($user,$convertTime);
                }
                unset($tmp);
        }
        
        
        return $data;
    }
      
     public function addProductComment($product_id,$uid,$content){ 
         $id = str_replace('skin:product:', '', $product_id);
         $dbModel = new ProductdbModel();
         $product = $dbModel->getProductById($id);
         if(!$product){
               throw new ApiException(427114,400," this product doesn't exist! ");
         }       
        
        //存评论内容
        $comment_id = "skin:comment:".uniqid();
        $comment_array = array("comment_id"=>$comment_id,"uid"=>$uid,"content"=>$content,"time"=>time());
        $this->set($comment_id, $comment_array);
        
        //更新评论列表
        $comments_id = "skin:commemts:".$product_id;
        $old = $this->get($comments_id,false);
        $list = array();
        if($old){
            $list = $old;
        }else{
            $list["product_id"] = $product_id;
        }        
        $list["comment_ids"][] = $comment_id;
        $this->set($comments_id, $list); 
        
        return $comment_id;
    }  
    
    public function getHtmlComments($uid,$product_id,$page,$size){        
        try{
            $data = $this->getComments($product_id,$uid,$page,$size);
        } catch (ApiException $ex) {
            $data = array();
        }
        return $data;
    }
   
    public function htmlFav($uid,$comment_id){
       $errcode = 0; 
       
       $user_fav_list_id = "user:comment:fav:".$uid;                  
       $user_collect_ret = $this->get($user_fav_list_id, false);        
       
       if($user_collect_ret && isset($user_collect_ret["data"][$comment_id])){
           //用户赞过，取消赞
           $errcode = $this->favCancel($uid, $comment_id);            
       }else{
           //用户没赞,添加赞
           $errcode = $this->fav($uid, $comment_id);
       }     
       
       return $errcode;
   }
   
   protected function fav($uid,$doc_id){
       $list_doc_id = "skin:comment:fav:".$doc_id;
       $user_fav_list_id = "user:comment:fav:".$uid;
       
       try{
            $comment = $this->get($doc_id);
       }catch(App\Exception\ServiceException $se){
           return 1;
       }
       
       if($comment){
           $comment["favnum"] = isset($comment["favnum"])?++$comment["favnum"]:1;
            if($comment["favnum"] <= 0){
               $comment["favnum"] = 1;
           }
       }
       
       $list = $this->get($list_doc_id,false);
       if($list){
           $new_list = $list;
       }
       $new_list["doc_id"] = $doc_id;
       $new_list["users"][$uid] = $uid;
       
       
       $user_fav_list = $this->get($user_fav_list_id,false);
       if($user_fav_list){
           $new_user_fav_list = $user_fav_list;
       }
       $new_user_fav_list["uid"] = $uid;
       $new_user_fav_list["data"][$doc_id] = $doc_id;
       
       $param[] = array("doc_id"=>$doc_id,"data"=>$comment);
       $param[] = array("doc_id"=>$list_doc_id,"data"=>$new_list);
       $param[] = array("doc_id"=>$user_fav_list_id,"data"=>$new_user_fav_list);
       
       $this->write($param);
       
       return 0;
   }
   
   protected function favCancel($uid,$doc_id){
       $list_doc_id = "skin:comment:fav:".$doc_id;
       $user_fav_list_id = "user:comment:fav:".$uid;
       
       try{
            $comment = $this->get($doc_id);
       }catch(App\Exception\ServiceException $se){
           return 1;
       }
       
       if($comment){
           $comment["favnum"] = isset($comment["favnum"])?--$comment["favnum"]:0;
           if($comment["favnum"] < 0){
               $comment["favnum"] = 0;
           }
       }
       
       $list = $this->get($list_doc_id,false);
       if($list && isset($list["users"][$uid])){
           unset($list["users"][$uid]);
       }    
       
       $user_fav_list = $this->get($user_fav_list_id,false);
       if($user_fav_list && isset($user_fav_list["data"][$doc_id])){
           unset($user_fav_list["data"][$doc_id]);
       }       
       
       $param[] = array("doc_id"=>$doc_id,"data"=>$comment);
       $param[] = array("doc_id"=>$list_doc_id,"data"=>$list);
       $param[] = array("doc_id"=>$user_fav_list_id,"data"=>$user_fav_list);
       
       $this->write($param);
       
       return 0;
   }
   
   protected function favCheck($uid,$doc_id){
       $check = 0;
       
       $user_fav_list_id = "user:comment:fav:".$uid;
       $user_fav_list = $this->get($user_fav_list_id,false);
       if($user_fav_list && isset($user_fav_list["data"][$doc_id])){
           $check = 1;
       }  
       
       return $check;
   }
       
}
