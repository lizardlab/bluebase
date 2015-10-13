<?php
session_start();
// make sure request info is going to logged in user
if(isset($_SESSION['auth']) && $_SESSION['auth'] === 1){
    include 'database_connector.php';
    class response {
        public $admins;
        
        public function __construct($admins = null) {
            $this->admins = $admins;
        }
    }

    $connector = new database_connector();
    $result = $connector->run_query("SELECT `username` FROM `admins`;", null, PDO::FETCH_NUM);
    $admin = array();
    for($i = 0; $i < sizeof($result); $i++)
        $admin[] = $result[$i][0];
    $resp = new response($admin);
    echo(json_encode($resp, JSON_PRETTY_PRINT));

}
else{
    http_response_code(403);
    echo "Not authenticated";
    echo print_r($_SESSION);
}
?>