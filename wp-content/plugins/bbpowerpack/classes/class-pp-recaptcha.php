<?php
/**
 * Handles logic for reCAPTCHA.
 */
class BB_PowerPack_ReCaptcha {
	private $secret_key;
	private $validate_type;
	private $response;
	private $action;

	protected $is_success = false;
	protected $error = false;

	public function __construct( $secret_key, $validate_type, $response, $action = 'Form' ) {
		$this->secret_key = $secret_key;
		$this->validate_type = $validate_type;
		$this->response = $response;
		$this->action = $action;

		// Do recaptcha validation here so we can only load for php 5.3 and above.
		require_once FL_BUILDER_DIR . 'includes/vendor/recaptcha/autoload.php';

		$this->verify_recaptcha();
	}

	private function verify_recaptcha() {
		if ( function_exists( 'curl_exec' ) ) {
			$recaptcha = new \ReCaptcha\ReCaptcha( $this->secret_key, new \ReCaptcha\RequestMethod\CurlPost() );
		} else {
			$recaptcha = new \ReCaptcha\ReCaptcha( $this->secret_key );
		}

		if ( 'invisible_v3' === $this->validate_type ) {
			// @codingStandardsIgnoreStart
			// V3
			$response = $recaptcha->setExpectedHostname( $_SERVER['SERVER_NAME'] )
							->setExpectedAction( $this->action )
							->setScoreThreshold( 0.5 )
							->verify( $this->response, $_SERVER['REMOTE_ADDR'] );
			// @codingStandardsIgnoreEnd
		} else {
			// V2
			$response = $recaptcha->verify( $this->response, $_SERVER['REMOTE_ADDR'] );
		}

		if ( ! $response->isSuccess() ) {
			$this->is_success = false;
			$error_codes = array();
			foreach ( $response->getErrorCodes() as $code ) {
				$error_codes[] = $code;
			}
			$this->error = implode( ' | ', $error_codes );
		} else {
			$this->is_success = true;
			$this->error = false;
		}
	}

	public function is_success() {
		return $this->is_success;
	}

	public function get_error_message() {
		return $this->error;
	}
}