<?php
/**
 * "Candidate Survey (unbranded)" page template.
 *
 * Registers a page template from the plugin (no theme edits) that bypasses Divi's
 * header, footer, sidebar and Theme Builder layouts entirely. On that template the
 * page also drops the site branding that would otherwise leak in around the content:
 * the " - Kelowna Museums" title suffix, og:site_name, the site favicon and the
 * site-wide social share image. Analytics/cookie scripts hooked to wp_head/wp_footer
 * still run.
 *
 * Editors pick it from Page Attributes > Template on the page that holds the shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KCS_TEMPLATE', 'kcs-standalone' );

/**
 * Is the current request rendering a page on the unbranded template?
 */
function kcs_is_standalone() {
	static $is = null;
	if ( null === $is ) {
		$is = is_singular() && KCS_TEMPLATE === get_page_template_slug( get_queried_object_id() );
	}
	return $is;
}

/* Make the template selectable in Page Attributes (and the block editor). */
add_filter( 'theme_page_templates', function ( $templates, $theme, $post, $post_type ) {
	if ( 'page' === $post_type || null === $post_type ) {
		$templates[ KCS_TEMPLATE ] = 'Candidate Survey (unbranded)';
	}
	return $templates;
}, 10, 4 );

/* Serve the plugin's template file when a page uses it. */
add_filter( 'template_include', function ( $template ) {
	if ( kcs_is_standalone() ) {
		return KCS_DIR . '/templates/standalone.php';
	}
	return $template;
}, 99 );

/* -------------------------------------------------------------------------
 * Assets: the page's own type and palette (prototype fonts), plus base layout.
 * ---------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', function () {
	if ( ! kcs_is_standalone() ) {
		return;
	}
	wp_enqueue_style(
		'kcs-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Source+Sans+3:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'kcs-standalone', KCS_URL . '/assets/standalone.css', array( 'kcs-survey', 'kcs-fonts' ), KCS_VERSION );
	kcs_enqueue_assets();
}, 20 );

add_filter( 'body_class', function ( $classes ) {
	if ( kcs_is_standalone() ) {
		$classes[] = 'kcs-standalone';
	}
	return $classes;
} );

/* -------------------------------------------------------------------------
 * De-branding: document title, favicon, social meta.
 * ---------------------------------------------------------------------- */

/* Core: drop the site name from the <title>. */
add_filter( 'document_title_parts', function ( $parts ) {
	if ( kcs_is_standalone() ) {
		unset( $parts['site'], $parts['tagline'] );
	}
	return $parts;
}, 99 );

/* The SEO Framework: no " - Site Name" branding on the title or social title. */
add_filter( 'the_seo_framework_use_title_branding', function ( $use ) {
	return kcs_is_standalone() ? false : $use;
} );

/* The SEO Framework: describe the page from the survey intro rather than the site default. */
add_filter( 'the_seo_framework_generated_description', function ( $desc ) {
	if ( ! kcs_is_standalone() ) {
		return $desc;
	}
	$settings = kcs_get_settings();
	$intro    = trim( preg_replace( '/\s+/', ' ', (string) $settings['intro'] ) );
	if ( '' === $intro ) {
		return $desc;
	}
	if ( function_exists( 'the_seo_framework' ) && method_exists( the_seo_framework(), 'trim_excerpt' ) ) {
		return the_seo_framework()->trim_excerpt( $intro, 0, 155 );
	}
	return mb_strlen( $intro ) > 155 ? rtrim( mb_substr( $intro, 0, 152 ) ) . '…' : $intro;
} );

/* The SEO Framework: no site-wide fallback share image (a featured image on the page still works). */
add_filter( 'the_seo_framework_image_generation_params', function ( $params ) {
	if ( kcs_is_standalone() && is_array( $params ) ) {
		$params['fallback'] = array();
	}
	return $params;
} );

/* The SEO Framework: strip og:site_name. */
add_filter( 'the_seo_framework_meta_render_data', function ( $tags ) {
	if ( ! kcs_is_standalone() || ! is_array( $tags ) ) {
		return $tags;
	}
	foreach ( $tags as $id => $tag ) {
		$name = isset( $tag['attributes']['property'] ) ? $tag['attributes']['property'] : ( isset( $tag['attributes']['name'] ) ? $tag['attributes']['name'] : '' );
		if ( 'og:site_name' === $name || 'twitter:site' === $name ) {
			unset( $tags[ $id ] );
		}
	}
	return $tags;
} );

/* The SEO Framework: no structured data (Organization / WebSite / breadcrumb all name the site). */
add_filter( 'the_seo_framework_schema_graph_data', function ( $graph ) {
	return kcs_is_standalone() ? array() : $graph;
} );

/* Head <link>/<meta> tags that carry the site or theme name. */
add_action( 'template_redirect', function () {
	if ( ! kcs_is_standalone() ) {
		return;
	}
	// Core: RSS links titled "Site Name » Feed", and oEmbed discovery (its JSON names the site).
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Divi: <meta name="generator" content="Kelowna Museums Society v.1.0.0">.
	remove_action( 'wp_head', 'head_addons', 7 );

	// The Events Calendar: "Site Name » iCal Feed" link.
	if ( function_exists( 'tribe' ) ) {
		try {
			remove_action( 'wp_head', array( tribe( 'tec.iCal' ), 'set_feed_link' ), 2 );
		} catch ( \Throwable $e ) {
			// TEC not loaded; nothing to remove.
		}
	}
} );

/* Favicon / touch icons. */
add_filter( 'get_site_icon_url', function ( $url ) {
	return kcs_is_standalone() ? '' : $url;
}, 99 );
add_filter( 'site_icon_meta_tags', function ( $tags ) {
	return kcs_is_standalone() ? array() : $tags;
}, 99 );
