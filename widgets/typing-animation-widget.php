<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Typing Animation Widget.
 *
 * Elementor widget that displays a typing animation.
 *
 * @since 1.1.0
 */
class Elementor_Typing_Animation_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve typing animation widget name.
     *
     * @since 1.1.0
     * @access public
     * @return string Widget name.
     */
    public function get_name() {
        return 'typing-animation';
    }

    /**
     * Get widget title.
     *
     * Retrieve typing animation widget title.
     *
     * @since 1.1.0
     * @access public
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Typing Animation', 'elementor-toolkit' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve typing animation widget icon.
     *
     * @since 1.1.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-animation-text';
    }

    /**
     * Get custom help URL.
     *
     * Retrieve a URL where the user can get more information about the widget.
     *
     * @since 1.1.0
     * @access public
     * @return string Widget help URL.
     */
    public function get_custom_help_url() {
        return 'https://example.com/docs/typing-animation-widget';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the typing animation widget belongs to.
     *
     * @since 1.1.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'elementor-toolkit' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the typing animation widget belongs to.
     *
     * @since 1.1.0
     * @access public
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return [ 'typing', 'animation', 'text' ];
    }

    /**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.1.0
	 * @access public
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'elementor-toolkit' ];
	}

    /**
     * Register typing animation widget controls.
     *
     * Add input fields to allow the user to customize the widget settings.
     *
     * @since 1.1.0
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'elementor-toolkit' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text_to_type',
            [
                'label' => esc_html__( 'Text to Type', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Hello, World!', 'elementor-toolkit' ),
            ]
        );

        $this->add_control(
            'typing_speed',
            [
                'label' => esc_html__( 'Typing Speed (ms)', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render typing animation widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.1.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="typing-animation-widget" data-text="<?php echo esc_attr( $settings['text_to_type'] ); ?>" data-speed="<?php echo absint( $settings['typing_speed'] ); ?>"></div>
        <?php
    }

}