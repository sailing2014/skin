<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Exception\ServiceException;

class PwdController extends ControllerBase
{
    /**
     * 重置密码、更新密码操作
     * POST /v1/user/password
     */
    public function addAction()
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
                            'reset',
                            'update'
                        )
                    )
                )
            )
        );
        
        $this->checkParams($validators);       
        
        if ($this->_body->method == 'reset') {
            $this->reset();
        }
        
        if ($this->_body->method == 'update') {
            $this->update();
        }
    }

    /**
     * 重置密码
     */
    private function reset()
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
            'new_password' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\S{6,32}$/'
                    )
                )
            ),
            'smsCode' => 'required'
        );
        
       $this->checkParams($validators);
        
        $userModel = new UserModel();
        
        try{
            $userModel->checkValidation($this->_body->mobile, $this->_body->smsCode, 1);
            $user = $userModel->getUserByName($this->_body->mobile);
            $userModel->changePwd($user["uid"], $this->_body->new_password);
            $this->responseJson(0);
        } catch (ServiceException $se) {
            $this->throwException($se, array(3191,3114,3151));
        }        
     
        
    }

    /**
     * 更改密码操作
     */
    private function update()
    {        
        $this->checkToken();
                
        $validators = array(
            'old_password' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\S{6,32}$/'
                    )
                )
            ),
            'new_password' => array(
                array(
                    'required'
                ),
                array(
                    'regex',
                    array(
                        'pattern' => '/^\S{6,32}$/'
                    )
                )
            )
        );
        
        $this->checkParams($validators);       
        
        $userModel = new UserModel();
        try{
            $user = $userModel->getUserByName($this->_body->mobile);
            $userModel->checkPwd($user["uid"], $this->_body->old_password);
            $userModel->changePwd($user["uid"], $this->_body->new_password);
            $this->responseJson(0);
        }catch(ServiceException $se)
        {
            $this->throwException($se, array(3221,3151));
        }       
      
    }
}
