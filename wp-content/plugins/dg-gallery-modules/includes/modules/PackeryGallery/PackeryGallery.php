<?php

class DGGM_PackeryGallery extends ET_Builder_Module {
    public $slug       = 'dggm_packerygallery';
    public $vb_support = 'on';
    use DGGM_UTLS;

    protected $module_credits = array(
		'module_uri' => '',
		'author'     => 'DiviGear',
		'author_uri' => '',
    );

    public function init() {
        $this->name = esc_html__( 'Packery Gallery', 'et_builder' );
        $this->main_css_element = "%%order_class%%";
        $this->icon_path        =  DGGM_ASSETS_DIR_PATH . 'img/module-icons/packery.svg';
    }

    public function get_settings_modal_toggles(){
        return array(
            'general'   => array(
                'toggles'      => array(
                    'gallery' => esc_html__('Gallery', 'dggm-gallery-modules'),
                    'settings' => esc_html__('Gallery Settings', 'dggm-gallery-modules'),
                    'hover'     => esc_html__('Hover Settings', 'dggm-gallery-modules')
                ),
            ),
            'advanced'   => array(
                'toggles'   => array(
                    'font_styles'    => array (
                        'title'             => esc_html__('Font Styles', 'dggm-gallery-modules'),
                        'tabbed_subtoggles' => true,
                        'sub_toggles' => array(
                            'caption'   => array(
                                'name' => 'Caption',
                            ),
                            'description'     => array(
                                'name' => 'Description',
                            )
                        ),
                    ),
                    'image'                 => esc_html__('Image Filter', 'dggm-gallery-modules'),
                    'more_btn'              => esc_html__('Load More Button', 'dggm-gallery-modules'),
                    'dg_borders'            => esc_html__('Item Border', 'dggm-gallery-modules')
                )
            ),
        );
    }

    public function get_advanced_fields_config() {
        $advanced_fields = array();

        $advanced_fields['fonts'] = array(
            'caption'     => array(
                'label'         => esc_html__( 'Caption', 'dggm-gallery-modules' ),
                'toggle_slug'   => 'font_styles',
                'sub_toggle'    => 'caption',
                'tab_slug'		=> 'advanced',
                'hide_text_shadow'  => true,
                'hide_text_align'  => true,
                'line_height' => array (
                    'default' => '1em',
                ),
                'font_size' => array(
                    'default' => '16px',
                ),
                'css'      => array(
                    'main' => "%%order_class%% .dg_pg_caption",
                    'hover' => "%%order_class%% .dg_pg_image:hover .dg_pg_caption",
                    'important'	=> 'all'
                ),
            ),
            'description'     => array(
                'label'         => esc_html__( 'Description', 'dggm-gallery-modules' ),
                'toggle_slug'   => 'font_styles',
                'sub_toggle'    => 'description',
                'tab_slug'		=> 'advanced',
                'hide_text_shadow'  => true,
                'hide_text_align'  => true,
                'line_height' => array (
                    'default' => '1em',
                ),
                'font_size' => array(
                    'default' => '14px',
                ),
                'css'      => array(
                    'main' => "%%order_class%% .dg_pg_description",
                    'hover' => "%%order_class%% .dg_pg_image:hover .dg_pg_description",
                    'important'	=> 'all'
                ),
            ),
            'more_btn'     => array(
                'label'         => esc_html__( 'Button', 'dggm-gallery-modules' ),
                'toggle_slug'   => 'more_btn',
                'tab_slug'		=> 'advanced',
                'hide_text_shadow'  => true,
                'line_height' => array (
                    'default' => '1em',
                ),
                'font_size' => array(
                    'default' => '14px',
                ),
                'css'      => array(
                    'main' => "%%order_class%% .pg-more-image-btn",
                    'hover' => "%%order_class%% .pg-more-image-btn:hover",
                    'important'	=> 'all'
                ),
            )
        );

        $advanced_fields['borders'] = array(
            'default'               => false,
            'pg_more_br'              => array (
                'css'               => array(
                    'main' => array(
                        'border_radii' => '%%order_class%% .pg-more-image-btn',
                        'border_radii_hover' => '%%order_class%% .pg-more-image-btn:hover',
                        'border_styles' => '%%order_class%% .pg-more-image-btn',
                        'border_styles_hover' => '%%order_class%% .pg-more-image-btn:hover',
                    )
                ),
                'tab_slug'          => 'advanced',
                'toggle_slug'       => 'more_btn',
                'label_prefix'      => 'Button'
            ),
            'item_border'              => array (
                'css'               => array(
                    'main' => array(
                        'border_radii' => '%%order_class%% .dg_pg_image',
                        'border_radii_hover' => '%%order_class%% .dg_pg_image:hover',
                        'border_styles' => '%%order_class%% .dg_pg_image',
                        'border_styles_hover' => '%%order_class%% .dg_pg_image:hover',
                    )
                ),
                'tab_slug'          => 'advanced',
                'toggle_slug'       => 'dg_borders',
                'label_prefix'      => 'Item'
            )
        );
        $advanced_fields['box_shadow'] = array(
            'default'   => false,
            'more_button'     => array(
                'css' => array(
                    'main' => "%%order_class%% .pg-more-image-btn",
                    'hover' => "%%order_class%% .pg-more-image-btn:hover",
                ),
                'tab_slug'          => 'advanced',
                'toggle_slug'       => 'more_btn'
            )
        );
        $advanced_fields['text'] = false;
        $advanced_fields['filters'] = false;
        $advanced_fields['transform'] = false;
        $advanced_fields['margin_padding'] = array(
            'css' => array(
                'main' => array(
                    '%%order_class%% .dg_pg_container',
                )
            ),
        );
    
        return $advanced_fields;
    }

    public function get_fields() {
        $gallery = array(
            'gallery' => array(
				'label'            => esc_html__( 'Gallery Images', 'dggm-gallery-modules' ),
				'description'      => esc_html__( 'Choose images that you would like to appear in the image gallery.', 'dggm-gallery-modules' ),
				'type'             => 'upload-gallery',
				'toggle_slug'      => 'gallery',
            )
        );
        $settings = array(
            'pg_layout'    => array(
                'label'    => esc_html__('Layout', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    'one'       => esc_html__('Layout One', 'dggm-gallery-modules'),
                    'two'       => esc_html__('Layout Two', 'dggm-gallery-modules'),
                    'three'     => esc_html__('Layout Three', 'dggm-gallery-modules'),
                    'four'      => esc_html__('Layout Four', 'dggm-gallery-modules'),
                    'five'      => esc_html__('Layout Five', 'dggm-gallery-modules'),
                    'six'       => esc_html__('Layout Six', 'dggm-gallery-modules'),
                    'seven'     => esc_html__('Layout Seven', 'dggm-gallery-modules'),
                    'eight'     => esc_html__('Layout Eight', 'dggm-gallery-modules'),
                    'nine'      => esc_html__('Layout Nine', 'dggm-gallery-modules'),
                    'ten'      => esc_html__('Layout Ten', 'dggm-gallery-modules')
                ),
                'default'       => 'one',
                'toggle_slug'   => 'settings'
            ),
            'keep_dsk_tab'    => array(
                'label'         => esc_html__('Desktop view on tablet', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'settings'
            ),
            'tablet_view'    => array(
                'label'         => esc_html__('Tablet View', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    '1' => esc_html__( 'Single Item', 'dggm-gallery-modules' ),
					'2'  => esc_html__( 'Two Item', 'dggm-gallery-modules' ),
                ),
                'default'       => '1',
                'toggle_slug'   => 'settings',
                'show_if_not'   => array (
                    'keep_dsk_tab' => 'on'
                )
            ),
            'mobile_view'    => array(
                'label'         => esc_html__('Mobile View', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    '1' => esc_html__( 'Single Item', 'dggm-gallery-modules' ),
					'2'  => esc_html__( 'Two Item', 'dggm-gallery-modules' ),
                ),
                'default'       => '1',
                'toggle_slug'   => 'settings'
            ),
            'space_between'    => array (
                'label'             => esc_html__( 'Space Between', 'dggm-gallery-modules' ),
                'type'              => 'range',
                'toggle_slug'       => 'settings',
                'default'           => '20px',
                'default_unit'      => 'px',
                'allowed_units'     => array ('px'),
                'description'       => esc_html__('Space between each image.', 'dggm-gallery-modules'),
                'range_settings'    => array(
                    'min'  => '1',
                    'max'  => '50',
                    'step' => '1',
                ),
                'responsive'        => true,
                'mobile_options'    => true
            ),
            'load_more'    => array(
                'label'         => esc_html__('Load More button', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'settings'
            ),
            'init_count'    => array(
                'label'         => esc_html__('Initial Image Load', 'dggm-gallery-modules'),
                'type'          => 'text',
                'default'       => '6',
                'toggle_slug'   => 'settings',
                'show_if'       => array(
                    'load_more' => 'on'
                ),
                'show_if_not'   => array(
                    'filter_nav' => 'on'
                )
            ),
            'image_count'    => array(
                'label'         => esc_html__('Load More Image Count', 'dggm-gallery-modules'),
                'type'          => 'text',
                'default'       => '4',
                'toggle_slug'   => 'settings',
                'show_if'       => array(
                    'load_more' => 'on'
                ),
                'show_if_not'   => array(
                    'filter_nav' => 'on'
                )
            ),
            'load_more_text'    => array(
                'label'         => esc_html__('Load More Button Text', 'dggm-gallery-modules'),
                'type'          => 'text',
                'default'       => 'Load More',
                'toggle_slug'   => 'settings',
                'show_if'       => array(
                    'load_more' => 'on'
                ),
                'show_if_not'   => array(
                    'filter_nav' => 'on'
                )
            ),
            'use_url'    => array(
                'label'         => esc_html__('Use Custom Link', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'settings'
            ),
            'url_target'    => array(
                'label'         => esc_html__('Link Target', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    'same_window'               => esc_html__('In The Same Window', 'dggm-gallery-modules'),
                    'new_window'              => esc_html__('In The New Tab', 'dggm-gallery-modules')
                ),
                'default'       => 'same_window',
                'toggle_slug'   => 'settings',
                'show_if'       => array(
                    'use_url'   => 'on'
                )
            ),
            'use_lightbox'    => array(
                'label'         => esc_html__('Lightbox', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'settings',
                'show_if_not'   => array(
                    'use_url' => 'on'
                )
            ),
            'use_lightbox_download'    => array(
                'label'         => esc_html__('Lightbox Download Button', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'settings',
                'show_if'       => array(
                    'use_lightbox' => 'on'
                )
            ),
            'use_lightbox_content'    => array(
                'label'         => esc_html__('Lightbox Content', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
                    'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'settings',
                'show_if'       => array(
                    'use_lightbox' => 'on'
                ),
                'show_if_not'   => array(
                    'use_url' => 'on'
                )
            )
        );
        $hover = array(
            'overlay'    => array(
                'label'         => esc_html__('Overlay', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'hover'
            ),
            'overlay_primary'  => array(
                'label'             => esc_html__( 'Overlay Primary color', 'dggm-gallery-modules' ),
                'type'              => 'color-alpha',
                'toggle_slug'       => 'hover',
                'default'           => '#00B4DB',
                'show_if'           => array(
                    'overlay'  => 'on'
                )
            ),
            'overlay_secondary'  => array(
                'label'             => esc_html__( 'Overlay Secondary color', 'dggm-gallery-modules' ),
                'type'              => 'color-alpha',
                'toggle_slug'       => 'hover',
                'default'           => '#0083B0',
                'show_if'           => array(
                    'overlay'  => 'on'
                )
            ),
            'overlay_direction'    => array (
                'label'             => esc_html__( 'Overlay Gradient Direction', 'dggm-gallery-modules' ),
                'type'              => 'range',
                'toggle_slug'       => 'hover',
                'default'           => '180deg',
                'default_unit'      => 'deg',
                'allowed_units'     => array ('deg'),
                'range_settings'    => array(
                    'min'  => '1',
                    'max'  => '360',
                    'step' => '1',
                ),
                'mobile_options'    => true,
                'show_if'           => array(
                    'overlay'  => 'on'
                )
            ),
            'border_anim'    => array(
                'label'         => esc_html__('Border Animation', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'hover'
            ),
            'anm_border_color'  => array(
                'label'             => esc_html__( 'Border Color', 'dggm-gallery-modules' ),
                'type'              => 'color-alpha',
                'toggle_slug'       => 'hover',
                'default'           => '#ffffff',
                'show_if'           => array(
                    'border_anim'  => 'on'
                )
            ),
            'anm_border_width'    => array (
                'label'             => esc_html__( 'Border Width', 'dggm-gallery-modules' ),
                'type'              => 'range',
                'toggle_slug'       => 'hover',
                'default'           => '3px',
                'default_unit'      => '',
                'allowed_units'     => array ('px'),
                'range_settings'    => array(
                    'min'  => '1',
                    'max'  => '20',
                    'step' => '1',
                ),
                'mobile_options'    => true,
                'show_if'           => array(
                    'border_anim'  => 'on'
                )
            ),
            'anm_border_margin'    => array (
                'label'             => esc_html__( 'Border Space', 'dggm-gallery-modules' ),
                'type'              => 'range',
                'toggle_slug'       => 'hover',
                'default'           => '15px',
                'default_unit'      => '',
                'allowed_units'     => array ('px'),
                'range_settings'    => array(
                    'min'  => '0',
                    'max'  => '50',
                    'step' => '1',
                ),
                'mobile_options'    => true,
                'show_if'           => array(
                    'border_anim'  => 'on'
                )
            ),
            'anm_content_padding'    => array (
                'label'             => esc_html__( 'Content Space', 'dggm-gallery-modules' ),
                'type'              => 'range',
                'toggle_slug'       => 'hover',
                'default'           => '1em',
                'default_unit'      => '',
                'allowed_units'     => array ('em'),
                'range_settings'    => array(
                    'min'  => '.5',
                    'max'  => '3',
                    'step' => '.1',
                ),
                'mobile_options'    => true
            ),
            'border_anm_style'    => array(
                'label'         => esc_html__('Border Animation Style', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    'c4-border-center'          => esc_html__('Border Center', 'dggm-gallery-modules'),
                    'c4-border-top'             => esc_html__('Border Top', 'dggm-gallery-modules'),
                    'c4-border-bottom'          => esc_html__('Border Bottom', 'dggm-gallery-modules'),
                    'c4-border-right'           => esc_html__('Border Right', 'dggm-gallery-modules'),
                    'c4-border-vert'            => esc_html__('Border Vertical', 'dggm-gallery-modules'),
                    'c4-border-horiz'           => esc_html__('Border Horizontal', 'dggm-gallery-modules'),
                    'c4-border-top-left'        => esc_html__('Border Top Left', 'dggm-gallery-modules'),
                    'c4-border-top-right'       => esc_html__('Border Top Right', 'dggm-gallery-modules'),
                    'c4-border-bottom-left'     => esc_html__('Border Bottom Left', 'dggm-gallery-modules'),
                    'c4-border-bottom-right'    => esc_html__('Border Bottom Right', 'dggm-gallery-modules'),
                    'c4-border-corners-1'       => esc_html__('Border Corner 1', 'dggm-gallery-modules'),   
                    'c4-border-corners-2'       => esc_html__('Border Corner 2', 'dggm-gallery-modules'),   
                    'c4-border-cc-1'            => esc_html__('Border CC 1', 'dggm-gallery-modules'),   
                    'c4-border-cc-2'            => esc_html__('Border CC 2', 'dggm-gallery-modules'),   
                    'c4-border-cc-3'            => esc_html__('Border CC 3', 'dggm-gallery-modules'),   
                    'c4-border-ccc-1'           => esc_html__('Border CCC 1', 'dggm-gallery-modules'),   
                    'c4-border-ccc-2'           => esc_html__('Border CCC 2', 'dggm-gallery-modules'),   
                    'c4-border-ccc-3'           => esc_html__('Border CCC 3', 'dggm-gallery-modules'),   
                    'c4-border-fade'            => esc_html__('Border Fade', 'dggm-gallery-modules'),   
                ),
                'default'       => 'c4-border-fade',
                'toggle_slug'   => 'hover',
                'show_if'       => array(
                    'border_anim'   => 'on'
                )
            ),
            'show_caption'    => array(
                'label'         => esc_html__('Show Caption', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'hover',
            ),
            'always_show_title'  => array(
                'label'         => esc_html__('Always Show Caption', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'hover',
                'show_if' => array(
                    'show_caption' => 'on'
                )
            ),
            'content_reveal_caption'    => array(
                'label'    => esc_html__('Caption Reveal', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    'c4-reveal-up'            => esc_html__('Reveal Top', 'dggm-gallery-modules'),
                    'c4-reveal-down'          => esc_html__('Reveal Down', 'dggm-gallery-modules'),
                    'c4-reveal-left'          => esc_html__('Reveal Left', 'dggm-gallery-modules'),
                    'c4-reveal-right'         => esc_html__('Reveal Right', 'dggm-gallery-modules'),
                    'c4-fade-up'              => esc_html__('Fade Up', 'dggm-gallery-modules'),
                    'c4-fade-down'            => esc_html__('Fade Down', 'dggm-gallery-modules'),
                    'c4-fade-left'            => esc_html__('Fade Left', 'dggm-gallery-modules'),
                    'c4-fade-right'           => esc_html__('Fade Right', 'dggm-gallery-modules'),
                    'c4-rotate-up-right'      => esc_html__('Rotate Up Right', 'dggm-gallery-modules'),
                    'c4-rotate-up-left'       => esc_html__('Rotate Up Left', 'dggm-gallery-modules'),
                    'c4-rotate-down-right'    => esc_html__('Rotate Down Right', 'dggm-gallery-modules'),
                    'c4-rotate-down-left'     => esc_html__('Rotate Down Left', 'dggm-gallery-modules')
                ),
                'default'       => 'c4-fade-up',
                'toggle_slug'   => 'hover',
                'show_if' => array(
                    'show_caption' => 'on'
                ),
                'show_if_not'   => array(
                    'always_show_title' => 'on'
                ),
            ),
            'show_description'    => array(
                'label'         => esc_html__('Show Description', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'hover',
            ),
            'always_show_description'  => array(
                'label'         => esc_html__('Always Show Description', 'dggm-gallery-modules'),
                'type'          => 'yes_no_button',
                'options'       => array (
                    'off' => esc_html__( 'Off', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'On', 'dggm-gallery-modules' ),
                ),
                'default'       => 'off',
                'toggle_slug'   => 'hover',
                'show_if' => array(
                    'show_description' => 'on'
                )
            ),
            'content_reveal_description'    => array(
                'label'    => esc_html__('Description Reveal', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    'c4-reveal-up'            => esc_html__('Reveal Top', 'dggm-gallery-modules'),
                    'c4-reveal-down'          => esc_html__('Reveal Down', 'dggm-gallery-modules'),
                    'c4-reveal-left'          => esc_html__('Reveal Left', 'dggm-gallery-modules'),
                    'c4-reveal-right'         => esc_html__('Reveal Right', 'dggm-gallery-modules'),
                    'c4-fade-up'              => esc_html__('Fade Up', 'dggm-gallery-modules'),
                    'c4-fade-down'            => esc_html__('Fade Down', 'dggm-gallery-modules'),
                    'c4-fade-left'            => esc_html__('Fade Left', 'dggm-gallery-modules'),
                    'c4-fade-right'           => esc_html__('Fade Right', 'dggm-gallery-modules'),
                    'c4-rotate-up-right'      => esc_html__('Rotate Up Right', 'dggm-gallery-modules'),
                    'c4-rotate-up-left'       => esc_html__('Rotate Up Left', 'dggm-gallery-modules'),
                    'c4-rotate-down-right'    => esc_html__('Rotate Down Right', 'dggm-gallery-modules'),
                    'c4-rotate-down-left'     => esc_html__('Rotate Down Left', 'dggm-gallery-modules'),
                ),
                'default'       => 'c4-fade-up',
                'toggle_slug'   => 'hover',
                'show_if_not'   => array(
                    'always_show_description'  => 'on'
                ),
                'show_if' => array(
                    'show_description' => 'on'
                )
            ),
            'content_position'    => array(
                'label'         => esc_html__('Content Position', 'dggm-gallery-modules'),
                'type'          => 'select',
                'options'       => array (
                    'c4-layout-top-left'        => esc_html__('Top Left', 'dggm-gallery-modules'),
                    'c4-layout-top-center'      => esc_html__('Top Center', 'dggm-gallery-modules'),
                    'c4-layout-top-right'       => esc_html__('Top Right', 'dggm-gallery-modules'),
                    'c4-layout-center-left'     => esc_html__('Center Left', 'dggm-gallery-modules'),
                    'c4-layout-center'          => esc_html__('Center', 'dggm-gallery-modules'),
                    'c4-layout-center-right'    => esc_html__('Center Right', 'dggm-gallery-modules'),
                    'c4-layout-bottom-left'     => esc_html__('Bottom Left', 'dggm-gallery-modules'),
                    'c4-layout-bottom-center'   => esc_html__('Bottom Center', 'dggm-gallery-modules'),
                    'c4-layout-bottom-right'    => esc_html__('Bottom Right', 'dggm-gallery-modules')
                ),
                'default'       => 'c4-layout-top-left',
                'toggle_slug'   => 'hover'
            )
        );
        $tag = array(
            'caption_tag' => array (
                'default'         => 'h4',
                'label'           => esc_html__( 'Caption Tag', 'dggm-gallery-modules' ),
                'type'            => 'select',
                'options'         => array(
                    'h1'    => esc_html__( 'h1 tag', 'dggm-gallery-modules' ),
                    'h2'    => esc_html__( 'h2 tag', 'dggm-gallery-modules' ),
                    'h3'    => esc_html__( 'h3 tag', 'dggm-gallery-modules' ),
                    'h4'    => esc_html__( 'h4 tag', 'dggm-gallery-modules' ),
                    'h5'    => esc_html__( 'h5 tag', 'dggm-gallery-modules' ),
                    'h6'    => esc_html__( 'h6 tag', 'dggm-gallery-modules'),
                    'p'     => esc_html__( 'p tag', 'dggm-gallery-modules'),
                    'span'  => esc_html__( 'span tag', 'dggm-gallery-modules')
                ),
                'toggle_slug'     => 'font_styles',
                'tab_slug'        => 'advanced',
                'sub_toggle'      => 'caption'
            ),
            'description_tag' => array (
                'default'         => 'p',
                'label'           => esc_html__( 'Description Tag', 'dggm-gallery-modules' ),
                'type'            => 'select',
                'options'         => array(
                    'h1'    => esc_html__( 'h1 tag', 'dggm-gallery-modules' ),
                    'h2'    => esc_html__( 'h2 tag', 'dggm-gallery-modules' ),
                    'h3'    => esc_html__( 'h3 tag', 'dggm-gallery-modules' ),
                    'h4'    => esc_html__( 'h4 tag', 'dggm-gallery-modules' ),
                    'h5'    => esc_html__( 'h5 tag', 'dggm-gallery-modules' ),
                    'h6'    => esc_html__( 'h6 tag', 'dggm-gallery-modules'),
                    'p'     => esc_html__( 'p tag', 'dggm-gallery-modules'),
                    'span'  => esc_html__( 'span tag', 'dggm-gallery-modules')
                ),
                'toggle_slug'     => 'font_styles',
                'tab_slug'        => 'advanced',
                'sub_toggle'      => 'description'
            )
        );
        $button = array(
            'more_btn_align' => array(
                'label'             => esc_html__( 'Alignment', 'dggm-gallery-modules' ),
				'type'              => 'text_align',
				'options'           => et_builder_get_text_orientation_options(array('justified')),
				'tab_slug'          => 'advanced',
                'toggle_slug'       => 'more_btn'
            ),
            'spinner_color' => array(
                'label'             => esc_html__( 'Loading Icon Color', 'dggm-gallery-modules' ),
				'type'              => 'color-alpha',
				'tab_slug'          => 'advanced',
                'toggle_slug'       => 'more_btn',
                'default'           => '#2665e0'
            )
        );
        $more_btn_bg = $this->dg_add_bg_field(array(
            'label'				=> 'Background',
            'key'               => 'more_btn_bg',
            'toggle_slug'       => 'more_btn',
            'tab_slug'			=> 'advanced',
            'hover'				=> 'tabs',
            'image'             => false
        ));
        $title = $this->add_margin_padding(array(
            'title'         => 'Caption',
            'key'           => 'title',
            'toggle_slug'   => 'margin_padding',
            'option'        => 'padding'
        ));
        $description = $this->add_margin_padding(array(
            'title'         => 'Description',
            'key'           => 'description',
            'toggle_slug'   => 'margin_padding',
            'option'        => 'padding'
        ));
        $load_more_button = $this->add_margin_padding(array(
            'title'         => 'Load More',
            'key'           => 'load_more',
            'toggle_slug'   => 'margin_padding'
        ));
        $use_load_more_icon = array(
            'more_btn_use_icon' => array(
				'label'                 => esc_html__( 'Use Icon', 'dggm-gallery-modules' ),
				'type'                  => 'yes_no_button',
				'option_category'       => 'basic_option',
				'options'               => array(
					'off' => esc_html__( 'No', 'dggm-gallery-modules' ),
					'on'  => esc_html__( 'Yes', 'dggm-gallery-modules' ),
                ),
                'toggle_slug'           => 'more_btn',
                'tab_slug'              => 'advanced',
				'affects'               => array (
                    'more_btn_font_icon',
                    'more_btn_icon_size'
				)
            ),
            'more_btn_font_icon' => array(
                'label'                 => esc_html__( 'Icon', 'dggm-gallery-modules' ),
                'type'                  => 'select_icon',
                'option_category'       => 'basic_option',
                'class'                 => array( 'et-pb-font-icon' ),
                'toggle_slug'           => 'more_btn',
                'tab_slug'              => 'advanced',
                'depends_show_if'       => 'on'
            ),
            'more_btn_icon_size' => array (
                'label'             => esc_html__( 'Icon Size', 'dggm-gallery-modules' ),
				'type'              => 'range',
				'option_category'   => 'font_option',
				'toggle_slug'       => 'more_btn',
                'tab_slug'          => 'advanced',
				'default'           => '22px',
				'default_unit'      => 'px',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
                ),
				'mobile_options'    => true,
				'depends_show_if'   => 'on',
				'responsive'        => true
            )           
        );

        return array_merge(
            $gallery,
            $settings,
            $hover,
            $tag,
            $button,
            $more_btn_bg,
            $title,
            $description,
            $load_more_button,
            $use_load_more_icon
        );
    }

    public function get_transition_fields_css_props() {
        $fields = parent::get_transition_fields_css_props();

        $button = '%%order_class%% .pg-more-image-btn';

        $fields['load_more_margin'] = array ('margin' => $button);
        $fields['load_more_padding'] = array ('padding' => $button);

        $fields['title_padding'] = array ('padding' => '%%order_class%% .dg_pg_caption');
        $fields['description_padding'] = array ('padding' => '%%order_class%% .dg_pg_description');

        $fields = $this->dg_background_transition(array (
            'fields'        => $fields,
            'key'           => 'more_btn_bg',
            'selector'      => $button
        ));
        // fix border transition
        $fields = $this->dg_fix_border_transition(
            $fields, 
            'pg_more_br', 
            $button
        );
        $fields = $this->dg_fix_border_transition(
            $fields, 
            'item_border', 
            '%%order_class%% .dg_pg_image'
        );

        return $fields;
    }
    
    public function additional_css_styles($render_slug) {
        if (isset($this->props['space_between'])) {
            $this->dg_process_range( array(
                'render_slug'       => $render_slug,
                'slug'              => 'space_between',
                'type'              => 'padding-left',
                'selector'          => '%%order_class%% .dg_pg_container .dg_pg_item'
            ) );
            $this->dg_process_range( array(
                'render_slug'       => $render_slug,
                'slug'              => 'space_between',
                'type'              => 'padding-bottom',
                'selector'          => '%%order_class%% .dg_pg_container .dg_pg_item'
            ) );
            $this->dg_process_range( array(
                'render_slug'       => $render_slug,
                'slug'              => 'space_between',
                'type'              => 'margin-left',
                'selector'          => '%%order_class%% .dg_pg_inner',
                'negative'          => true
            ) );
        }

        if($this->props['overlay'] === 'on') {
            ET_Builder_Element::set_style($render_slug, array(
                'selector' => '%%order_class%% .c4-izmir .dg-overlay',
                'declaration' => sprintf('background-image: linear-gradient(%4$s, %1$s 0, %2$s %3$s);',
                    $this->props['overlay_primary'],
                    $this->props['overlay_secondary'],
                    '100%',
                    $this->props['overlay_direction']
                )
            ));
        }
        if($this->props['border_anim'] === 'on') {
            ET_Builder_Element::set_style($render_slug, array(
                'selector' => '%%order_class%% .c4-izmir',
                'declaration' => sprintf('
                    --border-color: %1$s;',
                    $this->props['anm_border_color']
                ),
            ));
            $this->dg_process_range( array(
                'render_slug'       => $render_slug,
                'slug'              => 'anm_border_width',
                'type'              => '--border-width',
                'selector'          => '%%order_class%% .c4-izmir'
            ) );
            $this->dg_process_range( array(
                'render_slug'       => $render_slug,
                'slug'              => 'anm_border_margin',
                'type'              => '--border-margin',
                'selector'          => '%%order_class%% .c4-izmir'
            ) );
        }
        $this->dg_process_range( array(
            'render_slug'       => $render_slug,
            'slug'              => 'anm_content_padding',
            'type'              => '--padding',
            'selector'          => '%%order_class%% .c4-izmir'
        ) );

        if (isset($this->props['more_btn_align'])) {
            ET_Builder_Element::set_style($render_slug, array(
                'selector' => '%%order_class%% .dg_pg_button_container',
                'declaration' => sprintf('text-align: %1$s;', $this->props['more_btn_align'])
            ));
        }
        if ($this->props['keep_dsk_tab'] !== 'on') {
            ET_Builder_Element::set_style($render_slug, array(
                'selector' => '%%order_class%% .dg_pg_item',
                'declaration' => 'width: calc(100%/'.intval($this->props['tablet_view']).');',
                'media_query' => ET_Builder_Element::get_media_query('max_width_980')

            ));
        }
        if (isset($this->props['mobile_view'])) {
            ET_Builder_Element::set_style($render_slug, array(
                'selector' => '%%order_class%% .dg_pg_item',
                'declaration' => 'width: calc(100%/'.intval($this->props['mobile_view']).');',
                'media_query' => ET_Builder_Element::get_media_query('max_width_767')

            ));
        }
        // loadign icon color
        $this->dg_process_color(array(
            'render_slug'       => $render_slug,
            'slug'              => 'spinner_color',
            'type'              => 'fill',
            'selector'          => '%%order_class%% .pg-more-image-btn .spinner svg'
        ));
        $this->dg_process_bg(array(
            'render_slug'       => $render_slug,
            'slug'              => 'more_btn_bg',
            'selector'          => '%%order_class%% .pg-more-image-btn',
            'hover'             => '%%order_class%% .pg-more-image-btn:hover'
        ));
        // loading font-size
        $this->dg_process_range( array(
            'render_slug'       => $render_slug,
            'slug'              => 'more_btn_icon_size',
            'type'              => 'font-size',
            'selector'          => '%%order_class%% .dg-pg-load-more-icon'
        ) );
        // spacing: title
        $this->set_margin_padding_styles(array(
            'render_slug'       => $render_slug,
            'slug'              => 'title_padding',
            'type'              => 'padding',
            'selector'          => '%%order_class%% .dg_pg_caption',
            'hover'             => '%%order_class%% .item-content:hover .dg_pg_caption',
        ));
        // spacing: description
        $this->set_margin_padding_styles(array(
            'render_slug'       => $render_slug,
            'slug'              => 'description_padding',
            'type'              => 'padding',
            'selector'          => '%%order_class%% .dg_pg_description',
            'hover'             => '%%order_class%% .item-content:hover .dg_pg_description',
        ));
        // spacing: load more
        $this->set_margin_padding_styles(array(
            'render_slug'       => $render_slug,
            'slug'              => 'load_more_margin',
            'type'              => 'margin',
            'selector'          => '%%order_class%% .pg-more-image-btn',
            'hover'             => '%%order_class%% .pg-more-image-btn:hover',
        ));
        $this->set_margin_padding_styles(array(
            'render_slug'       => $render_slug,
            'slug'              => 'load_more_padding',
            'type'              => 'padding',
            'selector'          => '%%order_class%% .pg-more-image-btn',
            'hover'             => '%%order_class%% .pg-more-image-btn:hover',
        ));
        // icon font family
        if(method_exists('ET_Builder_Module_Helper_Style_Processor', 'process_extended_icon')) {
            $this->generate_styles(
                array(
                    'utility_arg'    => 'icon_font_family',
                    'render_slug'    => $render_slug,
                    'base_attr_name' => 'more_btn_font_icon',
                    'important'      => true,
                    'selector'       => '%%order_class%% .dg-pg-load-more-icon',
                    'processor'      => array(
                        'ET_Builder_Module_Helper_Style_Processor',
                        'process_extended_icon',
                    ),
                )
            );
        }
    }

    public function render( $attrs, $content, $render_slug ) {
        $this->additional_css_styles($render_slug);

        wp_enqueue_script('dggm-imagesloaded-lib');
        wp_enqueue_script('dggm-lightgallery-lib');
        wp_enqueue_script('dggm-packery-gallery-lib');
        wp_enqueue_script('dggm-packery-gallery');

        $load_more = $this->props['load_more'];
        $more_btn_use_icon = $this->props['more_btn_use_icon'];

        $gallery_array = dg_pg_options($this->props);
        $images = dg_pg_render_gallery_markup($gallery_array);

        $load_more_icon = $more_btn_use_icon === 'on' ? sprintf('<span class="dg-pg-load-more-icon">%1$s</span>',
            $this->props['more_btn_font_icon'] !== '' ? esc_attr(et_pb_process_font_icon( $this->props['more_btn_font_icon'] )) : '4'
        ) : '';

        $load_more_icon_class = $more_btn_use_icon === 'on' ? ' has_icon' : '';

        $load_more_button = ( $load_more === 'on' ) ?
            sprintf('<div class="dg_pg_button_container">
                    <button class="pg-more-image-btn%4$s" data-loaded="%2$s">%1$s %3$s
                    <span class="spinner">
                        <svg width="135" height="140" viewBox="0 0 135 140" xmlns="http://www.w3.org/2000/svg" fill="#fff">
                            <rect y="10" width="15" height="120" rx="6">
                                <animate attributeName="height"
                                    begin="0.5s" dur="1s"
                                    values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                                    repeatCount="indefinite" />
                                <animate attributeName="y"
                                    begin="0.5s" dur="1s"
                                    values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                                    repeatCount="indefinite" />
                            </rect>
                            <rect x="30" y="10" width="15" height="120" rx="6">
                                <animate attributeName="height"
                                    begin="0.25s" dur="1s"
                                    values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                                    repeatCount="indefinite" />
                                <animate attributeName="y"
                                    begin="0.25s" dur="1s"
                                    values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                                    repeatCount="indefinite" />
                            </rect>
                            <rect x="60" width="15" height="140" rx="6">
                                <animate attributeName="height"
                                    begin="0s" dur="1s"
                                    values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                                    repeatCount="indefinite" />
                                <animate attributeName="y"
                                    begin="0s" dur="1s"
                                    values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                                    repeatCount="indefinite" />
                            </rect>
                            <rect x="90" y="10" width="15" height="120" rx="6">
                                <animate attributeName="height"
                                    begin="0.25s" dur="1s"
                                    values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                                    repeatCount="indefinite" />
                                <animate attributeName="y"
                                    begin="0.25s" dur="1s"
                                    values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                                    repeatCount="indefinite" />
                            </rect>
                            <rect x="120" y="10" width="15" height="120" rx="6">
                                <animate attributeName="height"
                                    begin="0.5s" dur="1s"
                                    values="120;110;100;90;80;70;60;50;40;140;120" calcMode="linear"
                                    repeatCount="indefinite" />
                                <animate attributeName="y"
                                    begin="0.5s" dur="1s"
                                    values="10;15;20;25;30;35;40;45;50;0;10" calcMode="linear"
                                    repeatCount="indefinite" />
                            </rect>
                        </svg>
                    </span></button>
                </div>', 
                sanitize_text_field($this->props['load_more_text']),
                sanitize_text_field($this->props['init_count']),
                $load_more_icon,
                esc_attr($load_more_icon_class)
            ) : '';

        $data_settings = dg_pg_options($this->props);
        
        return sprintf('<div class="dg_pg_container%4$s" data-settings=\'%3$s\'>
                <div class="dg_pg_inner">
                    %1$s
                </div>
                %2$s
            </div>',
            $images,
            $load_more_button,
            wp_json_encode($data_settings),
            $this->props['use_lightbox'] === 'on' ? ' ig_has_lightbox' : ''
        );
    }
}
new DGGM_PackeryGallery;