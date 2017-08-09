<?php

namespace App\Models;

use OSS\OssClient;
use OSS\Core\OssException;

use App\Models\ServiceBase;
use App\Exception\ApiException;

/**
 *  upload model
 *
 * @author yang.f
 *        
 */
class UploadModel extends ServiceBase { 
    
     public function uploadOne($filename,$filePath){
        try {
            $assumeRole = new \Helper\AssumeRole("upload");
            $cre = $assumeRole->getSecurityToken(); 
            $ossClient = new OssClient($cre->AccessKeyId, $cre->AccessKeySecret, OSS_ENDPOINT,false,$cre->SecurityToken);
            $ossClient->uploadFile(OSS_BUCKET, $filename, $filePath);

            $urlFormat = '%s-%s/%s';
            $url = sprintf($urlFormat, 'oss', OSS_BUCKET.'.qiwocloud1.com', $filename);
            return "http://".$url;
           } catch (OssException $e) {
               throw $e;
           }
    }
    
    
     public function checkTypeSize($upload_type,$real_type,$size){
            
           $config = $this->upload[$upload_type];
           
            //check type       
            if ( !in_array($real_type, $config['allowtype']) ) {
                throw new ApiException(480001,400);
            }
            
            // check size            
            if ($size > $config["maxsize"]) {
                throw new ApiException(480002,400);
            }
    }
    
    public function checkUploadType($type=""){
        if($type){
            if(!in_array($type, array("image","video","audio"))){
                throw new ApiException(400001,400,"Parameter type should be 'image','video', or 'audio'");
            }
        }else{
            throw new ApiException(400001,400,"Parameter type is required");
        }
    }
    
    /**
     *      
     * @return array
     * @throws ApiException
     */
    public function uploadImage(){
        $filePath = $ext = '';
        if (! $this->request->hasFiles()) {
            // no files
            throw new ApiException(480003,400);
        }
        
        $files = $this->request->getUploadedFiles();     
        // more than one
        if (count($files) > 1) {
            throw new ApiException(480004,400);
        } else {
            $files = $files[0];            
            $issafe = $files->isUploadedFile();
            if (! $issafe) {
               throw new ApiException(480005,400);
            }
                      
            $type = $files->getRealType();
            $size = $files->getSize();
            $this->checkTypeSize("image",$type,$size);
            
            $filePath = $files->getTempName();      
            $fileNameArr = explode('.', $_FILES["file"]['name']);
            $ext = $fileNameArr[count($fileNameArr) - 1];
        }
        
        $object = 'skin/user/' . uniqid() . '.' . $ext;        
        $url = $this->uploadOne($object, $filePath); 
        @unlink($filePath);        
        return $url;
    } 
}
