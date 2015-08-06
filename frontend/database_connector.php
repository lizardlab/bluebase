<?php
include 'config_handler.php';

class database_connector {
    private $database_type;
    private $host;
    private $database_name;
    private $username;
    private $password;
    private $port;
    
    private $connection;
    
    public function __construct($database_type = '', $host = '', $database_name = '', $username = '', $password = '', $port = '') {
        // Get the default values from the config file
        $config_handler = new config_handler();
        $config = $config_handler->get_config();
        
        // Assign the config details as the default for this particular connection
        $this->database_type = $config['database_config']['database_type'];
        $this->host = $config['database_config']['host'];
        $this->database_name = $config['database_config']['database_name'];
        $this->username = $config['database_config']['username'];
        $this->password = $config['database_config']['password'];
        $this->port = $config['database_config']['port'];
        
        // These are the connection details for the database connection
        if ($host != null) {
            $this->database_type = $database_type;
        }
        if ($host != null) {
            $this->host = $host;
        }
        if ($database_name != null) {
            $this->database_name = $database_name;
        }
        if ($username != null) {
            $this->username = $username;
        }
        if ($password != null) {
            $this->password = $password;
        }
        if ($port != null) {
            $this->port = $port;
        }
        
        $this->create_connection();
        ini_set('mysqli.default_socket', '/tmp/mysql5.sock');
    }
    
    private function create_connection() {
        // This is the connection to the database. All queries will run through this object
        //echo $this->database_type.':host='.$this->host.';port='.$this->port.';dbname='.$this->database_name, $this->username, $this->password;
        $this->connection = new PDO($this->database_type.':host='.$this->host.';port='.$this->port.';dbname='.$this->database_name, $this->username, $this->password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function run_query($query, $parameters = array(), $fetch_style = PDO::FETCH_BOTH) {
    
        // Prepare the statement
        $stmt = $this->connection->prepare($query);
        
        // Execute with parameteres depending on if there were any given
        if (!empty($parameters)) {
            $stmt->execute($parameters);
        } else {
            $stmt->execute();
        }
        
        if ($stmt->columnCount()) {
            // return the result if applicable as a 2d array
            return $stmt->fetchAll($fetch_style);
        }
    }
}
?>