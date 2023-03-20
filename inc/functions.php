<?php

class SpiritualGifts {

	public function __construct() {
		include("config.php");
		$this->records = $info;
		date_default_timezone_set("America/Denver");
		$this->date_time_now = date("Y-m-d H:i:s");
	}

	public function config() {
		return $this->records;
		// $config['main_site'] = 'https://www.idahograce.com/';
		// $config['site_url'] = 'http://localhost/spiritual-gifts/';
		//return $config;
	}

	public function records() {
		return $this->records;
	}

	public function gift_types() {
		$config = $this->config();
		$site_url = $config['site_url'];
		$json_types = @file_get_contents($site_url . 'data/gifts.json');
		$data = ($json_types) ? json_decode($json_types) : '';
		return $data;
	}

	

	public function submit_member($data) {
		if($data) {
			$member_info = array();
			$timeNow = $this->date_time_now;
			foreach($data as $field=>$value) {
				if($value) {
					$member_info[] = $value;
					$_SESSION[$field] = $value;
				}
			}
			if($member_info) {
				$_SESSION['spiritual_gift_test_started'] = $timeNow;
				return $_SESSION;
			}
		}
	}

	public function sessionKill() {
		session_destroy();
		$config = $this->config();
		$site_url = $config['site_url'];
		header('Location: ' . $site_url);
	}

	public function dataCrypt( $string, $action = 'e' ) {
	  // you may change these values to your own
	  $secret_key = 'VL6OT9M9CNX1TKD';
	  $secret_iv = '896251CDQPDVIW1';

	  $output = false;
	  $encrypt_method = "AES-256-CBC";
	  $key = hash( 'sha256', $secret_key );
	  $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

	  if( $action == 'e' ) {
	      $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	  }
	  else if( $action == 'd' ){
	      $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	  }

	  return $output;
	}

}