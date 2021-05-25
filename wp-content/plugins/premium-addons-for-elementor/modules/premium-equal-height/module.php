<?php
/**
 * Class: Module
 * Name: Premium Equal Height
 * Slug: premium-equal-height
 */

namespace PremiumAddons\Modules\Premium_Equal_Height;

// Elementor Classes.
use Elementor\Repeater;
use Elementor\Controls_Manager;

// PremiumAddons Classes.
use PremiumAddons\Includes\Controls\Premium_Select;
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Equal Height addon.
 */
class Module {

	/**
	 * Class object
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		// Enqueue the required JS file.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Create Premium Equal Height tab at the end of section layout tab.
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/section/print_template', array( $this, '_print_template' ), 10, 2 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ), 10, 1 );

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ( true === \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) || ( function_exists( 'elementor_location_exits' ) && ( elementor_location_exits( 'archive', true ) || elementor_location_exits( 'single', true ) ) ) ) {
			wp_add_inline_script(
				'elementor-frontend',
				'window.scopes_array = {};
                window.backend = 0;
                jQuery( window ).on( "elementor/frontend/init", function() {
                    elementorFrontend.hooks.addAction( "frontend/element_ready/section", function( $scope, $ ){
                        if ( "undefined" == typeof $scope ) {
                                return;
                        }
                        if ( $scope.hasClass( "premium-equal-height-yes" ) ) {
                            var id = $scope.data("id");
                            window.scopes_array[ id ] = $scope;
                        }
                        if(elementorFrontend.isEditMode()){		
                            var url = PaModulesSettings.equalHeight_url;
                            jQuery.cachedAssets = function( url, options ) {
                                // Allow user to set any option except for dataType, cache, and url.
                                options = jQuery.extend( options || {}, {
                                    dataType: "script",
                                    cache: true,
                                    url: url
                                });
                                // Return the jqXHR object so we can chain callbacks.
                                return jQuery.ajax( options );
                            };
                            jQuery.cachedAssets( url );
                            window.backend = 1;
                        }
                    });
                });
                jQuery(document).ready(function(){
                    if ( jQuery.find( ".premium-equal-height-yes" ).length < 1 ) {
                        return;
                    }
                    
                    var url = PaModulesSettings.equalHeight_url;
                    
                    jQuery.cachedAssets = function( url, options ) {
                        // Allow user to set any option except for dataType, cache, and url.
                        options = jQuery.extend( options || {}, {
                            dataType: "script",
                            cache: true,
                            url: url
                        });
                        
                        // Return the jqXHR object so we can chain callbacks.
                        return jQuery.ajax( options );
                    };
                    jQuery.cachedAssets( url );
                });	'
			);
		}
	}

	/**
	 * Register Premium Equal Height controls.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_eq_height',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Equal Height', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'premium_eq_height_update',
			array(
				'label' => '<div class="elementor-update-preview editor-pa-preview-update" style="background-color: #fff;"><div class="elementor-update-preview-title">Update changes to page</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Apply</button></div></div>',
				'type'  => Controls_Manager::RAW_HTML,
			)
		);

		$element->add_control(
			'premium_eq_height_switcher',
			array(
				'label'              => __( 'Enable Equal Height', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'render_type'        => 'template',
				'prefix_class'       => 'premium-equal-height-',
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'premium_eq_height_type',
			array(
				'label'     => __( 'Apply on', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'widget',
				'options'   => array(
					'widget' => __( 'Widgets', 'premium-addons-pro' ),
					'custom' => __( 'Custom Selector', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_eq_height_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_eq_height_target',
			array(
				'label'              => __( 'Widgets', 'premium-addons-pro' ),
				'type'               => Premium_Select::TYPE,
				'render_type'        => 'template',
				'label_block'        => true,
				'multiple'           => true,
				'frontend_available' => true,
				'condition'          => array(
					'premium_eq_height_switcher' => 'yes',
					'premium_eq_height_type'     => 'widget',
				),
			)
		);

		$element->add_control(
			'premium_eq_height_custom_target',
			array(
				'label'       => __( 'Selectors', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( '.class-name, .class-name2 .my-custom-class', 'premium-addons-pro' ),
				'description' => __( 'Enter selectors separated with \' , \' ', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_eq_height_switcher' => 'yes',
					'premium_eq_height_type'     => 'custom',
				),
			)
		);

		$element->add_control(
			'premium_eq_height_enable_on',
			array(
				'label'       => __( 'Enable Equal Height on', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'default'     => array( 'desktop', 'tablet', 'mobile' ),
				'condition'   => array(
					'premium_eq_height_switcher' => 'yes',
				),
			)
        );
        
        $url     = 'https://premiumaddons.com/docs/elementor-column-equal-height/';
		$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'equal_height_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, __( 'How to use Premium Equal Height option »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Premium Equal Height output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 4.2.5
	 * @access public
	 * @param object $template for current template.
	 * @param object $element for current element.
	 */
	public function _print_template( $template, $element ) {

		if ( $element->get_name() !== 'section' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();

		?>
		<# if( 'yes' === settings.premium_eq_height_switcher ) {

			view.addRenderAttribute( 'eq_height', 'id', 'premium-temp-equal-height-' + view.getID() );
			var targetType = settings.premium_eq_height_type,

				target = 'custom' === targetType ? settings.premium_eq_height_custom_target.split(',') : settings.premium_eq_height_target,

				addonSettings = {
					'targetType': targetType,
					'target': target,
					'enableOn':settings.premium_eq_height_enable_on
				};

			view.addRenderAttribute( 'equal_height', {
				'id' : 'premium-temp-equal-height-' + view.getID(),
				'data-pa-eq-height': JSON.stringify( addonSettings ) 
			});

		#>
			<div {{{ view.getRenderAttributeString( 'equal_height' ) }}}></div>
		<# } #>
		<?php

		$slider_content = ob_get_contents();

		ob_end_clean();

		$template = $slider_content . $old_template;
		return $template;
	}

	/**
	 * Render Premium Equal Height output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 4.2.5
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$data = $element->get_data();

		$type = $data['elType'];

		$settings = $element->get_settings_for_display();

		if ( ( 'section' === $type ) && 'yes' === $settings['premium_eq_height_switcher'] ) {

			$target_type = $settings['premium_eq_height_type'];

			$target = ( 'custom' === $target_type ) ? explode( ',', $settings['premium_eq_height_custom_target'] ) : $settings['premium_eq_height_target'];

			$addon_settings = array(
				'targetType' => $target_type,
				'target'     => $target,
				'enableOn'   => $settings['premium_eq_height_enable_on'],
			);

			$element->add_render_attribute( '_wrapper', 'data-pa-eq-height', wp_json_encode( $addon_settings ) );
		}
	}

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;
	}
}
