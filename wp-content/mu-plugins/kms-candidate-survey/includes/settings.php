<?php
/**
 * Survey settings: page copy + the questions.
 *
 * Stored in a single option (KCS_OPTION):
 *   eyebrow, heading, intro, footer_note   strings
 *   style                                  plain | warm | warm-dark  (see kcs_style_options)
 *   question_count                         int, 1..KCS_MAX_QUESTIONS
 *   questions                              [ 1 => [ prompt (limited HTML), choices (newline list), followup ], ... ]
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
		'style'          => 'plain',
		'question_count' => 4,
		'questions'      => array(),
	);
}

/**
 * Public page styles (value => label).
 */
function kcs_style_options() {
	return array(
		'plain'     => 'Plain (default) — white page, neutral sans-serif, ruled lists',
		'warm'      => 'Warm — cream page, serif headings, rounded cards',
		'warm-dark' => 'Warm dark — the warm style on a dark background',
	);
}

/**
 * The active page style key.
 */
function kcs_get_style() {
	$settings = kcs_get_settings();
	return array_key_exists( $settings['style'], kcs_style_options() ) ? $settings['style'] : 'plain';
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

		$prompt = isset( $raw['prompt'] ) ? kcs_sanitize_prompt( $raw['prompt'] ) : '';
		if ( kcs_prompt_is_empty( $prompt ) ) {
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
 * HTML allowed in a question prompt (matches the limited editor toolbar).
 */
function kcs_prompt_allowed_html() {
	return array(
		'p'      => array(),
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'u'      => array(),
		'a'      => array(
			'href'   => true,
			'title'  => true,
			'target' => true,
			'rel'    => true,
		),
	);
}

/**
 * Sanitize a question prompt: keep only the allowed inline HTML, drop empty paragraphs.
 */
function kcs_sanitize_prompt( $html ) {
	$html = wp_kses( (string) $html, kcs_prompt_allowed_html() );
	$html = preg_replace( '#<p>(\s|&nbsp;|<br\s*/?>)*</p>#i', '', $html );
	return trim( $html );
}

/**
 * Prompt HTML for display (safe; already sanitised on save, filtered again on output).
 */
function kcs_prompt_html( $html ) {
	return wp_kses( (string) $html, kcs_prompt_allowed_html() );
}

/**
 * Does the prompt contain any visible text?
 */
function kcs_prompt_is_empty( $html ) {
	return '' === trim( wp_strip_all_tags( str_replace( '&nbsp;', ' ', (string) $html ) ) );
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

	$clean['style'] = isset( $input['style'] ) ? sanitize_key( wp_unslash( $input['style'] ) ) : 'plain';
	if ( ! array_key_exists( $clean['style'], kcs_style_options() ) ) {
		$clean['style'] = 'plain';
	}

	$clean['question_count'] = isset( $input['question_count'] ) ? (int) $input['question_count'] : 4;
	$clean['question_count'] = max( 1, min( KCS_MAX_QUESTIONS, $clean['question_count'] ) );

	$clean['questions'] = array();
	$raw_questions      = isset( $input['questions'] ) && is_array( $input['questions'] ) ? $input['questions'] : array();
	for ( $slot = 1; $slot <= KCS_MAX_QUESTIONS; $slot++ ) {
		$q = isset( $raw_questions[ $slot ] ) && is_array( $raw_questions[ $slot ] ) ? $raw_questions[ $slot ] : array();

		$clean['questions'][ $slot ] = array(
			'prompt'   => isset( $q['prompt'] ) ? kcs_sanitize_prompt( wp_unslash( $q['prompt'] ) ) : '',
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

			<h2>Appearance</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="kcs-style">Page style</label></th>
					<td>
						<select id="kcs-style" name="<?php echo esc_attr( $opt ); ?>[style]">
							<?php foreach ( kcs_style_options() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['style'], $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description">Applies wherever the shortcode is shown. The warm styles load their own fonts (Fraunces and Source Sans 3).</p>
					</td>
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
							<th scope="row"><label for="kcs_q<?php echo (int) $slot; ?>_prompt">Question</label></th>
							<td>
							<?php
							wp_editor(
								$q['prompt'],
								'kcs_q' . $slot . '_prompt',
								array(
									'textarea_name' => $base . '[prompt]',
									'textarea_rows' => 3,
									'media_buttons' => false,
									'wpautop'       => false,
									'teeny'         => true,
									'quicktags'     => array( 'buttons' => 'strong,em,link' ),
									'tinymce'       => array(
										'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
										'toolbar2'       => '',
										'wpautop'        => false,
										'paste_as_text'  => true,
										'valid_elements' => 'p,br,strong/b,em/i,u,a[href|title|target|rel]',
										'forced_root_block' => 'p',
									),
								)
							);
							?>
							<p class="description">Bold, italics, links and line breaks are kept; any other HTML is removed. Leave blank to skip this question on the public page.</p>
							</td>
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
		function reinitEditor(row) {
			// TinyMCE sizes itself on init; an editor initialised inside a hidden row
			// comes up collapsed, so re-create it once the row is visible.
			if (!window.tinymce || !window.tinyMCEPreInit) return;
			row.querySelectorAll('textarea.wp-editor-area').forEach(function (ta) {
				var id = ta.id, settings = tinyMCEPreInit.mceInit[id], ed = tinymce.get(id);
				if (!settings || !ed || !ed.isHidden || ed.isHidden()) return;
				if (ed.getContainer() && ed.getContainer().offsetHeight > 0) return;
				ed.remove();
				tinymce.init(settings);
			});
		}
		function sync() {
			var n = parseInt(count.value, 10) || 1;
			rows.forEach(function (row) {
				var wasHidden = row.hidden;
				row.hidden = parseInt(row.getAttribute('data-slot'), 10) > n;
				if (wasHidden && !row.hidden) reinitEditor(row);
			});
		}
		count.addEventListener('input', sync);
		count.addEventListener('change', sync);
	})();
	</script>
	<?php
}
