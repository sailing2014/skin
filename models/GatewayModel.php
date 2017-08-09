<?php

namespace App\Models;

use App\Models\UploadModel;
use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Exception\ServiceException;

/**
 *  gateway model
 *
 * @author yang.f
 *        
 */
class GatewayModel extends ServiceBase { 
    public function addGateway($mac, $uid, $bta)
    {
        $nowTime = time();
        $old = $this->getDevice($bta,false);
        $oldUid = 0;
        $update_flag = true;
        if($old){
                $gateway = $old;
                if($gateway["uid"] == $uid){ 
                    $update_flag = false;
                }else{//换用户,更新p2pPwd
                    $oldUid = $gateway["uid"]; 
                    $gateway["uid"] = $uid;                    
                }
                $gateway['bta'] = $bta;
        }else{
             // 第一次绑定
            $gateway = array(
                'uid' => $uid,  
                'bta' => $bta,
                'name' => 'Skin Camera',
                'adminUid' => $uid,
                'macAddress' => $mac,
                'password' => '',
                'groupId' => '',
                'status' => 1,
                'developerId' => 0,
                'hwVersion' => '',
                'fwVersion' => '',
                'registerTime' => $nowTime,
                'lastLoginTime' => $nowTime,
                'p2pId' => 'admin',
                'p2pUid' => '',
                'p2pPwd' => ''
            );
           
//            $gateway['p2pUid'] = $this->applyP2PUid($mac);
            $this->setBindingStatus($uid, $bta);
        }
        
//       if($update_flag){
//           $gateway['p2pPwd'] = rand(10000000, 99999999);            
//       }       
       $this->setGateway($bta,$gateway,$oldUid);
       
       return $gateway;
    }
    
    protected function setGateway($bta,$gateway,$oldUid=0){
        try{
            $this->setDevice($bta, $gateway);
        } catch (ServiceException $se) {
            throw new ApiException(500, 500, ":" . $se->getMessage());
        }
        
        // 记录用户deviceId列表
        $userDeviceIdsKey = 'SkinUserDeviceIds::' . $gateway['uid'];
        $DeviceIds = $this->get($userDeviceIdsKey, false);
        $data = array();
        if($DeviceIds){
            $data = $DeviceIds;            
        }
        $data[$bta] = array("bta"=>$bta); 
        $this->set($userDeviceIdsKey, $data );
        
        if($oldUid){
            $oldUserDeviceIdsKey = 'SkinUserDeviceIds::' . $oldUid;
            $oldUserDeviceIds = $this->get($oldUserDeviceIdsKey, false);
            if($oldUserDeviceIds && isset($oldUserDeviceIds[$bta])){
                unset($oldUserDeviceIds[$bta]);
                if($oldUserDeviceIds){
                    $this->set($oldUserDeviceIdsKey, $oldUserDeviceIds);
                }else{
                    $this->delete($oldUserDeviceIdsKey);
                }
            }
        }
        
    }
    
    public function getGateway($bta){
        $bta = strtolower($bta);
        try{
            $gateway = $this->getDevice($bta);
        } catch (ServiceException $se) {
            if( ($se->getCode() == 7111) || ($se->getCode() == 5046) ){
                throw new ApiException(427111,400);
            }else{
                throw new ApiException(400,400);
            }
        }
        
        return $gateway;
    }
    

    private function applyP2PUid($macAddress)
    {        
        $url = $this->api['device.apply_p2p_uid'];
        try{
            $p2pUid = $this->sendHttpRequest( $url,array('mac_address' => $macAddress) );
        }catch(App\Exception\ServiceException $se){
            if($se->getCode() == 4043){
                throw new ApiException(4043, 400, "Gateway has been registered");
            }else{
                throw new ApiException(500, 500, ":" . $se->getMessage());
            }
        }
        
        return $p2pUid["data"]["p2p_uid"];
    }
    
    /**
     * reset gateway
     * 
     * @param string $did device id,namely bta
     */
    public function resetGateway($did){
        
        try{
              $device = $this->getDevice($did);
          } catch (ServiceException $ex) {
              throw new ApiException(427115,200);
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
        $userDeviceIdsKey = 'SkinUserDeviceIds::' . $device["uid"];
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

    protected function setBindingStatus($uid, $bta)
    {
        $key = "BINDING::$uid";
        $this->setCache($key, array(
            'step' => 1,
            'did' => $bta
            ), 120);
    }
    
    public function getBindingStatus($uid)
    {
        $key = "BINDING::$uid";
        $status = $this->getCache($key,FALSE);
        if (!$status) {
            $status = array(
                'step' => 0,
                'did' => ''
            );
        } else {
            $this->deleteCache($key);
        }
        return $status;
    }
    

    /**
     * 
     * @param string $bta blue tooth address 
     * @param int $type light type. 1-->white light(白光),2-->polarized light(偏振光),3--> ultraviolet light(紫外光)    
     * @param int $body_part  body_part. 1 is forehead(额头), 2 is canthus(眼角),3 is cheek(脸颊),4 is nose(鼻翼)
     * @param int $auto  photograph way,default is 0. 0 is manual way(手动),1 is automatic way(全自动).
     * @param int $time    
     * @param string $file file path
     * @param float $water skin water value
     */
    public function addImage($bta, $type,$body_part,$auto,$time, $file,$water=0)
    {   
        $gateway = $this->getGateway($bta);
        $uid = $gateway["uid"];
        if( ($auto == 0) && ($type == 1)){//手动拍摄，是白光,记录时间戳,即一次手动拍摄的开始时间
            $this->setTypeOneTime($bta,$auto,$body_part,$time);
        }else if(($auto == 1) && ($type == 1) && ($body_part == 1) ){ //自动拍摄，是白光，并且是第一个拍摄的部位，即一次自动拍摄的开始时间
            $this->setTypeOneTime($bta,$auto,$body_part,$time);
        }else{
            $time = $this->getTypeOneTime($bta,$auto,$body_part,$time);
        }
        
        //body part record
        $doc_id = "skin:raw:".$uid.":".$bta.":".$body_part.":".$time;
        $orig = $this->get($doc_id, FALSE);        
        $new = array();
        if($orig){
            $new = $orig;
        }else{
            $new["uid"] = $uid;   
            $new["did"] = $bta;
            $new["body_part"] = $body_part;
            $new["score"] = 0;
            $new["time"] = $time; 
        }
        
        $results = $this->skinDetect($type,$file); 
        $this->addImageRecord($uid,$bta, $type,$body_part,$auto,$time,$results);
        if($water && ($auto == 0) && ($type == 3) ){//手动上传最后一种光上传水分
        //更新type=1算出的水油值
            $this->updateImageRecord($uid,$bta, $type,$body_part,$auto,$time, $water);
        }else{
            $water = 0;
        }
        
        if($results){           
            if(isset($new["types"])){                
                $new["types"] = $this->merge($new["types"], $results,$water);                
            }else{
                    $new["types"] = $results;
                }
        }
       
        if( count($new["types"]) == 5 ){
            $new["score"] = 0;
            foreach ($new["types"] as $val) {
                switch ($val["type"]) {
                    case 1:
                    case 3:
                    case 4:
                        $new["score"] += 0.2 * $val["score"];
                        break;
                    case 2:
                        $new["score"] += 0.1 * $val["score"];
                        break;
                    case 5:
                        $new["score"] += 0.3 * $val["score"];
                        break;
                    default:
                        break;
                }
            }
            $new["score"] = round($new["score"], 2);
        }
        
        $new["doc_type"] = "skin_raw";
        $this->set($doc_id,$new);
                
        //全脸拍摄
        if($auto == 1 && $body_part == 4 && $type == 3){
            $new = $this->autoAddImage($bta,$uid,$time);            
        } 
                
            
        //记录最新latest record
        $latest_id = "skin:raw:latest:".$uid.":".$bta;
        $new["doc_type"] = "skin_latest";
        $latest_data = $new;
        $latest_dd_param[] = array("doc_id"=>$latest_id,"data"=>$latest_data);
          
        //记录部位最新latest body part record
        $latest_body_part_id = "skin:raw:latest:".$uid.":".$bta.":".$body_part;
        $latest_body_part_data = $new;
        $latest_body_part_data["body_part"] = $body_part;
        $latest_body_part_data["doc_type"] = "skin_body_part_latest";
        $latest_dd_param[] = array("doc_id"=>$latest_body_part_id,"data"=>$latest_body_part_data);
        
        $this->write($latest_dd_param);
        
        return $results;
    }
       
    private function merge($types,$results,$water){
        $alltypes = array_merge($types,$results);
        $tmp = array();
        $new = array();
        foreach($alltypes as $val){
            $tmp[$val["type"]] = $val["score"];
        }
        
        if($tmp){           
            foreach ($tmp as $key => $value){
                if( $water &&($key == 1)){//如果是1，水油平均值，则要加上水分值计算，之前存的只是油分值
                    $value = round( (0.5*$value + 0.5*$water),2);
                }
                $new[] = array("type"=>$key,"score"=>$value);
            }
        }
               
        return $new;
    }
    private function autoAddImage($bta,$uid,$time){
        
        $types = array();        
        
        $ids = "";
        for($i=1;$i<=4;$i++){                         
            $ids .= "skin:raw:".$uid.":".$bta.":".$i.":".$time.",";
        }
        $ids = substr($ids, 0,-1);
        $id_arr = explode(',', $ids);
        $ret = $this->getMultiple($id_arr,false);
        
        if($ret){
            
            $score["type1"] = $score["type2"] = $score["type3"] = $score["type4"] = $score["type5"] = 0;
            foreach($ret as $val){
                foreach ($val["types"] as $type){                    
                        $score["type".$type["type"]] += $type["score"];                   
                }
            }
          
            for($i=1;$i<=5;$i++){
                $types[] = array("type"=>$i, "score" => round( ($score["type".$i]) / 4, 2 ) ); 
            } 
        }
                
        
        $new["uid"] = $uid;     
        $new["did"] = $bta;
        $new["body_part"] = 0;
        $new["score"] = 0;
        $new["time"] = $time;  
        $new["types"] = $types;
        
        if( count($new["types"]) == 5 ){
            foreach ($new["types"] as $val) {
                switch ($val["type"]) {
                    case 1:
                    case 3:
                    case 4:
                        $new["score"] += 0.2 * $val["score"];
                        break;
                    case 2:
                        $new["score"] += 0.1 * $val["score"];
                        break;
                    case 5:
                        $new["score"] += 0.3 * $val["score"];
                        break;
                    default:
                        break;
                }
            }
        }
        
        $new["score"] = round($new["score"], 2);
        $new["doc_type"] = "skin_raw";
        $doc_id = "skin:raw:".$uid.":".$bta.":0:".$time;        
        $latest_dd_param[] = array("doc_id"=>$doc_id,"data"=>$new);
        
        //记录部位最新
        $latest_body_part_id = "skin:raw:latest:".$uid.":".$bta.":0";
        $latest_body_part_data = $new;
        $latest_body_part_data["doc_type"] = "skin_body_part_latest";
        $latest_dd_param[] = array("doc_id"=>$latest_body_part_id,"data"=>$latest_body_part_data);
        
        $this->write($latest_dd_param);        
        return $new;
    }
    
    private function skinDetect($light_type,$filename){
      $data = $results = array();
      
       switch ($light_type) {
           case 1:
               $data = array(
                                "type1"=>round(calcSkinFeature($filename, 1),2),
                                "type4"=>round(calcSkinFeature($filename, 4),2),
                                "type5"=>round(calcSkinFeature($filename, 5),2),
                                "type6"=>round(calcSkinFeature($filename, 6),2),
                                "type9"=>round(calcSkinFeature($filename, 9),2)
                            );
               $score1 = $data["type1"];
               $score2 = round((0.2*$data["type5"] + 0.3*$data["type4"] + 0.5*$data["type6"]),2);
               $results[] = array("type"=>1,"score"=>$score1);
               $results[] = array("type"=>2,"score"=>$score2);
               break;
           case 2:
               $data = array(
                                "type7"=>round(calcSkinFeature($filename, 7),2),
                                "type8"=>round(calcSkinFeature($filename, 8),2)
                            );
               $score4 = $data["type7"];
               $score5 = $data["type8"];
               $results[] = array("type"=>4,"score"=>$score4);
               $results[] = array("type"=>5,"score"=>$score5);
               break;
           case 3:
               $data = array(
                                "type2"=>round(calcSkinFeature($filename, 2),2),
                                "type3"=>round(calcSkinFeature($filename, 3),2)
                            );
               $score3 = round((0.7*$data["type2"] + 0.3*$data["type3"]),2);
               $results[] = array("type"=>3,"score"=>$score3);
               break;
           default:               
               break;
       }
       
       return $results;
    }
   
    /**
     * part type record "e.g"{ "uid": 10000002, "bta": "207320000011", "type": 1, "body_part": 1,
                    "auto": 0,  "time": 1477373969, "url": "http://oss-qiwo-dev.qiwocloud1.com/skin/user/580f02747b4e9.png",
                    "results": [{ "type": 1,"score": 90},{"type": 2,"score": 84.7}],
                    "create_at": 1477378677,"doc_type": "skin:analytics", "_ser_type_": "devicedata"}
     * @param type $uid
     * @param type $bta
     * @param type $type
     * @param type $body_part
     * @param type $auto
     * @param type $time
     * @param type $results
     * @return type
     */  
   public function addImageRecord($uid,$bta, $type,$body_part,$auto,$time, $results){
       $data = array();
       $uploadModel = new UploadModel();
       $url = $uploadModel->uploadImage();
       if($url){
           $doc_id = "record:".$uid.":".$bta.":".$type.":".$body_part.":".$auto.":".$time;
           $data = array(
                            "uid" => intval($uid),
                            "bta" => $bta,
                            "type" => intval($type),
                            "body_part" => intval($body_part),
                            "auto" => intval($auto),
                            "time" => intval($time),
                            "url" => $url,
                            "results" => $results,
                            "create_at" => time(),
                            "doc_type" => "skin:analytics"
                        );
            $this->set($doc_id, $data);
       }
       return $url;
   }
  
  /**
   * update part record 
   * 
   * update score when type =1 ,water value added
   * 
   * @param type $uid
   * @param type $bta
   * @param type $type
   * @param type $body_part
   * @param type $auto
   * @param type $time
   * @param type $water
   */
   public function updateImageRecord($uid,$bta, $type,$body_part,$auto,$time, $water){  
            $doc_id = "record:".$uid.":".$bta.":1:".$body_part.":".$auto.":".$time;
            $old = $this->get($doc_id, false);
            if($old){   
                $results = $old["results"];
                if($results){
                    foreach ($results as $key => $val) {
                        if($val["type"] == 1){
                            $results[$key]["score"] = round((0.5*$val["score"] + 0.5*$water),2);
                        }
                    }
                }
                $old["results"] = $results;
                $this->set($doc_id, $old);
            }
   }
   /**
    * Store timestamp which type is 1
    * store white light timestamp
    * @param string $bta
    * @param int $auto
    * @param int $body_part
    * @param int $time
    */
   private function setTypeOneTime($bta,$auto,$body_part,$time){  
        $doc_id = "skin:snapshot:time:".$bta.":".$auto.":".$body_part;
        $data = array("bta"=>$bta,"auto"=>$auto,"body_part"=>$body_part,"time"=>$time,"create_at"=>time());
        $this->setCache($doc_id, $data);
   }
   
   private function getTypeOneTime($bta,$auto,$body_part,$time){
      
       if($auto){
            $doc_id = "skin:snapshot:time:".$bta.":".$auto.":1";
       }else{            
            $doc_id = "skin:snapshot:time:".$bta.":".$auto.":".$body_part;
       }
       
       $data = $this->getCache($doc_id,false);
       if($data){
           $time = $data["time"];
       }
       return $time;
   }
}
