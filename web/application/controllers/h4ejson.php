<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Json
*/
class H4ejson extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();

		$this->logged_in = false;
		$this->logged_user = 1;
	}

	public function index()
	{
		$this->_return_json_success('');
	}

	/**
	 * accepts param tag in the uri so it can be called as below:
	 *
	 * base_url/json/feeds - this will return all the feeds of the user currently logged in
	 * base_url/json/feeds/tag/fashion - this will return the list of feeds tagged fashion for the user currently logged in
	 */
	public function feeds()
	{
		$params = $this->uri->uri_to_assoc();

		$this->db->select('f.id, f.title, f.url, t.id as tag_id, t.name as tag_name, t.slug');
		$this->db->from('feeds as f');

		$this->db->join('feeds_tags as ft', 'f.id = ft.feed_id');
		$this->db->join('tags as t', 't.id = ft.tag_id');

		if( $this->logged_in ) {
			$this->db->where('f.user_id', $this->logged_user['id'] );
		}
		if( !empty($params['tag']) ) {
			$this->db->where('t.slug', $params['tag']);
		}

		/**
		 * ok, now that we have all the data we need, we should organize the output this way:
		 * [{id:X, title:Y, url:Z, tags:[{id:x, name:y, slug:z}]}, ...]
		 */
		$query = $this->db->get();
		$result = array();
		foreach ($query->result() as $row) {
			if(empty($result[$row->id])) {
				$feed = new stdClass();
				$feed->id = $row->id;
				$feed->title = $row->title;
				$feed->url = $row->url;
				$feed->tags = array();
				$result[$row->id] = $feed;
			}
			$tag = new stdClass();
			$tag->id = $row->tag_id;
			$tag->name = $row->tag_name;
			$tag->slug = $row->slug;
			$result[$row->id]->tags[] = $tag;
		}
		return $this->_return_json_success( array_values($result) );
	}

	public function tags()
	{
		$this->load->model('vocabulary_model');
		$vocabulary_id = $this->vocabulary_model->get_vocabulary_id(VOCABULARY_EXTRACTED_TAGS);
		$tags = $this->vocabulary_model->get_tags($vocabulary_id);
		return $this->_return_json_success( $tags );
	}

	public function media()
	{
		$this->db->where('type', 'image');
		$this->db->order_by('created', 'desc');
		$this->db->limit(36);
		$query = $this->db->get('feeditemmedia');
		return $this->_return_json_success( $query->result() );
	}

	public function feeditems()
	{
		$params = $this->uri->uri_to_assoc();

		$this->db->select('fi.id, fi.feed_id, fi.title, fi.permalink, fi.date, fic.abstract as description, f.title as feed_title, f.url');
		$this->db->from('feeditems as fi');
		$this->db->join('feeds as f', 'fi.feed_id = f.id');
		$this->db->join('feeditemcontents as fic', 'fi.id = fic.feeditem_id');

		$meta = new stdClass();
		$meta->params = '';
		if(!empty($params['tag'])) {
			$this->db->join('feeds_tags as ft', 'f.id = ft.feed_id');
			$this->db->join('tags as t', 't.id = ft.tag_id');
			$this->db->where('t.slug', $params['tag']);
			$meta->params = 'tag/'.$params['tag'].'/';
		}
		if(!empty($params['id'])) {
			$this->db->where('f.id', $params['id']);
			$meta->params = 'id/'.$params['id'].'/';
		}
		if( $this->logged_in ) {
			$this->db->where('f.user_id', $this->logged_user['id'] );
		}
		$this->db->order_by('date', 'desc');

		$meta->page = 1;
		$meta->pagesize = 4;
		if(!empty($params['page'])) {
			if(!is_numeric($params['page'])) {
				$meta->page = 1;
			} else {
				$meta->page = (int)($params['page'] > 0 ? $params['page'] : 1);
			}
		}
		$this->db->limit($meta->pagesize, $meta->pagesize * ($meta->page - 1));
		$query = $this->db->get();

		$items = array();
		foreach($query->result() as $row) {

			$item = new stdClass();
			$item->id = $row->id;
			$item->feed_id = $row->feed_id;
			$item->title = strip_tags($row->title);
			$item->permalink = $row->permalink;
			$item->date = $row->date;
			$item->description = strip_tags( $row->description, '<div><p><a>');
			$item->feed_title = $row->feed_title;
			$item->url = $row->url;

			$this->db->where('feeditem_id', $row->id);
			$this->db->where('type', 'image');
			$this->db->order_by('primary', 'desc');
			$query_media = $this->db->get('feeditemmedia');
			if($query_media->num_rows() > 0) {
				$item->pic = $query_media->row()->url;
			}

			$items[] = $item;
		}
		$result = new stdClass();
		$result->items = $items;

		// now rebuild the query to count the results for pagination
		$this->db->from('feeditems as fi');
		$this->db->join('feeds as f', 'fi.feed_id = f.id');

		if(!empty($params['tag'])) {
			$this->db->join('feeds_tags as ft', 'f.id = ft.feed_id');
			$this->db->join('tags as t', 't.id = ft.tag_id');
			$this->db->where('t.slug', $params['tag']);
		}
		if(!empty($params['id'])) {
			$this->db->where('f.id', $params['id']);
		}
		if( $this->logged_in ) {
			$this->db->where('f.user_id', $this->logged_user['id'] );
		}
		$meta->count_all_results = $this->db->count_all_results();
		$meta->count_all_pages = ceil($meta->count_all_results / $meta->pagesize);

		$meta->prev = $meta->params.'page/'.($meta->page > 1 ? $meta->page - 1 : 1);
		$meta->next = $meta->params.'page/'.($meta->page < $meta->count_all_pages ? $meta->page + 1 : $meta->count_all_pages);
		$result->meta = $meta;

		return $this->_return_json_success( $result );
	}

	/**
	 * so we need to pull out something like the following:
	 * {success: {
	 *  	tags: [{id:x, name:y, slug:z, type:a}, ...], 
	 *  	media: [], 
	 *  	content:[]
	 * }}
	 */
	public function reactions()
	{
		$params = $this->uri->uri_to_assoc();
		$content = new stdClass();
		$tags = array();
		$media = array();
		$entities = new stdClass();

		if(!empty($params['id'])) {
			$this->db->select('fi.id, fi.title, fi.permalink, fi.date, fic.abstract, fic.content');
			$this->db->from('feeditems as fi');
			$this->db->join('feeditemcontents as fic', 'fi.id = fic.feeditem_id', 'left');
			$this->db->where('fi.id', $params['id']);
			$query = $this->db->get();
			if( $query->num_rows() > 0 ) {
				$row = $query->row();
				$content->id = $row->id;
				$content->title = $row->title;
				$content->permalink = $row->permalink;
				$content->date = $row->date;
				$content->abstract = $row->abstract;
				$content->content = $row->content;

				$this->load->model('scraper_model');
				$this->load->model('vocabulary_model');
				$result = $this->scraper_model->scrape_stamat_ner($row->id);

				$locations = array();
				foreach ($result->success->locations as $location) {
					$location_obj = new stdClass();
					$location_obj->id = rand(1,10000);
					$location_obj->name = $location;
					$location_obj->slug = $this->vocabulary_model->slugify($location);
					$locations[] = $location_obj;
				}
				$people = array();
				foreach ($result->success->people as $person) {
					$person_obj = new stdClass();
					$person_obj->id = rand(1,10000);
					$person_obj->name = $person;
					$person_obj->slug = $this->vocabulary_model->slugify($person);
					$people[] = $person_obj;
				}
				$organizations = array();
				foreach ($result->success->organizations as $organization) {
					$organization_obj = new stdClass();
					$organization_obj->id = rand(1,10000);
					$organization_obj->name = $organization;
					$organization_obj->slug = $this->vocabulary_model->slugify($organization);
					$organizations[] = $organization_obj;
				}
				$entities->locations = $locations;
				$entities->people = $people;
				$entities->organizations = $organizations;
			}

			$this->db->select('t.id, t.name, t.slug, v.name as type');
			$this->db->from('tags as t');
			$this->db->join('tagtriples as tt', 't.id = tt.object_entity_id');
			$this->db->join('vocabularies as v', 't.vocabulary_id = v.id');
			$this->db->where('t.stop_word', 0);
			$this->db->where('tt.subject_entity_id', $params['id']);

			$query = $this->db->get();
			foreach ($query->result() as $row) {
				$tags[] = $row;
			}

			$this->db->where('feeditem_id', $params['id']);
			$this->db->order_by('primary desc, type asc');
			$query = $this->db->get('feeditemmedia');
			foreach ($query->result() as $row) {
				$media[] = $row;
			}
		}

		$result = new stdClass();
		$result->content = $content;
		$result->tags = $tags;
		$result->media = $media;
		$result->entities = $entities;
		return $this->_return_json_success( $result );
	}

	public function eu_fetch()
	{
		$link = $this->input->post('link');
		if(!empty($link)) {
			$this->load->model('scraper_model');
			$result = $this->scraper_model->scrape_url($link);
			$this->_return_json_success($result);
		} else {
			$this->_return_json_error('empty query');
		}
	}

	// returns success message in json
	private function _return_json_success($success) {
		$this->_return_json('success', $success);
	}
	
	// returns error message in json
	private function _return_json_error($error) {
		$this->_return_json('error', $error);
	}
	
	// returns a json array
	private function _return_json($response, $message) {
		$data = array(
			'json' => array(
				$response => $message
			)
		);
		$this->load->view('json', $data);
	}
}