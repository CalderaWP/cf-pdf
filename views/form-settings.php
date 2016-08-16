<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( ! isset( $forms ) ){
	return;
}

?>
<fieldset class="caldera-config-group">
	<legend>
		<strong>
			<?php esc_html_e( 'Enable PDF Generations For:', 'cf-pdf' ); ?>
		</strong>
	</legend>
	<?php foreach ( $forms as $form ) {
		$id = $form[ 'ID' ];
		$name = $form[ 'name' ];
		$enable_name = sprintf( 'enable[%s]', $id );
		$enabled = CF_PDF_Form_Settings::enabled( $form[ 'ID' ] );
		if( $enabled ){
			$enabled = 'checked';
		}else{
			$enabled = '';
		}
		printf( '<div class="caldera-config-group"><input id="%s" type="checkbox" name="%s" %s>', esc_attr( $id ), esc_attr( $enable_name ), $enabled );
		printf( '<label for="%s">%s</label></div>', esc_attr( $id ), esc_html( $name ) );
	} ?>

</fieldset>
