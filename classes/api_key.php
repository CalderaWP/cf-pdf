<?php

/**
 * Class CF_PDF_API_Key
 *
 * DB abstraction for API key settings
 */
class CF_PDF_API_Key extends CF_PDF_Settings{

	/**
	 * @inheritDoc
	 */
	protected static $key_option = '_cf_pdf_api_key';

	/**
	 * @inheritDoc
	 */
	public static function save( $value ){
		if( $value == strtolower( preg_replace("/[^ \w]+/", "", $value) ) ){
			$stored  = update_option( self::$key_option, $value );
			return $stored;
		}

		return false;
	}
}