<?php
namespace App\Models;

use Phalcon\Mvc\User\Component;
use Helper\Util;
use Httpful\Httpful;
use App\Exception\ServiceException;

abstract class ServiceBase extends Component
{

    /**
     * http request format ,default `json`
     *
     * @var string
     */
    protected $format = 'json';
    
    /**
     * 模块名称，亦称服务名称
     * @var string
     */
    protected $serviceName = '';

    /**
     * 签名生成
     */
    protected function getSafeSign()
    {
        $time = time();
        $user_agent = $this->buildUserAgent();
        return array(
            "api_key" => API_KEY,
            "api_token" => sha1(API_SECRET . $time),
            "time" => $time,
            "user_agent"=>$user_agent
        );
    }

    /**
     * default post
     *
     * @param string $url            
     * @param array $data
     *            NOTES: array values don't json_encode!!!!
     * @param bool $throwException 
     *              throw ServiceException? default is true
     * @param string $verb
     *            [optional]
     * @param bool $addSign
     *            need signature? default is true,will add token.. in request body
     * @param array $headers
     *            custom headers 
     * @param int $timeout
     *             second
     * @return array return array on success, on failure will throw ServiceException.
     */
    protected function sendHttpRequest($url, $data = array(), $throwException = true ,$verb = 'POST', $addSign = true, $headers = array(),$timeout=3)
    {
        if ($addSign) {
            $sign = $this->getSafeSign();
            $data = $sign + $data;
        }
        
        $res = Util::httpSender($url, $data, $verb, $headers, $this->format,$timeout);
        
        $this->_resp = $res['body'];
        $body = json_decode($res['body'], true);
        if ($res['httpStatus'] >= 400 || ( isset($body['_status']['_code']) && $body['_status']['_code'] != 200 && $throwException) ) {
            $this->logger->log('API-URL : ' . $url . ', DATA : ' . json_encode($data) . ',RETURN STATUS CODE: ' . $body['_status']['_code'] . ' , ' . 'MESSAGE:' . $body['_status']['_message']);
            
            throw new ServiceException($body['_status']['_message'], $body['_status']['_code'], $res['httpStatus'], $this->serviceName);
        }
        
        return $body;
    }
    
    protected function getXML($url,$data=array()){
         $ret = "";
         $res = Util::httpSender($url, $data, "GET", array(), "xml");
         if($res['httpStatus'] == 200){
             $ret = $res["body"];
         }
         return  $ret;
    }




    public function queryES($param,$page=1,$size=10){
        if($page>=1){ //page start from 0
            --$page;
        }
        $from = $page*$size;
        
//         $url = "http://qiwoelasticsearch.chinacloudapp.cn:9200/test/couchbaseDocument/_search?from=$from&size=$size";  
            $url = "http://10.0.0.12:9200/test/couchbaseDocument/_search?from=$from&size=$size";
    
        $res = Util::httpSender($url, $param); 

        $this->_resp = $res['body'];        
        $body = json_decode($res['body'], true);
        if(isset($body["status"]) &&  $body["status"]!== 200){
            throw new \App\Exception\ApiException($body["status"],$body["status"],"Query from elastic search failed!");
        }
        
        return $body;
    }
    
    public function getActive($title){
        $active = 0;
        
        $url = "http://114.55.19.45/composition/index?keywords=$title&p=1&rows=1";
        try{
        $res = Util::httpSender($url,array(),"GET");
        }catch(GatewayException $se){
            return $active;
        }
        $this->_resp = $res['body'];        
        $body = json_decode($res['body'], true);
        if(isset($body["data"]["items"][0]["active"]) && $body["data"]["items"][0]["active"]){
            $active = 1;
        }
        return $active;
    }
     
    
    public function get($doc_id,$throwException=true)
    {
        $url = $this->api['devicedata.get'];        
        $data = array("doc_id"=>$doc_id);        
        $response = $this->sendHttpRequest($url, $data,$throwException);      
        if(isset($response["data"])){
            return $response["data"];
        }else{
            return false;
        }
    }
    
     public function getMultiple($doc_id,$throwException=true)
    {
        $url = $this->api['devicedata.getMultiple'];        
        $data = array("doc_ids"=>$doc_id);     
        $response = $this->sendHttpRequest($url, $data,$throwException);  
        if(isset($response["data"])){
            return $response["data"];
        }else{
            return false;
        }
    }
    
    public function getMultipleTitleFromData($doc_id,$throwException=true)
    {
        $ret = $this->getMultiple($doc_id,$throwException);
        $data = array();
        if($ret){
            foreach($ret as $key=>$val){
                $data[] = array("doc_id"=>$key,"title"=>$val["title"]);
            }
        }
        return $data;
    }
    
    public function set($doc_id,$data){
        $url = $this->api['devicedata.set'];
        $data = array("doc_id"=>$doc_id,"data"=>$data);        
        $this->sendHttpRequest($url, $data);
    }
    public function write($data){
        $url = $this->api['devicedata.write'];
        $data = array("data"=>$data);        
        $this->sendHttpRequest($url, $data);
    }
    
    public function update($doc_id,$data){
        $url = $this->api['devicedata.update'];
        $data = array("doc_id"=>$doc_id,"data"=>$data);        
        $this->sendHttpRequest($url, $data);
    }
    
    public function delete($doc_id)
    {
        $url = $this->api['devicedata.deleteByDocIds'];        
        $data = array("doc_ids"=>$doc_id);   
        $this->sendHttpRequest($url, $data);   
    }   
   
    public function addMutiple($data){
        $url = $this->api['devicedata.write'];        
        $data = array("data"=>$data);   
        $ret = $this->sendHttpRequest($url, $data);  
        return $ret;
    }
    
    public function getList($view_table, $condition, $page = 1, $page_size = 10,$pre="devicedata"){
        $url = $this->api[$pre.'.getList'];   
        $data = array("view_table"=>$view_table,"condition"=>$condition,"page"=>$page,"page_size"=>$page_size);          
        $ret = $this->sendHttpRequest($url, $data); 
        return $ret["data"];
    }
    
    public function getstatisList($view_table, $condition, $page = 1, $page_size = 10){
        $url = $this->api['devicedata.getStatis'];   
        $data = array("view_table"=>$view_table,"condition"=>$condition,"page"=>$page,"page_size"=>$page_size);  
        $ret = $this->sendHttpRequest($url, $data);  
        return $ret["data"];
    }
    
     public function setCache($key, $value, $expires=0)
    {
        $url = $this->api['devicedata.setCache'];
        $param = array(
            'doc_id' => $key,
            'data' => $value,
            'expires' => $expires
        );
        $this->sendHttpRequest($url, $param);
    }

    public function deleteCache($key)
    {
        $url = $this->api['devicedata.deleteCache'];
        $param = array(
            'doc_id' => $key
        );       
        $this->sendHttpRequest($url, $param);        
    }

    public function getCache($key,$throwException)
    {
        $url = $this->api['devicedata.getCache'];
        $param = array(
            'doc_id' => $key
        );
        $response = $this->sendHttpRequest($url, $param,$throwException);      
        if(isset($response["data"])){
            return $response["data"];
        }else{
            return false;
        }
    }
    
    
   // devicemanage
     public function setDevice($did,$data){
        $url = $this->api['device.update'];
        $data["doc_type"] = "skin_device";
        $param = array("device_id"=>$did,"data"=>$data);        
        $this->sendHttpRequest($url, $param);
    }
    
    public function removeDevice($did)
    {
        $url = $this->api['device.remove'];        
        $param = array("key"=>$did);   
        $this->sendHttpRequest($url, $param);   
    }   
    
    public function getDevice($did,$throwException=true)
    {
        $url = $this->api['device.get'];        
        $param = array("key"=>$did);   
        $response = $this->sendHttpRequest($url, $param,$throwException);   
        if(isset($response["data"])){
            return $response["data"];
        }else{
            return false;
        }
    } 
    
    public function getDevices($ids,$throwException=true)
    {
        $url = $this->api['device.getMultiple'];        
        $param = array("doc_ids"=>$ids);   
        $response = $this->sendHttpRequest($url, $param,$throwException);   
        if(isset($response["data"])){
            return $response["data"];
        }else{
            return false;
        }
    } 
    
    //// other
    
    public function convertTime($create_at){         
        $str = $create_time = date('Y-m-d', $create_at);
        $date_1 = new \DateTime($create_time);
        $time =  date('Y-m-d');
        $date_2 = new \DateTime($time);
        $interval = $date_2->diff($date_1);  
        
        $days = $interval->days;
        if($days == 0){
            $str = date("H:m",$create_at);
        }else if($days == 1){
            $str = "昨天";
        }
       
        return $str;
    }  
    
    public function getIncreseId(){
        $id = 100000;
        $doc_id = "skin:increase:id";
        $now = $this->get($doc_id,false);
        if($now){
            $id = $now["id"];
        }
        
        ++$id;
        
        $this->set($doc_id, array("id"=>$id));
        return $id;
    }
    
    /**
     * search list by keywords, search by elastic search
     * 
     * @param string $keywords keywords e.g "防晒"
     * @param string $field doc field  e.g "title"
     * @param string $type doc type e.g "todo"
     * @param int $page default is 1
     * @param int $size default is 10
     * @return array
     */
    public function searchByWords($keywords,$field,$type,$page=1,$size=10){
        $multi = array("query"=>$keywords,"fields"=>array("doc.".$field) );
        $multi_param = array("query"=>array("multi_match"=>$multi)); 
        $filter_param = array("filter"=>array("term"=>array("doc.doc_type"=>"skin:".$type)));
        $param = array("query"=>array("filtered"=>$multi_param+$filter_param));
        
        $ret = $this->queryES($param,$page,$size);
        
        $data["total"] = $ret["hits"]["total"];
        $data["page"] = intval($page);
        $data["size"] = intval($size);
        
        foreach ($ret["hits"]["hits"] as $val) {      
            $data["list"][] =  array("id"=>  str_replace("{:DEVICEDATA:}", "",$val["_id"]) )+ $val["_source"]["doc"];
        }
       
        return $data;        
    }
    
     /**
     * @return string
     */
    public function buildUserAgent()
    {
        $user_agent = 'User-Agent: skin/Httpful/' . Httpful::VERSION . ' (cURL/';
        $curl = \curl_version();

        if (isset($curl['version'])) {
            $user_agent .= $curl['version'];
        } else {
            $user_agent .= '?.?.?';
        }
        $user_agent .= ')';
        return $user_agent;
    }

    /**
     * get remote file size 
     * 
     * @param string $url e.g "http://oss-qiwo-dev.qiwocloud1.com/skin/5790605c9901b.mp4"
     * @return int filesize
     */
  public function getRemoteFilesize($url=""){
        $length = 0;
        $matches = array();
        if($url){
            $ret =  get_headers($url);
            if($ret){
                foreach($ret as $val){
                    if (preg_match('/Content-Length: (\d+)/', $val, $matches)) {
                            $length = (int)$matches[1];
                            break;
                   }
                }
            }
        }
        
        return $length;
  }
  
}