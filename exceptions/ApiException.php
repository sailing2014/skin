<?php
namespace App\Exception;

use Phalcon\Di;

/**
 * 当前应用对外异常、错误封装
 * 
 * @author changyu.wang
 *        
 */
class ApiException extends ServiceException
{

    /**
     *
     * @param number $code
     *            errorCode
     * @param number $httpStatus
     *            http Status
     * @param string $message
     *            [optional] extra message to client
     */  
    public function __construct($code = null, $httpStatus = null, $message = null)
    {
        $di = Di::getDefault();
        $errcode = $di->getShared('errcode');
        $message = array_key_exists($code, $errcode) ? $errcode[$code] . " ".$message : $message;
        parent::__construct($message, $code, $httpStatus);
    }
}