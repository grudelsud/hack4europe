<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

include_once('simplepie/SimplePieAutoloader.php');
include_once('simplepie/idn/idna_convert.class.php');

class RSS_Parser {

	public $feed;

	public function __construct()
	{
		$this->feed = new SimplePie();

		// $CI =& get_instance();
		// 
		// $dbdriver = $CI->db->dbdriver;
		// $username = $CI->db->username;
		// $password = $CI->db->password;
		// $hostname = $CI->db->hostname;
		// $port = $CI->db->port;
		// $database = $CI->db->database;
		// 
		// $dsn = $dbdriver.'://'.$username.':'.$password.'@'.$hostname.':'.$port.'/'.$database;
		// $this->feed->set_cache_location( $dsn );
	}

	function set_feed_url( $url )
	{
		$this->feed->set_feed_url($url);
		$this->feed->enable_cache(false);
		$this->feed->init();
	}

	function get_feed()
	{
		return $this->feed;
	}
}

/* End of file rss_parser.php */