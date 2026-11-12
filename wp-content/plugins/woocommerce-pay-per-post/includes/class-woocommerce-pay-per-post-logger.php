<?php

use Pramadillo\PayForPost\Monolog\Handler\StreamHandler;
use Pramadillo\PayForPost\Monolog\Logger;

class Woocommerce_Pay_Per_Post_Logger {


	public $log;
	protected $log_file_name = 'debug.log';
	protected $log_file_uri;
	protected $log_file_url;
	protected $log_directory;
	protected $log_directory_url;
	private $debug;


	public function __construct() {

		$this->debug             = get_option( WC_PPP_SLUG . '_enable_debugging' );
		$uploads_dir             = wp_upload_dir();
		$this->log_directory     = $uploads_dir['basedir'] . '/woocommerce-pay-per-post-logs';
		$this->log_directory_url = $uploads_dir['baseurl'] . '/woocommerce-pay-per-post-logs';
		$this->log_file_uri      = $this->log_directory . '/' . $this->log_file_name;
		$this->log_file_url      = $this->log_directory_url . '/' . $this->log_file_name;

		if ( $this->debug ) {

			$this->log = new Logger( WC_PPP_SLUG );
			$this->create_log_file();

			try {
				$this->log->pushHandler( new StreamHandler( $this->log_file_uri, Logger::DEBUG ) );
			} catch ( Exception $e ) {
				die( $e->getMessage() );
			}

		}


	}

	public function log( $message, $context = [] ) {
		if ( $this->debug && isset( $_GET['wc-ppp-debug'] ) ) {
			$this->log->debug( $message, $context );
		}
	}


	public function delete_log_file() {
		@unlink( $this->log_file_uri );
	}

	private function create_log_file() {

		if ( ! file_exists( $this->log_directory ) ) {
			wp_mkdir_p( $this->log_directory );
		}

		if ( ! file_exists( $this->log_file_uri ) ) {
			$this->log( 'Initial Log File Created' );
		}

	}

	public function get_log_uri(): string {
		return $this->log_file_uri;
	}

	public function get_log_url(): string {
		return $this->log_file_url;
	}

}
