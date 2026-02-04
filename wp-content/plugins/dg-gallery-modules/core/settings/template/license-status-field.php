<div class="settings-field dg-activation">
    <input id="<?php echo esc_attr( $id ); ?>"
           class="license-status apikey-status <?php echo empty( $value ) ? "deactive" : esc_attr( $value ); ?>"
           type='text'
           name='<?php echo esc_attr( $name ); ?>'
           value='<?php echo empty( $value ) ? "deactive" : esc_attr( $value ); ?>'/>
</div>