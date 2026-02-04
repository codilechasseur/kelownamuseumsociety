<?php
$link_text = ( ! empty( $doc_url_text ) ) ? $doc_url_text : 'Learn more';
?>
<div class="settings-field">
	<?php if ( ! empty( $doc_text ) ) { ?>
        <p><?php echo esc_html( $doc_text ); ?></p>
	<?php } ?>
	<?php if ( ! empty( $doc_url ) ) { ?>
        <a href="<?php echo esc_url( $doc_url ); ?>" target="_blank"><?php echo esc_html( $link_text ); ?> <span>&#129122;</span></a>
	<?php } ?>
</div>