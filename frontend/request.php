<?php
include 'database_connector.php';
include 'auth_classes.php';
class response {
	public $statistics;
	public $users;
	
	public function __construct($statistics = null, $users = null) {
		$this->statistics = $statistics;
		$this->users = $users;
	}
}

$connector = new database_connector();
$result = $connector->run_query("SELECT `userid`,`username`,`fname`,`lname`,`expires`,`disabled` FROM `users`;", null, PDO::FETCH_NUM);
$userCount = $connector->run_query("SELECT COUNT(*) FROM `users`", null, PDO::FETCH_NUM)[0][0];
$disabled = $connector->run_query("SELECT COUNT(*) FROM `users` WHERE `disabled` = 1", null, PDO::FETCH_NUM)[0][0];
// don't want to count disabled users as expired
$expired = $connector->run_query("SELECT COUNT(*) FROM `users` WHERE expires < ? AND disabled = 0", array(date("Y-m-d")), PDO::FETCH_NUM)[0][0];
$invalid = $disabled + $expired;

$valid = $userCount - $invalid;
$dataz = array($valid, intval($expired), intval($disabled));
$users = array();
for($i = 0; $i < sizeof($result); $i++){
	$users[$i] = user::construct_with_array($result[$i]);
}
$resp = new response($dataz, $users);
echo(json_encode($resp, JSON_PRETTY_PRINT));

?>