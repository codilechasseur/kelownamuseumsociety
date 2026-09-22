<?php
/**
 * Plugin Name: KMS Candidate Survey
 * Description: Candidate post type, survey settings page and the [candidate_survey] shortcode for the Culture Vote Kelowna candidate survey results page.
 * Version: 1.0.0
 * Author: Kelowna Museums Society
 *
 * Loader for the real plugin, which lives in ./kms-candidate-survey/ so that it can
 * ship its own CSS/JS assets (mu-plugins only auto-load top-level PHP files).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/kms-candidate-survey/plugin.php';
