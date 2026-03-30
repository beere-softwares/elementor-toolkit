<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use ElementorPro\Plugin;

/**
 * Toolkit Beere Custom Checkout Widget.
 * Extends the original Elementor Pro Checkout widget to add rearrangeable sections and quantities.
 */
class Toolkit_Beere_Custom_Checkout_Widget extends \ElementorPro\Modules\Woocommerce\Widgets\Checkout {

    public function get_name() {
        return 'toolkit-beere-custom-checkout';
    }

    public function get_title() {
        return esc_html__( 'Custom Checkout (Toolkit)', 'toolkit-for-elementor-by-beere' );
    }

    protected function register_controls() {
        parent::register_controls();

        $this->update_control(
            'section_content',
            [
                'label' => esc_html__( 'General Settings (Toolkit)', 'toolkit-for-elementor-by-beere' ),
            ]
        );

        // Inject Rearrange Control
        $this->start_injection( [
            'at' => 'after',
            'of' => 'checkout_layout',
        ] );

        $this->add_control(
            'rearrange_sections',
            [
                'label' => esc_html__( 'Rearrange Sections', 'toolkit-for-elementor-by-beere' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'toolkit-for-elementor-by-beere' ),
                'label_off' => esc_html__( 'No', 'toolkit-for-elementor-by-beere' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'sections_order_list',
            [
                'label' => esc_html__( 'Sections Order', 'toolkit-for-elementor-by-beere' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'section_id',
                        'label' => esc_html__( 'Section', 'toolkit-for-elementor-by-beere' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            'billing' => esc_html__( 'Billing Details', 'toolkit-for-elementor-by-beere' ),
                            'shipping' => esc_html__( 'Shipping Details', 'toolkit-for-elementor-by-beere' ),
                            'additional' => esc_html__( 'Additional Information', 'toolkit-for-elementor-by-beere' ),
                            'order_review' => esc_html__( 'Order Summary', 'toolkit-for-elementor-by-beere' ),
                            'payment' => esc_html__( 'Payment', 'toolkit-for-elementor-by-beere' ),
                        ],
                        'default' => 'billing',
                    ],
                ],
                'default' => [
                    [ 'section_id' => 'billing' ],
                    [ 'section_id' => 'shipping' ],
                    [ 'section_id' => 'additional' ],
                    [ 'section_id' => 'order_review' ],
                    [ 'section_id' => 'payment' ],
                ],
                'title_field' => '{{{ section_id }}}',
                'condition' => [
                    'rearrange_sections' => 'yes',
                ],
            ]
        );

        $this->end_injection();

        // Inject Quantity Control
        $this->start_injection( [
            'at' => 'after',
            'of' => 'sections_order_list',
        ] );

        $this->add_control(
            'enable_quantity',
            [
                'label' => esc_html__( 'Enable Editable Quantity', 'toolkit-for-elementor-by-beere' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'toolkit-for-elementor-by-beere' ),
                'label_off' => esc_html__( 'No', 'toolkit-for-elementor-by-beere' ),
                'return_value' => 'yes',
                'default' => 'no',
                'separator' => 'before',
            ]
        );

        $this->end_injection();

        // Quantity Style Section
        $this->start_controls_section(
            'section_toolkit_quantity_style',
            [
                'label' => esc_html__( 'Quantity Styling (Toolkit)', 'toolkit-for-elementor-by-beere' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_quantity' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'qty_input_width_toolkit',
            [
                'label' => esc_html__( 'Input Width', 'toolkit-for-elementor-by-beere' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .quantity input.qty' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'qty_button_bg_color_toolkit',
            [
                'label' => esc_html__( 'Button Background', 'toolkit-for-elementor-by-beere' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quantity .button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'qty_button_text_color_toolkit',
            [
                'label' => esc_html__( 'Button Text Color', 'toolkit-for-elementor-by-beere' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quantity .button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( 'yes' === $settings['enable_quantity'] ) {
            $this->add_quantity_hooks();
        }

        if ( 'yes' !== $settings['rearrange_sections'] ) {
            parent::render();
            return;
        }

        // Custom render for rearrangeable sections
        $is_editor = Plugin::elementor()->editor->is_edit_mode();
        if ( $is_editor ) {
            $store_current_user = wp_get_current_user()->ID;
            wp_set_current_user( 0 );
        }

        $this->add_render_hooks();

        // Output shortcode but we need to control the internal template...
        // Actually, if we want to rearrange, we have to bypass the [woocommerce_checkout] shortcode
        // and manually render what it does but in our order.
        
        $checkout = WC()->checkout();

        if ( ! is_user_logged_in() && 'no' !== get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
             wc_get_template( 'checkout/form-login.php', array( 'checkout' => $checkout ) );
        }
        
        wc_get_template( 'checkout/form-coupon.php', array( 'checkout' => $checkout ) );

        if ( empty( WC()->cart->get_cart() ) ) {
            echo '<div class="woocommerce">';
            wc_get_template( 'checkout/cart-empty.php' );
            echo '</div>';
        } else {
            ?>
            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
                <div id="customer_details" class="col2-set">
                    <?php
                    foreach ( $settings['sections_order_list'] as $section ) {
                        switch ( $section['section_id'] ) {
                            case 'billing':
                                echo '<div class="col-1">';
                                do_action( 'woocommerce_checkout_billing' );
                                echo '</div>';
                                break;
                            case 'shipping':
                                echo '<div class="col-2">';
                                do_action( 'woocommerce_checkout_shipping' );
                                echo '</div>';
                                break;
                            case 'additional':
                                do_action( 'woocommerce_checkout_after_customer_details' );
                                break;
                            case 'order_review':
                                do_action( 'woocommerce_checkout_before_order_review_heading' );
                                echo '<h3 id="order_review_heading">' . esc_html__( 'Your order', 'woocommerce' ) . '</h3>';
                                do_action( 'woocommerce_checkout_before_order_review' );
                                echo '<div id="order_review" class="woocommerce-checkout-review-order">';
                                do_action( 'woocommerce_checkout_order_review' );
                                echo '</div>';
                                do_action( 'woocommerce_checkout_after_order_review' );
                                break;
                            case 'payment':
                                // In standard WC, payment is part of order_review action. 
                                // But some themes/plugins might separate it.
                                break;
                        }
                    }
                    ?>
                </div>
            </form>
            <?php
        }

        $this->remove_render_hooks();

        if ( $is_editor ) {
            wp_set_current_user( $store_current_user );
        }
    }

    private function add_quantity_hooks() {
        add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'editable_checkout_quantities' ], 10, 3 );
        add_action( 'wp_footer', [ $this, 'checkout_quantity_script' ] );
    }

    public function editable_checkout_quantities( $quantity_html, $cart_item, $cart_item_key ) {
        $product = $cart_item['data'];
        if ( $product->is_sold_individually() ) {
            return $quantity_html;
        }

        $html = '<div class="quantity">';
        $html .= '<input type="button" value="-" class="minus button wp-element-button">';
        $html .= sprintf(
            '<input type="number" name="cart[%s][qty]" value="%s" size="4" title="Qty" class="input-text qty text" min="1" step="1">',
            $cart_item_key,
            $cart_item['quantity']
        );
        $html .= '<input type="button" value="+" class="plus button wp-element-button">';
        $html .= '</div>';
        
        return $html;
    }

    public function checkout_quantity_script() {
        if ( ! is_checkout() ) return;
        ?>
        <script type="text/javascript">
        jQuery( function( $ ) {
            $( document ).on( 'click', '.plus, .minus', function() {
                var $qty = $( this ).closest( '.quantity' ).find( '.qty' );
                var currentVal = parseFloat( $qty.val() ) || 1;
                var step = 1;
                if ( $( this ).is( '.plus' ) ) {
                    $qty.val( currentVal + step );
                } else {
                    if ( currentVal > 1 ) {
                        $qty.val( currentVal - step );
                    }
                }
                $( 'body' ).trigger( 'update_checkout' );
            });
            $( document ).on( 'change', '.qty', function() {
                var val = parseFloat( $(this).val() );
                if ( isNaN(val) || val <= 0 ) {
                    $(this).val(1);
                }
                $( 'body' ).trigger( 'update_checkout' );
            });
        });
        </script>
        <?php
    }
}
