<?php 
// Active Mim Type upload
add_filter( 'mime_types', 'dggm_mime_types' );
function dggm_mime_types( $existing_mimes ) {

    $existing_mimes['csv'] = 'text/csv';
    return $existing_mimes;
}
// Image Alternative Text from url
function dggm_image_alt_by_url( $image_url ) {
    global $wpdb;
    // phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
    $attachment = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM %1s WHERE guid='%2s';", esc_sql($wpdb->posts) , esc_sql($image_url) ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery
    // phpcs:enable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
    if($wpdb->last_error !== '')
        return false;
    //$attachment = $wpdb->get_col($query);

    if(count ($attachment) < 1)
        return false;
    $image_id  = intval($attachment[0]);
    $image_alt = get_post_meta( $image_id , '_wp_attachment_image_alt', true );

    return $image_alt;
}

/**
 * render pattern or mask markup
 *
 */
function dggm_render_pattern_or_mask_html( $props, $type ) {
    $html = array(
        'pattern' => '<span class="et_pb_background_pattern"></span>',
        'mask' => '<span class="et_pb_background_mask"></span>'
    );
    return $props == 'on' ? $html[$type] : '';
}
/**
 * Use Post , Product , CPT Function file
 *
 * @param Array $settings
 * @return HTML
 */
function dggm_print_background_mask_and_pattern_dynamic_modules( $settings ) {
    $pattern_background = isset($settings['background_enable_pattern_style']) ? dggm_render_pattern_or_mask_html($settings['background_enable_pattern_style'], 'pattern') : '';
    $masking_background = isset($settings['background_enable_mask_style']) ? dggm_render_pattern_or_mask_html($settings['background_enable_mask_style'], 'mask') : '';
    return $pattern_background . $masking_background;
}

function dg_gm_add_attachment_field( $form_fields, $post ) {
	$form_fields['dg-gm-url'] = [
		'label' => __( 'Custom URL', 'dggm-gallery-modules' ),
		'input' => 'url',
		'value' => esc_attr( get_post_meta( $post->ID, 'dg_gm_url', true ) ),
		'helps' => __( 'Enter Custom URL for DiviGear Gallery', 'dggm-gallery-modules' ),
	];

	return $form_fields;
}

add_filter( 'attachment_fields_to_edit', 'dg_gm_add_attachment_field', 10, 2 );

function dg_gm_save_attachment_field_save( $post, $attachment ) {

	if ( isset( $attachment['dg-gm-url'] ) ) {
		update_post_meta( $post['ID'], 'dg_gm_url', esc_url( $attachment['dg-gm-url'] ) );
	}

	return $post;
}

add_filter( 'attachment_fields_to_save', 'dg_gm_save_attachment_field_save', 10, 2 );

function dg_get_custom_url( $use_url, $post_id ) {
	return 'on' === $use_url ? sprintf( 'data-customurl="%1$s"', esc_attr( get_post_meta( $post_id, 'dg_gm_url', true ) ) ) : '';
}

require_once( DGGM_MAIN_DIR . '/includes/functions/dg_imagegallery_functions.php');
require_once( DGGM_MAIN_DIR . '/includes/functions/dg_jsgallery_functions.php');
require_once( DGGM_MAIN_DIR . '/includes/functions/dg_packery_functions.php');

require_once( DGGM_MAIN_DIR . '/admin/class-divigear-gallery.php');