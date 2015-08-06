<?php
class config_handler {
    private $arr_config;
    private $config_file_path;
    
    public function __construct() {
	// Get, open, and read the config file, and dump into '$contents'
	// this gets the path relatively which makes the program modular
        $this->config_file_path = realpath(dirname(__FILE__)) . '/config.json';
        $handle = fopen($this->config_file_path, 'r');
        $contents = fread($handle, filesize($this->config_file_path));
        fclose($handle);
        
        // Decode the json into an associative array
        $this->arr_config = json_decode($contents, true);
    }
    
    public function get_config() {
        // Simply return the array
        return $this->arr_config;
    }
    
    public function write_new_config($new_config_arr) {
        // Get and write the new array to the config file
        $handle = fopen($this->config_file_path, 'w');
        fwrite($handle, json_encode($new_config_arr, JSON_PRETTY_PRINT));
        fclose($handle);
        
        // Set the new array as the config array
        $this->arr_config = $new_config_arr;
    }
}
?>
