<?php
namespace App\Models\Mysql;

use Database\Mysql;

/**
 * Base model class
 * 
 */
class BaseModel 
{	
    protected $db = null;

    public function __construct() {
        $config_file = "/home/skin/conf/database.php";        
        if (file_exists($config_file)) {
           $dbconfig = require $config_file;   
            $this->db = Mysql::getIntance($dbconfig['mysql_master'], $dbconfig['mysql_slave']);
        }
    }
	
}