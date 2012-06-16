<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Scraper_model
*/
class Scraper_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	private function _execute_curl( $rest_call, $request_type, $auth_type, $auth_params, $post_params, &$debug = NULL )
	{
		$this->curl->create( $rest_call );
		$debug['rest_call'] = $rest_call;

		$this->curl->option(CURLOPT_TIMEOUT, 60);

		if( $auth_type == 'http_login' ) {
			$this->curl->http_login( $auth_params['username'], $auth_params['password'] );
			$debug['auth'] = $auth_params;
		}
		if( $request_type == 'post' ) {
			$this->curl->post( $post_params );
			$debug['post'] = $post_params;
		}
		return $this->curl->execute();
	}

	private function _get_scraper( $name )
	{
		$this->db->where('name', $name);
		$result = $this->db->get('scrapers');
		return $result->row();	
	}

	function get_scraped_content( $feeditem_id )
	{
		// check if content is already scraped, or fetch it from readitlater
		$this->db->where('feeditem_id', $feeditem_id);
		$query = $this->db->get('feeditemcontents');
		if( $query->num_rows() > 0 ) {
			$row = $query->row();
			$content = $row->content;
		} else {
			// $feeditemcontent_id = $this->scrape_readitlater( $feeditem_id );
			$feeditemcontent_id = $this->scrape_diffbot( $feeditem_id );
			$this->db->where('id', $feeditemcontent_id);
			$query = $this->db->get('feeditemcontents');
			$row = $query->row();
			$content = $row->content;
		}
		return $content;
	}

	function scrape_url( $url )
	{
		$rest_call = $url;
		$request_type = 'get';
		$auth_type = '';
		$auth_params = '';
		$post_params = array();
		$response_e = $this->_execute_curl($rest_call, $request_type, $auth_type, $auth_params, $post_params);
		return json_decode($response_e);
	}

	function scrape_stamat_ner( $feeditem_id )
	{
		$this->db->where('feeditem_id', $feeditem_id);
		$query = $this->db->get('feeditemcontents');
		if($query->num_rows() == 0) {
			return FALSE;
		} else {
			$row = $query->row();
			$rest_call = 'http://hack4europe.net:9000/extractEntities';
			$request_type = 'post';
			$auth_type = '';
			$auth_params = '';
			$post_params['content'] = strip_tags($row->content);
			$response_e = $this->_execute_curl($rest_call, $request_type, $auth_type, $auth_params, $post_params);
			return json_decode($response_e);
		}
	}

}
