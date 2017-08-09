<?php

namespace App\Controllers;

use App\Models\DeviceModel;
use App\Models\GatewayModel;
use App\Exception\ApiException;

class DeviceController extends ControllerBase
{
    
    /**
     * add device 
     * 
     * POST /v1/device
     */
    public function addDeviceAction(){
        
        $user = $this->checkToken();
        
        $fields = array(
            'mac' => 'required',
            'bta' => 'required'
        );
        $this->checkParams($fields);        
        
        $macAddress = $this->getJsonParam('mac');
        $uid = $user["uid"];
        $bta = strtolower($this->getJsonParam('bta'));
        
        $gatewayModel = new GatewayModel();
        $gatewayModel->addGateway($macAddress, $uid, $bta);
        $this->responseJson(0);
    }
    
    /**
     * get device
     * 
     * GET /v1/device
     */    
    public function getDeviceListAction(){
        $user = $this->checkToken();
        
        $deviceModel =  new DeviceModel();
        $data = $deviceModel->getDeviceList($user["uid"]);
        $this->responseJson($data,"data",true);
    }
    
    /**
     * check binding status
     * 
     * GET /v1/device/check
     */
    public function checkBindingAction(){
        $user = $this->checkToken();        
        
        $gatewayModel = new GatewayModel();
        $data = $gatewayModel->getBindingStatus($user["uid"]);
        $this->responseJson($data);
    }
    
    /**
     * update device
     * 
     * PUT /v1/device/{did}
     */
    public function updateDeviceAction($did){
        $user = $this->checkToken();
        
        $fields = array("name"=>"required");
        $this->checkParams($fields);
        
        $name = $this->_body->name;
        $deviceModel = new DeviceModel(); 
        $deviceModel->updateName($user["uid"],$did, $name);
        $this->responseJson(0);
    }
    
    /**
     * remove device
     * 
     * DELETE /v1/device/{did}
     */
    public function removeDeviceAction($did){
          $user = $this->checkToken();
          $deviceModel = new DeviceModel();             
          $deviceModel->deleteDevice($user["uid"],$did);
          $this->responseJson(0);
    }
    
    /**
     * get latest record
     * 
     * GET　/v1/device/record/latest/{did}?body_part=0
     */
    public function getLatestResultAction($did){
        $user = $this->checkToken();
        $body_part = intval($this->request->getQuery('body_part','int',-1));
        $deviceModel = new DeviceModel();   
        $data = $deviceModel->getLatestRecord($user["uid"],$did,$body_part);
        $this->responseJson($data,"data",true);
    }       
     
    
    /**
    * get current or previous or next record 
    * 
    * GET　/v1/device/record/{did}?body_part={body_part}&time={time}&record={record_type}
    */
    public function getRecordAction($did){
        $user = $this->checkToken();
        $body_part = intval($this->request->get('body_part','int',0));
        $time = $this->request->get("time","int","");
        $field = array("time"=>"required");
        $this->checkParams($field, array("time"=>$time));
        $time = intval($time);
        
        $deviceModel = new DeviceModel();        
        $data = $deviceModel->getRecord($user["uid"],$did,$body_part,$time);
        
        $this->responseJson($data,"data",true);
    }
    
    /**
     * get result list by circle
     * 
     * GET /v1/device/average/list/{did}?body_part={body_part}&circle={circle}&type={type}&time={time}
     */
    public function getListAction($did){
        $user = $this->checkToken();
        
        $body_part = intval($this->request->get("body_part", "int",1));
        $circle = intval($this->request->get("circle","int",1));
        $type = intval($this->request->get("type","int",0));      
        $time = intval($this->request->get("time","int",time()));  
       
        $field = array(
                       "body_part"=>array(array("required"),array("include",array("domain"=>array(0,1,2,3,4)))),
                       "type"=>array(array("required"),array("include",array("domain"=>array(0,1,2,3,4,5)))),
                       "circle"=>array(array("required"),array("include",array("domain"=>array(1,2))))
                    );
        $this->checkParams($field,array("body_part"=>$body_part,"circle"=>$circle,"type"=>$type));
        
        $deviceModel = new DeviceModel();
        $data = $deviceModel->getAvgList($user["uid"],$did,$body_part,$type,$circle,$time);
        $this->responseJson($data,"data",true);
    }
    
    
    /**
     * get daily list 
     * 
     * GET /v1/device/daily/list/{did}?body_part={body_part}&type={type}&time={time}
     */
    public function getDailyAction($did){
//        $user = $this->checkToken();
        $user["uid"] = 10000016;
        $body_part = $this->request->get("body_part", "int",0);      
        $type = $this->request->get("type","int",0);      
        $time = $this->request->get("time","int",time());          
        $page = $this->request->get("page", "int",1);      
        $size = $this->request->get("size", "int",100);      
        $field = array(
                       "body_part"=>array(array("required"),array("include",array("domain"=>array(0,1,2,3,4)))),
                       "type"=>array(array("required"),array("include",array("domain"=>array(0,1,2,3,4,5))))
                    );
        $this->checkParams($field,array("body_part"=>$body_part,"type"=>$type));

        $deviceModel = new DeviceModel();
        $data = $deviceModel->getDailyList($user["uid"],$did,intval($body_part),intval($type),intval($time),$page,$size);
        $this->responseJson($data,"data",true);
    }
    
    /**
     * DELETE /v1/device/record?body_part={body_part}&time={time}
     */
    public function deleteRecordAction(){
        $this->responseJson(0);
    }
    
    
        
}
