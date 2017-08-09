<?php
namespace App\Controllers;

use OSS\OssClient;
use OSS\Core\OssException;
use App\Exception\ApiException;

class StorageController extends ControllerBase
{   
    
    /**
     * POST /v1/oss/token
     */
    public function addAction()
    {
        $this->checkToken();
       
        $credentials = $this->getCredentials(); 
        $credentials->AccessKeySecret = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, MCRYPT_PRIVATE_KEY, $credentials->AccessKeySecret, MCRYPT_MODE_CBC, MCRYPT_IV));
        $credentials->SecurityToken = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, MCRYPT_PRIVATE_KEY, $credentials->SecurityToken, MCRYPT_MODE_CBC, MCRYPT_IV));
        $credentials->AccessKeyId = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, MCRYPT_PRIVATE_KEY, $credentials->AccessKeyId, MCRYPT_MODE_CBC, MCRYPT_IV));

        $credentials->BucketName = OSS_BUCKET;
        $this->responseJson($credentials);
    }
      
    
    protected function getCredentials($all=false){
        $assumeRole = new \Helper\AssumeRole("upload");
        $credentials = $assumeRole->getSecurityToken($all); 
        return $credentials;
    }


    /**
     * 文件上传
     */
    public function uploadAction()
    {       
        $this->checkApiToken();

        $upload_type = $this->request->get("type");
        $this->checkUploadType($upload_type);
        
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
            $this->checkTypeSize($upload_type,$type,$size);
            
            $filePath = $files->getTempName();
            $fileNameArr = explode('.', $_FILES["file"]['name']);
            $ext = $fileNameArr[count($fileNameArr) - 1];
            
        }
        
        $object = 'skin/' . uniqid() . '.' . $ext;
        
        try {
            $cre = $this->getCredentials();
            $ossClient = new OssClient($cre->AccessKeyId, $cre->AccessKeySecret, OSS_ENDPOINT,false,$cre->SecurityToken);
            $ossClient->uploadFile(OSS_BUCKET, $object, $filePath);
//            @unlink($filePath);
            $urlFormat = '%s-%s/%s';
            $url = sprintf($urlFormat, 'oss', OSS_BUCKET.'.qiwocloud1.com', $object);
            $this->responseJson("http://".$url);
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
   
}
