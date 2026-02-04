<?php
require_once ( __DIR__ . '/settings/settings.php');
DG_Config::set_panels(
    array(
        'dg_gallery_modules'   => __('Gallery Modules', "dggm-gallery-modules"),
    )
);
DG_Config::set_sections(array( 
    'dggm_module_activation' => array(
        'name'  => __( "Activation", "dggm-gallery-modules" ),
        'panel' => 'dg_gallery_modules'
    ), 
    'dggm_module_docs' => array(
        'name'  => __( "Documentation", "dggm-gallery-modules" ),
        'panel' => 'dg_gallery_modules'
    ), 
));


DG_Config::set_settings(array(
   
    // license key
    'dggm_license_key_setting' => array(
        'name'  => __( "License Key", "dggm-gallery-modules" ),
        'type'  => 'license',
        'section_slug'  => 'dggm_module_activation',
        'effected_field' => 'dggm_license_key_status'
    ),
    'dggm_license_key_status' => array(
        'name'  => __( "License Status", "dggm-gallery-modules" ),
        'type'  => 'license-status',
        'section_slug'  => 'dggm_module_activation'
    ),
    // Documentation
    'dggm_whay_license_key' => array(
        'name'  => __( "Why do I need the license key ?", "dggm-gallery-modules" ),
        'type'  => 'docs',
        'section_slug'  => 'dggm_module_docs',
        'doc_text'  => __('If you would like to install any future plugin 
                            updates easily through the WordPress plugin page you will need to have the activated license key.<br/>
                            Though you can use the divi module without the license key.', 'dg_avbanced_tab'),
        // 'doc_url'   => "https://www.divigear.com/request-api-key/",
        // 'doc_url_text'   => "Request License Key",
    ),
    // 'dggm_how_to_use' => array(
    //     'name'  => __( "How to use the module", "dggm-gallery-modules" ),
    //     'type'  => 'docs',
    //     'section_slug'  => 'dggm_module_docs',
    //     'doc_text'  => __('You can get introduced with the plugin settings following the short video.', 'dg_avbanced_tab'),
    //     'doc_url'   => "https://www.youtube.com/watch?v=39LRya6Pzuo",
    //     'doc_url_text'   => "Watch the tutorial",
    // ),
    'dggm_how_to_update' => array(
        'name'  => __( "How to update the plugin", "dggm-gallery-modules" ),
        'type'  => 'docs',
        'section_slug'  => 'dggm_module_docs',
        'doc_text'  => __('When there is a new update available for plugins, You can
                            update the plugins both manually or with the WordPress plugin
                            page. You can install these updates within the plugins page only
                            if you have the activated license key.', 'dg_avbanced_tab'),
        // 'doc_url'   => "https://www.divigear.com/request-api-key/",
        // 'doc_url_text'   => "Request License Key",
    ),
    'dggm_how_get_support' => array(
        'name'  => __( "How can I get support ?", "dggm-gallery-modules" ),
        'type'  => 'docs',
        'section_slug'  => 'dggm_module_docs',
        'doc_text'  => __('If you are facing any trouble using the plugin please let us know
                            through the below link.', 'dg_avbanced_tab'),
        'doc_url'   => "https://www.divigear.com/product-support/",
        'doc_url_text'   => "Get Support",
    ),
    'dggm_feature_request' => array(
        'name'  => __( "Feature request", "dggm-gallery-modules" ),
        'type'  => 'docs',
        'section_slug'  => 'dggm_module_docs',
        'doc_text'  => __('If you have any feature suggestion which is not included within
                        the plugin please let us know. We will try to add the feature if it
                        possible.', 'dg_avbanced_tab'),
        'doc_url'   => "https://www.divigear.com/new-feature-request/",
        'doc_url_text'   => "Request a feature",
    ),
    'dggm_become_an_affiliate' => array(
        'name'  => __( "Become a partner", "dggm-gallery-modules" ),
        'type'  => 'docs',
        'section_slug'  => 'dggm_module_docs',
        'doc_text'  => __('Like to be a partner of DiviGear.com, you are welcome.', 'dg_avbanced_tab'),
        'doc_url'   => "https://www.divigear.com/affiliate-registration/",
        'doc_url_text'   => "Become a partner",
    ),
));


