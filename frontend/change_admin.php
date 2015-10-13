<?php
session_start();
include 'database_connector.php';
class changed_user{
	public $success;
	public $message;
	public function __construct($success = false, $message = "failure") {
		$this->success = $success;
		$this->message = $message;
	}
}
$connector = new database_connector();
// make sure change is coming from logged in user
if(isset($_SESSION['auth']) && $_SESSION['auth'] === 1){
    // if it's a DELETE request then they're trying to delete the user
    if($_SERVER["REQUEST_METHOD"] == "DELETE"){
        $_DELETE = array();
        parse_str(file_get_contents('php://input'), $_DELETE);
        $userid = $_DELETE["usernameOld"];
        $result = $connector->run_query("SELECT * FROM `admins` WHERE `username` = ?;", array($userid), PDO::FETCH_NUM);
        if(sizeof($result) == 0){
            $resp = new changed_user(false, "User ID does not exist");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
        // make sure admin exists
        else{
            $adminNum = $connector->run_query("SELECT COUNT(*) FROM `admins`;", null, PDO::FETCH_NUM)[0][0];
            // make sure they're not the last user and deleting themselves
            if($adminNum == 1){
                $resp = new changed_user(false, "You cannot delete the last admin");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
            // all clear, delete
            else{
                $connector->run_query("DELETE FROM `admins` WHERE `username` = ?;", array($userid));
                $result = $connector->run_query("SELECT * FROM `admins` WHERE `username` = ?;", array($userid), PDO::FETCH_NUM);
                // report back whether it was successful
                if(sizeof($result) == 0){
                    $resp = new changed_user(true, "Admin deleted successfully");
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                }
                else{
                    $resp = new changed_user(false, "Admin deletion failed");
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                } 
            }
        }
    }
    // otherwise assume it's a POST, to change a current user
    else{
        // best practice to validate backend regardless of frontend validation
        $username = $_POST["username"];
        $usernameOld = $_POST["usernameOld"];
        // make sure the user isn't blank
        $username = trim($username);
        if($username != ""){
            // make sure that the user doesn't exist
            // database would reject duplicate username since its UNIQUE field
            // but this allows for easier discovery of any weird problems
            $existUser = $connector->run_query("SELECT * FROM `admins` WHERE `username` = ?;", array($usernameOld), PDO::FETCH_ASSOC);
            // check if the userid we're changing exists
            if(sizeof($existUser) == 1){
                $existName = $connector->run_query("SELECT * FROM `admins` WHERE `username` = ?;", array($username), PDO::FETCH_ASSOC);
                // check if username is same or if new username isn't taken
                if($existUser[0]["username"] == $usernameOld || sizeof($existName) == 0){
                    // validate that password and repeated password are equal
                    if($_POST["password"] == $_POST["repeat"]){
                        $password = $_POST["password"];
                        // if they didn't provide a password, keep the current one
                        if($password == "")
                            $password = $existUser[0]["password"];
                        // otherwise hash new password
                        else{
                            $password = password_hash($password, PASSWORD_DEFAULT);
                        }
                        $changed_user = array($username, $password, $usernameOld);
                        $connector->run_query("UPDATE `admins` SET `username` = ?, `password` = ? WHERE `username` = ?;", $changed_user);
                        $resp = new changed_user(true, "User changed successfully");
                        echo(json_encode($resp, JSON_PRETTY_PRINT));
                    }
                    // throw error that passwords do not match
                    else{
                        $resp = new changed_user(false, "Passwords do not match");
                        echo(json_encode($resp, JSON_PRETTY_PRINT));
                    }
                }
                // throw that username is already taken (but not from the current userid)
                else{
                    $resp = new changed_user(false, "New username already exists");
                    echo(json_encode($resp, JSON_PRETTY_PRINT));
                }
            }
            // throw error that the user attempt to be changed doesn't exist at all
            else{
                $resp = new changed_user(false, "User ID does not exist");
                echo(json_encode($resp, JSON_PRETTY_PRINT));
            }
        }
        else{
            $resp = new changed_user(false, "Username cannot be blank");
            echo(json_encode($resp, JSON_PRETTY_PRINT));
        }
    }
}
else{
   $resp = new changed_user(false, "Not authenticated");
   echo(json_encode($resp, JSON_PRETTY_PRINT));
}
?>