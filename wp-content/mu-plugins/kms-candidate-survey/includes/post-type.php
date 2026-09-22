<?php
/**
 * `kms_candidate` post type + the "Survey response" meta box.
 *
 * Candidates are not publicly queryable: there is no per-candidate URL and no archive.
 * The only public output is the [candidate_survey] shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Response status options (value => label).
 */
function kcs_status_options() {
	return array(
		'no_reply'  => 'No reply yet',
		'responded' => 'Responded',
		'declined'  => 'Declined to respond',
	);
}

add_action( 'init', function () {
	register_post_type(
		KCS_POST_TYPE,
		array(
			'labels'              => array(
				'name'                  => 'Candidates',
				'singular_name'         => 'Candidate',
				'add_new'               => 'Add Candidate',
				'add_new_item'          => 'Add Candidate',
				'edit_item'             => 'Edit Candidate',
				'new_item'              => 'New Candidate',
				'view_item'             => 'View Candidate',
				'search_items'          => 'Search Candidates',
				'not_found'             => 'No candidates found.',
				'not_found_in_trash'    => 'No candidates found in Trash.',
				'all_items'             => 'All Candidates',
				'menu_name'             => 'Candidates',
				'name_admin_bar'        => 'Candidate',
				'item_published'        => 'Candidate published.',
				'item_updated'          => 'Candidate updated.',
			),
			'description'         => 'Candidates in the Culture Vote Kelowna survey.',
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		)
	);
} );

/**
 * Title placeholder + hint in the editor.
 */
add_filter( 'enter_title_here', function ( $text, $post ) {
	return KCS_POST_TYPE === $post->post_type ? 'Candidate name' : $text;
}, 10, 2 );

/* -------------------------------------------------------------------------
 * Meta box
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes_' . KCS_POST_TYPE, function () {
	add_meta_box( 'kcs-response', 'Survey response', 'kcs_render_meta_box', KCS_POST_TYPE, 'normal', 'high' );
} );

function kcs_render_meta_box( $post ) {
	$questions = kcs_get_questions();
	$party     = get_post_meta( $post->ID, '_kcs_party', true );
	$status    = get_post_meta( $post->ID, '_kcs_status', true );
	if ( ! array_key_exists( $status, kcs_status_options() ) ) {
		$status = 'no_reply';
	}

	wp_nonce_field( 'kcs_save_' . $post->ID, 'kcs_nonce' );
	?>
	<table class="form-table kcs-meta" role="presentation">
		<tr>
			<th scope="row"><label for="kcs-party">Party / affiliation</label></th>
			<td><input type="text" id="kcs-party" name="kcs_party" class="regular-text" value="<?php echo esc_attr( $party ); ?>">
			<p class="description">Optional. Shown beside the candidate's name.</p></td>
		</tr>
		<tr>
			<th scope="row"><label for="kcs-status">Response status</label></th>
			<td>
				<select id="kcs-status" name="kcs_status">
					<?php foreach ( kcs_status_options() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description">Set to "Responded" once answers are entered. This drives the "Responded" count and the status dot on the public page.</p>
			</td>
		</tr>
	</table>

	<?php if ( empty( $questions ) ) : ?>
		<div class="notice notice-warning inline"><p>
			No survey questions have been set up yet. Add them under
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . KCS_POST_TYPE . '&page=kcs-settings' ) ); ?>">Survey Settings</a>
			to enter this candidate's answers.
		</p></div>
	<?php endif; ?>

	<?php foreach ( $questions as $slot => $q ) :
		$choice  = (string) get_post_meta( $post->ID, '_kcs_q' . $slot . '_choice', true );
		$written = (string) get_post_meta( $post->ID, '_kcs_q' . $slot . '_written', true );
		$orphan  = '' !== $choice && ! in_array( $choice, $q['choices'], true );
		?>
		<fieldset class="kcs-meta-question">
			<legend><strong>Q<?php echo (int) $slot; ?>.</strong> <?php echo esc_html( $q['prompt'] ); ?></legend>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="kcs-q<?php echo (int) $slot; ?>-choice">Answer</label></th>
					<td>
						<select id="kcs-q<?php echo (int) $slot; ?>-choice" name="kcs_q[<?php echo (int) $slot; ?>][choice]">
							<option value="">— No answer —</option>
							<?php foreach ( $q['choices'] as $option ) : ?>
								<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $choice, $option ); ?>><?php echo esc_html( $option ); ?></option>
							<?php endforeach; ?>
							<?php if ( $orphan ) : ?>
								<option value="<?php echo esc_attr( $choice ); ?>" selected>(no longer an option) <?php echo esc_html( $choice ); ?></option>
							<?php endif; ?>
						</select>
						<?php if ( empty( $q['choices'] ) ) : ?>
							<p class="description">This question has no answer options yet — add them in Survey Settings.</p>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="kcs-q<?php echo (int) $slot; ?>-written">Written response</label></th>
					<td><textarea id="kcs-q<?php echo (int) $slot; ?>-written" name="kcs_q[<?php echo (int) $slot; ?>][written]" class="large-text" rows="5"><?php echo esc_textarea( $written ); ?></textarea>
					<?php if ( '' !== $q['followup'] ) : ?>
						<p class="description"><?php echo esc_html( $q['followup'] ); ?></p>
					<?php endif; ?></td>
				</tr>
			</table>
		</fieldset>
	<?php endforeach; ?>
	<?php
}

add_action( 'save_post_' . KCS_POST_TYPE, function ( $post_id, $post ) {
	if ( ! isset( $_POST['kcs_nonce'] ) || ! wp_verify_nonce( $_POST['kcs_nonce'], 'kcs_save_' . $post_id ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$party = isset( $_POST['kcs_party'] ) ? sanitize_text_field( wp_unslash( $_POST['kcs_party'] ) ) : '';
	kcs_update_meta( $post_id, '_kcs_party', $party );

	$status = isset( $_POST['kcs_status'] ) ? sanitize_key( wp_unslash( $_POST['kcs_status'] ) ) : 'no_reply';
	if ( ! array_key_exists( $status, kcs_status_options() ) ) {
		$status = 'no_reply';
	}
	update_post_meta( $post_id, '_kcs_status', $status );

	$answers = isset( $_POST['kcs_q'] ) && is_array( $_POST['kcs_q'] ) ? wp_unslash( $_POST['kcs_q'] ) : array();
	foreach ( kcs_get_questions() as $slot => $q ) {
		$a      = isset( $answers[ $slot ] ) && is_array( $answers[ $slot ] ) ? $answers[ $slot ] : array();
		$choice = isset( $a['choice'] ) ? sanitize_text_field( $a['choice'] ) : '';

		// Only accept a current option, or the value already stored (so an answer whose
		// option text was later edited in Survey Settings is not silently wiped).
		$existing = (string) get_post_meta( $post_id, '_kcs_q' . $slot . '_choice', true );
		if ( '' !== $choice && ! in_array( $choice, $q['choices'], true ) && $choice !== $existing ) {
			$choice = '';
		}

		kcs_update_meta( $post_id, '_kcs_q' . $slot . '_choice', $choice );
		kcs_update_meta( $post_id, '_kcs_q' . $slot . '_written', isset( $a['written'] ) ? sanitize_textarea_field( $a['written'] ) : '' );
	}
}, 10, 2 );

/**
 * Store a value, or delete the row when it is empty (keeps postmeta tidy).
 */
function kcs_update_meta( $post_id, $key, $value ) {
	if ( '' === $value || null === $value ) {
		delete_post_meta( $post_id, $key );
	} else {
		update_post_meta( $post_id, $key, $value );
	}
}

/* -------------------------------------------------------------------------
 * Admin list table
 * ---------------------------------------------------------------------- */

add_filter( 'manage_' . KCS_POST_TYPE . '_posts_columns', function ( $columns ) {
	$out = array();
	foreach ( $columns as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'title' === $key ) {
			$out['kcs_party']  = 'Party';
			$out['kcs_status'] = 'Status';
			$out['kcs_answers'] = 'Answers';
		}
	}
	return $out;
} );

add_action( 'manage_' . KCS_POST_TYPE . '_posts_custom_column', function ( $column, $post_id ) {
	switch ( $column ) {
		case 'kcs_party':
			echo esc_html( get_post_meta( $post_id, '_kcs_party', true ) );
			break;

		case 'kcs_status':
			$status  = get_post_meta( $post_id, '_kcs_status', true );
			$options = kcs_status_options();
			$label   = isset( $options[ $status ] ) ? $options[ $status ] : $options['no_reply'];
			$class   = 'responded' === $status ? 'kcs-badge kcs-badge--yes' : ( 'declined' === $status ? 'kcs-badge kcs-badge--declined' : 'kcs-badge' );
			echo '<span class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
			break;

		case 'kcs_answers':
			$questions = kcs_get_questions();
			$answered  = 0;
			foreach ( array_keys( $questions ) as $slot ) {
				if ( '' !== (string) get_post_meta( $post_id, '_kcs_q' . $slot . '_choice', true ) ) {
					$answered++;
				}
			}
			echo esc_html( $answered . ' / ' . count( $questions ) );
			break;
	}
}, 10, 2 );

add_filter( 'manage_edit-' . KCS_POST_TYPE . '_sortable_columns', function ( $columns ) {
	$columns['kcs_status'] = 'kcs_status';
	$columns['kcs_party']  = 'kcs_party';
	return $columns;
} );

add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || KCS_POST_TYPE !== $query->get( 'post_type' ) ) {
		return;
	}
	$orderby = $query->get( 'orderby' );
	if ( 'kcs_status' === $orderby ) {
		$query->set( 'meta_key', '_kcs_status' );
		$query->set( 'orderby', 'meta_value' );
	} elseif ( 'kcs_party' === $orderby ) {
		$query->set( 'meta_key', '_kcs_party' );
		$query->set( 'orderby', 'meta_value' );
	} elseif ( '' === $orderby ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}
} );

/**
 * Small admin stylesheet for the meta box and list badges.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || KCS_POST_TYPE !== $screen->post_type ) {
		return;
	}
	wp_enqueue_style( 'kcs-admin', KCS_URL . '/assets/admin.css', array(), KCS_VERSION );
} );
