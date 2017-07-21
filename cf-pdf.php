<?php
/**
 Plugin Name: Caldera Forms PDF
 * Plugin URI:  https://caldera.space
 * Description: Create PDFs from Caldera Forms Submissions
 * Version: 1.0.2
 * Author:      Caldera Labs
 * Author URI:  https://CalderaWP.com
 * License:     GPLv2+
 * Text Domain: cf-pdf
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2016 CalderaWP LLC
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Make plugin go
 *
 * @since 0.1.0
 */
add_action( 'init', 'cf_pdf_init', 5 );

/**
 * Initialize plugin
 *
 * @uses "init"
 *
 * @since 0.1.0
 */
function cf_pdf_init(){
	if( class_exists( 'Caldera_Forms_Autoloader' ) ){
		if( class_exists( 'Caldera_Forms_Email_Save' ) ){
			//register autoloader
			Caldera_Forms_Autoloader::add_root( 'CF_PDF', __DIR__ . '/classes' );

			//add hooks
			add_action( 'init', 'cf_pdf_listener' );
			add_filter( 'caldera_forms_mailer', array( 'CF_PDF_Capture', 'capture' ), 10, 3 );
			add_filter( 'caldera_forms_ajax_return', 'cf_pdf_add_link_pdf', 10, 2 );
			add_filter( 'caldera_forms_render_notices', 'cf_pdf_add_link_not_ajax', 10, 2 );

			if( current_user_can( Caldera_Forms::get_manage_cap() ) ){
				add_action( 'wp_ajax_cf_pdf_admin_save', 'cf_pdf_save_key_ajax_cb' );
			}

			//Make sure we have our DB table
			if( 1 != get_option( 'cf_pdf_db_version', 0 ) ){
				cf_pdf_db_delta_v1();
			}

			//load admin
			if( is_admin() ){
				$page = new CF_PDF_Page( __DIR__ . '/views',  plugin_dir_url(__FILE__) );
				$menu = new CF_PDF_Menu( $page );
				$menu->init();
			}

		}

	}

}



/**
 * Create the custom table for storing messages
 *
 * @since 0.1.0
 */
function cf_pdf_db_delta_v1(){
	global $wpdb;

	$table_name      = $wpdb->prefix . 'cf_pdf_emails';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
	  ID mediumint(9) NOT NULL AUTO_INCREMENT,
	  object longtext NOT NULL,
	  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  form_id text NOT NULL,
	  entry_id text NOT NULL,
	  UNIQUE KEY ID (ID)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$vars = dbDelta( $sql );
	update_option( 'cf_pdf_db_version', 1 );
}

/**
 * Route to PDF send if needed
 *
 * @since 0.1.0
 */
function cf_pdf_listener(){
	if( isset( $_GET[ 'cf-pdf' ], $_GET[ 'nonce' ], $_GET[ 'form_id' ], $_GET[ 'entry_id' ] ) && $_GET[ 'cf-pdf' ] ){
		$entry_id = absint( $_GET[ 'entry_id' ] );
		$form_id = strip_tags( $_GET[ 'form_id' ] );
		if( CF_PDF_Link::verify_nonce(  $_GET[ 'nonce' ], $entry_id, $form_id  ) ){
			cf_pdf_send( $entry_id , $form_id );
		}

		wp_die( esc_html__( 'Error generating PDF', 'cf-pdf'));
	}


}

/**
 * Send to remote API for PDF generation
 *
 * @since 0.1.0
 *
 * @param int|string $entry_id Entry ID
 * @param string $form_id Form ID
 */
function cf_pdf_send( $entry_id, $form_id ){
	$client = new CF_PDF_Client( $entry_id, $form_id );
	if( $client->can_send() ){
		$client->send();
		$link = $client->get_link();
		if( ! empty( $link ) ){
			wp_redirect( esc_url_raw( $link ) );
			exit;
		}
	}


}

/**
 * Get form name
 *
 * @since 0.1.0
 *
 * @param string $form_id Form ID
 *
 * @return string
 */
function cf_pdf_get_file_name( $form_id, $entry_id = null ){
	$form = Caldera_Forms_Forms::get_form( $form_id );
	if( is_array( $form ) && isset( $form[ 'name' ] ) ){
		$name = $form[ 'name' ];
	}else{
		$name = __( 'Form Submission', 'cf-pdf' );
	}

	/**
	 * Change name of the PDF
	 *
	 * @since 0.3.0
	 *
	 * @param string $name Name for PDF
	 * @param string $form_id Form ID
	 * @param array $form Form config
	 * @param int|null Entry ID
	 */
	return apply_filters( 'cf_pdf_pdf_name', $name, $form_id, $form, $entry_id );
}


/**
 * Add the PDF link to response
 *
 * @since 0.1.0
 *
 * @uses "caldera_forms_ajax_return" filter
 *
 * @param array $out Response data
 * @param array $form Form config
 *
 * @return mixed
 */
function cf_pdf_add_link_pdf( $out, $form ){
	if( isset( $data[ 'cf_er' ] ) || false == CF_PDF_Form_Settings::enabled( $form[ 'ID' ] ) ){
		return $out;
	}

	$entry_id = $out[ 'data' ][ 'cf_id' ];
	$form_id = $form[ 'ID' ];
	$pdf_id = CF_PDF_DB::get_instance()->find_id( $entry_id, $form_id );
	if( ! is_numeric( $pdf_id ) ){
		return $out;
	}

	$link = CF_PDF_Link::create_link( $entry_id, $form_id );

	if( filter_var( $link, FILTER_VALIDATE_URL ) ){

		$out[ 'html' ] .= cf_pdf_link_html( $form, $link );
	}

	return $out;
}

/**
 * Add link to success messages when the form is NOT submitted via AJAX
 *
 * @since 0.2.0
 *
 * @uses "caldera_forms_render_notices" filter
 *
 * @param array $notices
 * @param $form
 *
 * @return array
 */
function cf_pdf_add_link_not_ajax( $notices, $form ){
	if( ! isset( $_GET[ 'cf_id' ] ) ||  ! isset( $_GET[ 'cf_su' ] ) ){
		return $notices;
	}

	$entry_id = absint( $_GET[ 'cf_id' ] );
	$form_id = $form[ 'ID' ];
	$pdf_id = CF_PDF_DB::get_instance()->find_id( $entry_id, $form_id );
	if( ! is_numeric( $pdf_id ) ){
		return $notices;
	}

	$link = CF_PDF_Link::create_link( $entry_id, $form_id );
	if( filter_var( $link, FILTER_VALIDATE_URL ) ){

		$html = cf_pdf_link_html( $form, $link );
		if( isset( $notices[ 'success' ], $notices[ 'success' ][ 'note' ] ) ){
			$notices[ 'success' ][ 'note' ] = '<div class=" alert alert-success">' . $notices[ 'success' ][ 'note' ] . '</div>' . $html;
		}else{
			$notices[ 'success' ][ 'note' ] = $html;
		}

	}

	return $notices;

}

/**
 * Create HTML for linl
 *
 * @param array $form Form config
 * @param string $link The actual link.
 *
 * @return string
 */
function cf_pdf_link_html( $form, $link ){

	/**
	 * Filter the classes for the generate PDF link HTML
	 *
	 * @since 0.1.0
	 *
	 * @param string $classes The classes as string.
	 * @param array $form Form config
	 */
	$classes = apply_filters( 'cf_pdf_link_classes', ' alert alert-success', $form );


	/**
	 * Filter the visible content for the generate PDF link HTML
	 *
	 * @since 0.1.0
	 *
	 * @param string $message Link message
	 * @param array $form Form config
	 */
	$message = apply_filters( 'cf_pdf_link_message', __( 'Download Form Entry As PDF', 'cf-pdf', $form ), $form );

	/**
	 * Filter the title attribute for the generate PDF link HTML
	 *
	 * @since 0.1.0
	 *
	 * @param string $title Title attribute.
	 * @param array $form Form config
	 */
	$title = apply_filters( 'cf_pdf_link_title',  __( 'Download Form Entry As PDF', 'cf-pdf' ), $form );

	return sprintf( '<div class="%s"><a href="%s" title="%s" target="_blank">%s</a></div>',
		esc_attr( $classes ),
		esc_url( $link ),
		esc_attr( $title ),
		esc_html( $message )
	);
}
/**
 * Save settings via AJAX
 *
 * @uses "wp_ajax_cf_pdf_admin_save" action
 *
 * @since 0.1.0
 */
function cf_pdf_save_key_ajax_cb(){

	if( isset( $_POST[ 'cf-pdf-nonce' ] ) ){
		if( wp_verify_nonce( $_POST[ 'cf-pdf-nonce' ], 'cf_pdf_admin_save' ) ){
			if ( isset( $_POST[ 'cf-pdf-api-key' ] ) ) {
				$saved = CF_PDF_API_Key::save( $_POST[ 'cf-pdf-api-key' ] );
			}
			if( isset( $_POST[ 'enable' ] ) && is_array( $_POST[ 'enable' ] ) ){
				CF_PDF_Form_Settings::bulk_update( $_POST[ 'enable' ] );
			}else{
				CF_PDF_Form_Settings::disable_for_all();
			}

			status_header( 200 );
			wp_send_json_success();



		}else{
			status_header( 403 );
			wp_send_json_error();
		}
	}

	status_header( 400 );
	wp_send_json_error();

}

/**
 * Show admin notice about Caldera Forms Pro
 *
 * @since 1.5.3
 */
function cf_pdf_admin_notice() {
	$class = 'notice notice-error';
	$message = sprintf(
		esc_html__( 'Caldera Forms PDF is now a part of Caldera Forms Pro learn more at %s', 'caldera-forms-pdf' ), '<a href="https://calderaforms.com/prou?tm_source=wp-admin&utm_medium=plugins&utm_campaign=caldera-forms-pdf" target="_blank">CalderaFormsPro.com</a>'
		);

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message  );
}
add_action( 'admin_notices', 'cf_pdf_admin_notice' );