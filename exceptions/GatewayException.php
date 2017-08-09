<?php
namespace App\Exception;

/**
 * 调用外部服务请求异常
 * 
 * @author changyu.wang
 *        
 */
class GatewayException extends AppException
{

    public function __construct(\Exception $e)
    {
        $this->message = $e->getMessage();
        $this->code = $e->getCode();
    }
}