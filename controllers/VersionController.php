<?php

namespace App\Controllers;

use App\Models\VersionModel;

/**
 * 升级使用
 *
 * @author changyu.wang
 *
 */
class VersionController extends ControllerBase
{

    const FIRMWARE = 'SKIN-FIRMWARE';
    const APP_ANDROID = 'SKIN-APP-ANDROID';
    const APP_IOS = 'SKIN-APP-IOS';

   
    /**
     * 检测是否有新版本
     * plat 0--> android 1-->ios 2-->firmware
     * GET /v1/version?plat={product type}&version=1.0.0
     */
    public function getVersionAction()
    {
        $plat = $this->request->getQuery('plat');
        $validators = array(
            'plat' => array(
                array('required'),
                array('include', array('domain' => array(0,1,2)))
            )
        );
        $this->checkParams($validators, array('plat' => $plat));
        
        $user = $this->checkToken();
        
        $productType = self::APP_ANDROID;
        switch ($plat) {
            case 1:
                $productType = self::APP_IOS;
                break;
            case 2:
                $productType = self::FIRMWARE;
                break;
            default:
                break;
        }
        
        $version = $this->request->getQuery('version','string','');
        
        $upModel = new VersionModel();
        $result = $upModel->getNewestVersion($productType, $user["uid"],$version);
        return $this->responseJson($result);
    }
}
