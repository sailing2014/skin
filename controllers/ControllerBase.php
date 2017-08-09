<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use App\Exception\ApiException;
use App\Exception\ServiceException;
use App\Models\UserModel;

abstract class ControllerBase extends Controller
{

    /**
     *
     * @var request body object
     */
    protected $_body = null;

    /**
     * don't use `initialize`,`initialize` only once per request
     * we need exec in every action
     *
     * @param unknown $dispatcher            
     */
    public function beforeExecuteRoute($dispatcher)
    {
        $this->_body = $this->request->getReqBody(); // default object
    }

    /**
     * the result to client
     *
     * @param mixed $data           
     * @param string $key data key,default is data 
     * @param boolean $force if force is true,then data will be output whether data is null
     * @return string the response
     */
    protected function responseJson($data,$key="data",$force=false)
    {
        
        $errorCodes = $this->errcode; // get errcode from inject service
        
        $result = array(
            'errorCode' => 0,
            'errorMessage' => $errorCodes[0]           
        );
         if($data || $force) {
            $result[$key] = $data;
         }         
        return $this->response->setJsonContent($result); //define ('JSON_UNESCAPED_UNICODE', 256);
    }

  /**
     *
     */
    public function jsonReturn($return){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($return));
    }
    /**
     * $fields example:
     * 1.name is required
     * array('name'=>'required')
     * 2.phone is required , and match regex
     * array('phone'=> array('required',array('regex',array(..))))
     * 3.age is `a` or `b`
     * array('age'=> array(array('include', 'domain'=>array('a','b'))))
     *
     * @param array $fields            
     * @param
     *            string error message if error exist, else null
     */
    protected function checkParams($fields, $data = null)
    {
        $errorMsg = '';
        if (is_null($data) && ! empty($this->_body)) {
            $data = $this->_body;
        }
        
        if (empty($data)) {
            $errorMsg = 'The request body is empty or not json format ? Please Check.';
            
            throw new ApiException(400001, 400, $errorMsg);
        }
        
        $rules = array(
            'required' => '\Phalcon\Validation\Validator\PresenceOf',
            'identical' => '\Phalcon\Validation\Validator\Identical',
            'email' => '\Phalcon\Validation\Validator\Email',
            'exclude' => '\Phalcon\Validation\Validator\ExclusionIn',
            'include' => '\Phalcon\Validation\Validator\InclusionIn',
            'regex' => '\Phalcon\Validation\Validator\Regex',
            'length' => '\Phalcon\Validation\Validator\StringLength',
            'between' => '\Phalcon\Validation\Validator\Between',
            'confirm' => '\Phalcon\Validation\Validator\Confirmation',
            'url' => '\Phalcon\Validation\Validator\Url'
        );
        
        $validation = new Validation();
        
        if (count($fields)) {
            foreach ($fields as $field => $validator) {
                if (is_array($validator)) { // if a field mutil validator or has params
                    foreach ($validator as $subValidator) {
                        if (is_array($subValidator)) {
                            
                            $classname = $rules[$subValidator[0]];                            
                            $params = array_key_exists(1, $subValidator) ? $subValidator[1] : null;
                            $validation->add($field, new $classname($params));
                        } else {
                            
                            $classname = $rules[$subValidator];
                            $validation->add($field, new $classname());
                        }
                    }
                } else {
                    $classname = $rules[$validator];
                    $validation->add($field, new $classname());
                }
            }
        }        
   
        $messages = $validation->validate($data);
        
        if (count($messages)) {
            foreach ($messages as $message) {
                
                $errorMsg = 'Field `' . $message->getField() . '` : ' . $message->getMessage();
                
                throw new ApiException(400001, 400, $errorMsg);
            }
        }
        
    }
    
    /**
     * 
     * @param ServiceException $se
     * @param array|int $errcode
     * @param int $basecode default is 410000
     * @throws ApiException
     */
    
    public function throwException($se,$errcode,$basecode=410000){
          if (! is_array($errcode)) {
                $errcode = array(
                    $errcode
                );
            }
         
          $code = $se->getCode();
          if (in_array($code, $errcode)) {        
                    throw new ApiException($basecode+$code,400);
                }else{                    
                    throw new ApiException($code,$se->getHttpStatus(),$se->getMessage());
          }     
    }
    
    protected function checkToken()
    {
        $token = $this->request->getReqHeader('TOKEN');
      
        // Check Token
        if($token){
            $userModel = new UserModel();
            try{
                $user = $userModel->checkToken($token); 
                return $user;
            }catch(ServiceException $se){
                $this->throwException($se, array(3114,3116));                
            }
        }else{
            throw new ApiException(400001,400);
        }
    }
    
    /**
     * 
     * @param token token
     * @return type
     * @throws ApiException
     */
    protected function checkHtmlToken($token)
    {      
      
        // Check Token
        if($token){
            $userModel = new UserModel();
            try{
                $user = $userModel->checkToken($token); 
                return $user;
            }catch(ServiceException $se){                
                if($se->getCode() == 3114){
                    $return = array("errorCode"=>413114,"errorMessage"=>"User get failed!");
                    $this->jsonReturn($return);
                }else{
                    $return = array("errorCode"=>413116,"errorMessage"=>"Token disabled");
                    $this->jsonReturn($return);
                }               
            }
        }else{
//            $return = array("errorCode"=>400001,"errorMessage"=>"Paramter Error!");
//             $this->jsonReturn($return);
            return array("uid"=>1);
        }
    }
    
    protected function checkApiToken()
    {
        $apikey = $this->request->getReqHeader('APIKEY');
        $apitoken = $this->request->getReqHeader('APITOKEN');
        $time = $this->request->getReqHeader('TIME');
        
        if ( $apikey && $apitoken && $time)
        {            
            if( ($apikey == API_KEY) &&  ( $apitoken == sha1(API_SECRET.$time) ) )
            {
                
            }else{                
                throw new ApiException(400002,400);
            }
            
        }else{
            throw new ApiException(400001,400);
        }
    }
       
    protected function getJsonParam($name, $default = null)
    {
        return isset($this->_body->$name) ? $this->_body->$name : $default;
    }
}
