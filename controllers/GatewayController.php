<?php

namespace App\Controllers;

use App\Models\GatewayModel;

class GatewayController extends ControllerBase
{

    /**
     * @url POST /v1/gateway
     */
    public function addGatewayAction()
    {
        $fields = array(
            'mac' => 'required',
            'bta' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\S{12}$/'
                    )
                )
            ),
            'uid' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\d+$/'
                    )
                )
            )
        );
        $this->checkParams($fields);        
        
        $macAddress = $this->getJsonParam('mac');
        $uid = intval($this->getJsonParam('uid'));
        $bta = strtolower($this->getJsonParam('bta'));
        
        $gatewayModel = new GatewayModel();
        $gateway = $gatewayModel->addGateway($macAddress, $uid, $bta);

        $gatewayReturn = array(            
            'mac' => $gateway['macAddress'],
            'bta' => $gateway['bta'],
            'uid' => $gateway['uid']
        );

        $this->responseJson($gatewayReturn);
    }
    
    /**
     * GET /v1/gateway/{bta}
     * @param string $bta
     */
     public function getOneAction($bta)
    {
        $gatewayModel = new GatewayModel();
        $gateway = $gatewayModel->getGateway($bta);
        $this->responseJson($gateway);
    }
    
    /**
     * reset device
     * 
     * @param type $bta
     */
    public function resetAction($bta){
        $gatewayModel = new GatewayModel();
        $gatewayModel->resetGateWay($bta);
        $this->responseJson(0);
    }
    
    
    
    /**
     * POST /v1/gateway/upload/image
     */
    public function uploadimageAction(){        
        $files = $this->request->getUploadedFiles();
        if(!$files){
            $files = "";
        }
        $bta = strtolower($this->request->get("bta","string",""));
        $type = intval($this->request->get("type","int",0));
        $body_part = intval($this->request->get("body_part","int",0));
        $auto = intval($this->request->get("auto","int",0));
        $time = $this->request->get("time","int","");
        $water = $this->request->get("water","float",0);
        $validators = array(     
                                "file"=>"required",
                                "bta"=>"required",
                                "type"=>array(  
                                                array("required"),
                                                array("include",array("domain"=>array(1,2,3)))
                                             ),
                                 "body_part"=>array(
                                                    array("required"),
                                                    array("include",array("domain"=>array(1,2,3,4)))
                                              ),
                                 "auto"=>array(                                                   
                                                    array("include",array("domain"=>array(0,1)))
                                              ),   
                                 "time"=>"required",
            
                                 "water"=>array(                                                   
                                                    array("between",array(
                                                                    'minimum' => 0,
                                                                    'maximum' => 100
                                                                    )                                                                        
                                                         )
                                              ),  
                           );
                           
        $this->checkParams($validators,     array(
                                                    "file"=>$files,"bta"=>$bta,"type"=>$type,
                                                    "body_part"=>$body_part,"auto"=>$auto,
                                                    "time"=>$time,"water"=>$water
                                                )
                           );
                         
        $gatewayModel = new GatewayModel();
        $file = $files[0]->getTempName();         
        $data = $gatewayModel->addImage($bta, $type,$body_part,$auto,intval($time), $file,$water);
        @unlink($file);
        $this->responseJson($data);
   } 
   
   
    
}
