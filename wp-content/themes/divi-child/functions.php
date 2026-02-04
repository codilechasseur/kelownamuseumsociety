<?php
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Begin remove Divi Blog Module featured image crop
function pa_blog_image_width($width) {
	return '9999';
}
function pa_blog_image_height($height) {
	return '9999';
}
add_filter( 'et_pb_blog_image_width', 'pa_blog_image_width' );
add_filter( 'et_pb_blog_image_height', 'pa_blog_image_height' );
// End remove Divi Blog Module featured image crop

// Add event snippet for Page view conversion on events archive page
function add_event_snippet()
{
	$show_snippet = false;
	// Check for events archive
	if (is_post_type_archive('tribe_events')) {
		$show_snippet = true;
	}
	// Check for specific museum pages
	if (isset($_SERVER['REQUEST_URI'])) {
		$uri = rtrim($_SERVER['REQUEST_URI'], '/');
		if ($uri === '/museum/laurel-packinghouse' || $uri === '/museum/okanagan-heritage-museum') {
			$show_snippet = true;
		}
	}
	if ($show_snippet) {
		echo "<!-- Event snippet for Page view conversion page -->
        <script>
          gtag('event', 'conversion', {'send_to': 'AW-16663347189/RTctCI-pr4oaEPX_2Yk-'});
        </script>";
	}
}
add_action('wp_head', 'add_event_snippet');
