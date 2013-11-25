<?php
if ( !defined( 'myCRED_VERSION' ) ) exit;
/**
 * WooCommerce Payment Gateway
 *
 * Custom Payment Gateway for WooCommerce.
 * @see http://docs.woothemes.com/document/payment-gateway-api/
 * @since 0.1
 * @version 1.2.1
 */
if ( !function_exists( 'mycred_init_woo_gateway' ) ) {
	/**
	 * Construct Gateway
	 * @since 0.1
	 * @version 1.0
	 */
	add_action( 'after_setup_theme', 'mycred_init_woo_gateway' );
	function mycred_init_woo_gateway()
	{
		if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
		class WC_Gateway_myCRED extends WC_Payment_Gateway {

			protected $mycred;

			/**
			 * Constructor
			 */
			public function __construct() {
				$this->mycred = mycred_get_settings();

				$this->id				  = 'mycred';
				$this->icon 	          = '';
				$this->has_fields 		  = false;
				$this->method_title       = __( 'myCRED', 'mycred' );
				$this->method_description = __( 'Let users pay using their myCRED balance.', 'mycred' );

				// Load the settings.
				$this->init_form_fields();
				$this->init_settings();

				// Define user set variables
				$this->title 		 = $this->get_option( 'title' );
				$this->description   = $this->get_option( 'description' );
				if ( $this->use_exchange() )
					$exchange_rate = (float) $this->get_option( 'exchange_rate' );
				else
					$exchange_rate = 1;
				
				if ( !is_numeric( $exchange_rate ) )
					$exchange_rate = 1;
				
				$this->exchange_rate = $exchange_rate;
				$this->log_template  = $this->get_option( 'log_template' );

				// Actions
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
				add_action( 'woocommerce_thankyou_mycred',                              array( $this, 'thankyou_page' ) );
			}

			/**
			 * Initialise Gateway Settings Form Fields
			 * @since 0.1
			 * @version 1.1
			 */
			function init_form_fields() {
				// Fields
				$fields['enabled'] = array(
					'title'   => __( 'Enable/Disable', 'mycred' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable myCRED Payment', 'mycred' ),
					'default' => 'no',
					'description' => __( 'Users who are not logged in or excluded from using myCRED will not have access to this gateway!', 'mycred' )
				);
				$fields['title'] = array(
					'title'       => __( 'Title', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Title to show for this payment option.', 'mycred' ),
					'default'     => __( 'Pay with myCRED', 'mycred' ),
					'desc_tip'    => true
				);
				$fields['description'] = array(
					'title'       => __( 'Customer Message', 'mycred' ),
					'type'        => 'textarea',
					'default'     => $this->mycred->template_tags_general( __( 'Deduct the amount from your %_plural% balance.', 'mycred' ) )
				);
				$fields['log_template'] = array(
					'title'       => __( 'Log Template', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Log entry template for successful payments. Available template tags: %order_id%, %order_link%', 'mycred' ),
					'default'     => __( 'Payment for Order: #%order_id%', 'mycred' )
				);

				// Only add exchange rate if the currecy is not set to mycred
				if ( $this->use_exchange() ) {
					$exchange_desc = __( 'How much is 1 %_singular% worth in %currency%?', 'mycred' );
					$exchange_desc = $this->mycred->template_tags_general( $exchange_desc );
					$exchange_desc = str_replace( '%currency%', get_woocommerce_currency(), $exchange_desc );

					$fields['exchange_rate'] = array(
						'title'       => __( 'Exchange Rate', 'mycred' ),
						'type'        => 'text',
						'description' => $exchange_desc,
						'default'     => 1,
						'desc_tip'    => true
					);
					$fields['show_total'] = array(
						'title'       => __( 'Show Total', 'mycred' ),
						'type'        => 'select',
						'label'       => $this->mycred->template_tags_general( __( 'Show the final price in %_plural% .', 'mycred' ) ),
						'options'     => array(
							''           => __( 'Do not show', 'mycred' ),
							'cart'       => __( 'Show in Cart', 'mycred' ),
							'checkout'   => __( 'Show on Checkout Page', 'mycred' ),
							'all'        => __( 'Show in Cart and on Checkout Page', 'mycred' )
						),
						'default'     => ''
					);
					$fields['total_label'] = array(
						'title'       => __( 'Label', 'mycred' ),
						'type'        => 'text',
						'default'     => $this->mycred->template_tags_general( __( 'Order Total in %_plural%', 'mycred' ) ),
						'desc_tip'    => true
					);
				}

				// Profit Sharing added in 1.3
				$fields['profit_sharing_percent'] = array(
					'title'       => __( 'Profit Sharing', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Option to share a percentage of the sale with the product owner.', 'mycred' ),
					'default'     => 0,
					'desc_tip'    => true
				);
				$fields['profit_sharing_log'] = array(
					'title'       => __( 'Log Template', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Log entry template for profit sharing. Available template tags: General and Post related.', 'mycred' ),
					'default'     => __( 'Sale of %post_title%', 'mycred' )
				);
				
				$this->form_fields = apply_filters( 'mycred_woo_fields', $fields, $this );
			}

			/**
			 * Use Exchange
			 * Checks to see if exchange is needed.
			 * @since 0.1
			 * @version 1.0
			 */
			function use_exchange() {
				$currency = get_woocommerce_currency();
				if ( $currency == 'MYC' ) return false;
				return true;
			}

			/**
			 * Admin Panel Options
			 * @since 0.1
			 * @version 1.0
			 */
			public function admin_options() { ?>

		<h3><?php _e( 'myCRED Payment', 'mycred' ); ?></h3>
		<p><?php echo $this->mycred->template_tags_general( __( 'Allows users to pay using their myCRED %_singular% balance. Please note that users with insufficient funds and users who are not logged in will not see this payment gateway on the checkout page.', 'mycred' ) ); ?></p>
		<table class="form-table">
<?php
				// Generate the HTML For the settings form.
				$this->generate_settings_html(); ?>

		</table><!--/.form-table-->
<?php
			}

			/**
			 * Process Payment
			 * @since 0.1
			 * @version 1.1.1
			 */
			function process_payment( $order_id ) {
				global $woocommerce;
				$cui = get_current_user_id();

				// Make sure we are still logged in
				if ( !is_user_logged_in() ) {
					$woocommerce->add_error( $this->mycred->template_tags_general( __( 'You must be logged in to pay with %_plural%', 'mycred' ) ) );
					return;
				}

				// Make sure we have not been excluded
				if ( $this->mycred->exclude_user( $cui ) ) {
					$woocommerce->add_error( $this->mycred->template_tags_general( __( 'You can not use this gateway. Please try a different payment option.', 'mycred' ) ) );
					return;
				}

				// Grab Order
				$order = new WC_Order( $order_id );

				// Cost
				$cost = $this->mycred->apply_exchange_rate( $order->order_total, $this->exchange_rate );

				// Check funds
				if ( $this->mycred->get_users_cred( $cui ) < $cost ) {
					$woocommerce->add_error( $this->mycred->template_tags_general( __( 'Insufficient funds. Please try a different payment option.', 'mycred' ) ) );
					return;
				}

				// Charge
				$this->mycred->add_creds(
					'woocommerce_payment',
					$cui,
					0-$cost,
					$this->log_template,
					$order_id,
					array( 'ref_type' => 'post' )
				);
				$order->payment_complete();

				// Profit Sharing
				if ( $this->profit_sharing_percent > 0 ) {
					// Get Items
					$items = $order->get_items();

					// Loop though items
					foreach ( $items as $item ) {
						// Get Product
						$product = get_post( (int) $item['product_id'] );

						// Continue if product has just been deleted or owner is buyer
						if ( $product === NULL || $product->post_author == $cui ) continue;

						// Calculate Cost
						$price = $item['line_total'];
						$quantity = $item['qty'];
						$cost = $price*$quantity;

						// Calculate Share
						$share = ( $this->profit_sharing_percent / 100 ) * $cost;

						// Payout
						$this->mycred->add_creds(
							'store_sale',
							$product->post_author,
							$this->mycred->number( $share ),
							$this->profit_sharing_log,
							$product->ID,
							array( 'ref_type' => 'post' )
						);
					}
				}

				// Let others play
				do_action( 'mycred_paid_for_woo', $order, $cui );

				// Return the good news
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			}

			/**
			 * Thank You Page
			 * @since 0.1
			 * @version 1.0
			 */
			function thankyou_page() {
				echo __( 'Your account has successfully been charged.', 'mycred' );
			}
		}
	}

	/**
	 * Log Entry
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'mycred_parse_log_entry_woocommerce_payment', 'mycred_woo_log_entry', 90, 2 );
	function mycred_woo_log_entry( $content, $log_entry )
	{
		// Prep
		$mycred = mycred_get_settings();
		$order = new WC_Order( $log_entry->ref_id );
		$cui = get_current_user_id();

		// Order ID
		$content = str_replace( '%order_id%', $order->id, $content );

		// Link to order if we can edit plugin or are the user who made the order
		if ( $cui == $order->user_id || $mycred->can_edit_plugin( $cui ) ) {
			$url = esc_url( add_query_arg( 'order', $order->id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) );
			$content = str_replace( '%order_link%', '<a href="' . $url . '">#' . $order->id . '</a>', $content );
		}
		else {
			$content = str_replace( '%order_link%', '#' . $order->id, $content );
		}

		return $content;
	}

	/**
	 * Parse Email Notice
	 * @since 1.2.2
	 * @version 1.0
	 */
	add_filter( 'mycred_email_before_send', 'mycred_woo_parse_email' );
	function mycred_woo_parse_email( $email )
	{
		if ( $email['request']['ref'] == 'woocommerce_payment' ) {
			$order = new WC_Order( (int) $email['request']['ref_id'] );
			if ( isset( $order->id ) ) {
				$url = esc_url( add_query_arg( 'order', $order->id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) );

				$content = str_replace( '%order_id%', $order->id, $email['request']['entry'] );
				$email['request']['entry'] = str_replace( '%order_link%', '<a href="' . $url . '">#' . $order->id . '</a>', $content );
			}
		}
		return $email;
	}

	/**
	 * Register Gateway
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'woocommerce_payment_gateways', 'mycred_register_woo_gateway' );
	function mycred_register_woo_gateway( $methods )
	{
		$methods[] = 'WC_Gateway_myCRED';
		return $methods;
	}

	/**
	 * Available Gateways
	 * "Removes" this gateway as a payment option if:
	 * - User is not logged in
	 * - User is excluded
	 * - Users balance is too low
	 *
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'woocommerce_available_payment_gateways', 'mycred_woo_available_gateways' );
	function mycred_woo_available_gateways( $gateways )
	{
		if ( !isset( $gateways['mycred'] ) ) return $gateways;

		// Check if we are logged in
		if ( !is_user_logged_in() ) {
			unset( $gateways['mycred'] );
			return $gateways;
		}

		// Get myCRED
		$mycred = mycred_get_settings();
		$cui = get_current_user_id();

		// Check if we are excluded from myCRED usage
		if ( $mycred->exclude_user( $cui ) ) {
			unset( $gateways['mycred'] );
			unset( $mycred );
			return $gateways;
		}

		global $woocommerce;

		// Calculate cost in CREDs
		$cost = $mycred->apply_exchange_rate( $mycred->number( $woocommerce->cart->total ), $gateways['mycred']->get_option( 'exchange_rate' ) );

		// Check if we have enough points
		if ( $mycred->get_users_cred( $cui ) < $cost ) {
			$gateways['mycred']->enabled = 'no';
		}

		// Clean up and return
		unset( $mycred );
		return $gateways;
	}

	/**
	 * Add Currency
	 * Adds myCRED as one form of currency.
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'woocommerce_currencies', 'mycred_woo_add_currency' );
	function mycred_woo_add_currency( $currencies )
	{
		$mycred = mycred_get_settings();
		$currencies['MYC'] = $mycred->plural();
		unset( $mycred );
		return $currencies;
	}

	/**
	 * Currency Symbol
	 * Appends the myCRED prefix or suffix to the amount.
	 * @since 0.1
	 * @version 1.0
	 */
	add_filter( 'woocommerce_currency_symbol', 'mycred_woo_currency', 10, 2 );
	function mycred_woo_currency( $currency_symbol, $currency )
	{
		switch ( $currency ) {
			case 'MYC':
				$mycred = mycred_get_settings();
				if ( !empty( $mycred->before ) )
					$currency_symbol = $mycred->before;
				elseif ( !empty( $mycred->after ) )
					$currency_symbol = $mycred->after;
			break;
		}
		return $currency_symbol;
	}

	/**
	 * Add CRED Cost
	 * Appends the cost in myCRED format.
	 * @since 0.1
	 * @version 1.0
	 */
	add_action( 'woocommerce_review_order_after_order_total', 'mycred_woo_after_order_total' );
	add_action( 'woocommerce_cart_totals_after_order_total', 'mycred_woo_after_order_total' );
	function mycred_woo_after_order_total()
	{
		if ( !is_user_logged_in() ) return;

		$mycred = mycred_get_settings();
		$cui = get_current_user_id();

		if ( $mycred->exclude_user( $cui ) ) return;

		// Only available for logged in non-excluded users
		global $woocommerce;

		$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
		if ( !isset( $available_gateways['mycred'] ) ) return;

		// Check if enabled
		$show = $available_gateways['mycred']->get_option( 'show_total' );
		if ( empty( $show ) ) return;
		elseif ( $show === 'cart' && !is_cart() ) return;
		elseif ( $show === 'checkout' && !is_checkout() ) return;

		// Make sure myCRED is not the currency used
		$currency = get_woocommerce_currency();
		if ( $currency != 'MYC' ) {

			// Apply Exchange Rate
			$rate = $available_gateways['mycred']->get_option( 'exchange_rate' );
			$mycred_cost = $mycred->apply_exchange_rate( $woocommerce->cart->total, $rate ); ?>

			<tr class="total">
				<th><strong><?php echo $mycred->template_tags_general( $available_gateways['mycred']->get_option( 'total_label' ) ); ?></strong></th>
				<td>
					<?php

			// Balance
			$balance = $mycred->get_users_cred( $cui );
			$balance = $mycred->number( $balance );

			// Insufficient Funds
			if ( $balance < $mycred_cost ) { ?>

					<strong class="mycred-low-funds" style="color:red;"><?php echo $mycred->format_creds( $mycred_cost ); ?></strong> 
<?php
			}
			// Funds exist
			else { ?>

					<strong class="mycred-funds"><?php echo $mycred->format_creds( $mycred_cost ); ?></strong> 
<?php
			} ?>

					<small class="mycred-current-balance"><?php

			// Current balance
			echo sprintf( '( %1s: %2s )', __( 'Your current balance', 'mycred' ), $mycred->format_creds( $balance ) ); ?></small>
				</td>
			</tr>
<?php
			unset( $available_gateways );
		}

		unset( $mycred );
	}
}
?>