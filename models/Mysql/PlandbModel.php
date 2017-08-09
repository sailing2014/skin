<?php

namespace App\Models\Mysql;

use App\Models\Mysql\BaseModel;

/**
 *  plan db model
 *
 * @author yang.f
 *        
 */
class PlandbModel extends BaseModel { 
  
    public function add($data){
        $ret = $this->db->autoExecute('step', $data, 1);
        return $ret;
    }
    
    /**
     * 
     * @param array $data 
     * @param string $table e.d. "step","day","step"
     * @return type
     */
    public function addToTable($data,$table){
        $ret = $this->db->autoExecute($table,$data,1);
        return $ret;
    }
    
    /**
     * 
     * @param array $data field array
     * @param string $where
     * @param string $table table
     * @return type
     */
    public function updateTable($data,$where,$table){
        $ret = $this->db->autoExecute($table,$data,2,$where);
        return $ret;
    }    

    
    /**
     * get plan by title
     * 
     * @param string $title
     * @param string $field e.g "id,title,thumb" 
     */
    public function searchPlanByWords($title,$field="*"){
        $sql = "SELECT ". $field. " FROM plan WHERE title LIKE '%".$title. "%'";
        $ret = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * select all plans from plan 
     * 
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getList($page=1,$size=10){
        $sql_count = "SELECT count(*) FROM plan";
        $sql_list = "SELECT * FROM plan ORDER BY top DESC,update_at DESC LIMIT ". ($page-1)*$size. ",".$size;
        
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
    public function getListBySql($where="",$page=1,$size=10){
        $sql = "SELECT * FROM plan ";        
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
     * get plan by id
     * @param string $id
     * @return array
     */
    public function getPlanById($id){
        $sql = "SELECT * FROM plan WHERE id = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
    /**
     * get day by day id
     * @param string $id day id
     * @return array
     */
    public function getDayById($id){
        $sql = "SELECT * FROM day WHERE id = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
    
    /**
     * get step by step id
     * @param string $id step id
     * @return array
     */
    public function getStepById($id){
        $sql = "SELECT * FROM step WHERE id = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
     /**
     * get day by day id
     * @param string $id day id
     * @return array
     */
    public function getDayStepsById($id){
        $sql = "SELECT * FROM step WHERE day_id = '". $id. "' ORDER by step ASC,update_at ASC";
        $ret = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * get day by plan id
     * @param string $id plan id
     * @param string $field default is '*'.e.g "id,step"
     * @param string $where default is null . e.g "step = 2"
     * @return array
     */
    public function getDaysByPlanId($id,$field="*",$where=""){
       
        if($where){
            $where = "WHERE ".$where. " AND ";
        }else{
             $where = "WHERE ";
        }
        
        $sql = "SELECT ". $field. " FROM `day`  ".$where ." plan_id = '". $id. "' ORDER by step ASC,update_at ASC";        
        $ret = $this->db->getAll($sql);
        return $ret;
    }    

    
    /**
     * Delete plan and plan days and plan steps by plan id
     * 
     * @param string $id plan id
     */
    public function deletePlan($id){       
       $days = $this->getDaysByPlanId($id, "id");
       $day_id = "";
       if($days){
           foreach($days as $day){
               $day_id .= "'". $day["id"]."',";
           }
           $day_id = substr($day_id, 0,-1);
           $this->deleteDay($day_id);
       }
       $sql = "DELETE FROM plan WHERE id = '". $id. "'";
       $ret = $this->db->execute($sql);
       return $ret;
    }
    
    /**
     * delete steps
     * 
     * @param string $step_id separated by ','
     */
    public function deleteSteps($step_id){
        $step_delete_sql = "DELETE FROM step WHERE id in (". $step_id. ")";
        $ret = $this->db->execute($step_delete_sql);
        return $ret;
    }
    
    /**
     * delete day and its steps
     * 
     * @param string $day_id separated by ','
     */
    public function deleteDay($day_id){      
        $ret = false;
        $step_id = "";
       if($day_id){
           $sql = "SELECT id FROM step WHERE day_id in (". $day_id. ")";
           $steps = $this->db->getAll($sql);           
       }
      
       if(isset($steps) && $steps){
           foreach($steps as $step){
               $step_id .= "'".$step["id"]. "',";
           }           
       }
       
       if($step_id){
           $step_id = substr($step_id, 0,-1);
           $this->deleteSteps($step_id);
       }
       
       if($day_id){
           $day_delete_sql = "DELETE FROM day WHERE id in (". $day_id. ")";
           $ret = $this->db->execute($day_delete_sql);
       }
       return $ret;
    }
    
     /**
     * add or minus one to field ParticipantNum
     * @param type $id
     * @param type $op
     */
    public function updatePlanParticipantNum($id,$op='+'){
        $sql = "UPDATE plan SET participant_num = ( participant_num ". $op. "  1) WHERE `id` = '".$id."'";
        $ret = $this->db->execute($sql);
        return $ret;
    }  
   
}
