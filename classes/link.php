<?php

/**
 * Class CF_PDF_Link
 *
 * Utility class for creating links for PDF generating endpoint
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Link {


	/**
	 * Create link for PDF generation
	 *
	 * @since 0.1.0
	 *
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 *
	 * @return string
	 */
	public static function create_link( $entry_id, $form_id ){
		return add_query_arg( array(
			'cf-pdf' => 1,
			'entry_id' => $entry_id,
			'form_id' => $form_id,
			'nonce' => self::nonce( $entry_id, $form_id ),
		),  home_url( 'cf-pdf' )  );

	}

	/**
	 * Check nonce on link
	 *
	 * @since 0.1.0
	 *
	 * @param string $nonce Nonce to check
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 *
	 * @return false|int
	 */
	public static function verify_nonce( $nonce, $entry_id, $form_id ){
		return wp_verify_nonce( $nonce, self::nonce_action( $entry_id, $form_id ) );
	}

	/**
	 * Create nonce
	 *
	 * @since 0.1.0
	 *
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 *
	 * @return string
	 */
	public static function nonce( $entry_id, $form_id ){
		return wp_create_nonce( self::nonce_action( $entry_id, $form_id ) );
	}

	/**
	 * Generate the nonce action
	 *
	 * @since 0.1.0
	 *
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 */
	protected static function  nonce_action( $entry_id, $form_id ){
		wp_create_nonce( 'cf-pdf-' . $entry_id . '-' . $form_id );
	}


}