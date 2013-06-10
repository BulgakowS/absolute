<?php

require_once( dirname(dirname(__FILE__)) . '/oauth/provider.php' );

/**
 * Flickr OAuth class
*/

class uber_media_source_flickr extends provider {
  
	public 	$host = 'http://api.flickr.com/services/rest/';
	public  $format = 'json';
	private $access_token_url = 'http://www.flickr.com/services/oauth/access_token';
	private $authenticate_token_url = 'http://www.flickr.com/services/oauth/authorize';
	private $authorize_url = 'http://www.flickr.com/services/oauth/authorize';
	private $request_token_url = 'http://www.flickr.com/services/oauth/request_token';
 	
 	private $consumer_key = 'd95b51541ae40a9950bea98e24a27cfd';
 	private $consumer_secret = 'ae1b30477cc1bdfb';
 	
 	private $max_count = 500;
 	private $default_count = 20;
 	
 	private $popup_width = 700;
 	private $popup_height = 600;
 
 	private $settings = array	( 	'getTaggedImages' => array( 'name' => 'Tagged Images',
 																'param' => true,
 																'param_type' => 'text',
 																'param_desc' => 'Enter a hashtag without the #'),
 									'getUsersImages' => array( 	'name' => 'User Images',
 																'param' => true,
 																'param_type' => 'text',
 																'param_desc' => 'Enter a username'),
 									
 									'getRecent' => array( 	'name' => 'Recent Images',
 															'param' => false	),	
 								);

 	function __construct($oauth_token = NULL, $oauth_token_secret = NULL) {
 	
 		parent::__construct(	$this->host,
 								$this->format,
 								$this->access_token_url,
 								$this->authenticate_token_url,
 								$this->authorize_url,
 								$this->request_token_url,
 								$this->consumer_key,
 								$this->consumer_secret,
 								$this->settings,
 								$this->max_count,
 								$this->default_count,
 								$this->popup_width,
 								$this->popup_height,
 								$oauth_token,
 								$oauth_token_secret
 							);
 	}
	
	function getFormat($url) { return "{$this->host}{$url}?format={$this->format}&nojsoncallback=1"; }
	
	private function getImages($images, $page) {
		$response = array();
		$new_images = array();
		if ($images && isset($images->photos->photo)) {
			if ($page == $images->photos->pages) $response['pagin'] = false;
		    foreach($images->photos->photo as $photo) {
			    $new_images[] = array( 	'id' => $photo->id,
			    						'full' => 'http://farm'. $photo->farm .'.static.flickr.com/'. $photo->server .'/'. $photo->id .'_'. $photo->secret .'_b.jpg',
			    						'thumbnail' => 'http://farm'. $photo->farm .'.static.flickr.com/'. $photo->server .'/'. $photo->id .'_'. $photo->secret .'_q.jpg',
			    						'link' => 'http://www.flickr.com/photos/'. $photo->owner .'/' .$photo->id,
			    						'caption' => (isset($photo->title) ? $this->filter_text($photo->title) : '')
			    					);
			}
		}
		$response['images'] = $new_images;
		return $response;
	}
	
	private function getUserId($username) {
		$params = array( 	'method' => 'flickr.people.findByUsername',
							'username' => $username
						);
		$userid = 0;
		$user = $this->get('', $params);
		if (isset($user->user) && isset($user)) {
			$user = $user->user;
			$userid = $user->id;
		} 
		return $userid;
		
	}

	function getOwnImages($count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$params = array(	'method' => 'flickr.photos.search', 
							'user_id' => 0, 
							'per_page' => $count, 
							'page' => $page);
		if ($safemode == 1) $params['safe_search'] = 1;
		$images = array();
		if ($userid != 0) $images = $this->get('', $params);
		return $this->getImages($images, $page);
	}
	
	function getUsersImages($username, $count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$userid = $this->getUserId($username);
		$params = array(	'method' => 'flickr.photos.search', 
							'user_id' => $userid, 
							'per_page' => $count, 
							'page' => $page);
		if ($safemode == 1) $params['safe_search'] = 1;
		$images = array();
		if ($userid != 0) $images = $this->get('', $params);
		return $this->getImages($images, $page);
	}
	
	function getTaggedImages($tags, $count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$params = array(	'method' => 'flickr.photos.search', 
							'tags' => $tags, 
							'tag_mode' => 'all',
							'per_page' => $count, 
							'page' => $page);
		if ($safemode == 1) $params['safe_search'] = 1;
		$images = array();
		$images = $this->get('', $params);
		return $this->getImages($images, $page);
	}
	
	function getRecent($count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$params = array(	'method' => 'flickr.photos.getRecent', 
							'per_page' => $count, 
							'page' => $page);
		if ($safemode == 1) $params['safe_search'] = 1;
		$images = array();
		$images = $this->get('', $params);
		return $this->getImages($images, $page);
	}

	
}
