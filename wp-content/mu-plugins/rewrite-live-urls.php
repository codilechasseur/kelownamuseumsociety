<?php
/**
 * Plugin Name: Rewrite Live URLs
 * Description: Replaces hard-coded live domain URLs with local relative paths
 * Version: 1.0
 */

// Use template_redirect to buffer early and rewrite all output
add_action( 'template_redirect', function() {
    if ( ! is_admin() ) {
        ob_start( function( $buffer ) {
            // Replace hard-coded live domain URLs with local relative paths
            $buffer = str_ireplace( 'https://www.kelownamuseums.ca/wp-content/uploads', '/wp-content/uploads', $buffer );
            $buffer = str_ireplace( 'http://www.kelownamuseums.ca/wp-content/uploads', '/wp-content/uploads', $buffer );
            $buffer = str_ireplace( '//www.kelownamuseums.ca/wp-content/uploads', '/wp-content/uploads', $buffer );
            return $buffer;
        } );
    }
}, 1 );
