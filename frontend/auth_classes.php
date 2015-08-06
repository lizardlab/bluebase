<?php
class user implements JsonSerializable{
	private $userid;
	private $username;
	private $fname;
	private $lname;
	private $expiration;
	private $disabled;
	public function __construct($userid, $username, $fname, $lname, $expiration, $disabled){
		$this->userid = $userid;
		$this->username = $username;
		$this->fname = $fname;
		$this->lname = $lname;
		$this->expiration = $expiration;
		$this->disabled = $disabled;
	}
	public static function construct_with_array($array){
		$instance = new self($array[0], $array[1], $array[2], $array[3], $array[4], $array[5]);
		return $instance;
	}
	public static function construct_from_id($id){
		$result = (new database_connector())->run_query("SELECT * FROM users WHERE userid=?", array($id));
    	$instance = self::construct_with_array($result[0]);
    	return $instance;
	}
	public static function construct_from_object($object){
		$instance = new self($object->userid, $object->username, $object->fname, $object->lname, $object->expiration, $object->disabled);
		return $instance;
	}
	public function jsonSerialize() {
    	return get_object_vars($this);
    }
}