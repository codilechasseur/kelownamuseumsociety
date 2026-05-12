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

// Disable Divi Builder for tribe_events to reduce memory usage (~24MB savings)
add_filter( 'et_builder_post_type_blocklist', function( $blocklist ) {
	$blocklist[] = 'tribe_events';
	return $blocklist;
} );

// Add event snippet for Page view conversion on events archive page
// function add_event_snippet()
// {
// 	$show_snippet = false;
// 	// Check for events archive
// 	if (is_post_type_archive('tribe_events')) {
// 		$show_snippet = true;
// 	}
// 	// Check for specific museum pages
// 	if (isset($_SERVER['REQUEST_URI'])) {
// 		$uri = rtrim($_SERVER['REQUEST_URI'], '/');
// 		if ($uri === '/museum/laurel-packinghouse' || $uri === '/museum/okanagan-heritage-museum') {
// 			$show_snippet = true;
// 		}
// 	}
// 	if ($show_snippet) {
// 		echo "<!-- Event snippet for Page view conversion page -->
//         <script>
//           gtag('event', 'conversion', {'send_to': 'AW-16663347189/RTctCI-pr4oaEPX_2Yk-'});
//         </script>";
// 	}
// }
// add_action('wp_head', 'add_event_snippet');

/**
 * Replacement shortcodes for the retired dica_divi_carousel plugin.
 * Supports basic [dica_divi_carousel] and [dica_divi_carouselitem] usage.
 */
function kms_dica_carousel_assets() {
	$css = '
	.kms-dica-carousel {
		position: relative;
		max-width: 100%;
		overflow: hidden;
	}

	.kms-dica-carousel__viewport {
		position: relative;
		min-height: 1px;
	}

	.kms-dica-carousel__item {
		position: absolute;
		inset: 0;
		opacity: 0;
		transition: opacity 500ms ease;
		pointer-events: none;
	}

	.kms-dica-carousel__item.is-active {
		position: relative;
		opacity: 1;
		pointer-events: auto;
	}

	.kms-dica-carousel__image {
		display: block;
		width: 100%;
		height: auto;
	}

	.kms-dica-carousel__caption {
		margin-top: 12px;
	}

	.kms-dica-carousel__dots {
		display: flex;
		gap: 8px;
		justify-content: center;
		align-items: center;
		margin-top: 12px;
	}

	.kms-dica-carousel__dot {
		width: 10px;
		height: 10px;
		border-radius: 50%;
		border: 0;
		padding: 0;
		background: rgba(0, 0, 0, 0.25);
		cursor: pointer;
	}

	.kms-dica-carousel__dot.is-active {
		background: rgba(0, 0, 0, 0.65);
	}
	';

	wp_register_style( 'kms-dica-carousel', false );
	wp_enqueue_style( 'kms-dica-carousel' );
	wp_add_inline_style( 'kms-dica-carousel', $css );

	$js = '
	(function () {
		function initCarousel(root) {
			var items = root.querySelectorAll(".kms-dica-carousel__item");
			var dots = root.querySelectorAll(".kms-dica-carousel__dot");
			var duration = parseInt(root.getAttribute("data-duration"), 10) || 5000;

			if (!items.length) {
				return;
			}

			if (items.length === 1) {
				if (dots[0]) {
					dots[0].classList.add("is-active");
				}
				return;
			}

			var current = 0;

			function setActive(next) {
				items[current].classList.remove("is-active");
				if (dots[current]) {
					dots[current].classList.remove("is-active");
				}

				current = next;

				items[current].classList.add("is-active");
				if (dots[current]) {
					dots[current].classList.add("is-active");
				}
			}

			var timer = setInterval(function () {
				setActive((current + 1) % items.length);
			}, duration);

			dots.forEach(function (dot, index) {
				dot.addEventListener("click", function () {
					setActive(index);
					clearInterval(timer);
					timer = setInterval(function () {
						setActive((current + 1) % items.length);
					}, duration);
				});
			});
		}

		document.querySelectorAll(".kms-dica-carousel").forEach(initCarousel);
	})();
	';

	wp_register_script( 'kms-dica-carousel', false, array(), null, true );
	wp_enqueue_script( 'kms-dica-carousel' );
	wp_add_inline_script( 'kms-dica-carousel', $js );
}
add_action( 'wp_enqueue_scripts', 'kms_dica_carousel_assets' );

function kms_dica_carousel_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'autoplay_speed' => 5000,
		),
		$atts,
		'dica_divi_carousel'
	);

	$duration = absint( $atts['autoplay_speed'] );
	if ( $duration < 1000 ) {
		$duration = 5000;
	}

	if ( ! isset( $GLOBALS['kms_dica_carousel_items'] ) || ! is_array( $GLOBALS['kms_dica_carousel_items'] ) ) {
		$GLOBALS['kms_dica_carousel_items'] = array();
	}
	if ( ! isset( $GLOBALS['kms_dica_carousel_stack'] ) || ! is_array( $GLOBALS['kms_dica_carousel_stack'] ) ) {
		$GLOBALS['kms_dica_carousel_stack'] = array();
	}

	$carousel_id = wp_unique_id( 'kms-dica-carousel-' );
	$GLOBALS['kms_dica_carousel_stack'][] = $carousel_id;
	$GLOBALS['kms_dica_carousel_items'][ $carousel_id ] = array();

	do_shortcode( $content );

	$items = $GLOBALS['kms_dica_carousel_items'][ $carousel_id ];
	unset( $GLOBALS['kms_dica_carousel_items'][ $carousel_id ] );
	array_pop( $GLOBALS['kms_dica_carousel_stack'] );

	if ( empty( $items ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="kms-dica-carousel" data-duration="<?php echo esc_attr( $duration ); ?>">
		<div class="kms-dica-carousel__viewport">
			<?php foreach ( $items as $index => $item ) : ?>
				<figure class="kms-dica-carousel__item<?php echo 0 === $index ? ' is-active' : ''; ?>">
					<?php if ( ! empty( $item['image'] ) ) : ?>
						<?php if ( $item['lightbox'] ) : ?>
							<a href="<?php echo esc_url( $item['image'] ); ?>">
								<img class="kms-dica-carousel__image" src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" />
							</a>
						<?php else : ?>
							<img class="kms-dica-carousel__image" src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" />
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $item['caption'] ) ) : ?>
						<figcaption class="kms-dica-carousel__caption"><?php echo wp_kses_post( $item['caption'] ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>

		<?php if ( count( $items ) > 1 ) : ?>
			<div class="kms-dica-carousel__dots" aria-hidden="true">
				<?php foreach ( $items as $index => $item ) : ?>
					<button class="kms-dica-carousel__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button"></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}

function kms_dica_carouselitem_shortcode( $atts, $content = null ) {
	if ( empty( $GLOBALS['kms_dica_carousel_stack'] ) || ! is_array( $GLOBALS['kms_dica_carousel_stack'] ) ) {
		return '';
	}

	$atts = shortcode_atts(
		array(
			'image'          => '',
			'image_lightbox' => 'off',
		),
		$atts,
		'dica_divi_carouselitem'
	);

	$carousel_id = end( $GLOBALS['kms_dica_carousel_stack'] );
	if ( false === $carousel_id ) {
		return '';
	}
	$caption     = trim( do_shortcode( (string) $content ) );

	$GLOBALS['kms_dica_carousel_items'][ $carousel_id ][] = array(
		'image'    => $atts['image'],
		'lightbox' => 'on' === strtolower( (string) $atts['image_lightbox'] ),
		'caption'  => wpautop( $caption ),
	);

	return '';
}

function kms_register_dica_carousel_shortcodes() {
	add_shortcode( 'dica_divi_carousel', 'kms_dica_carousel_shortcode' );
	add_shortcode( 'dica_divi_carouselitem', 'kms_dica_carouselitem_shortcode' );
}
add_action( 'init', 'kms_register_dica_carousel_shortcodes', 20 );

function kms_fix_smart_quotes_for_shortcodes( $content ) {
	return str_replace(
		array( '“', '”', '‘', '’' ),
		array( '"', '"', "'", "'" ),
		$content
	);
}

function kms_render_dica_shortcodes_from_plaintext( $content ) {
	if ( ! is_string( $content ) || false === strpos( $content, '[dica_divi_carousel' ) ) {
		return $content;
	}

	$fixed_content = kms_fix_smart_quotes_for_shortcodes( $content );

	$regex = '/' . get_shortcode_regex( array( 'dica_divi_carousel' ) ) . '/s';

	return preg_replace_callback(
		$regex,
		function ( $matches ) {
			return do_shortcode( $matches[0] );
		},
		$fixed_content
	);
}
add_filter( 'the_content', 'kms_render_dica_shortcodes_from_plaintext', 99 );
add_filter( 'et_builder_render_layout', 'kms_render_dica_shortcodes_from_plaintext', 99 );
