<?php
/**
 * Caldera Forms PDF Admin view
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
$forms = Caldera_Forms_Forms::get_forms( true );
?>
<style>
	p.submit{ display:inline;float:left;}
	span#cf-pdf-spinner {
		margin-top: 35px;
		padding-top: 20px;
	}
	p.cf-pdf-notice {
		display: inline-block;
		padding: 4px;
		border-radius: 4px;
	}
	p.cf-pdf-success {
		background: #a3bf61;
		color: #fff;
	}
	p.cf-pdf-error {
		background: #ff0000;
		color: #fff;
	}
	li.cf-pdf-notice-wrap{
		margin-top: -5px;
	}
	.caldera-editor-header {
		height: 50px !important;
	}
</style>
<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="dashicons-cf-logo"></span>
			<?php esc_html_e( 'Caldera Forms: PDF', 'cf-pds' ); ?>
		</li>
		<li class="cf-pdf-notice-wrap">
			<p id="cf-pdf-not-saved" class="error alert cf-pdf-notice cf-pdf-error" style="display: none;visibility: hidden" aria-hidden="true">
				<?php esc_html_e( 'Settings could not be saved. Please refresh the page and try again', 'cf-pdf' ); ?>
			</p>
			<p id="cf-pdf-saved" class="error alert cf-pdf-success cf-pdf-notice" style="display: none;visibility: hidden" aria-hidden="true">
				<?php esc_html_e( 'Settings Saved', 'cf-pdf' ); ?>
			</p>
		</li>

	</ul>
</div>
<div class="cf-pdf-admin-page-wrap" style="margin-top: 75px;">

	<form id="cf-pdf-admin-form">
		<input name="action" value="cf_pdf_admin_save" type="hidden" />
		<?php wp_nonce_field( 'cf_pdf_admin_save', 'cf-pdf-nonce', false ); ?>
		<div class="caldera-config-group">
			<label for="cf-pdf-api-key">
				<?php esc_html_e( 'API Key', 'cf-pdf' ); ?>
			</label>
			<input id="cf-pdf-api-key" value="<?php echo esc_attr( CF_PDF_API_Key::get() ); ?>" name="cf-pdf-api-key" />
		</div>
		<?php include __DIR__ . '/form-settings.php'; ?>
		<div class="caldera-config-group">
			<?php submit_button( __( 'Save', 'cf-pdf' ) ); ?>
			<span class="spinner" style="display:inline;float:left;" id="cf-pdf-spinner" aria-hidden="true"></span>
		</div>

	</form>
	<div class="caldera-config-group">
		<p id="cf-pdf-purchase-link">
			<?php printf( '<a href="%s" title="%s" target="_blank">%s</a>', 'http://caldera.space', 'Click to purchase an API key', 'Click here to purchase an API key.' ); ?>
		</p>
	</div>
</div>

