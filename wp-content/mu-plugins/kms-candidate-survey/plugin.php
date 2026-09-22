<?php
/**
 * KMS Candidate Survey — bootstrap.
 *
 * Content model
 *   - `kms_candidate` post type: title = candidate name. Meta (all hidden, `_kcs_` prefix):
 *       _kcs_party            text
 *       _kcs_status           responded | declined | no_reply
 *       _kcs_q{N}_choice      the chosen option text for question N
 *       _kcs_q{N}_written     optional written follow-up for question N
 *   - `kms_candidate_survey` option: page copy + the questions (see includes/settings.php).
 *
 * Rendering
 *   - `[candidate_survey]` shortcode (includes/render.php), designed to be placed in a
 *     Divi Text/Code module. Everything is server-rendered; assets/survey.js only adds
 *     the name search and expand/collapse behaviour.
 *   - "Candidate Survey (unbranded)" page template (includes/template.php) that drops
 *     the theme header/footer and site branding around the shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KCS_VERSION', '1.0.1' );
define( 'KCS_DIR', __DIR__ );
define( 'KCS_URL', content_url( 'mu-plugins/kms-candidate-survey' ) );
define( 'KCS_POST_TYPE', 'kms_candidate' );
define( 'KCS_OPTION', 'kms_candidate_survey' );
define( 'KCS_MAX_QUESTIONS', 10 );

require_once KCS_DIR . '/includes/settings.php';
require_once KCS_DIR . '/includes/post-type.php';
require_once KCS_DIR . '/includes/render.php';
require_once KCS_DIR . '/includes/template.php';
