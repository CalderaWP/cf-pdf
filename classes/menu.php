<?php

/**
 * Class CF_PDF_Menu
 *
 * Create admin menu
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Menu {

	/**
	 * Page instance
	 *
	 * @since 0.1.0
	 *
	 * @var CF_PDF_Page
	 */
	private $page;

	/**
	 * CF_PDF_Menu constructor.
	 *
	 * @param CF_PDF_Page $page
	 */
	public function __construct( CF_PDF_Page $page ) {
		$this->page = $page;

	}

	/**
	 * Hook to admin_menu to create view
	 *
	 * @since 0.1.0
	 */
	public function init(){
		add_action( 'admin_menu', array( $this->page, 'display' ) );

	}


}

