<?php
namespace Helper;

include_once __DIR__ . '/../aliyun-php-sdk-core/Config.php';

use Sts\Request\V20150401 as Sts;

class AssumeRole
{

    private $role = null;

    public function __construct($action)
    {
        $this->role = $action;
    }

    public function getSecurityToken($all=false)
    {
        if ($this->role == "upload") {
            $AccessKeyId = ACCESSKEYID_UPLOAD;
            $AccessKeySecret = ACCESSKEYSECRET_UPLOAD;
            $roleArn = ROLEARN_UPLOAD;
            $RoleSessionName = ROLESESSIONNAME_UPLOAD;
        } elseif ($this->role == "download") {
            $AccessKeyId = ACCESSKEYID_DOWNLOAD;
            $AccessKeySecret = ACCESSKEYSECRET_DOWNLOAD;
            $roleArn = ROLEARN_DOWNLOAD;
            $RoleSessionName = ROLESESSIONNAME_DOWNLOAD;
        } else {
            return false;
        }
        
        // 你需要操作的资源所在的region，STS服务目前只有杭州节点可以签发Token，签发出的Token在所有Region都可用
        // 只允许子用户使用角色
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $AccessKeyId, $AccessKeySecret);
        $client = new \DefaultAcsClient($iClientProfile);
        // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        // 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
        // 详情请参考《RAM使用指南》
        // 此授权策略表示读取所有OSS的只读权限
        $request = new Sts\AssumeRoleRequest();
        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName($RoleSessionName);
        $request->setRoleArn($roleArn);
        $request->setDurationSeconds(3600);
        // $response = $client->doAction($request);
        $response = $client->getAcsResponse($request);
        
        // $response->Credentials->SecurityToken = $this->encrypt($response->Credentials->SecurityToken); //$response->Credentials->SecurityToken使用时必须解密，
        // 解密方法为将整个字符串依次向右移位10位，即将原字符串的第1位移位到倒数第10位，第2位移位到倒数第9位……第10位移位到倒数第1位，第11位移位到第1位，
        // 以此类推。（亦可直接截取字符串前10位，拼接在剩余字符串的末尾
        if($all){
            return $response;
        }else{
            return $response->Credentials;
        }
    }
}
