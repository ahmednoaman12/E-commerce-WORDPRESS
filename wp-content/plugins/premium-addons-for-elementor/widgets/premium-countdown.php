<?php
/**
 * Premium Countdown.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Class Premium_Countdown
 */
class Premium_Countdown extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-countdown-timer';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Countdown', 'premium-addons-for-elementor' ) );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-countdown';
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-countdown',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'counter', 'time', 'event' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Countdown controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_countdown_global_settings',
			array(
				'label' => __( 'Countdown', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_style',
			array(
				'label'   => __( 'Style', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'd-u-s' => __( 'Inline', 'premium-addons-for-elementor' ),
					'd-u-u' => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'default' => 'd-u-u',
			)
		);

		$this->add_control(
			'premium_countdown_date_time',
			array(
				'label'          => __( 'Due Date', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'format' => 'Ym/d H:m:s',
				),
				'default'        => gmdate( 'Y/m/d H:m:s', strtotime( '+ 1 Day' ) ),
				'description'    => __( 'Date format is (yyyy/mm/dd). Time format is (hh:mm:ss). Example: 2020-01-01 09:30.', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_s_u_time',
			array(
				'label'       => __( 'Time Zone', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'wp-time'   => __( 'WordPress Default', 'premium-addons-for-elementor' ),
					'user-time' => __( 'User Local Time', 'premium-addons-for-elementor' ),
				),
				'default'     => 'wp-time',
				'description' => __( 'This will set the current time of the option that you will choose.', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_units',
			array(
				'label'       => __( 'Time Units', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'description' => __( 'Select the time units that you want to display in countdown timer.', 'premium-addons-for-elementor' ),
				'options'     => array(
					'Y' => __( 'Years', 'premium-addons-for-elementor' ),
					'O' => __( 'Month', 'premium-addons-for-elementor' ),
					'W' => __( 'Week', 'premium-addons-for-elementor' ),
					'D' => __( 'Day', 'premium-addons-for-elementor' ),
					'H' => __( 'Hours', 'premium-addons-for-elementor' ),
					'M' => __( 'Minutes', 'premium-addons-for-elementor' ),
					'S' => __( 'Second', 'premium-addons-for-elementor' ),
				),
				'default'     => array( 'O', 'D', 'H', 'M', 'S' ),
				'multiple'    => true,
				'separator'   => 'after',
			)
		);

		$this->add_control(
			'premium_countdown_separator',
			array(
				'label'       => __( 'Digits Separator', 'premium-addons-for-elementor' ),
				'description' => __( 'Enable or disable digits separator', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'premium_countdown_style' => 'd-u-u',
				),
			)
		);

		$this->add_control(
			'premium_countdown_separator_text',
			array(
				'label'     => __( 'Separator Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'premium_countdown_style'     => 'd-u-u',
					'premium_countdown_separator' => 'yes',
				),
				'default'   => ':',
			)
		);

		$this->add_responsive_control(
			'premium_countdown_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-countdown' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_on_expire_settings',
			array(
				'label' => __( 'Expire', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_expire_text_url',
			array(
				'label'       => __( 'Expire Type', 'premium-addons-for-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose whether if you want to set a message or a redirect link or leave it as digits', 'premium-addons-for-elementor' ),
				'options'     => array(
					'default' => __( 'Default', 'premium-addons-for-elementor' ),
					'text'    => __( 'Message', 'premium-addons-for-elementor' ),
					'url'     => __( 'Redirection Link', 'premium-addons-for-elementor' ),
				),
				'default'     => 'text',
			)
		);

		$this->add_control(
			'default_type_notice',
			array(
				'raw'             => __( 'Default option will show the expiration message as <b>Digits [00:00:00]. </b> .', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_countdown_expire_text_url' => 'default',
				),
			)
		);

		$this->add_control(
			'premium_countdown_expiry_text_',
			array(
				'label'     => __( 'On expiry Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::WYSIWYG,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( 'Countdown Expired!', 'prmeium_elementor' ),
				'condition' => array(
					'premium_countdown_expire_text_url' => 'text',
				),
			)
		);

		$this->add_control(
			'premium_countdown_expiry_redirection_',
			array(
				'label'     => __( 'Redirect To', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'condition' => array(
					'premium_countdown_expire_text_url' => 'url',
				),
				'default'   => get_permalink( 1 ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_transaltion',
			array(
				'label' => __( 'Strings Translation', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_countdown_day_singular',
			array(
				'label'   => __( 'Day (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Day',
			)
		);

		$this->add_control(
			'premium_countdown_day_plural',
			array(
				'label'   => __( 'Day (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Days',
			)
		);

		$this->add_control(
			'premium_countdown_week_singular',
			array(
				'label'   => __( 'Week (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Week',
			)
		);

		$this->add_control(
			'premium_countdown_week_plural',
			array(
				'label'   => __( 'Weeks (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Weeks',
			)
		);

		$this->add_control(
			'premium_countdown_month_singular',
			array(
				'label'   => __( 'Month (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Month',
			)
		);

		$this->add_control(
			'premium_countdown_month_plural',
			array(
				'label'   => __( 'Months (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Months',
			)
		);

		$this->add_control(
			'premium_countdown_year_singular',
			array(
				'label'   => __( 'Year (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Year',
			)
		);

		$this->add_control(
			'premium_countdown_year_plural',
			array(
				'label'   => __( 'Years (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Years',
			)
		);

		$this->add_control(
			'premium_countdown_hour_singular',
			array(
				'label'   => __( 'Hour (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Hour',
			)
		);

		$this->add_control(
			'premium_countdown_hour_plural',
			array(
				'label'   => __( 'Hours (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Hours',
			)
		);

		$this->add_control(
			'premium_countdown_minute_singular',
			array(
				'label'   => __( 'Minute (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Minute',
			)
		);

		$this->add_control(
			'premium_countdown_minute_plural',
			array(
				'label'   => __( 'Minutes (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Minutes',
			)
		);

		$this->add_control(
			'premium_countdown_second_singular',
			array(
				'label'   => __( 'Second (Singular)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Second',
			)
		);

		$this->add_control(
			'premium_countdown_second_plural',
			array(
				'label'   => __( 'Seconds (Plural)', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'Seconds',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/countdown-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Gettings started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_typhography',
			array(
				'label' => __( 'Digits', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_countdown_digit_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-amount' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_countdown_digit_typo',
				'scheme'    => Typography::TYPOGRAPHY_3,
				'selector'  => '{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-amount',
				'separator' => 'after',
			)
		);

		$this->add_control(
			'premium_countdown_timer_digit_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-amount' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_countdown_units_shadow',
				'selector' => '{{WRAPPER}} .countdown .pre_countdown-section',
			)
		);

		$this->add_responsive_control(
			'premium_countdown_digit_bg_size',
			array(
				'label'     => __( 'Background Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-amount' => 'padding: {{SIZE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_countdown_digits_border',
				'selector' => '{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-amount',
			)
		);

		$this->add_control(
			'premium_countdown_digit_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-amount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_unit_style',
			array(
				'label' => __( 'Units', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_countdown_unit_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-period' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_countdown_unit_typo',
				'scheme'   => Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-period',
			)
		);

		$this->add_control(
			'premium_countdown_unit_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .countdown .pre_countdown-section .pre_countdown-period' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_separator_width',
			array(
				'label'     => __( 'Spacing in Between', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 40,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .countdown .pre_countdown-section' => 'margin-right: calc( {{SIZE}}{{UNIT}} / 2 ); margin-left: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
				'condition' => array(
					'premium_countdown_separator!' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_separator_style',
			array(
				'label'     => __( 'Separator', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_countdown_style'     => 'd-u-u',
					'premium_countdown_separator' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_separator_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .pre-countdown_separator' => 'font-size: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'premium_countdown_separator_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .pre-countdown_separator' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_separator_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pre-countdown_separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_countdown_exp_message',
			array(
				'label'     => __( 'Expiration Message', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_countdown_expire_text_url' => 'text',
				),
			)
		);

		$this->add_control(
			'premium_countdown_message_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_countdown_message_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_countdown_message_typo',
				'scheme'   => Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .premium-countdown-exp-message',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_countdown_message_border',
				'selector' => '{{WRAPPER}} .premium-countdown-exp-message',
			)
		);

		$this->add_control(
			'premium_countdown_message_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_countdown_message_shadow',
				'selector' => '{{WRAPPER}} .premium-countdown-exp-message',
			)
		);

		$this->add_responsive_control(
			'premium_countdown_message_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_countdown_message_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-countdown-exp-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Countdown widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$target_date = str_replace( '-', '/', $settings['premium_countdown_date_time'] );

		$formats = $settings['premium_countdown_units'];
		$format  = implode( '', $formats );
		$time    = str_replace( '-', '/', current_time( 'mysql' ) );

		$sent_time = '';

		if ( 'wp-time' === $settings['premium_countdown_s_u_time'] ) {
			$sent_time = $time;
		}

		// Singular labels set up.
		$y     = ! empty( $settings['premium_countdown_year_singular'] ) ? $settings['premium_countdown_year_singular'] : 'Year';
		$m     = ! empty( $settings['premium_countdown_month_singular'] ) ? $settings['premium_countdown_month_singular'] : 'Month';
		$w     = ! empty( $settings['premium_countdown_week_singular'] ) ? $settings['premium_countdown_week_singular'] : 'Week';
		$d     = ! empty( $settings['premium_countdown_day_singular'] ) ? $settings['premium_countdown_day_singular'] : 'Day';
		$h     = ! empty( $settings['premium_countdown_hour_singular'] ) ? $settings['premium_countdown_hour_singular'] : 'Hour';
		$mi    = ! empty( $settings['premium_countdown_minute_singular'] ) ? $settings['premium_countdown_minute_singular'] : 'Minute';
		$s     = ! empty( $settings['premium_countdown_second_singular'] ) ? $settings['premium_countdown_second_singular'] : 'Second';
		$label = $y . ',' . $m . ',' . $w . ',' . $d . ',' . $h . ',' . $mi . ',' . $s;

		// Plural labels set up.
		$ys      = ! empty( $settings['premium_countdown_year_plural'] ) ? $settings['premium_countdown_year_plural'] : 'Years';
		$ms      = ! empty( $settings['premium_countdown_month_plural'] ) ? $settings['premium_countdown_month_plural'] : 'Months';
		$ws      = ! empty( $settings['premium_countdown_week_plural'] ) ? $settings['premium_countdown_week_plural'] : 'Weeks';
		$ds      = ! empty( $settings['premium_countdown_day_plural'] ) ? $settings['premium_countdown_day_plural'] : 'Days';
		$hs      = ! empty( $settings['premium_countdown_hour_plural'] ) ? $settings['premium_countdown_hour_plural'] : 'Hours';
		$mis     = ! empty( $settings['premium_countdown_minute_plural'] ) ? $settings['premium_countdown_minute_plural'] : 'Minutes';
		$ss      = ! empty( $settings['premium_countdown_second_plural'] ) ? $settings['premium_countdown_second_plural'] : 'Seconds';
		$labels1 = $ys . ',' . $ms . ',' . $ws . ',' . $ds . ',' . $hs . ',' . $mis . ',' . $ss;

		$pcdt_style = 'd-u-s' === $settings['premium_countdown_style'] ? ' side' : ' down';

		$event = 'digit';
		$text  = '';
		if ( 'text' === $settings['premium_countdown_expire_text_url'] ) {
			$event = 'onExpiry';
			$text  = '<div class="premium-countdown-exp-message">' . $this->parse_text_editor( $settings['premium_countdown_expiry_text_'] ) . '</div>';
		} elseif ( 'url' === $settings['premium_countdown_expire_text_url'] ) {
			$redirect = ! empty( $settings['premium_countdown_expiry_redirection_'] ) ? esc_url( $settings['premium_countdown_expiry_redirection_'] ) : '';
			$event    = 'expiryUrl';
			$text     = $redirect;
		}

		$separator_text = ! empty( $settings['premium_countdown_separator_text'] ) ? $settings['premium_countdown_separator_text'] : '';

		$countdown_settings = array(
			'label1'     => $label,
			'label2'     => $labels1,
			'until'      => $target_date,
			'format'     => $format,
			'event'      => $event,
			'text'       => $text,
			'serverSync' => $sent_time,
			'separator'  => $separator_text,
		);

		?>
		<div id="countDownContiner-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-countdown premium-countdown-separator-<?php echo esc_attr( $settings['premium_countdown_separator'] ); ?>" data-settings='<?php echo wp_json_encode( $countdown_settings ); ?>'>
			<div id="countdown-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-countdown-init countdown<?php echo esc_attr( $pcdt_style ); ?>"></div>
		</div>
		<?php
	}
}
