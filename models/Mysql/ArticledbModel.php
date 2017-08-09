<?php

namespace App\Models\Mysql;

use App\Models\Mysql\BaseModel;

/**
 *  articledb model
 *
 * @author yang.f
 *        
 */
class ArticledbModel extends BaseModel {    
    
    /**
     * select all product from product 
     * 
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getEncyclopediaList($page=1,$size=10){
        $sql_count = "SELECT count(*) FROM encyclopedia";
        $sql_list = "SELECT * FROM encyclopedia ORDER BY top DESC,update_at DESC LIMIT ". ($page-1)*$size. ",".$size;
        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        $ret["rows"] = $this->db->getAll($sql_list);
        return $ret;
    }
    
    /**
     * query list by conditions
     * 
     * @param string $where   
     * @param int $page
     * @param int $size
     * @return array Description
     */
    public function getEncyclopediaListBySql($where="",$page=1,$size=10){
        $sql = "SELECT * FROM encyclopedia ";        
        if($where){
            $sql .= "WHERE ". $where." ";
        }        
        
        $sql_count = str_replace('*', 'count(*)', $sql);
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        $sql .= "LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * get encyclopedia by od
     * @param string $id
     * @return array
     */
    public function getEncyclopediaById($id){
        $sql = "SELECT * FROM encyclopedia WHERE `unique` = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
    /**
     * 
     * @param type $data
     * @param type $where
     * @return type
     */
    public function updateEncyclopedia($data,$where){
        $ret = $this->db->autoExecute('encyclopedia', $data, 2, $where);
        return $ret;
    }
    
    /**
     * add or minus one to field 
     * @param type $id
     * @param type $field
     * @param type $op
     */
    public function updateEncyclopediaField($id,$field="pageView",$op="+"){
        $sql = "UPDATE encyclopedia SET ".$field." = ( ". $field. " ". $op. " 1) WHERE `unique` = '".$id."'";
        $this->db->execute($sql);
    }
    
     /**
     * query todo list by conditions
     * 
     * @param string $field separated by ',' .e.g "id,title,content"
     * @param string $where   
     * @return array Description
     */
    public function getTodoList($field="*",$where=""){
        $sql =  "SELECT ".$field ." from todo";
        $sql_count = "SELECT count(*) FROM todo ";        
        if($where){
            $sql .= " WHERE ". $where." ";
            $sql_count .= " WHERE ". $where;
        }        
                
        $ret["total_rows"] = intval($this->db->getOne($sql_count));        
        $ret["rows"] = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * query todo list by conditions
     * 
     * @param string $where 
     * @param int $page
     * @param int $size
     * @return array Description
     */
    public function getTodoListBySql($where="",$page=1,$size=10){
        $sql = "SELECT * FROM todo ";        
        if($where){
            $sql .= "WHERE ". $where." ";
        }        
        
        $sql_count = str_replace('*', 'count(*)', $sql);
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        $sql .= "LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * get todo by id
     * 
     * @param string $id todo unique id
     * @param string $field todo field,default is "id,title,content"
     * @return array
     */
    public function getTodoById($id,$field="id,title,content"){
        $sql = "SELECT ".$field. "  FROM todo WHERE id = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
    /**
     * add todo
     * @param type $data
     * @return type
     */
     public function add($data){
        $ret = $this->db->autoExecute('todo', $data, 1);
        return $ret;
    }
    
     /**
     * update todo
     * @param array $data
     * @param string $where
     * @return type
     */
    public function updateTodo($data,$where){
        $ret = $this->db->autoExecute('todo',$data,2,$where);
        return $ret;
    }
    
    /**
     * delete todo by id
     * 
     * @param string $id todo id
     * @return type
     */
    public function deleteTodo($id){
        $sql = "DELETE FROM todo WHERE `id` = '".$id."'";
        $ret = $this->db->execute($sql);
        return $ret;
    }
    
    /**
     * add encyclopedia
     * @param array $data
     * @return type
     */
     public function addEncyclopedia($data){
        $ret = $this->db->autoExecute('encyclopedia', $data, 1);
        return $ret;
    }
    
    /**
     * delete encyclopedia by id
     * 
     * @param string $id encyclopedia unique
     * @return type
     */
    
    public function deleteEncyclopedia($id){
        $sql = "DELETE FROM encyclopedia WHERE `unique`='".$id."'";
        $ret = $this->db->execute($sql);
        return $ret;
    }
    
     /**
     * query push list by conditions
     * 
     * @param string $field separated by ',' .e.g "id,title,content"
     * @param string $where   
     * @return array Description
     */
    public function getPushList($field="*",$where=""){
        $sql =  "SELECT ".$field ." from push";
        $sql_count = "SELECT count(*) FROM todo ";        
        if($where){
            $sql .= " WHERE ". $where." ";
            $sql_count .= " WHERE ". $where;
        }        
                
        $ret["total_rows"] = intval($this->db->getOne($sql_count));        
        $ret["rows"] = $this->db->getAll($sql);
        return $ret;
    }  
    
    /**
     * query push list by conditions
     * 
     * @param string $where 
     * @param int $page
     * @param int $size
     * @return array Description
     */
    public function getPushListBySql($where="",$page=1,$size=10){
        $sql = "SELECT * FROM push ";        
        if($where){
            $sql .= "WHERE ". $where." ";
        }        
        
        $sql_count = str_replace('*', 'count(*)', $sql);
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        $sql .= "LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * get push by id
     * @param string $id
     * @return array
     */
    public function getPushById($id,$field="id,title,content"){
        $sql = "SELECT ".$field. "  FROM push WHERE id = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
    /**
     * add push
     * @param type $data
     * @return type
     */
     public function addPush($data){
        $ret = $this->db->autoExecute('push', $data, 1);
        return $ret;
    }
    
    /**
     * update todo
     * @param array $data
     * @param string $where
     * @return type
     */
    public function updatePush($data,$where){
        $ret = $this->db->autoExecute('push',$data,2,$where);
        return $ret;
    }
    
    /**
     * delete todo by id
     * 
     * @param string $id todo id
     * @return type
     */
    public function deletePush($id){
        $sql = "DELETE FROM push WHERE `id` = '".$id."'";
        $ret = $this->db->execute($sql);
        return $ret;
    }
    
    
    /**
     * get Cplan 
     * @param string $code skin code
     * @param int $type time type
     */
    public function getCplan($code,$type){
        $sql = "SELECT id,title,description,thumb from c_plan WHERE LOCATE('".$code."',`code`) AND time_type = ".$type;
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
     /**
     * get Cplan detail
     * @param string $id c_plan id
     * @param string $field 
     */
    public function getCplanDetailById($id,$field='*'){
        $sql = "SELECT ".$field." FROM c_plan WHERE id = '".$id."'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    /**
     * get Cplan steps
     * @param string $id c_plan id
     * @param string $field 
     */
    public function getCplanStepsById($id,$field='*'){
        $sql = "SELECT ".$field." FROM c_step WHERE c_plan_id = '".$id."' ORDER BY step";
        $ret = $this->db->getAll($sql);
        return $ret;
    }
}
