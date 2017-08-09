<?php

namespace App\Models;

use App\Models\ArticleModel;
use App\Models\MessageModel;
use App\Models\ServiceBase;
use App\Exception\ApiException;

/**
 *  user model
 *
 * @author yang.f
 *        
 */
class UserModel extends ServiceBase {

    public function __construct() {
        $this->serviceName = 'user';
    }

    /**
     * check phone is exist ?
     *
     * @param unknown $identifier            
     * @return 
     */
    public function checkIdentifier($identifier)
    {
        $url = $this->api['user.check_identifier'];
        
        $response = $this->sendHttpRequest($url, array(
            'identifier' => $identifier
        ));     
        return $response;
    }
    
    /**
     * send smsCode to phone
     */
     public function sendValidation($phone)
    {
        $url = $this->api['user.validation'];
        $response = $this->sendHttpRequest($url, array(
            'phone' => $phone
        ));
       
        return $response;
    }
    
     /**
     *
     * @param unknown $phone            
     * @param unknown $code            
     * @param number $del
     *            0 or 1 Is delelte after check over
     * @return boolean|Ambigous <object, multitype:>
     */
    public function checkValidation($phone, $code, $del = 0)
    {
        $url = $this->api['user.check_validation'];
        $response = $this->sendHttpRequest($url, array(
            'phone' => $phone,
            'code' => $code,
            'del' => $del
        ));             
       
        return $response;
    }
    /**
     * 注册用户
     * 
     * @param type $user
     * @return type
     */
    
    public function addUser($user)
    {
        $url = $this->api['user.add'];
        
        $response = $this->sendHttpRequest($url, $user);
        
        return $response;
    }
    
    /**
     * 用户登录
     *
     * @param unknown $username            
     * @param unknown $password  
     */
    public function loginUser($username, $password)
    {
        $url = $this->api['user.login'];
        
        $response = $this->sendHttpRequest($url, array(
            'username' => $username,
            'password' => $password
        ));       
       
        if(isset($response["user"]["phone"])){
            if(strpos($response["user"]["phone"],":") === false){
                $response["user"]["mobile"] = $response["user"]["phone"];
            }else{
                $response["user"]["mobile"] = "";
            }
            
            unset($response["user"]["phone"]);
            
        }
        
        if(isset($response["user"]["mobile"]) && $response["user"]["mobile"] ){
            //记录短信验证登录状态
            $this->setSmsCodeStatus($response["user"]["mobile"], 1);
        }
        return $response;
    }
    
    /**
     * 设置用户短信验证状态
     * 
     * @param type $mobile user mobile
     * @param int $status smsCode status 0-->user doesn't login from smsCode yet,
     *                                     1-->user has login by smsCode,thus he can login by password directly
     */
    protected function setSmsCodeStatus($mobile,$status=0){        
        $doc_id = $mobile;
        $data = array("status"=>$status);
        $this->set($doc_id, $data);
    }
    
    public function checkSmsCodeStatus($mobile){
        $doc_id = $mobile;        
        $data = $this->get($doc_id,false);        
        if($data && $data["status"]){            
        }else{
            throw new ApiException(413117,400);
        }
    }

    /**
     * 用户登出
     *
     * @param int $uid            
     */
    public function logoutUser($uid)
    {
        $url = $this->api['user.logout'];
        
        $this->sendHttpRequest($url, array(
            'uid' => $uid
        ));
        
        $user = $this->getUserByUid($uid);
        if(isset($user["phone"]) && !empty($user["phone"])){
            //记录短信验证退出状态
            $this->setSmsCodeStatus($user["phone"],0);
        } 
        
        //删除push token
        try{
            $messageModel = new MessageModel();
            $messageModel->deletePushAccount($uid);
        } catch (ApiException $ex) {

        }
    }
    
    /**
     * check token
     *
     * @param unknown $token            
     */
    public function checkToken($token)
    {
        $url = $this->api['user.check_token'];
        
        $response = $this->sendHttpRequest($url, array(
            'token' => $token
        ));
        
        return $response["user"];
    }
    
    /**
     * 根据uid获取用户信息
     *
     * @param unknown $uid            
     */
    public function getUserByUid($uid)
    {
        $url = $this->api['user.get'];
        
        $response = $this->sendHttpRequest($url, array(
            'uid' => $uid
        ));
      
        return $response["user"];
    }
    
    /**
     * update user info
     *
     * @param int $uid            
     * @param array $profile
     *            userinfo array
     * @return boolean
     */
    public function updateUser($uid, array $profile)
    {
        $url = $this->api['user.update'];
        
        $response = $this->sendHttpRequest($url, array(
            'uid' => $uid,
            'profile' => $profile
        ));
                
        return $response;
    }
    
    /**
     * get user info by mobile
     *
     * @param string $username  mobile 
     */
    public function getUserByName($username)
    {
        $url = $this->api['user.get_by_name'];
        
        $response = $this->sendHttpRequest($url, array(
            'username' => $username
        ));      
   
        return $response["user"];
    }

     /**
     * user change password
     *
     * @param int $uid            
     * @param string $newPwd            
     */
    public function changePwd($uid, $newPwd)
    {
        $url = $this->api['user.update_pwd'];
        
        $response = $this->sendHttpRequest($url, array(
            'uid' => $uid,
            'new_password' => $newPwd
        )); 
        
        return $response;
    }
    
    /**
     * check user password before update password
     *
     * @param int $uid            
     * @param string $pwd            
     */
    public function checkPwd($uid, $pwd)
    {
        $url = $this->api['user.check_pwd'];
        
        $response = $this->sendHttpRequest($url, array(
            'uid' => $uid,
            'password' => $pwd
        ));
                
        return $response;
    }
    
    /**
     * 
     * @param type $platform
     * @param type $access_token
     * @param type $openid
     * @return boolean
     */
    
     public function checkAccessToken($platform, $access_token, $openid)
    {
        $appSecret = '';
        
        switch ($platform) {
            case "qq":
                $appSecret = QQ_APP_SECRET;
                break;
            case "weibo":
                $appSecret = WEIBO_APP_SECRET;
                break;
            case "wechat":
                $appSecret = WECHAT_APP_SECRET;
                break;
            default:
                $appSecret = "";
        }
        if ($appSecret && $access_token == md5($appSecret . $openid)) {            
        }else{
            throw new ApiException(413116,400);
        }
        
    }
    
    /**
     * 
     * @param string $city  city
     */
    public function getWeather($city){
        $url = $this->api['weather']; 
        $data = $this->sendHttpRequest($url.$city,array(), false, "GET",false);
        $ret = array();
     
        if(isset($data["HeWeather data service 3.0"][0]["status"]) &&  $data["HeWeather data service 3.0"][0]["status"] == "ok"){
            $ret["basic"]["city"] = $data["HeWeather data service 3.0"][0]["basic"]["city"];             
            $ret["now"]["cond"] = array("txt"=>$data["HeWeather data service 3.0"][0]["now"]["cond"]["txt"],"png"=>"http://files.heweather.com/cond_icon/".$data["HeWeather data service 3.0"][0]["now"]["cond"]["code"].".png"); 
            $ret["now"]["tmp"] = $data["HeWeather data service 3.0"][0]["now"]["tmp"];  
            $ret["now"]["hum"] = $data["HeWeather data service 3.0"][0]["now"]["hum"];
            $ret["now"]["uv"] = $data["HeWeather data service 3.0"][0]["suggestion"]["uv"]["brf"];            
            
            $ret["suggestion"] = $data["HeWeather data service 3.0"][0]["suggestion"]["uv"]["txt"];
            
            $type = $this->getCondType();
            for ($i=0;$i<=2;$i++)
            {
                $ret["daily_forecast"][$i]["date"] = $data["HeWeather data service 3.0"][0]["daily_forecast"][$i]["date"];  
                $ret["daily_forecast"][$i]["cond"] = array("txt"=>$data["HeWeather data service 3.0"][0]["daily_forecast"][$i]["cond"]["txt_".$type],"png"=>"http://files.heweather.com/cond_icon/".$data["HeWeather data service 3.0"][0]["daily_forecast"][$i]["cond"]["code_".$type].".png");
                $ret["daily_forecast"][$i]["tmp"] = $data["HeWeather data service 3.0"][0]["daily_forecast"][$i]["tmp"];
                $ret["daily_forecast"][$i]["hum"] = $data["HeWeather data service 3.0"][0]["daily_forecast"][$i]["hum"]; 
                $ret["daily_forecast"][$i]["uv"] = $this->getUV($city,$i); 
            }
        }else{
            throw  new ApiException(4171111,400,$data["HeWeather data service 3.0"][0]["status"]);
        }
        if(isset($data["HeWeather data service 3.0"][0]["aqi"]["pm25"])){
            $pm25 = $data["HeWeather data service 3.0"][0]["aqi"]["pm25"];
        }else{
            $pm25 = -1;
        }
        $dd_data = array(  "city"=>$ret["basic"]["city"],
                            "cond"=>$ret["now"]["cond"]["txt"],"tmp"=>$ret["now"]["tmp"],
                            "hum"=>$ret["now"]["hum"],"uv"=>$ret["now"]["uv"],"pm25"=>$pm25,
                            "doc_type"=>"skin:city"
                         );
        $dd_id = "skin:weather:".$ret["basic"]["city"];    
        $this->set($dd_id, $dd_data);
        return $ret;
    }
    
    protected function getCondType(){       
        $type = "d";
        $time = date('H-i-s');
        $timeArr = explode('-', $time);
        $h = $timeArr[0];
        if($h >=18 || $h == 0){
            $type = "n";
        }
        return $type;
    }


    /**
     * get uv from http://php.weather.sina.com.cn/xml.php
     * 
     */
    public function getUV($city,$day=0){
        $uv = "";
        $city = mb_convert_encoding($city, "gb2312", "utf-8");
        $url = $this->api['uv'].$city."&day=".$day;
        $ret = $this->getXML($url);
        if($ret){
            $xml_array = json_decode(json_encode(simplexml_load_string($ret)),TRUE); //读取xml文件  
            if(isset($xml_array["Weather"]["zwx_l"])  && !empty($xml_array["Weather"]["zwx_l"]) ){
                $uv = $xml_array["Weather"]["zwx_l"]; //取得humans标签的对象数组 
            }
        }
        return $uv;
    }
    
    public function getUserList($page,$size){
        try{
        $ret = $this->getList("users", array("reg_time"=>array("start"=>0,"end"=>time())), $page, $size,"user");
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        $data = array();
        if($ret){
            $data["total_rows"] = $ret["total_rows"];
            $data["page"] = intval($page);
            $data["size"] = intval($size);
            $data["data"] = array();
            foreach($ret["rows"] as $val){
               $data["data"][] = $this->formatUser($val["value"]);
            }
        }
        
        return $data;
    }
    
    public function getIntlUserByUid($uid){
        $user = array("uid"=>0,"mobile"=>"","nickname"=>"","image"=>"","gender"=>3,"age"=>0,"province"=>"","area"=>"");
        $url = $this->api['user.get'];         
        try{
            $ret = $this->sendHttpRequest($url, array(
                'uid' => $uid
            ));
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        
        if(isset($ret["user"])){
            $user = $this->formatUser($ret["user"]);
        }
        return $user;
    }
    
    protected function formatUser($ret){
        $user = array("uid"=>0,"mobile"=>"","nickname"=>"","image"=>"","gender"=>3,"age"=>0,"province"=>"","area"=>"");
         if($ret){ 
            $user = array_merge($user, $ret);         
            $user["mobile"] = $user["phone"];
            if(strpos($user["mobile"], ":") !== false){
                $user["mobile"] = "";
            } 
            unset($user["password"],$user["phone"]);
            if(isset($user["name"])){
                unset($user["name"]);
            }
        }
        
        return $user;
    }
    
    public function setUserCity($uid,$city){
        $doc_id = "skin:city:".$uid;
        $data = array("uid"=>intval($uid),"city"=>$city);
        
        $this->set($doc_id, $data);
    }
    public function getUserCity($uid){
      $doc_id = "skin:city:".$uid;
      $data = $this->get($doc_id, false);
      return $data;
    }
    
    public function getCompleteTestUid($size=100){
        try{
            $ret = $this->getList("users", array("reg_time"=>array("start"=>0,"end"=>time())), 1, $size,"user");
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        
        $total_row = isset($ret["total_rows"])?$ret["total_rows"]:0;
        if($total_row){
            $times = intval(ceil($total_row / $size));
            for($i=1; $i <= $times; $i++){
                $this->getUserIndexTodo($i,$size);
            }
        }        
        
    }
    
    private function getUserIndexTodo($page,$size){
        $push_account_arr = array();
        try{
            $ret = $this->getList("users", array("reg_time"=>array("start"=>0,"end"=>time())), $page, $size,"user");
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        
        if(!$ret["total_rows"]){
            return false;
        }
        
        $articleModel = new ArticleModel;
        $messageModel = new MessageModel();       
        $type = $this->getTodoTypeByTime();
//        $type =3;
        if($type !== 2){
            $planPushData = $this->getPushPlanContent($type);
        }
        foreach($ret["rows"] as $val){
            $uid = $val["value"]["uid"];        
            $city = $this->getUserCity($uid);
            // `````````` 
//            if( ($uid == 10000002) || ($uid == 10000032) ){
            //````````
            if($city){               
                try{
                    $this->getWeather($city["city"]);
                    $data = $articleModel->getBackstagePushList($uid, $city["city"]);                      
                }catch(ApiException $se){

                }               

            }
            if(isset($data) && $data){
                        $pushData = $this->getPushContentByTodoList($data, $type);    
                        unset($data);
            }
            if(isset($planPushData) && $planPushData){
                            $pushData[] = $planPushData;
            }     
            if(isset($pushData) && $pushData){
                $push_flag = true;
                //获取push_id,保证一个push_id发一次。
                //可能为了测试同一只手机会用微信，手机号都登过，这样会产生2个不同的uid，但是是一样的push_id
                $push_doc_id="skin:app_push:".$uid;
                $push_account = $this->get($push_doc_id,false);
                if( $push_account && isset($push_account["push_id"]) ){
                    if( in_array($push_account["push_id"], $push_account_arr) ){
                        $push_flag = false;
                    }else{
                        array_push($push_account_arr, $push_account["push_id"]);
                    }
                }
                try{
                    $messageModel->sendTodo($uid, $pushData,$push_flag);
                } catch (ApiException $ex) {

                }                        
                unset($pushData,$push_flag,$push_doc_id,$push_account);
            }
            unset($uid);      
        //`````````
//        } //if uid = 10000002
        //`````````
        }
       
    }
    
    /**
     * 1-->上午12点之前, 2-->中午12点到下午3点，3-->下午3点到10点
     */
    protected function getTodoTypeByTime(){
        $type = 1;
        $time = date('H-m-s');
        $arr = explode('-', $time);
        $h = $arr[0];
        if($h >=12 && $h <=15){
            $type = 2;
        }else if($h >15 && $h <= 22){
            $type = 3;
        }
        return $type;
    }

     /**
     * @param int type 1-->上午12点之前, 2-->中午12点到下午3点，3-->下午3点到10点
     */
    protected function getPushPlanContent($type){
        $ret = array("title"=>"晨间护理","content"=>"一日之计在于晨，美丽肌肤从早晨开始。");
        
        $time = date('z-w ');
        $arr = explode('-', $time);
        $z = $arr[0];
        $w = $arr[1];
        
        if($type == 1){ //晨间护肤
            if($z % 2){ //odd day
                $ret["content"] = "晨间护理让自己光彩照人一整天。";
            }
        }else if($type == 3){ //晚间护肤           
            if($w == 0){ //Sunday
                $ret["title"] = "晚间升华护理";
                $ret["content"] = "周末升级护理，肌肤活力闪亮一整周。";
                if($z % 2){ // odd day and sunday
                    $ret["content"] = "周末非凡滋润+修复，肌肤整周元气满满。";
                }
            }else{ // not sunday
                $ret["title"] = "晚间护理";
                $ret["content"] = "夜晚修护，爱美的人从来不会忘记。";
                if($z % 2){ // odd day
                    $ret["content"] = "晚间是皮肤保养的黄金时段，你敢错过吗？";
                }
            }            
        }
     
        return $ret;
    }

    protected function getPushContentByTodoList($data,$type){
        $ret = array();
        if($type == 1 ){
            foreach($data as $val){
                    if(         
                            (strpos($val["title"],"防晒")!==false) || 
                            (strpos($val["title"],"控油")!==false) || 
                            (strpos($val["title"],"抗敏")!==false) 
                      ){
                        $ret[] = array("title"=>$val["title"],"content"=>$val["content"]);
                    }
            }
        }else if($type == 2){
            foreach($data as $val){
                    if(         
                            (strpos($val["title"],"补水")!==false) 
                      ){
                        $ret[] = array("title"=>$val["title"],"content"=>$val["content"]);
                    }
            }
        }else{
            foreach($data as $val){
                    if(         
                            (strpos($val["title"],"深层清洁")!==false) || 
                            (strpos($val["title"],"深度补水")!==false) 
                      ){
                        $ret[] = array("title"=>$val["title"],"content"=>$val["content"]);
                    }
            }
        }
        
        return $ret;
    }
    
    
    public function getImageList($page,$size){
        try{
        $ret = $this->getList("skin_analytics", array("create_at"=>array("start"=>0,"end"=>time())), $page, $size);
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        $data = array();
        if($ret){
            $data["total_rows"] = $ret["total_rows"];
            $data["page"] = intval($page);
            $data["size"] = intval($size);
            $data["data"] = array();
            foreach($ret["rows"] as $val){
               $data["data"][] = $val["value"] + array("doc_id"=>$val["doc_id"]);
            }
        }
        
        return $data;
    }
    
    /**
     * @param int $uid user id
     * @param string $doc_id c_plan id
     */
     public function addCplanClock($uid,$doc_id){
        $new = array();
        $doc_id = "skin:user:cplan:clock:".$uid;
        $old = $this->get($doc_id, false);
        if($old){
            $new = $old;
        }else{
            $new = array(   
                            "uid"=>$uid,
                            "doc_id"=>$doc_id,
                            "time"=>time(),
                            "count"=>0,
                            "doc_type"=>"skin_cplan_clock"
                        );
        }
        
        $new["count"] +=1;
        $this->set($doc_id, $new);
    }
    
    public function addClock($uid){
        $new = array();
        $doc_id = "skin:user:clock:".$uid;
        $old = $this->get($doc_id, false);
        if($old){
            $new = $old;
        }else{
            $new = array("uid"=>$uid,"clock"=>0,"doc_type"=>"skin_clock");
        }
        ++$new["clock"]; 
        
        $this->set($doc_id, $new);
    }
    
    public function getClockList($page,$size){
         try{
        $ret = $this->getList("skin_clocks", array("doc_type"=>"skin_clock"), $page, $size);
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        $data = array();
        if($ret){
            $data["total_rows"] = $ret["total_rows"];
            $data["page"] = intval($page);
            $data["size"] = intval($size);
            $data["data"] = array();
            foreach($ret["rows"] as $val){
               $data["data"][] = $val["value"] + array("doc_id"=>$val["doc_id"]);
            }
        }
        
        return $data;
    }
    
    public function getUserImageList($uid,$page=1,$size=10,$start=0,$end=1){        
        try{
            $ret = $this->getList(  "skin_user_analytics", 
                                    array("uid"=>$uid,"create_at"=>array("start"=>$start,"end"=>$end)), 
                                    $page, $size
                                  );
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427111,400);
        }
        $data = array();
        if($ret){
            $data["total_rows"] = $ret["total_rows"];
            $data["page"] = intval($page);
            $data["size"] = intval($size);
            $data["data"] = array();
            foreach($ret["rows"] as $val){
               $data["data"][] = array(
                                        "uid"=>$val["value"]["uid"],
                                        "bta" => $val["value"]["bta"],
                                        "type" => $val["value"]["type"],
                                        "body_part" => $val["value"]["body_part"],                                        
                                        "auto" => $val["value"]["auto"],
                                        "time" => date('Y-m-d H:i:s', $val["value"]["time"]),
                                        "url" => $val["value"]["url"],
                                        "results" => $val["value"]["results"],
                                        "create_at" => date('Y-m-d H:i:s',$val["value"]["create_at"])
                    );
            }
        }
        
        return $data;
    }
}
