<?php

/**
 * Class CF_PDF_Page
 *
 * Create admin page
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Page {


	/**
	 * Root for view directory
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $view_dir;

	/**
	 c
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * CF_PDF_Page constructor.
	 *
	 * @param string $view_dir Directory path for views
	 * @param string $url URL for assets
	 */
	public function __construct( $view_dir, $url  ) {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		$this->view_dir = $view_dir;
		$this->url = $url;
	}

	/**
	 * Create admin page view
	 *
	 * @since 0.1.0
	 */
	public function display() {
		add_submenu_page(
			'caldera-forms',
			__( 'Caldera Forms PDF', 'cf-pdf'),
			__( 'Caldera Forms PDF', 'cf-pdf'),
			'manage_options',
			'cf-pdf',
			[ $this, 'render' ]
		);
	}

	/**
	 * Redner admin page view
	 *
	 * @since 0.1.0
	 */
	public function render() {
		ob_start();
		include  $this->view_dir . '/admin.php';
		echo ob_get_clean();

	}

	/**
	 * Register scripts
	 *
	 * @uses "admin_enqueue_scripts"
	 *
	 * @param string $hook Current hook
	 */
	public function register_scripts( $hook ){
		if( 'caldera-forms_page_cf-pdf' == $hook ){
			wp_enqueue_style( 'caldera-forms-admin-styles', CFCORE_URL . 'assets/css/admin.css', array(), CFCORE_VER );
			wp_enqueue_script( 'cf-pdf', $this->url . '/admin.js', array( 'jquery' ) );
		}

	}

}