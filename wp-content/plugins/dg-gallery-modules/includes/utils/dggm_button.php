<?php

class DGGM_BUTTON {
    private $module;

    function __construct($module) {
        $this->module = $module;
    }

    /**
     * Set Up button settings
     * 
     * @param Array $options
     * @return Array $fields
     */
    public function get_content_fields($options = []) {
        $default = array(
            'key'           => '',
            'toggle_slug'   => '',
            'sub_toggle'    => null,
            'tab_slug'      => 'general'
        );
        $args = wp_parse_args( $options, $default );
        extract($args); // phpcs:ignore WordPress.PHP.DontExtract
        $fields = array ();

        $fields[$key . '_button_text'] = array(
            'label'           => esc_html__( 'Button Text', 'dggm-gallery-modules' ),
            'type'            => 'text',
            'option_category' => 'basic_option',
            'description'     => esc_html__( 'Input your desired button text, or leave blank for no button.', 'dggm-gallery-modules' ),
            'toggle_slug'     => $toggle_slug,
            'tab_slug'        => $tab_slug
        );
        $fields[$key . '_button_url'] = array(
            'label'           => esc_html__( 'Button URL', 'dggm-gallery-modules' ),
            'type'            => 'text',
            'option_category' => 'basic_option',
            'description'     => esc_html__( 'Input URL for your button.', 'dggm-gallery-modules' ),
            'toggle_slug'     => $toggle_slug,
            'tab_slug'        => $tab_slug
        );
        $fields[$key . '_button_url_new_window'] = array(
            'default'         => 'off',
            'default_on_front'=> true,
            'label'           => esc_html__( 'Url Opens', 'dggm-gallery-modules' ),
            'type'            => 'select',
            'option_category' => 'configuration',
            'options'         => array(
                'off' => esc_html__( 'In The Same Window', 'dggm-gallery-modules' ),
                'on'  => esc_html__( 'In The New Tab', 'dggm-gallery-modules' ),
            ),
            'toggle_slug'     => $toggle_slug,
            'tab_slug'        => $tab_slug
        );

        if ( $sub_toggle !== null ) {
            foreach( $fields as $field => $value) {
                $fields[$field]['sub_toggle'] = $sub_toggle;
            }
        }

        return $fields;
    }
    /**
     * Set Up button style settings
     * 
     * @param Array $options
     * @return Array $fields
     */
    public function get_style_fields($options = []) {
        $default = array(
            'key'           => '',
            'toggle_slug'   => '',
            'full_width'    => false,
            'sub_toggle'    => null,
            'tab_slug'      => 'general'
        );
        $args = wp_parse_args( $options, $default );
        extract($args); // phpcs:ignore WordPress.PHP.DontExtract
        $fields = array ();

        $fields[$key.'_button_align'] = array (
            'label'             => esc_html__( 'Alignment', 'dggm-gallery-modules' ),
            'type'              => 'text_align',
            'options'           => et_builder_get_text_orientation_options(array('justified')),
            'tab_slug'          => $tab_slug,
            'toggle_slug'       => $toggle_slug
        );

        if($full_width === true) {
            $fields[$key.'_button_fullwidth'] = array (
                'label'             => esc_html__('Full Width', 'dggm-gallery-modules'),
                'type'              => 'yes_no_button',
                'options'           => array(
					'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'           => 'off',
                'tab_slug'          => $tab_slug,
                'toggle_slug'       => $toggle_slug
            );
        }

        if ( $sub_toggle !== null ) {
            foreach( $fields as $field => $value) {
                $fields[$field]['sub_toggle'] = $sub_toggle;
            }
        }

        return $fields;
    }
    /**
     * Set Up button style
     * 
     * @param Array $options
     * @return VOID
     */
    public function process_btn_styles($options = []) {
        $default = array(
            'render_slug'       => '',
            'slug'              => '',
            'selector'          => '',
            'hover'             => '',
            'align_container'   => '',
            'important'         => false
        );
        $options = wp_parse_args( $options, $default );
        extract($options); // phpcs:ignore WordPress.PHP.DontExtract

        // alignment
        if (isset($this->module->props[$slug . '_button_align']) && !empty($this->module->props[$slug . '_button_align'])) {
            $align = $this->module->props[$slug . '_button_align'] !== '' ? $this->module->props[$slug . '_button_align'] : 'left';
            $align_container = $align_container !== '' ? $align_container : $selector;

            ET_Builder_Element::set_style($render_slug, array(
                'selector' => $align_container,
                'declaration' => sprintf('text-align:%1$s;', $align)
            ));
        }
        // full width style
        if(isset($this->module->props[$slug . '_button_fullwidth']) && 
            $this->module->props[$slug . '_button_fullwidth'] === 'on') {
                ET_Builder_Element::set_style($render_slug, array(
                    'selector' => $selector,
                    'declaration' => 'width:100%;'
                ));
            }
    }
}