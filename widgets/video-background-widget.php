<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Video Background Widget.
 *
 * Elementor widget that displays a video background.
 *
 * @since 1.2.0
 */
class Elementor_Video_Background_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve video background widget name.
     *
     * @since 1.2.0
     * @access public
     * @return string Widget name.
     */
    public function get_name() {
        return 'video-background';
    }

    /**
     * Get widget title.
     *
     * Retrieve video background widget title.
     *
     * @since 1.2.0
     * @access public
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Video Background', 'elementor-toolkit' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve video background widget icon.
     *
     * @since 1.2.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-play';
    }

    /**
     * Get custom help URL.
     *
     * Retrieve a URL where the user can get more information about the widget.
     *
     * @since 1.2.0
     * @access public
     * @return string Widget help URL.
     */
    public function get_custom_help_url() {
        return 'https://example.com/docs/video-background-widget';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the video background widget belongs to.
     *
     * @since 1.2.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'elementor-toolkit' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the video background widget belongs to.
     *
     * @since 1.2.0
     * @access public
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return [ 'video', 'background', 'container' ];
    }

    /**
     * Register video background widget controls.
     *
     * Add input fields to allow the user to customize the widget settings.
     *
     * @since 1.2.0
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Content', 'elementor-toolkit' ),
            ]
        );

        $this->add_control(
            'video_link',
            [
                'label' => esc_html__( 'Video Link', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'media_type' => 'video',
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Hello, World!', 'elementor-toolkit' ),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__( 'Description', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'This is a video background widget.', 'elementor-toolkit' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Button Text', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Click Me', 'elementor-toolkit' ),
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => esc_html__( 'Button Link', 'elementor-toolkit' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'elementor-toolkit' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );


        $this->end_controls_section();
    }

    /**
     * Render video background widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.2.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $video_url = $settings['video_link']['url'];
        ?>
        <div class="video-background-widget">
            <?php if ( ! empty( $video_url ) ) : ?>
                <video autoplay muted loop class="video-background-widget__video">
                    <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
                </video>
            <?php endif; ?>
            <div class="video-background-widget__content">
                <h2 class="video-background-widget__title"><?php echo esc_html( $settings['title'] ); ?></h2>
                <div class="video-background-widget__description"><?php echo wp_kses_post( $settings['description'] ); ?></div>
                <a href="<?php echo esc_url( $settings['button_link']['url'] ); ?>" class="video-background-widget__button elementor-button">
                    <?php echo esc_html( $settings['button_text'] ); ?>
                </a>
            </div>
        </div>
        <?php
    }
}