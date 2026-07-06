<?php
/**
 * KMS Performance Fixes (MU-Plugin)
 *
 * Deployed via the repo (wp-content/mu-plugins/ is included in the GitHub Actions
 * release). These are the "Bucket A" code-level fixes for the site slowness:
 *
 *   1. Tame the cron cache-purge cascade. TEC's recurring-event and cleanup crons
 *      write posts on visitor-triggered wp-cron runs. Each save_post makes Divi
 *      regenerate its static CSS AND makes the Kinsta MU-plugin purge the entire
 *      page cache over HTTP (~5s + a cold cache for every visitor afterwards).
 *      During cron we skip both.
 *
 *   2. Disable author archives + block ?author=N enumeration. These are the
 *      biggest source of bot 404s that were bloating the Redirection log table.
 *
 *   3. Fast-404 a short list of known bot probe paths so WordPress does not render
 *      the full (Divi) 404 template for obvious garbage requests.
 *
 * Each behaviour is individually toggleable via a constant (all default ON).
 * Define the constant as false in wp-config.php to disable a single behaviour.
 *
 * NOTE: This does NOT change any plugin settings or database rows. The matching
 * one-time server actions (disabling TEC auto-trash, truncating the Redirection
 * 404 log, enabling system cron + Redis) are done separately on Kinsta.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'KMS_PERF_TAME_CRON_CACHE' )        || define( 'KMS_PERF_TAME_CRON_CACHE', true );
defined( 'KMS_PERF_DISABLE_AUTHOR_ARCHIVES' ) || define( 'KMS_PERF_DISABLE_AUTHOR_ARCHIVES', true );
defined( 'KMS_PERF_BLOCK_BOT_PROBES' )        || define( 'KMS_PERF_BLOCK_BOT_PROBES', true );

/**
 * 1. During wp-cron, stop programmatic post saves from triggering the expensive
 *    Divi static-CSS regeneration and the full-site Kinsta cache purge.
 *
 *    Divi hook  : add_action( 'save_post', array( 'ET_Core_PageResource', 'save_post_cb' ), 10, 3 )
 *    Kinsta hook: purges by calling https://localhost/kinsta-clear-cache-* over HTTP
 */
if ( KMS_PERF_TAME_CRON_CACHE ) {

	add_action( 'init', function () {
		if ( ! wp_doing_cron() ) {
			return;
		}
		// Divi: don't rebuild static CSS on cron-driven event writes.
		if ( class_exists( 'ET_Core_PageResource' ) ) {
			remove_action( 'save_post', array( 'ET_Core_PageResource', 'save_post_cb' ), 10 );
		}
	}, 99 );

	// Kinsta: no-op the full-cache purge HTTP calls while running cron.
	add_filter( 'pre_http_request', function ( $pre, $args, $url ) {
		if ( wp_doing_cron() && false !== strpos( (string) $url, '/kinsta-clear-cache' ) ) {
			return array(
				'headers'  => array(),
				'body'     => '',
				'response' => array( 'code' => 200, 'message' => 'OK (suppressed during cron)' ),
				'cookies'  => array(),
				'filename' => null,
			);
		}
		return $pre;
	}, 10, 3 );
}

/**
 * 2. Author archives + ?author=N enumeration -> the main bot-404 source.
 */
if ( KMS_PERF_DISABLE_AUTHOR_ARCHIVES ) {

	// ?author=1 style enumeration (front-end only). Redirect before the query resolves.
	add_action( 'init', function () {
		if ( is_admin() ) {
			return;
		}
		if ( isset( $_GET['author'] ) && preg_match( '/^\d+$/', (string) wp_unslash( $_GET['author'] ) ) ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	} );

	// /author/<slug>/ archive pages.
	add_action( 'template_redirect', function () {
		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	} );
}

/**
 * 3. Fast-404 for obvious bot probe paths (exact-path match, front-end only).
 *    Conservative list — only paths that are never real pages on this site.
 */
if ( KMS_PERF_BLOCK_BOT_PROBES ) {

	add_action( 'parse_request', function () {
		$probes = array(
			'/wp',
			'/wordpress',
			'/backup',
			'/ads.txt',
			'/meta.json',
			'/.well-known/traffic-advice',
		);

		$path = parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH );
		$path = '/' . trim( (string) $path, '/' );

		if ( in_array( $path, $probes, true ) ) {
			status_header( 404 );
			nocache_headers();
			exit;
		}
	} );
}
