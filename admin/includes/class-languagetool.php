<?php
 
class LanguageTool {
	
	var $api_url = 'https://languagetool.org/api/v2/';
     
    public function __construct( $api_url = '' ) {
		
		if (strlen($api_url) > 0)
			$this->api_url = $api_url;
		
    }
     
    public function check( $text, $language_code = '', $enabledCategories ) {
		
		if ( strlen($language_code) == 0 )
			$language_code = get_option('proofreading-language-default');
		if ( strlen($language_code) == 0 )
			$language_code = 'it';
		
		$body = array(
			'text' => $text,
			'language' => $language_code
		);
		
		if ($enabledCategories != null && $enabledCategories != ''){
			$body['enabledCategories'] = $enabledCategories;
			$body['enabledOnly'] = 'true';
		}
		
		$args = array(
			'body' => $body,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array(),
			'sslverify' => false
		);
		$response = wp_remote_post( $this->api_url . 'check', $args ); 
		
		if ( !is_wp_error( $response ) ) {
			$result = wp_remote_retrieve_body($response);
			
			if ($this->startsWith($result, 'Error: Text checking was stopped due to too many errors')) {
				$result = array('error' => 'TOO_MANY_ERRORS');
			}
			else{
				$result = json_decode($result);
			}
		}
		else {
			$result = array('error' => 'INVALID_REQUEST');
		}
		return $result;
    }
	
	private function startsWith($haystack, $needle)
	{
		 $length = strlen($needle);
		 return (substr($haystack, 0, $length) === $needle);
	}
	
	public function languages( ) {
		
		$response = wp_remote_get( $this->api_url . 'languages', array( 'sslverify' => false ) ); 
		
		if ( !is_wp_error( $response ) ) {
			$result = wp_remote_retrieve_body($response);
			$result = json_decode($result);
		}
		else {
			$result = array('error' => 'INVALID_REQUEST');
		}
		return $result;
    }
}
?>