<?php
if ( ! class_exists( 'ET_Builder_Element' ) ) {
	return;
}
trait DGGM_Background {
    /**
     * add background field
     */
    function dg_add_bg_field( $args = array() )
    {
        $default    = array(
            'label'				=> '',
            'key'               => '',
            'toggle_slug'       => '',
            'sub_toggle'		=> null,
            'tab_slug'			=> '',
            'mobile_options'    => true,
            'hover'				=> 'tabs',
            'color'             => true,
            'gradient'          => true,
            'image'             => true,
            'order_reverse'     => false,
            'show_if'           => null,
            'show_if_not'       => null,
        );
        $args   = wp_parse_args( $args, $default );
        $fields = array();
        $key = $args['key'];

        $_fields = array(
            'label'               => sprintf(esc_html__('%1$s', 'dggm-gallery-modules'), $args['label']),
            'tab_slug'            => $args['tab_slug'],
            'toggle_slug'         => $args['toggle_slug'],
            'attr_suffix'         => 'df',
            'type'                => 'composite',
            'hover'               => $args['hover'],
            'composite_type'      => 'default',
            'composite_structure' => array(),
            'show_if'             => $args['show_if'],
            'show_if_not'         => $args['show_if_not']
        );

        $background_fields = array();

        if ($args['color'] === true) {
            $background_fields['color'] = array (
                'icon'     => 'background-color',
                'controls' => array(
                    "{$key}_bgcolor" => array(
                        'label' => esc_html__( 'Background Color', 'dggm-gallery-modules' ),
                        'type'  => 'color-alpha',
                        'hover' => $args['hover'],
                    ),
                ),
            );
        }

        if ($args['gradient'] === true) {
            $background_fields['color_gradient'] = array (
                'icon'     => 'background-gradient',
                'controls' => array(
                    "{$key}_use_gradient" => array(
                        'label'           => esc_html__( 'Use gradient background', 'dggm-gallery-modules' ),
                        'type'            => 'yes_no_button', 
                        'options'           => array(
                            'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                            'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
                        ),
                        'default'   => 'off'
                    ),
                    "{$key}_color_gradient_1" => array(
                        'label' => esc_html__( 'Select color', 'dggm-gallery-modules' ),
                        'type'  => 'color-alpha',
                        'default'   => "#2b87da",
                        'show_if' => array(
                            "{$key}_use_gradient" => 'on'
                        ),
                        'hover' => $args['hover']
                    ),
                    "{$key}_color_gradient_2" => array(
                        'label' => esc_html__( 'Select color', 'dggm-gallery-modules' ),
                        'type'  => 'color-alpha',
                        'default'   => "#29c4a9",
                        'show_if' => array(
                            "{$key}_use_gradient" => 'on'
                        ),
                        'hover' => $args['hover']
                    ),
                    "{$key}_gradient_type" => array(
                        'label' => esc_html__( 'Gradient Type', 'dggm-gallery-modules' ),
                        'type'  => 'select',
                        'options'         => array(
                            'leniar'    => esc_html__( 'Linear', 'dggm-gallery-modules' ),
                            'radial'    => esc_html__( 'Radial', 'dggm-gallery-modules' )
                        ),
                        'default'   => 'leniar',
                        'show_if' => array(
                            "{$key}_use_gradient" => 'on'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_radial_direction" => array(
                        'label' => esc_html__( 'Radial Direction', 'dggm-gallery-modules' ),
                        'type'  => 'select',
                        'options'         => array(
                            'center'    => esc_html__( 'Center', 'dggm-gallery-modules' ),
                            'top_left'    => esc_html__( 'Top Left', 'dggm-gallery-modules' ),
                            'top'    => esc_html__( 'Top', 'dggm-gallery-modules' ),
                            'top_right'    => esc_html__( 'Top Right', 'dggm-gallery-modules' ),
                            'right'    => esc_html__( 'Right', 'dggm-gallery-modules' ),
                            'bottom_right'    => esc_html__( 'Bottom Right', 'dggm-gallery-modules' ),
                            'bottom'    => esc_html__( 'Bottom', 'dggm-gallery-modules' ),
                            'bottom_left'    => esc_html__( 'Bottom Left', 'dggm-gallery-modules' ),
                            'left'    => esc_html__( 'Left', 'dggm-gallery-modules' ),
                        ),
                        'default'   => 'center',
                        'show_if' => array(
                            "{$key}_use_gradient" => 'on',
                            "{$key}_gradient_type" => 'radial'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_gradient_direction" => array(
                        'label'             => esc_html__( 'Gradient Direction', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '180deg',
                        'default_on_front'  => '',
                        'default_unit'      => 'deg',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '360',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_use_gradient" => 'on'
                        ),
                        'show_if_not'       => array(
                            "{$key}_gradient_type" => 'radial'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_start_position" => array(
                        'label'           => esc_html__( 'Start Position', 'dggm-gallery-modules' ),
                        'type'            => 'range', 
                        'default'   => '0%',
                        'show_if' => array(
                            "{$key}_use_gradient" => 'on'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_end_position" => array(
                        'label'           => esc_html__( 'End Position', 'dggm-gallery-modules' ),
                        'type'            => 'range', 
                        'default'   => '100%',
                        'show_if' => array(
                            "{$key}_use_gradient" => 'on'
                        ),
                        'hover'  => $args['hover'],
                    )
                )
            );

            if ($args['image'] === true){
                $background_fields['color_gradient']['controls']["{$key}_above_image"] =  array(
                    'label'           => esc_html__( 'Place Gradiet Above Background Image', 'dggm-gallery-modules' ),
                    'type'            => 'yes_no_button', 
                    'options'           => array(
                        'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                        'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
                    ),
                    'show_if' => array(
                        "{$key}_use_gradient" => 'on'
                    )
                );
            }
            
        }

        if ($args['image'] === true) {
            $background_fields['image'] = array (
                'icon'     => 'background-image',
                'controls' => array(
                    "{$key}_background_image" => array(
                        'label' => esc_html__( 'Background Image', 'dggm-gallery-modules' ),
                        'type'  => 'upload',
                        'upload_button_text' => esc_attr__( 'Set Image', 'dggm-gallery-modules' ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_background_image_size" => array(
                        'label' => esc_html__( 'Background Image Size', 'dggm-gallery-modules' ),
                        'type'  => 'select',
                        'options'         => array(
                            'cover'    => esc_html__( 'Cover', 'dggm-gallery-modules' ),
                            'fit'    => esc_html__( 'Fit', 'dggm-gallery-modules' ),
                            'actual_size'    => esc_html__( 'Actual Size', 'dggm-gallery-modules' ),
                            'custom'    => esc_html__('Custom Size', 'dggm-gallery-modules')
                        ),
                        'default'   => 'cover',
                        'hover' => $args['hover'],
                    ),
                    "{$key}_size_width" => array(
                        'label'             => esc_html__( 'Background Width', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '50%',
                        'default_on_front'  => '',
                        'default_unit'      => '%',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '100',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_size" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_size_height" => array(
                        'label'             => esc_html__( 'Background Height', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '50%',
                        'default_on_front'  => '',
                        'default_unit'      => '%',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '100',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_size" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_size_width" => array(
                        'label'             => esc_html__( 'Background Width', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '50%',
                        'default_on_front'  => '',
                        'default_unit'      => '%',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '100',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_size" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_size_height" => array(
                        'label'             => esc_html__( 'Background Height', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '50%',
                        'default_on_front'  => '',
                        'default_unit'      => '%',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '100',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_size" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_background_image_position" => array(
                        'label' => esc_html__( 'Background Image Position', 'dggm-gallery-modules' ),
                        'type'  => 'select',
                        'options'         => array(
                            'top_left'    => esc_html__( 'Top Left', 'dggm-gallery-modules' ),
                            'top_center'    => esc_html__( 'Top Center', 'dggm-gallery-modules' ),
                            'top_right'    => esc_html__( 'Top Right', 'dggm-gallery-modules' ),
                            'center_left'    => esc_html__( 'Center Left', 'dggm-gallery-modules' ),
                            'center'    => esc_html__( 'Center', 'dggm-gallery-modules' ),
                            'center_right'    => esc_html__( 'Center Right', 'dggm-gallery-modules' ),
                            'bottom_left'    => esc_html__( 'Bottom Left', 'dggm-gallery-modules' ),
                            'bottom_center'    => esc_html__( 'Bottom Center', 'dggm-gallery-modules' ),
                            'bottom_right'    => esc_html__( 'Bottom Right', 'dggm-gallery-modules' ),
                            'custom'          => esc_html__('Custom Position', 'dggm-gallery-modules')
                        ),
                        'default'   => 'center',
                        'hover' => $args['hover'],
                    ),
                    "{$key}_position_horizontal" => array(
                        'label'             => esc_html__( 'Horizontal Position', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '0px',
                        'default_on_front'  => '',
                        'default_unit'      => 'px',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '1000',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_position" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_position_vertical" => array(
                        'label'             => esc_html__( 'Vertical Position', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '0px',
                        'default_on_front'  => '',
                        'default_unit'      => 'px',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '1000',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_position" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_position_horizontal" => array(
                        'label'             => esc_html__( 'Horizontal Position', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '0px',
                        'default_on_front'  => '',
                        'default_unit'      => 'px',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '1000',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_position" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_position_vertical" => array(
                        'label'             => esc_html__( 'Vertical Position', 'dggm-gallery-modules' ),
                        'type'              => 'range', 
                        'default'           => '0px',
                        'default_on_front'  => '',
                        'default_unit'      => 'px',
                        'range_settings'         => array(
                            'min'    => '0',
                            'max'    => '1000',
                            'step'    => '1'
                        ),
                        'show_if'           => array(
                            "{$key}_background_image_position" => 'custom'
                        ),
                        'hover'  => $args['hover'],
                    ),
                    "{$key}_background_image_repeat" => array(
                        'label' => esc_html__( 'Background Image Repeat', 'dggm-gallery-modules' ),
                        'type'  => 'select',
                        'options'         => array(
                            'no_repeat'    => esc_html__( 'No Repeat', 'dggm-gallery-modules' ),
                            'repeat'    => esc_html__( 'Repeat', 'dggm-gallery-modules' ),
                            'repeat_x'    => esc_html__( 'Repeat X (horizontal)', 'dggm-gallery-modules' ),
                            'repeat_y'    => esc_html__( 'Repeat Y (vertical)', 'dggm-gallery-modules' ),
                            'space'    => esc_html__( 'Space', 'dggm-gallery-modules' ),
                            'round'    => esc_html__( 'Round', 'dggm-gallery-modules' ),
                        ),
                        'default'   => 'no_repeat',
                        'hover' => $args['hover'],
                    )
                ),
            );
        }
        if ($args['order_reverse'] === true) {
            $background_fields = array_reverse($background_fields);
        }
        
        $_fields['composite_structure'] = $background_fields;

        $fields[$args['key']] = $_fields;

        return $fields;
    }
}