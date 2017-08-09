<?php
namespace Helper;

use Phalcon\Http\Request;

/**
 * 添加headers \ bodys 设置获取方式
 *
 * @author changyu.wang
 *        
 */
final class HttpRequest extends Request
{

    /**
     * http header token
     *
     * @var array
     *
     */
    private $_reqHeaders = array();

    /**
     * http request body
     *
     * @var string
     */
    private $_reqBody = '';

    function __construct()
    {
        $this->_reqHeaders = $this->getHeaders();
        $this->_reqBody = $this->getRawBody(); // string
    }

    /**
     *
     * Gets decoded JSON HTTP raw request body
     *
     * @param boolean $isassoc
     *            default false
     * @return array|object false will return object, else return array
     */
    public function getReqBody($isassoc = false)
    {
        if ($isassoc) {
            return json_decode($this->_reqBody, true);
        } else {
            return json_decode($this->_reqBody);
        }
    }

    /**
     * Get the http json body param value by field
     *
     * @param string $field            
     * @return null | mixed
     */
    public function getReqJsonVal($field)
    {
        $params = json_decode($this->_reqBody, true);
        if (array_key_exists($field, $params)) {
            return $params[$field];
        } else {
            return null;
        }
    }

    /**
     *
     * @param
     *            string | array | object $body
     *            
     */
    public function setReqBody($body)
    {
        if (is_string($body)) {
            $this->_reqBody = $body;
        } else {
            $this->_reqBody = json_encode($body, JSON_UNESCAPED_UNICODE);
        }
    }

    public function getReqHeader($key)
    {
        // $key = ucfirst(strtolower($key)); //apache need
        if (array_key_exists($key, $this->_reqHeaders)) {
            return $this->_reqHeaders[$key];
        }
        
        return null;
    }

    public function setReqHeader($key, $value)
    {
        // $key = ucfirst(strtolower($key));//apache need
        $this->_reqHeaders[$key] = $value;
    }
}
