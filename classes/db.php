<?php

/**
 * Class CF_PDF_DB
 *
 * Database abstraction for saving form entry HTML
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_DB extends Caldera_Forms_DB_Base {


	/**
	 * @inheritDoc
	 */
	protected $primary_fields = array(
		'object' => array(
			'%s',
			array( 'CF_PDF_Capture', 'prepare_to_save_object' ),
		),
		'created' => array(
			'%s',
			'strip_tags'
		),
		'form_id' => array(
			'%s',
			'strip_tags'
		),
		'entry_id' => array(
			'%s',
			'strip_tags'
		),
	);

	/**
	 * @inheritDoc
	 */
	protected $index = 'ID';

	/**
	 * @inheritDoc
	 */
	protected $table_name = 'cf_pdf_emails';

	/**
	 * Store class instance
	 *
	 * @since 0.1.0
	 *
	 * @var CF_PDF_DB
	 */
	private static $instance;

	/**
	 * Get class instance
	 *
	 * @since 0.1.0
	 *
	 * @return CF_PDF_DB
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Find saved entry by entry ID and form ID
	 *
	 * @since 0.1.0
	 *
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 *
	 * @return array|void
	 */
	public function find( $entry_id, $form_id ){

		global $wpdb;
		$table_name = $this->get_table_name();
		$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE `entry_id` = %d AND `form_id` = %s", $entry_id, $form_id );
		$results = $wpdb->get_results( $sql, ARRAY_A  );
		if( ! empty( $results ) ){
			return $results[0];
		}

	}

	/**
	 * Find saved message ID by entry ID and form ID
	 *
	 * @param int|string $entry_id Entry ID
	 * @param string $form_id Form ID
	 *
	 * @return int|void
	 */
	public function find_id( $entry_id, $form_id ){
		$entry = $this->find( $entry_id, $form_id );
		if( ! empty( $entry ) && isset( $entry[ 'ID' ] ) ){
			return intval( $entry[ 'ID' ] );
		}

	}

}