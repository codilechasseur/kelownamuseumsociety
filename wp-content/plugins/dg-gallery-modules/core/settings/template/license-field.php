<?php
$button            = array( 'active' => 'Deactivate', 'deactive' => 'Activate' );
$activate_btn_text = empty( $effected ) ? "Activate" : $button[ $effected ];
$enabled           = ! empty( trim( $value ) ) ? 'enabled' : 'disabled';
?>
<div class="settings-field dg-activation">
    <input class="dg-license-key" id="<?php echo esc_attr( $id ); ?>" class="apikey" type='password'
           name='<?php echo esc_attr( $name ); ?>'
           value='<?php echo esc_attr( trim( $value ) ); ?>'
           data-value='<?php echo esc_attr( trim( $value ) ); ?>'/>
    <button <?php echo esc_attr( $enabled ); ?> id="action-button" class="act-btn"
                                                data-license-key="<?php echo esc_attr( $id ); ?>"
                                                data-effected-key="<?php echo esc_attr( $effected_key ); ?>">
        <span class="text"><?php echo esc_attr( $activate_btn_text ); ?></span>
        <!-- <span class="loader">&#8635;</span> -->
        <span class="loader"><img src="<?php echo esc_attr( DGSETTINGSURL ); ?>/assets/img/spin.gif" width="20"
                                  height="20"/></span>
    </button>
    <p class="error"></p>
</div>

