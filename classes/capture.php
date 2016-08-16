<?php

/**
 * Class CF_PDF_Capture
 *
 * Record submission emails as HTML for later generation
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Capture {

	/**
	 * Get data from email generation
	 *
	 * @uses "caldera_forms_mailer" filter
	 *
	 * @since 0.1.0
	 *
	 * @param $mail
	 * @param $data
	 * @param $form
	 *
	 * @return mixed
	 */
	public static function capture( $mail, $data, $form ){

		$entry_id = Caldera_Forms::get_field_data( '_entry_id', $form );
		if( is_array( $form  ) && self::should_capture( $form ) ){
			$message = new CF_PDF_Message( $mail );
			self::save( $message, $form[ 'ID' ], $entry_id );

		}

		return $mail;
	}

	/**
	 * Save the data from a message
	 *
	 * @since 0.1.0
	 *
	 * @param CF_PDF_Message $message
	 * @param $form_id
	 * @param $entry_id
	 */
	public static function save( CF_PDF_Message $message, $form_id, $entry_id ){
		$data = [
			'object' => wp_json_encode( $message ),
			'created' => current_time( 'mysql' ),
			'form_id' => $form_id,
			'entry_id' => $entry_id
		];
		/**
		 * Filter data from form submission before saving for later use generating PDFs
		 *
		 * @since 0.1.0
		 *
		 * @param array $data Data to save
		 * @param array $message Message data
		 * @param string $form_id ID of form for submission
		 */
		$data = apply_filters( 'cf_pdf_presave_message',$data, $message, $form_id );
		CF_PDF_DB::get_instance()->create( $data );

	}

	/**
	 * Check if a form is set to generate PDFs
	 *
	 * @since 0.1.0
	 *
	 * @param array $form
	 *
	 * @return bool
	 */
	protected static function should_capture( array  $form ){
		return CF_PDF_Form_Settings::enabled( $form[ 'ID' ] );

	}

	/**
	 * Validate saving of object
	 *
	 * @since 0.1.0
	 *
	 * @param $json
	 *
	 * @return string
	 */
	public static function prepare_to_save_object( $json ){
		if( is_object( $object = json_decode( $json ) ) ){
			return $json;
		}

		return  '';

	}



}