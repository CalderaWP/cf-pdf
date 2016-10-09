<?php

/**
 * Class CF_PDF_Client
 *
 * API client for sending data to caldera.space app
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Client {

	/**
	 * Saved data object
	 *
	 * @since 0.1.0
	 *
	 * @var stdClass
	 */
	protected $object;

	/**
	 * Entry ID
	 *
	 * @since 0.1.0
	 *
	 * @var string|int
	 */
	protected $entry_id;

	/**
	 * Form ID
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected  $form_id;

	/**
	 * Url to download PDF
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected  $link;

	/**
	 * Url for remote API
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $api = 'https://caldera.space/api/pdf';

	/**
	 * CF_PDF_Client constructor.
	 *
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 */
	public function __construct( $entry_id, $form_id ) {
		$this->entry_id = $entry_id;
		$this->form_id = $form_id;
		$this->set_object();
	}

	/**
	 *  Get URL to download PDF
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function get_link(){
		if( filter_var( $this->link, FILTER_VALIDATE_URL ) ){
			return $this->link;
		}

		return '';
	}

	/**
	 * Set saved data object if possible
	 *
	 * @since 0.1.0
	 */
	protected function set_object(){
		$saved  = CF_PDF_DB::get_instance()->find( $this->entry_id, $this->form_id );
		if( is_array( $saved ) && isset( $saved[ 'object' ] ) ){
			$json =  $saved[ 'object' ];
			if( is_object( $_obj = json_decode( $json ) ) ){
				$this->object = $_obj;
			}
		}


	}

	/**
	 * Check if sending should be possible
	 *
	 * @since 0.1.0
	 *
	 * @return bool
	 */
	public function can_send(){
		return $this->validate_saved_object();
	}

	/**
	 * Send data to remote API
	 *
	 * @since 0.1.0
	 */
	public function send(){
		/**
		 * Filter the URL we are sending request to generate PDF to
		 *
		 * @since 0.1.0
		 *
		 * @param string $url The API URL
		 */
		$url = apply_filters( 'cf_pdf_pdf_url', trailingslashit( $this->api )  );

		$r = wp_remote_post( $url, array(
			'body' => $this->get_args()
		) );

		if( 200 == wp_remote_retrieve_response_code( $r ) || 201 == wp_remote_retrieve_response_code( $r ) ){
			$body = wp_remote_retrieve_body( $r );
			if( is_object( $body = json_decode( $body ) ) ){
				if( isset( $body->link )  ){
					$this->link = $body->link;
				}

			}

		}else{
			wp_die( wp_remote_retrieve_body( $r  ) );
		}


	}

	/**
	 * Validate saved data
	 *
	 * @since 0.1.0
	 *
	 * @return bool
	 */
	protected function validate_saved_object(){
		if( ! is_object( $this->object ) ){
			return false;
		}

		if( ! isset( $this->object->message ) ){
			return false;
		}

		return true;
	}

	/**
	 * Create request arguments for the PDF generating API call
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	protected function get_args(){
		$name = cf_pdf_get_file_name( $this->form_id, $this->entry_id );
		$code = CF_PDF_API_Key::get();

		$request_args =  array(
			'name' => $name,
			'code' => $code,
			'html' => $this->object->message ,
			'entry_id' => $this->entry_id,
			'form_id' => $this->form_id
		);

		/**
		 * Filter request arguments for the PDF generating API call
		 *
		 * @since 0.1.0
		 *
		 * @param array $request_args
		 */
		return  apply_filters( 'cf_pdf_api_request_args', $request_args );
	}
}