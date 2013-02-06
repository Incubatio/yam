<?php
namespace VersionManager;
/**
 * @author NMO <nico@multeegaming.com> 
 */
class File
{
    protected $_file_path;

    protected $_history = array();

    public function __construct($file_path) {
        if(!file_exists($file_path)) trigger_error('"' . $file_path . '" is not a valid path'); 
            $this->_file_path = $file_path . '/.yam';
            $this->history = $this->get_history();
    }

    public function get_history() {
        if(is_null($this->_history) && file_exists($this->_file_path)) 
            $this->history = explode("\n", trim(file_get_contents($this->_file_path)));

        return $this->_history;
    }

    public function add($version, $mtime) {
        //TODO: add comparaison btw file_mtime, store mtime in .yam 
        $this->_history[] = $version;
        file_put_contents($this->_file_path, $version . "\n", FILE_APPEND);
    }

    public function remove() {
        $m = array_pop($this->_history);
        count($this->_history) === 0 ? unlink($this->_file_path) : file_put_contents($this->_file_path, implode("\n", $this->_history) . "\n");
    }

    public function drop() {
        unlink($this->_file_path);
    }

}
