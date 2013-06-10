<?php

require_once( dirname(dirname(__FILE__)) . '/oauth/provider.php' );

/**
 * 500px OAuth class
*/

class uber_media_source_500px extends provider {
  
	public 	$host = 'https://api.500px.com/v1/';
	public  $format = 'json';
	private $access_token_url = 'https://api.500px.com/v1/oauth/access_token';
	private $authenticate_token_url = 'https://api.500px.com/v1/oauth/authorize';
	private $authorize_url = 'https://api.500px.com/v1/oauth/authorize';
	private $request_token_url = 'https://api.500px.com/v1/oauth/request_token';
 	
 	private $consumer_key = 'sjQOB4EmdL7zg6BZK5XIhxhSVrC2y82pz1eBbzk6';
 	private $consumer_secret = 'VtMOtA0Keirpo7oOTftJcq88uKLLLN2RfxV2X7Xp';
 	
 	private $max_count = 100;
 	private $default_count = 20;
 	
 	private $popup_width = 800;
 	private $popup_height = 500;
 	
 	private $settings = array	(	'getTaggedImages' => array( 'name' => 'Tagged Images',
 																'param' => true,
 																'param_type' => 'text',
 																'param_desc' => 'Enter a hashtag without the #'),
 									'getUsersImages' => array( 	'name' => 'User Images',
 																'param' => true,
 																'param_type' => 'text',
 																'param_desc' => 'Enter a username'),
 									
 									'getPopular' => array( 	'name' => 'Popular Images',
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
	
	private function getImages($images, $page) {
		$response = array();
		$new_images = array();
		if ($images && isset($images->photos)) {
			if ($page == $images->total_pages) $response['pagin'] = false;
		    foreach($images->photos as $photo) {
			    $new_images[] = array(  'id' => $photo->id,
			    						'full' => str_replace('/2.jpg', '/4.jpg', $photo->image_url),
			    						'thumbnail' => $photo->image_url,
			    						'link' => 'http://500px.com/photo/'. $photo->id,
									    'caption' => (isset($photo->name) ? $this->filter_text($photo->name) : '')
			    					);
			}
		}
		$response['images'] = $new_images;
		return $response;
	}
	
	function getUsersImages($username, $count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$params = array('feature' => 'user', 'username' => $username);
		$params['rpp'] = $count;
		$params['page'] = $page;
		if ($safemode == 1) $params['exclude'] = 'Nude';
		$images = $this->get('photos', $params);
		return $this->getImages($images, $page);
	}
	
	function getTaggedImages($tag, $count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$params = array('tag' => $tag);
		$params['rpp'] = $count;
		$params['page'] = $page;
		if ($safemode == 1) $params['exclude'] = 'Nude';
		$images = $this->get('photos/search', $params);
		return $this->getImages($images, $page);
	}
	
	function getPopular($count = null, $safemode = 1, $page = 1) {
		$count = isset($count) ? $count : $this->default_count;
		$count = ($count > $this->max_count) ? $this->max_count : $count;
		$params = array('feature' => 'popular');
		$params['rpp'] = $count;
		$params['page'] = $page;
		if ($safemode == 1) $params['exclude'] = 'Nude';
		$images = $this->get('photos', $params);
		return $this->getImages($images, $page);
	}
	
}
