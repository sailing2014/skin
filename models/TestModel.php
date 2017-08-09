<?php

namespace App\Models;

use App\Models\ServiceBase;
use App\Exception\ApiException;
use App\Exception\ServiceException;

/**
 *  test model
 *
 * @author yang.f
 *        
 */
class TestModel extends ServiceBase {

    private $doc_id = "skin:test:types";  
    
    public function __construct() {
        $this->serviceName = 'test';
    }  
    
    public function updateTestType($testType,$title,$order=0)
    {    
      
        $this->checkTitle($title,$testType);
        
        $url = $this->api['devicedata.update'];  
        $data = array("doc_id"=>  $this->doc_id,
                       "data"=>array( "type".$testType => array("test_type"=>$testType,
                                                                "title"=>$title,
                                                                "order"=>$order,
                                                                "time"=>time()
                                                               )
                                    )
                      );
          
        $this->sendHttpRequest($url, $data);      
    }
   
    private function checkTitle($title,$type=0){
        $original = $this->get($this->doc_id);
        $errorcode = 427110;
        if($type){
            unset($original["type".$type]);
            $errorcode = 427113;
        }
        foreach ($original as $val) {
           if($val["title"] == $title){               
                    throw new ApiException($errorcode,400," This title exists!");             
            }
        }
  }

    public function getTestType()
    {
        $url = $this->api['devicedata.get'];
        
        $data = array("doc_id"=>"skin:test:types");
        
        $response = $this->sendHttpRequest($url, $data);           
        $ret = array();        
        foreach ($response["data"] as $val){
            unset($val["time"],$val["order"]);
            $ret[] = $val;
        }        
        return $ret;
    }
    
  public function addTestType($title,$order){
      $data = $this->get($this->doc_id, false);
      $type = array();
      $test_type = 1;
      
      if($data){
        foreach ($data as $val) {
            if($val["title"] == $title){
                throw new ApiException(427110,400," This title exists!");
            }
            $type[] = $val["test_type"];
        }
        if($type){            
            rsort($type);
            $count = count($type);
            if($type[$count-1] >=2){
                $test_type = $type[$count-1] -1;
            }else{
                $test_type += $type[0];
            }
        }
      }
      
      $url = $this->api['devicedata.update'];        
      $type_data = array(
                            "doc_id"=>"skin:test:types",
                            "data"=>array( "type".$test_type => array("test_type"=>$test_type,
                                                                    "title"=>$title,
                                                                    "order"=>$order,
                                                                    "time"=>time()
                                                                   )
                                       )
                      );
          
      $this->sendHttpRequest($url, $type_data);    
      return $test_type;
  }

    
    public function getIntlTestType()
    {
        $url = $this->api['devicedata.get'];
        
        $data = array("doc_id"=>"skin:test:types");
        
        $response = $this->sendHttpRequest($url, $data);           
        $ret = array();        
        foreach ($response["data"] as $val){
            $ret[] = $val;
        }        
        return $ret;
    }
    
    public function getIntlTestByType($test_type)
    {
        $data = $this->get($this->doc_id);
        if(isset($data["type".$test_type])){
            $ret = $data["type".$test_type];          
            return $ret;
        }else{
            throw new ApiException(427111,400);
        }
    }
    
    public function deleteIntlTestByType($test_type){   
              
        $data = $this->get($this->doc_id);
        if(isset($data["type".$test_type])){
            unset($data["type".$test_type]);      
            $this->set($this->doc_id, $data);
        }else{
            throw new ApiException(427115,400);
        }
        
    }
    
    public function addResults($test_type,$results){
        $doc_id = "skin:results:".$test_type;
        try{
            $this->set($doc_id, array("test_type"=>$test_type,"results"=>$results));
        }  catch (ServiceException $se){
            throw new ApiException(427112,400,"Set results failed!");
        }
    }
    
    public function getResult($test_type){
        $doc_id = "skin:results:".$test_type;       
        $data = $this->get($doc_id,false);
     
        if($data){
            return $data;
        } else{
            throw new ApiException(427111,400,"Get results failed!");
        }
    }
    
    public function deleteResults($test_type){
        $doc_id = "skin:results:".$test_type;
        try{
            $this->delete($doc_id);
        } catch (ServiceException $se) {
            throw new ApiException(427115,400,"Delete results failed!");
        }
    }
    
    public function getResultTitle($test_type,$result){
        $title = "";
        
        $doc_id = "skin:results:".$test_type;       
        $data = $this->get($doc_id,false);
     
        if($data){
            foreach($data["results"] as $val){
                if($val["result"] == $result){
                    $title = $val["title"];
                    break;
                }
            }
        }
        
        return $title;
    }
    
    public function addTestConfig($test_type,$result,$max,$min=0,$title="",$content=""){
        $doc_id = "skin:result:config:".$test_type.":result:".$result;         
        $old = $this->get($doc_id, false);
        if($old){
            throw new ApiException(401,400,"This test type and result exists!");
        }else{
            $time = time();
            $data = array(  "test_type"=>$test_type,"result"=>$result,
                            "max"=>$max,"min"=>$min,
                            "title"=>$title,"content"=>$content,
                            "create_at"=>$time,"update_at"=>$time
                         );
            try{
                $this->set($doc_id, $data);
                $this->setResultConfig($test_type,$result,$max,$min);
                $this->setConfigList($doc_id);
            }  catch (\App\Exception\ServiceException $se){
                throw new ApiException(427110,400,"add test type and result failed!");
            }
        }
        return $doc_id;
    }
    private function setConfigList($doc_id){
        $list_doc_id = "skin:result:config:list";
        $old = $this->get($list_doc_id,false);
        $data = array();        
        if($old){
            $data = $old;
        }
        $data[$doc_id] = array("doc_id"=>$doc_id);
        $this->set($list_doc_id, $data);
    }
    
    private function deleteConfigList($doc_id){
        $list_doc_id = "skin:result:config:list";
        try{
            $data = $this->get($list_doc_id);
            unset($data[$doc_id]);
            if($data){
                $this->set($list_doc_id, $data);
            }else{
                $this->delete($list_doc_id);
            }
        }catch (ServiceException $se){
            throw new ApiException(427115,400,"Config list doesn't exist!");
        }
    }
    
    public function getTestConfigList(){
        $doc_id = "skin:result:config:list";
        try{
            $list = $this->get($doc_id);
            $data = array();
            foreach($list as $key=>$val){
                $tmp = $this->get($key,false);
                if($tmp){
                    $data[] = array("doc_id"=>$key) + $tmp;
                }
                unset($tmp);
            }
            return $data;
        }catch (ServiceException $se){
            throw new ApiException(427111,400,"Config list doesn't exist!");
        }
    }


    public function updateTestConfig($doc_id,$test_type,$result,$max,$min,$title,$content){
        try{
            $old = $this->get($doc_id);
            $old_test_type = $old["test_type"];
            $old_result = $old["result"];       
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427113,400,"This test config doesn't exist!");
        }
        
        if( ($test_type == $old_test_type) && ($result == $old_result) ){
                $time = time();
                $data = array(                                  
                                "max"=>$max,"min"=>$min,
                                "title"=>$title,"content"=>$content,
                                "update_at"=>$time
                              );
                            
                $this->update($doc_id, $data);
                $this->setResultConfig($test_type,$result,$max,$min);
            }else{
                throw new ApiException(427113,400,"test_type and result cannot be modified!");
            }
    }
    
  
    
    public function deleteTestTypeConfig($doc_id){
        try{
            $data = $this->get($doc_id);
            $test_type = $data["test_type"];
            $result = $data["result"];          
           
            $this->deleteResultConfig($test_type,$result);
            $this->deleteConfigList($doc_id);
            $this->delete($doc_id);
        }catch(\App\Exception\ServiceException $se){
            throw new ApiException(427115,400,"This test config doesn't exist!");
        }
    }

    public function getTestConfig($doc_id){  
        $data = array("doc_id"=>$doc_id);
        try{
            $data += $this->get($doc_id);
            return $data;
        } catch (ServiceException $se) {
            throw new ApiException(427111,400,"This test config doesn't exist!");
        }
        
    }
    
    public function getTestTypeConfig($test_type){
        $data = array();
        $list_config_id = "skin:result:config:test:".$test_type;
         try{
            $list = $this->get($list_config_id);
         }  catch (ServiceException $se){
              throw new ApiException(427111,400,"This test type config doesn't exist!");
         }
         
         $doc_id = "skin:result:config:".$test_type.":result:";
         foreach ($list as $val){
             $config_doc_id = $doc_id.$val["result"];
             $tmp = $this->get($config_doc_id, false);
             if($tmp){
                 $data[] = array("doc_id"=>$config_doc_id) + $tmp;
             }
             unset($tmp,$config_doc_id);
         }
         
         return $data;
    }
    
    
    private function setResultConfig($test_type,$result,$max,$min=0){
        $list_config_id = "skin:result:config:test:".$test_type;
        $list = $this->get($list_config_id,false);
        $data = array();
        if($list){
            $data = $list;
        }
        $data["result".$result] = array("result"=>$result,"max"=>$max,"min"=>$min);
        
        $this->set($list_config_id, $data);
    }
    
    private function deleteResultConfig($test_type,$result){
        $list_config_id = "skin:result:config:test:".$test_type;
        try{
                $list = $this->get($list_config_id);  
                unset($list["result".$result]);
                if($list){
                $this->set($list_config_id, $list);
                }else{
                    $this->delete($list_config_id);
                }
            }  catch (ServiceException $se){
                throw new ApiException(427115,400,"This testtype config doesn't exist!");
            }
    }
    
    private function getResultConfig($test_type){
         $list_config_id = "skin:result:config:test:".$test_type;
         try{
            $data = $this->get($list_config_id);
         }  catch (ServiceException $se){
              throw new ApiException(427115,400,"This test type config doesn't exist!");
         }
         return $data;
    }
    
    public function getAnalyticsResult($test_type,$score){
        $result = 1;
        $config = $this->getResultConfig($test_type);
        if($config){
            foreach ($config as $val) {
                if($val["max"] >=$score && $val["min"]<= $score){
                    $result = $val["result"];
                    break;
                }
            }
        }
        
        $test_title = $this->getResultTitle($test_type, $result);
        $ret = array("test_type"=>$test_type,"result"=>$result,"result_title"=>$test_title);        
        
        $doc_id = "skin:result:config:".$test_type.":result:".$result;       
        $data = $this->get($doc_id,false);        
        if($data){
            $ret += array("title"=>$data["title"],"content"=>$data["content"]);
        }else{
            $ret += array("title"=>"@_@","content"=>"小编很懒O_O,内容啥都没有O");
        }
        return $ret;
    }
}
