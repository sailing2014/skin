<?php
namespace App\Exception;

/**
 * 调用外部接口错误封装
 *
 * @author changyu.wang
 *        
 */
class ServiceException extends AppException
{

    /**
     * error code
     *
     * @var int
     */
    protected $code = 500;

    /**
     * error message
     *
     * @var string
     */
    protected $message = 'Internal Server Error';

    /**
     * http Status
     *
     * @var int
     */
    protected $httpStatus = 500;
    
    /**
     * 服务名称，亦称模块名称
     * @var string 
     */
    protected $serviceName = '';

    public function __construct($message = null, $code = null, $httpStatus = null, $serviceName = null)
    {
        if (! is_null($message)) {
            $this->message = $message;
        }
        
        if (! is_null($code)) {
            $this->code = $code;
        }
        
        if (! is_null($httpStatus)) {
            $this->httpStatus = $httpStatus;
        }
        
        if (! is_null($serviceName)) {
            $this->serviceName = $serviceName;
        }
    }

    /**
     *
     * @return the $httpStatus
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
    
    /**
     * 
     * @return the $serviceName
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
}