<?php

// Service sample PDO
// Initlize    20170124    Joe

namespace ZFrame_Service;

require_once 'Config/Constant.php';
require_once 'Config/SqlDef.php';
class ZConnect {

    private $pdo;

    public function __construct() {
        $objConst = new \ZFrame_Service\CONSTANT();

        $conCfg = $objConst->getDBCon();

        $conStr = $conCfg->type . ":host=" . $conCfg->host . ";port="
                . $conCfg->port . ";dbname=" . $conCfg->dbname;

        $this->pdo = new \PDO($conStr, $conCfg->user, $conCfg->pwd, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public function __destruct() {
        $this->pdo = NULL;
    }

    public function _getPdo() {
        return $this->pdo;
    }

    public function _destroyPdo() {
        $this->pdo = NULL;
    }

    public function getItems() {
        $stat = $this->pdo->prepare(constant("select.getItems"));
        $stat->execute();
        $record = $stat->fetchAll();
        $stat->closeCursor();
        return $record;        
    }

    public function updateItems($oldList, $postData) {
        foreach ($oldList as $key => $value) {
            $stat = $this->pdo->prepare(constant("update.refreshWeight"));
            $stat->execute(array(':weight' => $value['weight'], ':id' => intval($value['id'])));        
        }
        $stat = $this->pdo->prepare(constant("update.setChoose"));
        $stat->execute(array(':id' => intval($postData->id))); 
    }

}
