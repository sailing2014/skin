<?php

namespace App\Models\Mysql;

use App\Models\Mysql\BaseModel;

/**
 *  productdb model
 *
 * @author yang.f
 *        
 */
class ProductdbModel extends BaseModel {    
   
   function getTitle(){
        $sql = "SELECT title from product where title like '%美%'";
      
        $ret = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * select all product from product where component id is not empty
     * 
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getList($page=1,$size=10){
        $sql_count = "SELECT count(*) FROM product WHERE component_id != ''";
        $sql_list = "SELECT * FROM product WHERE component_id != '' ORDER BY top DESC,update_at DESC "
                    . " LIMIT ". ($page-1)*$size. ",".$size;
        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        $ret["rows"] = $this->db->getAll($sql_list);
        return $ret;
    }
    
 /**
     * query internal list by conditions
     * 
     * @param string $field
     * @param string $where   
     * @param int $page
     * @param int $size
     * @return array Description
     */
    public function getIntlProductList($field="*",$where="",$page=1,$size=10){
        $sql = "SELECT ".$field." FROM product ";   
        $sql_count = "SELECT count(*) FROM product ";
        if($where){
            $sql .= "WHERE ( ". $where.") ";
            $sql_count .= "WHERE (". $where. ")";
        }
        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        
        $sql .= " ORDER by top,update_at LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);        
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
        $sql = "SELECT * FROM product WHERE (component_id != '')";        
        if($where){
            $sql .= " AND (". $where.") ";
        }      
        
        $sql_count = str_replace('*', 'count(*)', $sql);        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        
        $sql .= "LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);        
        return $ret;
    }
    
    /**
     * query list by conditions
     * 
     * @param string $field
     * @param string $where   
     * @param int $page
     * @param int $size
     * @return array Description
     */
    public function getFieldList($field="*",$where="",$page=1,$size=10){
        $sql = "SELECT ".$field." FROM product WHERE (component_id != '') ";   
        $sql_count = "SELECT count(*) FROM product WHERE (component_id != '') ";
        if($where){
            $sql .= "AND ( ". $where.") ";
            $sql_count .= "AND (". $where. ")";
        }
        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        
        $sql .= "LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);        
        return $ret;
    }
    
        /**
     * query list by conditions
     * 
     * @param string $field
     * @param string $where   
     * @param int $page
     * @param int $size
     * @return array Description
     */
    public function getIntlFieldList($field="*",$where="",$page=1,$size=10){
        $sql = "SELECT ".$field." FROM product ";   
        $sql_count = "SELECT count(*) FROM product ";
        if($where){
            $sql .= "WHERE ( ". $where.") ";
            $sql_count .= "WHERE (". $where. ")";
        }
        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        
        $sql .= "LIMIT ". ($page-1)*$size. ",".$size;
        $ret["rows"] = $this->db->getAll($sql);        
        return $ret;
    }
    
    public function addProduct($data){
        $ret = $this->db->autoExecute('product', $data, 1);
        return $ret;
    }
    
    public function updateProduct($data,$where){
        $ret = $this->db->autoExecute('product', $data, 2, $where);
        return $ret;
    }
    
    /**
     * get product by id
     * @param string $id
     * @return array
     */
    public function getProductById($id){
        $sql = "SELECT * FROM product WHERE id = '". $id. "'";
        $ret = $this->db->getRow($sql);
        return $ret;
    }
    
    /**
     * add or minus one to field 
     * @param type $id
     * @param type $field
     * @param type $op
     */
    public function updateProductField($id,$field="pageView",$op="+"){
        $sql = "UPDATE product SET ".$field." = ( ". $field. " ". $op. " 1) WHERE `id` = '".$id."'";
        $ret = $this->db->execute($sql);
        return $ret;
    }
    
    /**
     * delete product by id
     *  
     * @param string $id product id separated by ',' e.d  7203,7204,7205
     */
    public function deleteProductById($id){
        $sql = "DELETE FROM product WHERE id in (". $id. ")";
        $ret = $this->db->query($sql);
        return $ret;
    }
    
    public function addBrand($data){
        $ret = $this->db->autoExecute('brand', $data, 1);
        return $ret;
    }    
    public function updateBrand($data,$where){
        $ret = $this->db->autoExecute('brand', $data, 2,$where);
        return $ret;
    }   
     public function deleteBrandById($id){
        $sql = "DELETE FROM brand WHERE id in (". $id. ")";
        $ret = $this->db->query($sql);
        return $ret;
    } 
    
    public function getBrandId($keywords){
         $sql = "SELECT id FROM brand WHERE ( title like '%". 
                $keywords. "%' OR EN_title like '%" .$keywords. "%')";
         $ret = $this->db->getAll($sql);
         return $ret;
    }
    
    public function getComponentListBySql($field='*',$where="",$page,$size){
        $sql = "SELECT ". $field. " FROM component";
        $sql_count = "SELECT count(*) FROM component";
        if($where){
            $sql .= " WHERE ".$where;
            $sql_count .= " WHERE ".$where;
        }
        
        $sql .= " LIMIT ". ($page-1)*$size. ",".$size;
        
        $ret["total_rows"] = intval($this->db->getOne($sql_count));
        $ret["rows"] = $this->db->getAll($sql);
        
        return $ret;
    }
    
    /**
     * get component by id
     *  
     * @param string $id component id separated by ',' e.d  7203,7204,7205
     * @param string $field select field
     */
    public function getComponentById($id,$field="*"){
        $sql = "SELECT ". $field. " FROM component WHERE id in (". $id. ")";
        $ret = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * delete component by id
     *  
     * @param string $id component id separated by ',' e.d  7203,7204,7205
     */
    public function deleteComponentById($id){
        $sql = "DELETE FROM component WHERE id in (". $id. ")";
        $ret = $this->db->query($sql);
        return $ret;
    }
    
    public function addComponent($data){
        $ret = $this->db->autoExecute('component', $data, 1);
        return $ret;
    }
    
    public function updateComponent($data,$where){
        $ret = $this->db->autoExecute('component', $data, 2,$where);
        return $ret;
    }
    
    /**
     * get all brand
     * 
     * @param string $field
     * @return type
     */
    public function getBrandList($field='*'){
        $sql = "SELECT ".$field ." FROM brand order by section,title DESC";
        $ret = $this->db->getAll($sql);
        return $ret;
    }
    
    /**
     * get brand by id
     * 
     * @param string $id brand id
     */
    public function getBrandById($id,$field='*'){
        $sql = "SELECT ".$field ." FROM brand WHERE id in (". $id. ")";
        $ret = $this->db->getAll($sql);
        return $ret;
    }
}
