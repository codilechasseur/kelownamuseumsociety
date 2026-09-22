<?php
/**
 * Public output: the [candidate_survey] shortcode.
 *
 * Attributes
 *   header="yes|no"     Render the eyebrow / heading / intro block (default yes). Set to
 *                       "no" when the Divi page already has its own title section.
 *   heading_tag="h1"    Tag for the heading (h1–h3). Use h2 if the page title is already an h1.
 *   stats="yes|no"      Show the Candidates / Questions / Responded tiles (default yes).
 *   questions="yes|no"  Show "The Questions" list above the toolbar (default yes).
 *   footer="yes|no"     Show the footer attribution line (default yes).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	add_shortcode( 'candidate_survey', 'kcs_shortcode' );
} );

/**
 * Register assets; enqueue them early when the current post contains the shortcode
 * (so the CSS lands in <head>), and again from the shortcode as a fallback.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'kcs-survey', KCS_URL . '/assets/survey.css', array(), KCS_VERSION );
	wp_register_script( 'kcs-survey', KCS_URL . '/assets/survey.js', array(), KCS_VERSION, true );

	$post = get_post();
	if ( $post && is_singular() && has_shortcode( (string) $post->post_content, 'candidate_survey' ) ) {
		kcs_enqueue_assets();
	}
} );

function kcs_enqueue_assets() {
	wp_enqueue_style( 'kcs-survey' );
	wp_enqueue_script( 'kcs-survey' );
}

/**
 * All published candidates with their answers, sorted by surname then full name.
 *
 * @return array[]
 */
function kcs_get_candidates( array $questions ) {
	$posts = get_posts(
		array(
			'post_type'        => KCS_POST_TYPE,
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);

	$candidates = array();
	foreach ( $posts as $post ) {
		$name   = trim( get_the_title( $post ) );
		$status = get_post_meta( $post->ID, '_kcs_status', true );
		if ( ! array_key_exists( $status, kcs_status_options() ) ) {
			$status = 'no_reply';
		}

		$answers = array();
		foreach ( $questions as $slot => $q ) {
			$answers[ $slot ] = array(
				'choice'  => (string) get_post_meta( $post->ID, '_kcs_q' . $slot . '_choice', true ),
				'written' => (string) get_post_meta( $post->ID, '_kcs_q' . $slot . '_written', true ),
			);
		}

		$candidates[] = array(
			'id'       => $post->ID,
			'name'     => $name,
			'sort_key' => kcs_sort_key( $name ),
			'party'    => (string) get_post_meta( $post->ID, '_kcs_party', true ),
			'status'   => $status,
			'answers'  => $answers,
		);
	}

	usort( $candidates, function ( $a, $b ) {
		return strnatcasecmp( $a['sort_key'], $b['sort_key'] );
	} );

	/**
	 * Filter the ordered list of candidates before rendering.
	 *
	 * @param array[] $candidates
	 */
	return apply_filters( 'kcs_candidates', $candidates );
}

/**
 * "Jane Q. Public" -> "Public Jane Q." so the list reads alphabetically by surname.
 * Names without a space sort as-is.
 */
function kcs_sort_key( $name ) {
	$parts = preg_split( '/\s+/', trim( $name ) );
	if ( count( $parts ) < 2 ) {
		return $name;
	}
	$last = array_pop( $parts );
	return $last . ' ' . implode( ' ', $parts );
}

/**
 * Shortcode callback.
 */
function kcs_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'header'      => 'yes',
			'heading_tag' => 'h1',
			'stats'       => 'yes',
			'questions'   => 'yes',
			'footer'      => 'yes',
		),
		$atts,
		'candidate_survey'
	);

	$yes = function ( $v ) {
		return ! in_array( strtolower( (string) $v ), array( 'no', 'false', '0', 'off' ), true );
	};
	$heading_tag = in_array( strtolower( $atts['heading_tag'] ), array( 'h1', 'h2', 'h3' ), true ) ? strtolower( $atts['heading_tag'] ) : 'h1';

	kcs_enqueue_assets();

	$settings   = kcs_get_settings();
	$questions  = kcs_get_questions();
	$candidates = kcs_get_candidates( $questions );
	$responded  = count( array_filter( $candidates, function ( $c ) {
		return 'responded' === $c['status'];
	} ) );
	$statuses   = kcs_status_options();
	$id         = wp_unique_id( 'kcs-' );

	ob_start();
	?>
	<div class="kcs" id="<?php echo esc_attr( $id ); ?>">

		<?php if ( $yes( $atts['header'] ) ) : ?>
		<header class="kcs-header">
			<?php if ( '' !== $settings['eyebrow'] ) : ?>
				<div class="kcs-eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></div>
			<?php endif; ?>
			<?php if ( '' !== $settings['heading'] ) : ?>
				<<?php echo $heading_tag; ?> class="kcs-title"><?php echo esc_html( $settings['heading'] ); ?></<?php echo $heading_tag; ?>>
			<?php endif; ?>
			<?php if ( '' !== $settings['intro'] ) : ?>
				<p class="kcs-subhead"><?php echo nl2br( esc_html( $settings['intro'] ) ); ?></p>
			<?php endif; ?>
		</header>
		<?php endif; ?>

		<?php if ( $yes( $atts['stats'] ) ) : ?>
		<div class="kcs-stats" role="list">
			<div class="kcs-stat" role="listitem"><div class="kcs-stat-num"><?php echo (int) count( $candidates ); ?></div><div class="kcs-stat-label">Candidates</div></div>
			<div class="kcs-stat" role="listitem"><div class="kcs-stat-num"><?php echo (int) count( $questions ); ?></div><div class="kcs-stat-label">Questions</div></div>
			<div class="kcs-stat" role="listitem"><div class="kcs-stat-num"><?php echo (int) $responded; ?></div><div class="kcs-stat-label">Responded</div></div>
		</div>
		<?php endif; ?>

		<?php if ( $yes( $atts['questions'] ) && ! empty( $questions ) ) : ?>
		<section class="kcs-questions" aria-labelledby="<?php echo esc_attr( $id ); ?>-qh">
			<h2 class="kcs-questions-title" id="<?php echo esc_attr( $id ); ?>-qh">The Questions</h2>
			<ol class="kcs-qlist">
				<?php foreach ( $questions as $slot => $q ) : ?>
					<li class="kcs-qitem"><span class="kcs-qn">Q<?php echo (int) $slot; ?></span><span><?php echo esc_html( $q['prompt'] ); ?></span></li>
				<?php endforeach; ?>
			</ol>
		</section>
		<?php endif; ?>

		<?php if ( empty( $candidates ) ) : ?>
			<p class="kcs-empty">Candidate responses will be published here soon.</p>
		<?php else : ?>

		<div class="kcs-toolbar">
			<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>-search">Search candidates by name</label>
			<input class="kcs-search" id="<?php echo esc_attr( $id ); ?>-search" type="search" placeholder="Search candidates by name…" autocomplete="off" data-kcs="search">
			<button type="button" class="kcs-btn" data-kcs="expand">Expand all</button>
			<button type="button" class="kcs-btn" data-kcs="collapse">Collapse all</button>
			<span class="kcs-count" data-kcs="count" aria-live="polite"><?php echo (int) count( $candidates ); ?> of <?php echo (int) count( $candidates ); ?> shown</span>
		</div>

		<ul class="kcs-candidates" data-kcs="list">
			<?php foreach ( $candidates as $c ) :
				$responded_flag = 'responded' === $c['status'];
				$search_text    = strtolower( trim( $c['name'] . ' ' . $c['party'] ) );
				?>
				<li class="kcs-item" data-kcs="item" data-search="<?php echo esc_attr( $search_text ); ?>">
					<details class="kcs-candidate" id="<?php echo esc_attr( $id . '-c' . $c['id'] ); ?>">
						<summary class="kcs-summary">
							<svg class="kcs-chev" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false"><path d="M7 4l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							<span class="kcs-dot<?php echo $responded_flag ? ' is-filled' : ''; ?>" title="<?php echo esc_attr( $statuses[ $c['status'] ] ); ?>"></span>
							<span class="kcs-name"><?php echo esc_html( $c['name'] ); ?></span>
							<?php if ( '' !== $c['party'] ) : ?>
								<span class="kcs-party"><?php echo esc_html( $c['party'] ); ?></span>
							<?php endif; ?>
							<?php if ( 'declined' === $c['status'] ) : ?>
								<span class="kcs-status-tag">Declined</span>
							<?php endif; ?>
							<span class="screen-reader-text"><?php echo esc_html( $statuses[ $c['status'] ] ); ?></span>
						</summary>

						<div class="kcs-body">
							<?php if ( 'declined' === $c['status'] ) : ?>
								<p class="kcs-note">This candidate declined to take part in the survey.</p>
							<?php elseif ( 'no_reply' === $c['status'] ) : ?>
								<p class="kcs-note">No response has been received from this candidate yet.</p>
							<?php endif; ?>

							<?php foreach ( $questions as $slot => $q ) :
								$a = $c['answers'][ $slot ];
								?>
								<div class="kcs-qa">
									<div class="kcs-q"><span class="kcs-qn">Q<?php echo (int) $slot; ?></span><?php echo esc_html( $q['prompt'] ); ?></div>
									<?php if ( '' !== $a['choice'] ) : ?>
										<span class="kcs-chip"><?php echo esc_html( $a['choice'] ); ?></span>
									<?php else : ?>
										<span class="kcs-chip is-empty">No response on file</span>
									<?php endif; ?>
									<div class="kcs-followup<?php echo '' === $a['written'] ? ' is-empty' : ''; ?>">
										<?php if ( '' !== $q['followup'] ) : ?>
											<span class="kcs-fq"><?php echo esc_html( $q['followup'] ); ?></span>
										<?php endif; ?>
										<?php if ( '' !== $a['written'] ) : ?>
											<?php echo wpautop( esc_html( $a['written'] ) ); ?>
										<?php else : ?>
											No written response provided.
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</details>
				</li>
			<?php endforeach; ?>
		</ul>

		<p class="kcs-empty" data-kcs="empty" hidden>No candidates match “<span data-kcs="empty-query"></span>”.</p>

		<?php endif; ?>

		<?php if ( $yes( $atts['footer'] ) && '' !== $settings['footer_note'] ) : ?>
			<footer class="kcs-footer"><?php echo nl2br( esc_html( $settings['footer_note'] ) ); ?></footer>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
