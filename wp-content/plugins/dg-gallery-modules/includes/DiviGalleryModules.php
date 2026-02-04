<?php

class DGGM_DiviGalleryModules extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'dggm-gallery-modules';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'Divi Gallery Modules';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = DGGM_VERSION;

	/**
	 * DCPTA_AdvancedTabModule constructor.
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public function __construct( $name = 'Divi Gallery Modules', $args = array() ) {
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );
		parent::__construct( $name, $args );
	}

}

new DGGM_DiviGalleryModules;
