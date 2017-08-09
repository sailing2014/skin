<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Exception\ServiceException;

class TokenController extends ControllerBase {
    
    /**
     * 用户登录
     * POST /v1/user/token
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
            'smsCode' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\d{6}$/'
                    )
                )
            )
        );
        
        $this->checkParams($validators);
                
        $userModel = new UserModel();

        //验证码校验
        try{ 
            $userModel->checkValidation($this->_body->mobile, $this->_body->smsCode);
        }  catch ( ServiceException $se ){
            $this->throwException($se, 3191);
        }
        
        //查看用户是否存在，不存在注册一个
        try{
            $userModel->checkIdentifier($this->_body->mobile);       
        }catch (ServiceException $se){ 
                if ($se->getCode() == 3211) { // 3201 = "User does exist!" 3211 = "User does not exist!"                     
                    $user['username'] = $this->_body->mobile;
                    $user['password'] = '123456';  
                    $user['type'] = "phone";
                    $userModel->addUser($user); 
                }        
        }   
        
        //登录
        try{            
            $response = $userModel->loginUser($this->_body->mobile, '123456');
            $this->responseJson($response["user"],"user");
        }catch( ServiceException $se){        
            $this->throwException($se, array(
                    3115,
                    3211
                )); // User does not exist!
        }      
        
    }
    
     /**
     * 用户登录
     * POST /v1/user/pwd/token
     */
    public function addByPwdAction()
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
            'password' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\S{6,32}$/i'
                    )
                )
            )
        );
        
        $this->checkParams($validators);
                
        $userModel = new UserModel();

        //检查之前是否验证码登录
         $userModel->checkSmsCodeStatus($this->_body->mobile);
        
        //登录
        try{            
            $response = $userModel->loginUser($this->_body->mobile,  $this->_body->password);
            $this->responseJson($response["user"],"user");
        }catch( ServiceException $se){        
            $this->throwException($se, array(
                    3115,
                    3211
                )); // User does not exist!
        }      
        
    }

    /**
     * 用户登出操作
     * DELETE /v1/user/token/{uid}
     */
    public function deleteAction($uid)
    {
        $user = $this->checkToken();     
        
        $userModel = new UserModel();
        try{
            $userModel->logoutUser($user["uid"]);   
        }catch( ServiceException $se){
            $this->throwException($se, $se->getCode());
        }
        
        $this->responseJson(0);
        
    }

    /**
     * Oauth login
     * POST /v1/user/token/oauth
     */
    public function oauthAction()
    {
        $validators = array(
            'platform' => array(
                array(
                    'required'
                ),
                array(
                    'include',
                    array(
                        'domain' => array(
                            'qq',
                            'weibo',
                            'wechat'
                        )
                    )
                )
            ),
            'access_token'=>'required',
            'openid'=>'required'
        );
        
        $this->checkParams($validators);               
   
        $userModel = new UserModel();       
//        $userModel->checkAccessToken($this->_body->platform, $this->_body->access_token, $this->_body->openid);
        
        $username = $this->_body->platform.":".  $this->_body->openid; 
        try{           
            $userModel->checkIdentifier($username);       
         }catch (ServiceException $se){            
             if( $se->getCode() != 3201){
                 //注册openid
                $user = array();
                $user['username'] = $username;
                $user['password'] = "123456";  
                $user['type'] = "phone";
                try{
                    $userModel->addUser($user); 
                }  catch (ServiceException $se){
                    $this->throwException($se, 3110);
                }   
             }
        }    
        
        
        try{                      
            $response = $userModel->loginUser($username, "123456"); 
            $this->responseJson($response["user"],"user");
        }catch( ServiceException $se){    
            $this->throwException($se, array(
                    3115,
                    3211
                )); // User does not exist!
        }     
        
    }
    
    /**
     * check platform openid registered or not
     * 
     * GET /v1/user/token/oauth?platform={platform}&openid={openid}
     */
     public function checkIdentifierAction()
    {        
        $validators = array(
            'platform' => array(
                array(
                    'required'
                ),
                array(
                    'include',
                    array(
                        'domain' => array(
                            'qq',
                            'weibo',
                            'wechat'
                        )
                    )
                )
            ),           
            'openid'=>'required'
        );
        
        $platform = $this->request->get("platform", NULL, "");
        $openid = $this->request->get("openid",NULL,"");
        $this->checkParams($validators,array("platform"=>$platform,"openid"=>$openid));
        
        try{
            $userModel = new UserModel();
            $identifier = $platform.":".$openid;
            $userModel->checkIdentifier($identifier);       
         }catch (ServiceException $se){ 
             $exist = 0;
             if( $se->getCode() == 3201){
                 $exist = 1;
             }
             
             $this->responseJson(array($exist), "exist");
        }    
    }
}
