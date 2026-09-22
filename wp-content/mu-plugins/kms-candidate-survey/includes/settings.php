<?php
/**
 * Survey settings: page copy + the questions.
 *
 * Stored in a single option (KCS_OPTION):
 *   eyebrow, heading, intro, footer_note   strings
 *   question_count                         int, 1..KCS_MAX_QUESTIONS
 *   questions                              [ 1 => [ prompt, choices (newline list), followup ], ... ]
 *
 * Questions are keyed by a stable slot number (1..N). Candidate answers are stored
 * against the same slot number, so editing a question's wording never orphans answers.
 * Lowering the question count hides slots from the public page but keeps their data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default settings (used until the admin saves the page for the first time).
 */
function kcs_default_settings() {
	return array(
		'eyebrow'        => 'Culture Vote Kelowna · Arts & Culture Survey',
		'heading'        => 'Where City Council Candidates Stand on Arts & Culture',
		'intro'          => 'Ahead of the 2026 civic election, every candidate for Kelowna City Council was asked four questions about supporting arts, culture and heritage in the city. Responses are listed below by candidate — expand a name to read their full answers.',
		'footer_note'    => 'Compiled by Culture Vote Kelowna. Survey responses are presented as submitted by each candidate or their campaign.',
		'question_count' => 4,
		'questions'      => array(),
	);
}

/**
 * Fetch the merged settings array.
 */
function kcs_get_settings() {
	$saved = get_option( KCS_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	$settings = array_merge( kcs_default_settings(), $saved );

	$settings['question_count'] = max( 1, min( KCS_MAX_QUESTIONS, (int) $settings['question_count'] ) );
	if ( ! is_array( $settings['questions'] ) ) {
		$settings['questions'] = array();
	}

	return $settings;
}

/**
 * Active questions, in slot order, with choices parsed into arrays.
 *
 * Slots without a prompt are skipped so the public page never shows an empty question.
 *
 * @return array [ slot => [ 'slot' => int, 'prompt' => string, 'choices' => string[], 'followup' => string ] ]
 */
function kcs_get_questions() {
	$settings  = kcs_get_settings();
	$questions = array();

	for ( $slot = 1; $slot <= $settings['question_count']; $slot++ ) {
		$raw = isset( $settings['questions'][ $slot ] ) && is_array( $settings['questions'][ $slot ] )
			? $settings['questions'][ $slot ]
			: array();

		$prompt = isset( $raw['prompt'] ) ? trim( (string) $raw['prompt'] ) : '';
		if ( '' === $prompt ) {
			continue;
		}

		$questions[ $slot ] = array(
			'slot'     => $slot,
			'prompt'   => $prompt,
			'choices'  => kcs_parse_choices( isset( $raw['choices'] ) ? $raw['choices'] : '' ),
			'followup' => isset( $raw['followup'] ) ? trim( (string) $raw['followup'] ) : '',
		);
	}

	return $questions;
}

/**
 * "One option per line" textarea -> clean, de-duplicated list.
 */
function kcs_parse_choices( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$out   = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' !== $line && ! in_array( $line, $out, true ) ) {
			$out[] = $line;
		}
	}
	return $out;
}

/**
 * Sanitize the whole option on save.
 */
function kcs_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = kcs_default_settings();

	foreach ( array( 'eyebrow', 'heading' ) as $key ) {
		$clean[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : '';
	}
	foreach ( array( 'intro', 'footer_note' ) as $key ) {
		$clean[ $key ] = isset( $input[ $key ] ) ? sanitize_textarea_field( wp_unslash( $input[ $key ] ) ) : '';
	}

	$clean['question_count'] = isset( $input['question_count'] ) ? (int) $input['question_count'] : 4;
	$clean['question_count'] = max( 1, min( KCS_MAX_QUESTIONS, $clean['question_count'] ) );

	$clean['questions'] = array();
	$raw_questions      = isset( $input['questions'] ) && is_array( $input['questions'] ) ? $input['questions'] : array();
	for ( $slot = 1; $slot <= KCS_MAX_QUESTIONS; $slot++ ) {
		$q = isset( $raw_questions[ $slot ] ) && is_array( $raw_questions[ $slot ] ) ? $raw_questions[ $slot ] : array();

		$clean['questions'][ $slot ] = array(
			'prompt'   => isset( $q['prompt'] ) ? sanitize_textarea_field( wp_unslash( $q['prompt'] ) ) : '',
			'choices'  => isset( $q['choices'] ) ? implode( "\n", kcs_parse_choices( wp_unslash( $q['choices'] ) ) ) : '',
			'followup' => isset( $q['followup'] ) ? sanitize_text_field( wp_unslash( $q['followup'] ) ) : '',
		);
	}

	return $clean;
}

add_action( 'admin_init', function () {
	register_setting(
		'kcs_settings',
		KCS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kcs_sanitize_settings',
			'default'           => kcs_default_settings(),
		)
	);
} );

add_action( 'admin_menu', function () {
	add_submenu_page(
		'edit.php?post_type=' . KCS_POST_TYPE,
		'Survey Settings',
		'Survey Settings',
		'manage_options',
		'kcs-settings',
		'kcs_render_settings_page'
	);
} );

/**
 * Settings page markup.
 */
function kcs_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = kcs_get_settings();
	$opt      = KCS_OPTION;
	?>
	<div class="wrap kcs-settings">
		<h1>Candidate Survey Settings</h1>

		<p class="description" style="max-width:70ch">
			Set the survey questions and the copy shown on the public results page. To display the
			results, add the shortcode <code>[candidate_survey]</code> to any page (in Divi, use a
			Text or Code module). To publish it without the site's header, footer and branding,
			set that page's <em>Template</em> (under Page Attributes) to
			<strong>Candidate Survey (unbranded)</strong>. Candidates and their answers are entered under
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . KCS_POST_TYPE ) ); ?>">Candidates</a>.
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'kcs_settings' ); ?>

			<h2>Page copy</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="kcs-eyebrow">Eyebrow</label></th>
					<td><input type="text" id="kcs-eyebrow" class="large-text" name="<?php echo esc_attr( $opt ); ?>[eyebrow]" value="<?php echo esc_attr( $settings['eyebrow'] ); ?>">
					<p class="description">Small uppercase line above the heading.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="kcs-heading">Heading</label></th>
					<td><input type="text" id="kcs-heading" class="large-text" name="<?php echo esc_attr( $opt ); ?>[heading]" value="<?php echo esc_attr( $settings['heading'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="kcs-intro">Intro</label></th>
					<td><textarea id="kcs-intro" class="large-text" rows="4" name="<?php echo esc_attr( $opt ); ?>[intro]"><?php echo esc_textarea( $settings['intro'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="kcs-footer">Footer note</label></th>
					<td><textarea id="kcs-footer" class="large-text" rows="2" name="<?php echo esc_attr( $opt ); ?>[footer_note]"><?php echo esc_textarea( $settings['footer_note'] ); ?></textarea></td>
				</tr>
			</table>

			<h2>Questions</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="kcs-question-count">Number of questions</label></th>
					<td>
						<input type="number" id="kcs-question-count" name="<?php echo esc_attr( $opt ); ?>[question_count]" min="1" max="<?php echo (int) KCS_MAX_QUESTIONS; ?>" value="<?php echo (int) $settings['question_count']; ?>" style="width:5em">
						<p class="description">Answers are saved against a question's number, so keep the questions in the same order once candidates have been entered. Reducing this number hides questions but keeps any answers already entered.</p>
					</td>
				</tr>
			</table>

			<div id="kcs-questions">
			<?php for ( $slot = 1; $slot <= KCS_MAX_QUESTIONS; $slot++ ) :
				$q = isset( $settings['questions'][ $slot ] ) && is_array( $settings['questions'][ $slot ] ) ? $settings['questions'][ $slot ] : array();
				$q = array_merge( array( 'prompt' => '', 'choices' => '', 'followup' => '' ), $q );
				$base = $opt . '[questions][' . $slot . ']';
				?>
				<fieldset class="kcs-question" data-slot="<?php echo (int) $slot; ?>" <?php echo $slot > $settings['question_count'] ? 'hidden' : ''; ?>>
					<legend><strong>Question <?php echo (int) $slot; ?></strong></legend>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="kcs-q<?php echo (int) $slot; ?>-prompt">Question</label></th>
							<td><textarea id="kcs-q<?php echo (int) $slot; ?>-prompt" class="large-text" rows="2" name="<?php echo esc_attr( $base ); ?>[prompt]"><?php echo esc_textarea( $q['prompt'] ); ?></textarea>
							<p class="description">Leave blank to skip this question on the public page.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="kcs-q<?php echo (int) $slot; ?>-choices">Answer options</label></th>
							<td><textarea id="kcs-q<?php echo (int) $slot; ?>-choices" class="large-text code" rows="4" name="<?php echo esc_attr( $base ); ?>[choices]" placeholder="Strongly support&#10;Somewhat support&#10;Neutral / undecided&#10;Do not support"><?php echo esc_textarea( $q['choices'] ); ?></textarea>
							<p class="description">One option per line. These become the dropdown choices on each candidate's edit screen.</p></td>
						</tr>
						<tr>
							<th scope="row"><label for="kcs-q<?php echo (int) $slot; ?>-followup">Follow-up label</label></th>
							<td><input type="text" id="kcs-q<?php echo (int) $slot; ?>-followup" class="large-text" name="<?php echo esc_attr( $base ); ?>[followup]" value="<?php echo esc_attr( $q['followup'] ); ?>" placeholder="Optional — explain your answer.">
							<p class="description">Shown above the candidate's written response.</p></td>
						</tr>
					</table>
				</fieldset>
			<?php endfor; ?>
			</div>

			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	(function () {
		var count = document.getElementById('kcs-question-count');
		var rows = document.querySelectorAll('#kcs-questions .kcs-question');
		if (!count) return;
		function sync() {
			var n = parseInt(count.value, 10) || 1;
			rows.forEach(function (row) {
				row.hidden = parseInt(row.getAttribute('data-slot'), 10) > n;
			});
		}
		count.addEventListener('input', sync);
		count.addEventListener('change', sync);
	})();
	</script>
	<?php
}
