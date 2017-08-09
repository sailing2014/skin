<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ArticleModel;
use App\Exception\ApiException;
use App\Exception\ServiceException;

class UserController extends ControllerBase {
    
    /**
     * 完成注册用户
     * POST /v1/user
     */
    public function addAction()
    {
        $validators = array(
            'mobile' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\+[1-9]\d{3,31}$/i'
                    )
                )
            ),
            'smsCode' => 'required'
        );
        
       $this->checkParams($validators);       
        
        $userModel = new UserModel();
        // check validation code
        try{
            $userModel->checkValidation($this->_body->mobile, $this->_body->smsCode, 1);
        }catch (ServiceException $se){
                $this->throwException($se, 3191);//3191 Code validate failed                       
        }   
               
        $user = array();
        $user['username'] = $this->_body->mobile;
        $user['password'] = '123456';  
        $user['type'] = "phone";
        try{
            $userModel->addUser($user); 
            $this->responseJson(0);
        }  catch (ServiceException $se){
            $this->throwException($se, 3110);
        }   
    }   
    

    /**
     * 获取用户信息
     * GET /v1/user/{uid}
     */
    public function getAction($id)
    {        
        $user1 = $this->checkToken();
       
        $userModel = new UserModel();    
        try{           
            $user = $userModel->getUserByUid($user1["uid"]);  
            $data = array("mobile"=>$user["phone"]) + $user;
            unset($data["password"],$data["phone"]);                       
        }  catch (ServiceException $se){
            $this->throwException($se, 3114);
        }
        
        try{
            $articleModel = new ArticleModel();
            $skin = $articleModel->getCharacteByUid($user1["uid"]);
            $data["characteristics"] = $skin;
        } catch (ApiException $ex) {
            $data["characteristics"] = new \Phalcon\Logger\Formatter\Json();
        }
        
        $this->responseJson($data, "user"); 
    }

    /**
     * 更新用户信息
     * PUT /v1/user/{uid} 只能更新自己的信息
     * $id 参数无效
     */
    public function updateAction($id)
    {
        $user = $this->checkToken();      
        
        $profile = array();
        isset($this->_body->nickname) ? $profile['nickname'] = $this->_body->nickname : false;
        isset($this->_body->image) ? $profile['image'] = $this->_body->image : false;
        isset($this->_body->gender) ? $profile['gender'] = $this->_body->gender : false;
        isset($this->_body->province) ? $profile['province'] = $this->_body->province : false;
        isset($this->_body->area) ? $profile['area'] = $this->_body->area : false;
        isset($this->_body->age) ? $profile['age'] = $this->_body->age : false;
        
        if (empty($profile)) {           
            throw new ApiException(400001,400);
        }
        
        $validators = array(
            // 'nickname' => array(
            // array('length', array('max'=>15, 'allowEmpty'=> true))
            // ),
            'gender' => array(
                array(
                    'include',
                    array(
                        'domain' => array(
                            1,
                            2
                        ),
                        'allowEmpty' => true
                    )
                )
            ),
            'image' => array(
                array(
                    'url',
                    array(
                        'allowEmpty' => true
                    )
                )
            )
        );
        
        $this->checkParams($validators);
                
        // update user profile
        $userModel = new UserModel();
        
        try{
            $userModel->updateUser($user["uid"], $profile);
            $this->responseJson(0);
        }  catch (ServiceException $se){
            $this->throwException($se, 3141);           
        }
       
    }
    
    
    
    /* * 发送验证码、检测验证码
     * POST /v1/user/validation
     */
    public function requestSmsValidationAction()
    {
        $validators = array(
            'method' => array(
                array(
                    'required'
                ),
                array(
                    'include',
                    array(
                        'domain' => array(
                            'check',
                            'send'
                        )
                    )
                )
            )
        );
        
        $this->checkParams($validators);
       
        if ($this->_body->method == 'check') {
            $this->check();
        }
        
        if ($this->_body->method == 'send') {
            $this->send();
        }
    }

    /**
     * 发送验证码
     */
    private function send()
    {
        $validators = array(
            'mobile' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\+[1-9]\d{3,31}$/i'
                    )
                )
            ),
            'type' => array( // 1=>register,2=>login
                array(
                    'required'
                ),
                array(
                    'include',
                    array(
                        'domain' => array(
                            1,
                            2
                        )
                    )
                )
            )
        );
        
        $this->checkParams($validators);
     
        // 检测手机号       
        $userModel = new UserModel(); 
        $errorCode = 0;
        try{
            $userModel->checkIdentifier($this->_body->mobile);       
        }catch (ServiceException $se){ 
                if ($this->_body->type == 1) { // 注册时用户应不存在
                    if ($se->getCode() == 3201) { // 3201 = "User does exist!" 3211 = "User does not exist!"
                        $errorCode = 413201;
                    }
                } else { //登录时用户应已存在
                    if ($se->getCode() == 3211) { // 3201 = "User does exist!" 3211 = "User does not exist!"
                         $errorCode = 413211;
                    }
                }               
        }   
        
        if( $errorCode ){
            throw new APiException($errorCode,400);
        }
        
        try{
            $userModel->sendValidation($this->_body->mobile);  
        }catch(ServiceException $se){    
            $this->throwException($se, 3181);
        }
        
        $this->responseJson(0);
    }

    /**
     * 检测验证码
     */
    private function check()
    {
        $validators = array(
            'mobile' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\+[1-9]\d{3,31}$/i'
                    )
                )
            ),
            'smsCode' => 'required'
        );
        
        $this->checkParams($validators);       
  
        $userModel = new UserModel();
        try{
            $userModel->checkValidation($this->_body->mobile, $this->_body->smsCode);
        }  catch ( ServiceException $se ){
            $this->throwException($se, 3191);
        }
        
        $this->responseJson(0);
   
    }     

    /**
     * 查看手机号码是否注册
     * GET /v1/user/mobile/{:mobile}
     */
    public function checkResiterOrNotAction($mobile)
    {    
       
       $userModel = new UserModel();
        try{
            $userModel->checkIdentifier($mobile);       
         }catch (ServiceException $se){ 
             $exist = 0;
             if( $se->getCode() == 3201){
                 $exist = 1;
             }
             
             $this->responseJson(array($exist), "exist");
        }    
    }
    
    /**
     * Get weather 
     * 
     * @param city  pinyin or Chinese. 城市名称、支持中英文,不区分大小写和空格,城市和国家之间用英文逗号分割 e.g 北京、beijing、london,united kingdom
     * 
     * GET /v1/user/weather?city={city}
     */
    public function weatherAction(){
        $this->checkToken();
        
        $validators = array("city"=>"required");
        $city = $this->request->getQuery('city');
        $this->checkParams($validators, array("city"=>$city));
        
        $userModel = new UserModel();
        
        $data = $userModel->getWeather($city);
        $this->responseJson($data);
                
    } 
}
