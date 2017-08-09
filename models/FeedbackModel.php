<?php
namespace App\Models;

use App\Models\ServiceBase;

class FeedbackModel extends ServiceBase
{

    /**
     * 反馈意见
     * 
     * @param int $uid            
     * @param string $name            
     * @param unknown $content            
     * @param number $productid            
     * @return boolean
     */
    public function addIssue($uid, $name="", $content="", $productid = 10)
    {
        $url = $this->api['feedback'];
        
        $time = time();
        
        $params = array(
            "time" => $time,
            "api_token" => sha1($time . "abcdefg"), // must do this.don't modify
            "uid" => $uid,
            "name" => $name,
            "productid" => $productid,
            "content" => $content,
            "ip" => $this->request->getClientAddress()
        );
        
        $ret = \Httpful\Request::post($url, $params, 'form')->timeoutIn(3)->send();
        $response = json_decode($ret->raw_body, true);
        if (isset($response['_status']['_code']) && 200 == $response['_status']['_code']) {
            return true;
        } else {
            return false;
        }
    }
}