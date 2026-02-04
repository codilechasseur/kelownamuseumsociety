<div class="field<?php echo esc_attr( $dg_class ); ?>">
    <div class="field-label"><?php echo esc_html( $field['title'] ); ?></div>
	<?php call_user_func( $field['callback'], $field['args'] ); ?>
	<?php echo esc_html( $class ); ?>
</div>