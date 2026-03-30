<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Toolkit Beere Custom Checkout Widget for Elementor.
 * This widget allows rearranging sections and editing quantities.
 */
class Toolkit_Beere_Custom_Checkout_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'custom-checkout-widget';
    }

    public function get_title() {
        return esc_html__( 'Custom Checkout Toolkit', 'toolkit-for-elementor-by-beere' );
    }

    public function get_icon() {
        return 'eicon-checkout';
    }

    public function get_categories() {
        return [ 'toolkit-for-elementor-by-beere' ];
    }

    public function get_keywords() {
        return [ 'woocommerce', 'checkout', 'rearrange', 'quantity' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'General Settings', 'toolkit-for-elementor-by-beere' ),
            ]
        );

        $this->add_control(
            'sections_order',
            [
                'label' => esc_html__( 'Sections Order (Rearrange)', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'section_id',
                        'label' => esc_html__( 'Section', 'toolkit-for-elementor-by-beere' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'billing' => esc_html__( 'Billing Details', 'toolkit-for-elementor-by-beere' ),
                            'shipping' => esc_html__( 'Shipping Details', 'toolkit-for-elementor-by-beere' ),
                            'additional' => esc_html__( 'Additional Information', 'toolkit-for-elementor-by-beere' ),
                            'order_review' => esc_html__( 'Order Summary', 'toolkit-for-elementor-by-beere' ),
                        ],
                        'default' => 'billing',
                    ],
                ],
                'default' => [
                    [ 'section_id' => 'billing' ],
                    [ 'section_id' => 'shipping' ],
                    [ 'section_id' => 'additional' ],
                    [ 'section_id' => 'order_review' ],
                ],
                'title_field' => '{{{ section_id }}}',
            ]
        );

        $this->add_control(
            'enable_quantity',
            [
                'label' => esc_html__( 'Enable Editable Quantity', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'toolkit-for-elementor-by-beere' ),
                'label_off' => esc_html__( 'No', 'toolkit-for-elementor-by-beere' ),
                'return_value' => 'yes',
                'default' => 'no',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Style section for Quantity Buttons
        $this->start_controls_section(
            'section_quantity_style',
            [
                'label' => esc_html__( 'Quantity Styling', 'toolkit-for-elementor-by-beere' ),
                'condition' => [
                    'enable_quantity' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'qty_input_heading',
            [
                'label' => esc_html__( 'Input Styling', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );

        $this->add_responsive_control(
            'qty_input_width',
            [
                'label' => esc_html__( 'Input Width', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
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
            'qty_input_bg_color',
            [
                'label' => esc_html__( 'Input Background', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quantity input.qty' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'qty_buttons_heading',
            [
                'label' => esc_html__( 'Buttons Styling', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'qty_button_bg_color',
            [
                'label' => esc_html__( 'Button Background', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quantity .button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'qty_button_text_color',
            [
                'label' => esc_html__( 'Button Text Color', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quantity .button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'qty_button_border',
                'selector' => '{{WRAPPER}} .quantity .button, {{WRAPPER}} .quantity input.qty',
            ]
        );

        $this->add_responsive_control(
            'qty_button_padding',
            [
                'label' => esc_html__( 'Padding', 'toolkit-for-elementor-by-beere' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .quantity .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        $settings = $this->get_settings_for_display();

        if ( $settings['enable_quantity'] === 'yes' ) {
            $this->add_quantity_hooks();
        }

        $checkout = WC()->checkout();

        if ( ! is_user_logged_in() && 'no' !== get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
             wc_get_template( 'checkout/form-login.php', array( 'checkout' => $checkout ) );
        }
        
        wc_get_template( 'checkout/form-coupon.php', array( 'checkout' => $checkout ) );

        if ( empty( WC()->cart->get_cart() ) ) {
            echo '<div class="woocommerce">';
            wc_get_template( 'checkout/cart-empty.php' );
            echo '</div>';
            return;
        }

        ?>
        <style>
            .custom-checkout-sections .quantity {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            .custom-checkout-sections .quantity input.qty {
                text-align: center;
                padding: 5px;
            }
        </style>
        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
            
            <?php if ( $checkout->get_checkout_fields() ) : ?>
                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                
                <div class="custom-checkout-sections">
                    <?php
                    foreach ( $settings['sections_order'] as $section ) {
                        echo '<div class="checkout-section-wrapper checkout-section-' . esc_attr( $section['section_id'] ) . '">';
                        switch ( $section['section_id'] ) {
                            case 'billing':
                                do_action( 'woocommerce_checkout_billing' );
                                break;
                            case 'shipping':
                                do_action( 'woocommerce_checkout_shipping' );
                                break;
                            case 'additional':
                                do_action( 'woocommerce_checkout_after_customer_details' );
                                break;
                            case 'order_review':
                                ?>
                                <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'toolkit-for-elementor-by-beere' ); ?></h3>
                                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                                <div id="order_review" class="woocommerce-checkout-review-order">
                                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                                </div>
                                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                                <?php
                                break;
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            <?php endif; ?>

        </form>
        <?php

        if ( $settings['enable_quantity'] === 'yes' ) {
            // Script is added to wp_footer
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
