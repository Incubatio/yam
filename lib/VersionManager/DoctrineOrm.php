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

    protected $_history = array();

    public function __construct($config) {
        $this->_dbname = $config['orm']['connection']['dbname'];
        $tmpDbParams = $config['orm']['connection'];
        unset($tmpDbParams['dbname']);

        $connection = \Doctrine\DBAL\DriverManager::getConnection($tmpDbParams);

        $this->_connection = $connection;
        $this->history = $this->get_history();

    }

    protected function _init() {
        //$this->_orm = $orm;
        $query = 'USE `' . $this->_dbname . '`;  
            CREATE  TABLE IF NOT EXISTS `' . $this->_tablename . '` (
                    `id` INTEGER NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL UNIQUE,
                    `file_mtime` INTEGER(12) NOT NULL,
                    `created_at` DATETIME NOT NULL,
                    PRIMARY KEY (`id`));';
        $this->_connection->executeQuery($query);
        //CREATE  TABLE IF NOT EXISTS `'. $config['orm']['connection']['dbname'] .'`.`migrations` (
        //ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;');
    }

    public function get_history() {
        $connection = $this->_connection;

        if(in_array($this->_dbname, $connection->getSchemaManager()->listDatabases())) { 
            if(empty($this->_history)) {
                $this->_init();
                $this->_history = $connection->fetchAll( 'SELECT * FROM `migrations`;');
                // temporary hack to remove mtime, not supported yet
                $this->_history = array_map(function($a) { return $a['name']; }, $this->_history);
                // TODO: manage modified file time
                //var_dump($this->_history);
            } 
        }

        return $this->_history;
    }

    public function add($version, $mtime) {
        $this->_init(); 
        $this->_history[] = $version;


        $dt = date("Y-m-d H:i:s");

        $this->_connection->executeQuery( 'INSERT INTO `migrations` values("", "' . $version . '", ' . $mtime . ', "' . $dt . '");');

    }

    public function remove() {
        $this->_init(); 
        $m = array_pop($this->_history);
        $this->_connection->executeQuery( 'DELETE FROM `migrations` ORDER BY `id` DESC LIMIT 1');
    }

    public function drop() {
        // nothing to do, create.php will remove database that contain, migrations table
        $this->_connection->executeQuery('DROP  TABLE IF EXISTS `'. $this->_dbname .'`.`' . $this->_tablename . '`;');
    }
}
