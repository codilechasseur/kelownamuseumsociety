<?php
/**
 * Candidate Survey (unbranded) page template.
 *
 * Deliberately does NOT call get_header()/get_footer(): that is what keeps Divi's
 * header, footer, sidebar and Theme Builder layouts off the page. wp_head()/wp_footer()
 * still run so SEO tags, analytics and enqueued assets work normally.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="kcs-page" class="kcs-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php wp_footer(); ?>
</body>
</html>
