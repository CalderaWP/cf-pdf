<?php

/**
 * Class CF_PDF_Message
 *
 * Container for messages to be stored
 *
 * @package CF-PDF
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class CF_PDF_Message extends Caldera_Forms_Email_Save {

	/**
	 * Get size of attachments
	 *
	 * @since 0.1.0
	 */
	protected function a_size(){
		$size = 0;
		$attachments = $this->attachments();
		if( ! empty( $attachments ) ){
			foreach ( $attachments as $attachment ){
				if ( is_file( $attachment ) ) {
					$size += filesize( $attachment );
				}

			}

		}

		return $size;
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return [
			'message' => $this->body(),
			'a_size' => $this->a_size()
		];
	}

}