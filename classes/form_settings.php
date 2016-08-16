<?php

/**
 * Class CF_PDF_Form_Settings
 *
 * Database abstraction for enabled/disabled setting for PDF by form
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Form_Settings extends CF_PDF_Settings {


	/**
	 * @inhertDoc
	 */
	protected static $key_option = 'cf_pdf_forms';

	/**
	 * Get all forms that PDF generation is enabled for
	 *
	 * @since 0.2.0
	 *
	 * @return array
	 */
	public static function get_enabled(){
		$forms = static::get();
		if( ! is_array( $forms ) ){
			return [];
		}

		return $forms;
	}

	/**
	 * Do a bulk update
	 *
	 * @since 0.2.0
	 *
	 * @param array $enable_for
	 */
	public  static function bulk_update( array $enable_for ){
		if( ! empty( $enable_for )  ){
			$forms = Caldera_Forms_Forms::get_forms( true );
			if( empty( $forms ) ){
				return;
			}

			$enabled_forms = self::get_enabled();
			foreach ( $forms as $form_id => $form ){
				if( array_key_exists( $form_id, $enable_for ) ){
					if( ! self::enabled( $form_id ) ){
						$enabled_forms[] = $form_id;
					}
				} else{
					$enabled_forms = self::unset_key( $form_id, $enabled_forms );
				}
			}

			static::save( $enabled_forms );
		}else{
			self::disable_for_all();
		}

	}

	/**
	 * Disable PDF generation for all forms
	 *
	 * @since 0.2.0
	 */
	public static function disable_for_all(){
		static::save( [] );
	}

	/**
	 * Check if PDF generation is enabled for a specific form
	 *
	 * @since 0.2.0
	 *
	 * @param string $form_id Form ID to check for
	 *
	 * @return bool
	 */
	public static function enabled( $form_id ){
		return in_array( $form_id, static::get_enabled() );
	}

	/**
	 * Enable PDF generation for a form
	 *
	 * @since 0.2.0
	 *
	 * @param string $form_id ID of form to enable
	 */
	public static function enable( $form_id){
		$forms = static::get_enabled();

		if( ! self::enable( $form_id ) ){
			$forms[] = $form_id;
		}

		static::save( $forms );

	}

	/**
	 * Diable PDF generation for a form
	 *
	 * @since 0.2.0
	 *
	 * @param string $form_id ID of form to disable
	 */
	public static function disable( $form_id ){
		$forms = static::get();
		if( self::enabled( $form_id ) ){
			$forms = self::unset_key( $form_id, $forms );
			static::save( $forms );

		}


	}

	/**
	 * @param $form_id
	 * @param $forms
	 */
	protected static function unset_key( $form_id, $forms )
	{
		$key = array_search( $form_id, $forms );
		if ( false !== $key ) {
			unset( $forms[ $key ] );
		}

		return $forms;
	}
}