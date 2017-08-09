<?php

namespace App\Models;

use App\Exception\ServiceException;

class VersionModel extends ServiceBase
{  

    /**
     * 获取产品最新版本信息
     * @param string $productType
     * @param string $productId
     * @param string $version 
     * @return array
     */
    public function getNewestVersion($productType, $productId,$version="")
    {        
        $data = array(  
                            "isNew"=>1,
                            "isMustUpdate"=>0,"version"=>$version,
                            "downloadUrl"=>"","md5"=>"",
                            "description"=>""
                      );
        
        $url = $this->api['version.getNewest'];
        try{
                $response = $this->sendHttpRequest( $url,    array(
                                                                    'productType' => $productType, 
                                                                    'productId' => $productId
                                                                    )
                                                  );
        }catch(ServiceException $se){
            
        }
        
        if(isset($response["data"])){            
            if($response["data"]["version"] > $version){
                $data["isNew"] = 0;                
            }            
            if($response["data"]["type"] == "prompt"){
                $data["isMustUpdate"] = 1;
            }            
            $data["version"] = $response["data"]["version"];
            $data["downloadUrl"] = $response["data"]["download"]["system"]["url"];
            $data["md5"] = $response["data"]["download"]["system"]["md5"];
            $data["description"] = $response["data"]["description"];
        }
        
        return $data;
    }
}
