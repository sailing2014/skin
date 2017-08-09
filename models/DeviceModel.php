<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Exception\ServiceException;

/**
 *  device model
 *
 * @author yang.f
 *        
 */
class DeviceModel extends ServiceBase { 
       
   public function getDeviceList($uid){
       $doc_id = 'SkinUserDeviceIds::' . $uid;
       $data = array();
       
       try{
           $deviceIds = $this->get($doc_id);
       } catch (ServiceException $ex) {
           return $data;
       }
       
       foreach ($deviceIds as $val) {
           $id[] = "device::".$val["bta"];
       }
       
       if( isset($id) && !empty($id)){
           $devices = $this->getDevices($id,false);
       }
       
       if(isset($devices) && $devices){
           foreach($devices as $val){
               $data[] = array(
                                    "did"=> $val["bta"],
                                    "adminUid"=> $val["adminUid"],
                                    "macAddress"=> $val["macAddress"],
                                    "name"=> $val["name"],  
                                    "password"=> $val["password"],
                                    "status"=> $val["status"],
                                    "hwVersion"=> $val["hwVersion"],
                                    "fwVersion"=> $val["fwVersion"],
                                    "registerTime"=> $val["registerTime"],
                                    "lastLoginTime"=> $val["lastLoginTime"],            
                                    "p2pUid"=> $val["p2pUid"],
                                    "p2pPwd"=> $val["p2pPwd"]
                                );
               }
       }
       
       return $data;
   }  
    
  public function updateName($uid,$did, $name){
       try{
           $device = $this->getDevice($did);
       } catch (ServiceException $ex) {
           throw new ApiException(427113,200);
       }
       
       if(isset($device["adminUid"]) && ($uid == $device["adminUid"]) ){
          
      }else{
          throw new ApiException(491001,200);
      }
       
        $device["name"] = $name;
        $this->setDevice($did, $device);
          
  }



  public function deleteDevice($uid,$did){
      
      try{
          $device = $this->getDevice($did);
      } catch (ServiceException $ex) {
          throw new ApiException(427115,200);
      }
      
      if(isset($device["uid"]) && $uid == $device["uid"]){
          
      }else{
          throw new ApiException(491001,200);
      }
      
      //回收p2pUid
      if (isset($device["p2pUid"]) && !empty($device["p2pUid"]) ) {
                    $old_p2p_uid = $device["p2pUid"];
                    $macAddress = strtoupper($device["macAddress"]);
                    $param = array("mac_address" => $macAddress, "recycle" => 1, "old_p2p_uid" => $old_p2p_uid);
                    $url = $this->api['device.apply_p2p_uid'];  
                    try{                                              
                            $this->sendHttpRequest($url, $param,TRUE,"POST",TRUE,array(),10);  
                    }catch(App\Exception\ServiceException $se){
                            throw new ApiException(491002,200);
                    }
                    
        }       
      
      
      // 更新用户deviceId列表
        $userDeviceIdsKey = 'SkinUserDeviceIds::' . $uid;
        $userDeviceIds = $this->get($userDeviceIdsKey,false);
        if ( isset($userDeviceIds[$did])) {
            
            unset($userDeviceIds[$did]);
            
            if($userDeviceIds){
                    $this->set($userDeviceIdsKey, $userDeviceIds);
            }else{
                    $this->delete($userDeviceIdsKey);
            }       
            
        }      
        
        $this->removeDevice($did);
  }
   
  public function getLatestRecord($uid,$bta,$body_part=-1){     
       $ret = array(
                            "did"=>$bta,"body_part"=>$body_part,"score"=>0,"time"=>time(),
                            "previous"=>0,"next"=>0,
                            "types"=>array(
                                        array("type"=>1,"score"=>0),array("type"=>2,"score"=>0),
                                        array("type"=>3,"score"=>0),
                                        array("type"=>4,"score"=>0),array("type"=>5,"score"=>0)
                                        )
                        );
       if($body_part == -1){
            $latest_id = "skin:raw:latest:".$uid.":".$bta;
       }else{
           $latest_id = "skin:raw:latest:".$uid.":".$bta.":".$body_part;
       }
//      $latest_id = "skin:raw:latest:10000015:207320000005";
      try{
          $data = $this->get($latest_id);  
          $ret["body_part"] = $data["body_part"];
          $ret["score"] = $data["score"];
          $ret["time"] = $data["time"];
          $ret["types"] = $this->initTypes($data["types"]);
          
          $threeRecords = $this->getThreeRecord($uid, $data["did"], $data["body_part"], $data["time"]);
          $ret["previous"] = $threeRecords["list"]["previous"]["time"];
          $ret["next"] = $threeRecords["list"]["next"]["time"];
      } catch (ServiceException $ex) {
         
      }      
      
      
      return $ret;
  }
  
  /**
   * init type value 0
   * @param array $types
   * @return int
   */
  private  function initTypes($types=array()){
      $ret = $types;
      if(count($types) < 5){
          $exist_types = array();
          foreach($types as $val){
              $exist_types[] = $val["type"];
          }          
          $unexist_types = array_diff(array(1,2,3,4,5),$exist_types);          
          foreach($unexist_types as $v){
              $ret[] = array("type"=>$v,"score"=>0);
          }
      }
      
      return $ret;
  }


  public function getRecord($uid,$did,$body_part,$time){
       $ret = array(
                            "did"=>$did,"body_part"=>$body_part,"score"=>0,"time"=>$time,
                            "previous"=>0,"next"=>0,
                            "types"=>array(
                                        array("type"=>1,"score"=>0),array("type"=>2,"score"=>0),
                                        array("type"=>3,"score"=>0),
                                        array("type"=>4,"score"=>0),array("type"=>5,"score"=>0)
                                        )
                        );
       
      $doc_id = "skin:raw:".$uid.":".$did.":".$body_part.":".$time;
      try{
          $data = $this->get($doc_id);
          $ret["body_part"] = $data["body_part"];
          $ret["score"] = round($data["score"],2);
          $ret["time"] = $data["time"];
          $ret["types"] = $this->initTypes($data["types"]);
          
          $threeRecords = $this->getThreeRecord($uid, $data["did"], $data["body_part"], $data["time"]);
          $ret["previous"] = $threeRecords["list"]["previous"]["time"];
          $ret["next"] = $threeRecords["list"]["next"]["time"];
      } catch (ServiceException $ex) {
          
      }          
      
      return $ret;
  }
  
  public function getThreeRecord($uid,$did,$body_part,$time,$pre=2){   
      $three = array();
      $three["previous"] =  array("time"=>0,"types"=>array());
      $three["current"]= array("time"=>$time,"types"=>array());
      $three["next"] = array("time"=>0,"types"=>array());
      $records = array("did"=>$did,"body_part"=>0,"score"=>0,"time"=>$time,"list"=>$three);
         
      $view = "raw_list";
      $condition = array("uid"=>$uid,"did"=>$did,"body_part"=>$body_part);  
      $date = date('Y-m-d', $time);
      $date_arr = explode('-', $date);      
      $start = mktime(0,0,0,$date_arr[1],$date_arr[2],$date_arr[0]);      
      $end = $start  +  2*(24*3600);
      $start -= 2*24*3600;
      $condition["time"] = array("start"=>$start,"end"=>$end);
      $ret = $this->getList($view, $condition);
      
      if(! $ret["total_rows"]){     
          $records["list"]["current"]["types"] = $this->initTypes();
          return $records;
      }
      
      
      foreach ($ret["rows"] as $key => $val) {
          if($val["value"]["time"] == $time){
              $index = $key;
              break;
          }         
      }
      
      if(! isset($index)){
           $records["list"]["current"]["types"] = $this->initTypes();
           return $records;
      }else{
          $current_tmp = $ret["rows"][$index]["value"];
          $records["list"]["current"]["time"] = $current_tmp["time"];
          $records["list"]["current"]["types"] = $this->initTypes($current_tmp["types"]);
      }
            
      if($ret["total_rows"] > ($index+1)){ 
        $previous_tmp = $ret["rows"][$index+1]["value"];
        $records["list"]["previous"]["time"] = $previous_tmp["time"];
        $records["list"]["previous"]["types"] = $this->initTypes($previous_tmp["types"]);
      }
     
     if($index){
            $next_tmp = $ret["rows"][$index-1]["value"];
            $records["list"]["next"]["time"] = $next_tmp["time"];
            $records["list"]["next"]["types"] = $this->initTypes($next_tmp["types"]);          
     }
     
     return $records;
  }
  
  public function getPreRecord($uid,$did,$body_part,$time,$pre=1){      
      $data = array();
      $init = array("did"=>$did,"body_part"=>$body_part,"score"=>0,"time"=>$time,
                        "types"=>array(
                                    array("type"=>1,"score"=>0),array("type"=>2,"score"=>0),
                                    array("type"=>3,"score"=>0),array("type"=>4,"score"=>0),
                                    array("type"=>5,"score"=>0)
                                 )
                    );
      
      $view = "raw_list";
      $condition = array("uid"=>$uid,"did"=>$did,"body_part"=>$body_part);  
      
      $date = date('Y-m-d', $time);
      $date_arr = explode('-', $date);      
      $start = mktime(0,0,0,$date_arr[1],$date_arr[2],$date_arr[0]);      
      $end = $start  +  2*(24*3600);
      $start -= 2*24*3600;
      $condition["time"] = array("start"=>$start,"end"=>$end);
      $ret = $this->getList($view, $condition);
      
      if(! $ret["total_rows"]){         
          return $init;
      }
      
      
      foreach ($ret["rows"] as $key => $val) {
          if($val["value"]["time"] == $time){
              $index = $key;
              break;
          }         
      }
      
      if(! isset($index)){
           return $init;
      }
            
      if($pre == -1){
           if($ret["total_rows"] > ($index+1)){ 
                $data = $ret["rows"][++$index]["value"];
           }
      }else{
            if($index){
              $data = $ret["rows"][--$index]["value"];
            }
     }
     
     if(!$data){            
          return $init;
     }
     
     unset($data["uid"],$data["doc_type"]);
     return $data;
      
  } 

  
    public function getDailyList($uid,$did,$body_part,$type,$time,$page,$size){
      $data = array();
      
      $view = "raw_list";
      $condition = array("uid"=>$uid,"did"=>$did,"body_part"=>$body_part);  
      
      $date = date('Y-m-d', $time);
      $date_arr = explode('-', $date);      
      $start = mktime(0,0,0,$date_arr[1],$date_arr[2],$date_arr[0]);      
      $end = $start  +  (24*3600);
      
      $condition["time"] = array("start"=>$start,"end"=>$end);
      $ret = $this->getList($view, $condition,$page,$size);
      
      if(! $ret["total_rows"]){           
            return array(
                            "did"=>$did,"body_part"=>$body_part,"type"=>$type,
                            "records"=>array(array("score"=>0,"time"=>$time))
                         );                   
      }      
      
      if($type==0){
        foreach ($ret["rows"] as $val) {
           $data[] = array("score"=>$val["value"]["score"],"time"=>$val["value"]["time"]);      
        }
      }else{
          foreach ($ret["rows"] as $val) {              
            foreach($val["value"]["types"] as $f){
                if($f["type"] == $type){
                    $data[] = array("score"=>$f["score"],"time"=>$val["value"]["time"]);
                }
            } 
        }
      }
      
      if(!$data){
           $data = array(array("score"=>0,"time"=>$time));
      }      
      
      return array("did"=>$did,"body_part"=>$body_part,"type"=>$type,"records"=>$data);
    }
    
    public function getDailyAvgList($uid,$did,$body_part,$type,$time){
      $data = array("score"=>0,"time"=>$time);
      $sum = 0;
      
      $view = "raw_list";
      $condition = array("uid"=>$uid,"did"=>$did,"body_part"=>$body_part);  
      
      $date = date('Y-m-d', $time);
      $date_arr = explode('-', $date);      
      $start = mktime(0,0,0,$date_arr[1],$date_arr[2],$date_arr[0]); 
      $end =  $start + 24*3600;
            
      $condition["time"] = array("start"=>$start,"end"=>$end);
      $ret = $this->getList($view, $condition,1,100);
      
      if(! $ret["total_rows"]){
          return $data;
      }      
      
      if($type==0){
        foreach ($ret["rows"] as $val) {
           $sum += $val["value"]["score"];      
        }
      }else{
          foreach ($ret["rows"] as $val) {              
            foreach($val["value"]["types"] as $f){
                if($f["type"] == $type){
                    $sum += $f["score"];
                }
            } 
        }
      }
      
      if( !$sum ){
          return $data;
      } 
      
      $data["score"] = round( $sum / count($ret["rows"]), 2);
      
      return $data;
    }
    
    public function getAvgList($uid,$did,$body_part,$type,$circle,$time){
        $data = array();
        
        for( $i = 0; $i < 7; $i++){            
            $data[] = $this->getDailyAvgList($uid, $did, $body_part, $type, $time);
            $time -= 24*3600;
        }
        
        if($circle==2){
            for($i=0;$i<21;$i++){                
                $data[] = $this->getDailyAvgList($uid, $did, $body_part, $type, $time);
                $time -= 24*3600;
            }
        }       
     
        return array("did"=>$did,"body_part"=>$body_part,"type"=>$type,"circle"=>$circle,"time"=>$time,"average"=>$data);
       
    }
}
