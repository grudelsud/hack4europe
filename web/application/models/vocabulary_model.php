<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Vocabulary_model
*/
class Vocabulary_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


	/**
	 * Modifies a string to remove all non ASCII characters and spaces.
	 * Note : Works with UTF-8
	 * @param  string $string The text to slugify
	 * @return string         The slugified text
	 */
	static public function slugify( $string ) {
		$string = utf8_decode($string);
		$string = html_entity_decode($string);

		$a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
		$b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
		$string = strtr($string, utf8_decode($a), $b);

		$ponctu = array("?", ".", "!", ",");
		$string = str_replace($ponctu, "", $string);

		$string = trim($string);
		$string = preg_replace('/([^a-z0-9]+)/i', '-', $string);
		$string = strtolower($string);

		if (empty($string)) return 'n-a';

		return utf8_encode($string);
	}

	function add_tags( $vocabulary_id, $tag_array, $parent_id = NULL )
	{
		$result = array();
		foreach($tag_array as $tag) {
			$result[] = $this->add_tag( $vocabulary_id, $tag, $parent_id );
		}
		return $result;
	}

	function add_tag( $vocabulary_id, $name, $parent_id = NULL )
	{
		$this->db->where('name', $name );
		$query = $this->db->get('tags');
		if( $query->num_rows() > 0 ) {
			$row = $query->row();
			$row->count += 1;
			$data = array('count' => $row->count );
			$this->db->where('id', $row->id);
			$this->db->update('tags', $data);
			return $row;
		} else {
			$data = array( 'vocabulary_id'=>$vocabulary_id, 'name'=>$name, 'slug' => $this->slugify($name) );
			if( !empty($parent_id) ) {
				$data['parent_id'] = $parent_id;
			}
			$query = $this->db->insert( 'tags', $data );
			$tag_id = $this->db->insert_id();
			return $this->get_tag( $tag_id );
		}
	}

	function get_language_id( $name, $insert = FALSE )
	{
		$this->db->where('name', $name);
		$query = $this->db->get('languages');
		if( $query->num_rows() > 0 ) {
			$row = $query->row();
			return $row->id;
		} else {
			if( $insert ) {
				$this->db->insert('languages', array('name' => $name));
				return $this->db->insert_id();
			} else {
				return 0;
			}
		}
	}

	function get_vocabulary_id( $name, $insert = FALSE )
	{
		$this->db->where('name', $name);
		$query = $this->db->get('vocabularies');
		if( $query->num_rows() > 0 ) {
			$row = $query->row();
			return $row->id;
		} else {
			if( $insert ) {
				$this->load->model('user_model');
				$user_id = $this->user_model->logged_in();
				$this->db->insert('vocabularies', array('user_id' => $user_id, 'name' => $name));
				return $this->db->insert_id();
			} else {
				return 0;
			}
		}
	}

	function get_vocabularies()
	{
		$this->db->order_by('order', 'asc');
		$query = $this->db->get('vocabularies');
		return $query->result();		
	}

	function get_tag_id( $slug )
	{
		$this->db->where('slug', $slug);
		$query = $this->db->get('tags');
		if( $query->num_rows() > 0 ) {
			$row = $query->row();
			return $row->id;
		} else {
			return 0;
		}
	}
	function get_tag( $tag_id )
	{
		$this->db->where('id', $tag_id);
		$query = $this->db->get('tags');
		if( $query->num_rows() > 0 ) {
			return $query->row();
		} else {
			return NULL;
		}
	}
	
	function get_tags( $vocabulary_id, $exclude_stop_words = TRUE )
	{
		if( $exclude_stop_words ) {
			$this->db->where('stop_word', 0);
		}
		$this->db->where('vocabulary_id', $vocabulary_id);
		$this->db->order_by('parent_id asc, slug asc, count desc');
		$query = $this->db->get('tags');
		return $query->result();
	}

	function delete_tags( $tag_ids )
	{
		foreach( $tag_ids as $tag_id ) {

			$this->db->where('id', $tag_id);
			$query = $this->db->get('tags');
			$parent_id = $query->row()->parent_id;
					
			$this->db->where('parent_id', $tag_id);
			$this->db->update('tags', array('parent_id'=>$parent_id));
			
			$this->db->delete('tags', array('id'=>$tag_id));
			$this->db->delete('feeds_tags', array('tag_id'=>$tag_id));
		}
		return true;
	}
}
