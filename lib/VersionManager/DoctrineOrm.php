<?php
namespace VersionManager;
/**
 * @author NMO <nico@multeegaming.com> 
 */
class DoctrineOrm
{

    protected $_connection;

    protected $_dbname;
    protected $_tablename = 'migrations';

    protected $_is_init = false;
    protected $_history = array();

    public function __construct($config) {
        $this->_dbname = $config['orm']['connection']['dbname'];
        $tmpDbParams = $config['orm']['connection'];
        unset($tmpDbParams['dbname']);

        $connection = \Doctrine\DBAL\DriverManager::getConnection($tmpDbParams);

        $this->_connection = $connection;
        $this->history = $this->get_history();

    }

    protected function _check() {
        if(!$this->_is_init) die('Database ' . $this->_dbname . ' does not exists, can\'t create migrations table to track version, please check your create.php and your configuration file'); 
    }

    public function init() {
        //$this->_orm = $orm;
        if(!$this->_is_init && in_array($this->_dbname, $this->_connection->getSchemaManager()->listDatabases())) { 
            $query = 'USE `' . $this->_dbname . '`;  
            CREATE  TABLE IF NOT EXISTS `' . $this->_tablename . '` (
                    `id` INTEGER NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL UNIQUE,
                    `file_mtime` INTEGER(12) NOT NULL,
                    `created_at` DATETIME NOT NULL,
                    PRIMARY KEY (`id`));';
            $this->_connection->executeQuery($query);
            $this->_is_init = true; 
        } 
        //CREATE  TABLE IF NOT EXISTS `'. $config['orm']['connection']['dbname'] .'`.`migrations` (
        //ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;');
        return $this->_is_init;
    }

    public function get_history() {

        if($this->init()) {
            // temporary hack to remove mtime, not supported yet
            // TODO: manage modified file time
            //var_dump($this->_history);
            $result = $this->_connection->fetchAll( 'SELECT * FROM `' . $this->_tablename . '`;');
            $this->_history = array_map(function($a) { return $a['name']; }, $result);
        }

        return $this->_history;
    }

    public function add($version, $mtime) {
        $this->_check();
        $this->_history[] = $version;
        $dt = date("Y-m-d H:i:s");
        $this->_connection->executeQuery( 'INSERT INTO `' . $this->_tablename . '` values("", "' . $version . '", ' . $mtime . ', "' . $dt . '");');
    }

    public function remove() {
        $this->_check();
        $m = array_pop($this->_history);
        if(!empty($this->_history)) $this->_connection->executeQuery('DELETE FROM `' . $this->_tablename . '` ORDER BY `id` DESC LIMIT 1');
    }

    public function drop() {
        // nothing to do, create.php will remove database that contain, migrations table
        $this->_connection->executeQuery('DROP  TABLE IF EXISTS `'. $this->_dbname .'`.`' . $this->_tablename . '`;');
    }
}
