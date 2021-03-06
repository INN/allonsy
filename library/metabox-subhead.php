<?php
/**
 * Functions related to the "Subhead" meta box
 *
 * Based on the work in https://github.com/INN/largo/issues/1730 but
 * without the Largo dependency.
 *
 * Development work was done on the assumption that the name for
 * this text was 'subtitle'; Cal Health Report calls it a 'subhead' instead.
 */

/**
 * Register the 'subtitle' post meta
 */
function subtitle_meta_box_register_post_meta() {
	return register_post_meta(
		'post',
		'subtitle',
		array(
			'object_subtype' => 'post',
			'type' => 'string',
			'description' => __( 'The subhead, deck, subtitle, or lead is a preface shown on article pages below the main title.' , 'allonsy' ),
			'single' => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			// 'auth_callback' => '',
			'show_in_rest' => true,
		),
	);
}
add_action( 'init', 'subtitle_meta_box_register_post_meta' );

/**
 * Output the subtitle metabox.
 *
 * @link Copied from https://github.com/INN/umbrella-wcij/blob/16247cdd8f03a593633c82f8836c4c1e8aa83987/wp-content/themes/wisconsinwatch/inc/metaboxes.php with improvements
 */
function subtitle_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	wp_nonce_field( 'subtitle_meta_box_nonce', 'subtitle_meta_box_nonce' );
	?>
		<label for="subtitle" class="screen-reader-text"><?php esc_html_e( 'Subhead', 'largo' ); ?></label>
		<textarea name="subtitle" id="subtitle" class="widefat" rows="2" cols="20"><?php
			// PHP open/close are at the textarea boundary so we don't prepend/append this with tabs.
			if ( isset( $values['subtitle'] ) ) {
				echo sanitize_text_field( $values['subtitle'][0] );
			}
		?></textarea>
		<p><small><?php esc_html_e( 'Plain text is allowed in the post subhead.', 'largo' ); ?></small></p>
	<?php
}

/**
 * Save action for the metabox
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post Post object.
 * @param bool    $update whether the post is being updated
 * @return null
 */
function subtitle_meta_box_save( $post_id, $post, $update = false ) {
	if ( ! isset( $_POST['subtitle_meta_box_nonce'] ) ) {
		error_log(var_export( 'subtitle meta box nonce not set', true));
		return;
	}

	if ( ! wp_verify_nonce( $_POST['subtitle_meta_box_nonce'], 'subtitle_meta_box_nonce' ) ) {
		error_log(var_export( 'subtitle meta box nonce not valid', true));
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	$subtitle = wp_kses_post( $_POST['subtitle'] );

	if ( ! empty( $subtitle ) ) {
		update_post_meta( $post_id, 'subtitle', $subtitle );
	} else {
		delete_post_meta( $post_id, 'subtitle' );
	}

	return $subtitle;
}
add_action( 'save_post', 'subtitle_meta_box_save', 10, 3 );

/**
 * Register our subhead metabox
 *
 * This doesn't have anything special applied to it for Gutenberg.
 * If Publicsource switches to Gutenberg, read https://developer.wordpress.org/block-editor/developers/backward-compatibility/meta-box/ and update this box as necessary.
 */
add_action(
	'add_meta_boxes',
	function() {
		add_meta_box(
			'subtitle', // id
			__( 'Subhead', 'allonsy' ), // title
			'subtitle_meta_box_display', // callback
			array( 'post' ), // screen
			'normal', // context
			'high' // priority
		);
	}
);
