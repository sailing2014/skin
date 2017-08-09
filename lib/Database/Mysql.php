<?php

namespace Database;

//----------------------------------------------------
//      Master/Slave数据库读写分开操作类
//
// 作者: heiyeluren <http://blog.csdn.net/heiyeshuwu>
// 描述：支持所有写操作在一台Master执行，所有读操作在
//         Slave执行，并且能够支持多台Slave主机
//----------------------------------------------------
/**
 * 常量定义
 */
define("_DB_INSERT", 1);
define("_DB_UPDATE", 2);

/**
 * DB Common class
 *
 * 描述：能够分别处理一台Master写操作，多台Slave读操作
 */
class Mysql {
    private static $instance;
    /**
     * 数据库配置信息
     */
    var $wdbConf = array();
    var $rdbConf = array();
    /**
     * Master数据库连接
     */
    var $wdbConn = null;
    /**
     * Slave数据库连接
     */
    var $rdbConn = array();
    /**
     * 数据库结果
     */
    var $dbResult;
    /**
     * 数据库查询结果集
     */
    var $dbRecord;
    /**
     * SQL语句
     */
    var $dbSql;
    /**
     * 数据库编码
     */
    var $dbCharset = "UTF8";
    /**
     * 数据库版本
     */
    var $dbVersion = "4.1";

    /**
     * 初始化的时候是否要连接到数据库
     */
    var $isInitConn = false;

    /**
     * 是否要设置字符集
     */
    var $isCharset = true;

    /**
     * 数据库结果集提取方式
     */
    var $fetchMode = MYSQL_ASSOC;

    /**
     * 执行中发生错误是否记录日志
     */
    var $isLog = true;

    /**
     * 是否查询出错的时候终止脚本执行
     */
    var $isExit = false;

    //------------------------
    //
    //  基础的DB操作
    //
    //------------------------

    /**
     * 构造函数
     * 
     * 传递配置信息，配置信息数组结构：
     * $masterConf = array(
     *        "host"    => Master数据库主机地址
     *         "port"  => 端口
     *        "user"    => 登录用户名
     *        "pwd"    => 登录密码
     *        "db"    => 默认连接的数据库
     *    );
     * $slaveConf = array(
     *        "host"    => Slave1数据库主机地址|Slave2数据库主机地址|...
     *        "port"  => 端口
     *        "user"    => 登录用户名
     *        "pwd"    => 登录密码
     *        "db"    => 默认连接的数据库
     *    );
     */
    private function __construct($masterConf, $slaveConf = array()) {
        //构造数据库配置信息
        if (is_array($masterConf) && !empty($masterConf)) {
            $this->wdbConf = $masterConf;
        }
        if (!is_array($slaveConf) || empty($slaveConf)) {
            $this->rdbConf = $masterConf;
        } else {
            $this->rdbConf = $slaveConf;
        }
        //初始化连接（一般不推荐）
        if ($this->isInitConn) {
            $this->getDbWriteConn();
            $this->getDbReadConn();
        }
    }

    private function __clone() {
        
    }

    public static function getIntance($mysql_master, $mysql_slave) {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($mysql_master, $mysql_slave);
        }
        return self::$instance;
    }

    /**
     * 获取Master的写数据连接
     */
    function getDbWriteConn() {
        //判断是否已经连接
        if ($this->wdbConn && is_resource($this->wdbConn)) {
            return $this->wdbConn;
        }
        //没有连接则自行处理
        $db = $this->connect($this->wdbConf['host'].":".$this->wdbConf['port'], $this->wdbConf['user'], $this->wdbConf['pwd'], $this->wdbConf['db']);
        if (!$db || !is_resource($db)) {
            return false;
        }
        $this->wdbConn = $db;
        return $this->wdbConn;
    }

    /**
     * 获取Slave的读数据连接
     */
    function getDbReadConn() {
        //如果有可用的Slave连接，随机挑选一台Slave
        if (is_array($this->rdbConn) && !empty($this->rdbConn)) {
            $key = array_rand($this->rdbConn);
            if (isset($this->rdbConn[$key]) && is_resource($this->rdbConn[$key])) {
                return $this->rdbConn[$key];
            }
        }
        //连接到所有Slave数据库，如果没有可用的Slave机则调用Master
        $arrHost = explode("|", $this->rdbConf['host']);
        if (!is_array($arrHost) || empty($arrHost)) {
            return $this->getDbWriteConn();
        }
        $this->rdbConn = array();
        foreach ($arrHost as $tmpHost) {
            $db = $this->connect($tmpHost.":".$this->rdbConf['port'], $this->rdbConf['user'], $this->rdbConf['pwd'], $this->rdbConf['db']);
            if ($db && is_resource($db)) {
                $this->rdbConn[] = $db;
            }
        }
        //如果没有一台可用的Slave则调用Master
        if (!is_array($this->rdbConn) || empty($this->rdbConn)) {
            $this->errorLog("Not availability slave db connection, call master db connection");
            return $this->getDbWriteConn();
        }
        //随机在已连接的Slave机中选择一台
        $key = array_rand($this->rdbConn);
        if (isset($this->rdbConn[$key]) && is_resource($this->rdbConn[$key])) {
            return $this->rdbConn[$key];
        }
        //如果选择的slave机器是无效的，并且可用的slave机器大于一台则循环遍历所有能用的slave机器
        if (count($this->rdbConn) > 1) {
            foreach ($this->rdbConn as $conn) {
                if (is_resource($conn)) {
                    return $conn;
                }
            }
        }
        //如果没有可用的Slave连接，则继续使用Master连接
        return $this->getDbWriteConn();
    }

    /**
     * 连接到MySQL数据库公共方法
     */
    function connect($dbHost, $dbUser, $dbPasswd, $dbDatabase) {
        //连接数据库主机
        $db = mysql_connect($dbHost, $dbUser, $dbPasswd);
        if (!$db) {
            $this->errorLog("Mysql connect " . $dbHost . " failed");
            return false;
        }
        //选定数据库
        if (!mysql_select_db($dbDatabase, $db)) {
            $this->errorLog("select db $dbDatabase failed", $db);
            return false;
        }
        //设置字符集
        if ($this->isCharset) {
            if ($this->dbVersion == '') {
                $res = mysql_query("SELECT VERSION()");
                $this->dbVersion = mysql_result($res, 0);
            }

            if ($this->dbCharset != '' && preg_match("/^(5.|4.1)/", $this->dbVersion)) {
                if (mysql_query("SET NAMES '" . $this->dbCharset . "'", $db) === false) {
                    $this->errorLog("Set db_host '$dbHost' charset=" . $this->dbCharset . " failed.", $db);
                    return false;
                }
            }
        }
        return $db;
    }

    /**
     * 关闭数据库连接
     */
    function disconnect($dbConn = null, $closeAll = false) {
        //关闭指定数据库连接
        if ($dbConn && is_resource($dbConn)) {
            mysql_close($dbConn);
            $dbConn = null;
        }
        //关闭所有数据库连接
        if ($closeAll) {
            if ($this->rdbConn && is_resource($this->rdbConn)) {
                mysql_close($this->rdbConn);
                $this->rdbConn = null;
            }
            if (is_array($this->rdbConn) && !empty($this->rdbConn)) {
                foreach ($this->rdbConn as $conn) {
                    if ($conn && is_resource($conn)) {
                        mysql_close($conn);
                    }
                }
                $this->rdbConn = array();
            }
        }
        return true;
    }

    /**
     * 选择数据库
     */
    function selectDb($dbName, $dbConn = null) {
        //重新选择一个连接的数据库
        if ($dbConn && is_resource($dbConn)) {
            if (!mysql_select_db($dbName, $dbConn)) {
                $this->errorLog("Select database:$dbName failed.", $dbConn);
                return false;
            }
            return true;
        }
        //重新选择所有连接的数据库
        if ($this->wdbConn && is_resource($this->wdbConn)) {
            if (!mysql_select_db($dbName, $this->wdbConn)) {
                $this->errorLog("Select database:$dbName failed.", $this->wdbConn);
                return false;
            }
        }
        if (is_array($this->rdbConn && !empty($this->rdbConn))) {
            foreach ($this->rdbConn as $conn) {
                if ($conn && is_resource($conn)) {
                    if (!mysql_select_db($dbName, $conn)) {
                        $this->errorLog("Select database:$dbName failed.", $conn);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * 执行SQL语句（底层操作）
     */
    function query($sql, $isMaster = false) {
        if (trim($sql) == "") {
            $this->errorLog("Sql query is empty.");
            return false;
        }
        //获取执行SQL的数据库连接
        if (!$isMaster) {
            $optType = trim(strtolower(array_shift(explode(" ", ltrim($sql)))));
        }
        if ($isMaster || $optType != "select") {
            $dbConn = $this->getDbWriteConn();
        } else {
            $dbConn = $this->getDbReadConn();
        }
        if (!$dbConn || !is_resource($dbConn)) {
            $this->errorLog("Not availability db connection. Query SQL:" . $sql);
            if ($this->isExit) {
                exit;
            }
            return false;
        }
        //执行查询
        $this->dbSql = $sql;
        $this->dbResult = null;
        $this->dbResult = @mysql_query($sql, $dbConn);
        if ($this->dbResult === false) {
            $this->errorLog("Query sql failed. SQL:" . $sql, $dbConn);
            if ($this->isExit) {
                exit;
            }
            return false;
        }
        return true;
    }

    /**
     * 错误日志
     */
    function errorLog($msg = '', $conn = null) {
        if (!$this->isLog) {
            return;
        }
        if ($msg == '' && !$conn) {
            return false;
        }
        $log = "MySQL Error: $msg";
        if ($conn && is_resource($conn)) {
            $log .= " mysql_msg:" . mysql_error($conn);
        }
        $log .= " [" . date("Y-m-d H:i:s") . "]";
        //echo($log);
        error_log($log);
        return true;
    }

    //--------------------------
    //
    //       数据获取接口
    //
    //--------------------------
    /**
     * 获取SQL执行的全部结果集(二维数组)
     *
     * @param string $sql 需要执行查询的SQL语句
     * @return 成功返回查询结果的二维数组,失败返回false
     */
    function getAll($sql, $isMaster = false) {
        if (!$this->query($sql, $isMaster)) {
            return false;
        }
        $this->dbRecord = array();
        while ($row = @mysql_fetch_array($this->dbResult, $this->fetchMode)) {
            $this->dbRecord[] = $row;
        }
        @mysql_free_result($this->dbResult);
        if (!is_array($this->dbRecord) || empty($this->dbRecord)) {
            return false;
        }
        return $this->dbRecord;
    }

    /**
     * 获取单行记录(一维数组)
     *
     * @param string $sql 需要执行查询的SQL语句
     * @return 成功返回结果记录的一维数组,失败返回false
     */
    function getRow($sql, $isMaster = false) {
        if (!$this->query($sql, $isMaster)) {
            return false;
        }
        $this->dbRecord = array();
        $this->dbRecord = @mysql_fetch_array($this->dbResult, $this->fetchMode);
        @mysql_free_result($this->dbResult);
        if (!is_array($this->dbRecord) || empty($this->dbRecord)) {
            return false;
        }
        return $this->dbRecord;
    }

    /**
     * 获取一列数据(一维数组)
     *
     * @param string $sql 需要获取的字符串
     * @param string $field 需要获取的列,如果不指定,默认是第一列
     * @return 成功返回提取的结果记录的一维数组,失败返回false
     */
    function getCol($sql, $field = '', $isMaster = false) {
        if (!$this->query($sql, $isMaster)) {
            return false;
        }
        $this->dbRecord = array();
        while ($row = @mysql_fetch_array($this->dbResult, $this->fetchMode)) {
            if (trim($field) == '') {
                $this->dbRecord[] = current($row);
            } else {
                $this->dbRecord[] = $row[$field];
            }
        }
        @mysql_free_result($this->dbResult);
        if (!is_array($this->dbRecord) || empty($this->dbRecord)) {
            return false;
        }
        return $this->dbRecord;
    }

    /**
     * 获取一个数据(当条数组)
     *
     * @param string $sql 需要执行查询的SQL
     * @return 成功返回获取的一个数据,失败返回false
     */
    function getOne($sql, $field = '', $isMaster = false) {
        if (!$this->query($sql, $isMaster)) {
            return false;
        }
        $this->dbRecord = array();
        $row = @mysql_fetch_array($this->dbResult, $this->fetchMode);
        @mysql_free_result($this->dbResult);
        if (!is_array($row) || empty($row)) {
            return false;
        }
        if (trim($field) != '') {
            $this->dbRecord = $row[$field];
        } else {
            $this->dbRecord = current($row);
        }
        return $this->dbRecord;
    }

    /**
     * 获取指定各种条件的记录
     *
     * @param string $table 表名(访问的数据表)
     * @param string $field 字段(要获取的字段)
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)
     * @param string $order 排序(按照什么字段排序,不包括ORDER BY,默认为空)
     * @param string $limit 限制记录(需要提取多少记录,不包括LIMIT,默认为空)
     * @param bool $single 是否只是取单条记录(是调用getRow还是getAll,默认是false,即调用getAll)
     * @return 成功返回记录结果集的数组,失败返回false
     */
    function getRecord($table, $field = '*', $where = '', $order = '', $limit = '', $single = false, $isMaster = false) {
        $sql = "SELECT $field FROM $table";
        $sql .= trim($where) != '' ? " WHERE $where " : $where;
        $sql .= trim($order) != '' ? " ORDER BY $order " : $order;
        $sql .= trim($limit) != '' ? " LIMIT $limit " : $limit;
        if ($single) {
            return $this->getRow($sql, $isMaster);
        }
        return $this->getAll($sql, $isMaster);
    }

    /**
     * 获取指点各种条件的记录(跟getRecored类似)
     *
     * @param string $table 表名(访问的数据表)
     * @param string $field 字段(要获取的字段)
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)
     * @param array $order_arr 排序数组(格式类似于: array('id'=>true), 那么就是按照ID为顺序排序, array('id'=>false), 就是按照ID逆序排序)
     * @param array $limit_arr 提取数据的限制数组()
     * @return unknown
     */
    function getRecordByWhere($table, $field = '*', $where = '', $arrOrder = array(), $arrLimit = array(), $isMaster = false) {
        $sql = " SELECT $field FROM $table ";
        $sql .= trim($where) != '' ? " WHERE $where " : $where;
        if (is_array($arrOrder) && !empty($arrOrder)) {
            $arrKey = key($arrOrder);
            $sql .= " ORDER BY $arrKey " . ($arrOrder[$arrKey] ? "ASC" : "DESC");
        }
        if (is_array($arrLimit) && !empty($arrLimit)) {
            $startPos = intval(array_shift($arrLimit));
            $offset = intval(array_shift($arrLimit));
            $sql .= " LIMIT $startPos,$offset ";
        }
        return $this->getAll($sql, $isMaster);
    }

    /**
     * 获取指定条数的记录
     *
     * @param string $table 表名
     * @param int $startPos 开始记录
     * @param int $offset 偏移量
     * @param string $field 字段名
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)
     * @param string $order 排序(按照什么字段排序,不包括ORDER BY,默认为空)
     * @return 成功返回包含记录的二维数组,失败返回false
     */
    function getRecordByLimit($table, $startPos, $offset, $field = '*', $where = '', $oder = '', $isMaster = false) {
        $sql = " SELECT $field FROM $table ";
        $sql .= trim($where) != '' ? " WHERE $where " : $where;
        $sql .= trim($order) != '' ? " ORDER BY $order " : $order;
        $sql .= " LIMIT $startPos,$offset ";
        return $this->getAll($sql, $isMaster);
    }

    /**
     * 获取排序记录
     *
     * @param string $table 表名
     * @param string $orderField 需要排序的字段(比如id)
     * @param string $orderMethod 排序的方式(1为顺序, 2为逆序, 默认是1)
     * @param string $field 需要提取的字段(默认是*,就是所有字段)
     * @param string $where 条件(获取记录的条件语句,不包括WHERE,默认为空)
     * @param string $limit 限制记录(需要提取多少记录,不包括LIMIT,默认为空)
     * @return 成功返回记录的二维数组,失败返回false
     */
    function getRecordByOrder($table, $orderField, $orderMethod = 1, $field = '*', $where = '', $limit = '', $isMaster = false) {
        //$order_method的值为1则为顺序, $order_method值为2则2则是逆序排列
        $sql = " SELECT $field FROM $table ";
        $sql .= trim($where) != '' ? " WHERE $where " : $where;
        $sql .= " ORDER BY $orderField " . ( $orderMethod == 1 ? "ASC" : "DESC");
        $sql .= trim($limit) != '' ? " LIMIT $limit " : $limit;
        return $this->getAll($sql, $isMaster);
    }

    /**
     * 分页查询(限制查询的记录条数)
     *
     * @param string $sql 需要查询的SQL语句
     * @param int $startPos 开始记录的条数
     * @param int $offset 每次的偏移量,需要获取多少条
     * @return 成功返回获取结果记录的二维数组,失败返回false
     */
    function limitQuery($sql, $startPos, $offset, $isMaster = false) {
        $start_pos = intval($startPos);
        $offset = intval($offset);
        $sql = $sql . " LIMIT $startPos,$offset ";
        return $this->getAll($sql, $isMaster);
    }

    //--------------------------
    //
    //     无数据返回操作
    //
    //--------------------------
    /**
     * 执行执行非Select查询操作
     *
     * @param string $sql 查询SQL语句
     * @return bool  成功执行返回true, 失败返回false
     */
    function execute($sql, $isMaster = false) {
        if (!$this->query($sql, $isMaster)) {
            return false;
        }
        return true;
//        $count = @mysql_affected_rows($this->dbLink);
//        if ($count <= 0){
//            return false;
//        }
//        return true;
    }

    /**
     * 自动执行操作(针对Insert/Update操作)
     *
     * @param string $table 表名
     * @param array $field_array 字段数组(数组中的键相当于字段名,数组值相当于值, 类似 array( 'id' => 100, 'user' => 'heiyeluren')
     * @param int $mode 执行操作的模式 (是插入还是更新操作, 1是插入操作Insert, 2是更新操作Update)
     * @param string $where 如果是更新操作,可以添加WHERE的条件
     * @return bool 执行成功返回true, 失败返回false
     */
    function autoExecute($table, $arrField, $mode, $where = '', $isMaster = true) {

        if ($table == '' || !is_array($arrField) || empty($arrField)) {
            return false;
        }
        //$mode为1是插入操作(Insert), $mode为2是更新操作
        if ($mode == 1) {
            $sql = " INSERT INTO `$table` SET ";
        } elseif ($mode == 2) {
            $sql = " UPDATE `$table` SET ";
        } else {
            $this->errorLog("Operate type '$mode' is error, in call DB::autoExecute process table $table.");
            return false;
        }
        foreach ($arrField as $key => $value) {
            $sql .= "`$key`='$value',";
        }
        $sql = rtrim($sql, ',');
        if ($mode == 2 && $where != '') {
            $sql .= "WHERE $where";
        }
        return $this->execute($sql, $isMaster);
    }

    /**
     * 锁表表
     *
     * @param string $tblName 需要锁定表的名称
     * @return mixed 成功返回执行结果，失败返回错误对象
     */
    function lockTable($tblName) {
        return $this->query("LOCK TABLES $tblName", true);
    }

    /**
     * 对锁定表进行解锁
     *
     * @param string $tblName 需要锁定表的名称
     * @return mixed 成功返回执行结果，失败返回错误对象
     */
    function unlockTable($tblName) {
        return $this->query("UNLOCK TABLES $tblName", true);
    }

    /**
     * 设置自动提交模块的方式（针对InnoDB存储引擎）
     * 一般如果是不需要使用事务模式，建议自动提交为1，这样能够提高InnoDB存储引擎的执行效率，如果是事务模式，那么就使用自动提交为0
     *
     * @param bool $autoCommit 如果是true则是自动提交，每次输入SQL之后都自动执行，缺省为false
     * @return mixed 成功返回true，失败返回错误对象
     */
    function setAutoCommit($autoCommit = false) {
        $autoCommit = ( $autoCommit ? 1 : 0 );
        return $this->query("SET AUTOCOMMIT = $autoCommit", true);
    }

    /**
     * 开始一个事务过程（针对InnoDB引擎，兼容使用 BEGIN 和 START TRANSACTION）
     *
     * @return mixed 成功返回true，失败返回错误对象
     */
    function startTransaction() {
        if (!$this->query("BEGIN")) {
            return $this->query("START TRANSACTION", true);
        }
    }

    /**
     * 提交一个事务（针对InnoDB存储引擎）
     *
     * @return mixed 成功返回true，失败返回错误对象
     */
    function commit() {
        if (!$this->query("COMMIT", true)) {
            return false;
        }
        return $this->setAutoCommit(true);
    }

    /**
     * 发生错误，会滚一个事务（针对InnoDB存储引擎）
     *
     * @return mixed 成功返回true，失败返回错误对象
     */
    function rollback() {
        if (!$this->query("ROLLBACK", true)) {
            return false;
        }
        return $this->setAutoCommit(true);
    }

    //--------------------------
    //
    //    其他数据相关操作
    //
    //--------------------------
    /**
     * 获取最后一次查询的SQL语句
     *
     * @return string 返回最后一次查询的SQL语句
     */
    function getLastSql() {
        return $this->dbSql;
    }

    /**
     * 获取上次插入操作的的ID
     *
     * @return int 如果没有连接或者查询失败,返回0, 成功返回ID
     */
    function getLastId() {
        $dbConn = $this->getDbWriteConn();
        if (($lastId = mysql_insert_id($dbConn)) > 0) {
            return $lastId;
        }
        return $this->getOne("SELECT LAST_INSERT_ID()", '', true);
    }

    /**
     * 获取记录集里面的记录条数 (用于Select操作)
     *
     * @return int 如果上一次无结果集或者记录结果集为空,返回0, 否则返回结果集数量
     */
    function getNumRows($res = null) {
        if (!$res || !is_resource($res)) {
            $res = $this->dbResult;
        }
        return mysql_num_rows($res);
    }

    /**
     * 获取受到影响的记录数量 (用于Update/Delete/Insert操作)
     *
     * @return int 如果没有连接或者影响记录为空, 否则返回影响的行数量
     */
    function getAffectedRows() {
        $dbConn = $this->getDbWriteConn();
        if (($affetedRows = mysql_affected_rows($dbConn)) <= 0) {
            return $affetedRows;
        }
        return $this->getOne("SELECT ROW_COUNT()", "", true);
    }

}