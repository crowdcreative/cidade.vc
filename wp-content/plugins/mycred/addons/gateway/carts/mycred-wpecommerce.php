<?php
if ( !defined( 'myCRED_VERSION' ) ) exit;
/**
 * WP E-Commerce Payment Gateway
 *
 * Custom Payment Gateway for WP E-Commerce.
 * @see http://getshopped.org/resources/docs/get-involved/writing-a-new-payment-gateway/
 * @since 1.3
 * @version 1.0.1
 */
if ( !function_exists( 'mycred_init_wpecom_construct_gateway' ) ) {
	/**
	 * Construct Gateway
	 * @since 1.3
	 * @version 1.0
	 */
	add_action( 'after_setup_theme', 'mycred_init_wpecom_construct_gateway' );
	function mycred_init_wpecom_construct_gateway()
	{
		if ( !class_exists( 'wpsc_merchant' ) ) return;

		global $nzshpcrt_gateways, $mycred_wpecom_settings;

		$name = apply_filters( 'mycred_label', myCRED_NAME );
		$mycred_wpecom_settings = shortcode_atts( array(
			'log'       => __( 'Payment for Order: #%order_id%', 'mycred' ),
			'share'     => 0,
			'share_log' => __( 'Store sale', 'mycred' ),
			'rate'      => 1,
			'visitor'   => __( 'You must be logged in to use this gateway', 'mycred' ),
			'low_funds' => __( 'Insufficient Funds.', 'mycred' ),
			'message'   => __( 'Deduct the amount from your balance.', 'mycred' )
		), (array) get_option( 'mycred_wpecom_settings', '' ) );

		// Add gateway
		$nzshpcrt_gateways[] = array(
			'id'                     => 'mycred',
			'name'                   => strip_tags( $name ),
			'has_recurring_billing'  => false,
			'wp_admin_cannot_cancel' => false,
			'requirements'           => array( 'php_version' => '5.2.4' ),
			'form'                   => 'mycred_wpecom_gateway_settings',
			'submit_function'        => 'mycred_wpecom_gateway_settings_save',
			'payment_type'           => 'mycred',
			// this may be legacy, not yet decided
			'internalname'           => 'mycred'
		);

		class myCRED_WP_E_Commerce_Gateway {

			public $core = '';
			public $prefs = array();

			/**
			 * Construct
			 */
			function __construct() {
				global $mycred_wpecom_settings;
				$this->core = mycred_get_settings();
				$this->prefs = $mycred_wpecom_settings;

				add_action( 'wpsc_submit_checkout_gateway',          array( $this, 'process_gateway' ), 1, 2 );
				add_filter( 'wpsc_gateway_checkout_form_mycred',     array( $this, 'checkout_form' ) );
				add_filter( 'mycred_parse_log_entry_wpecom_payment', array( $this, 'parse_template_tags' ), 10, 2 );
			}

			/**
			 * Process Payment
			 * @since 1.3
			 * @version 1.0
			 */
			function process_gateway( $gateway, $purchase_log ) {
				if ( $gateway != 'mycred' ) return;

				// Prep
				$log_id = $purchase_log->get( 'id' );

				// Load Gateway
				$merchant_instance = new wpsc_merchant_mycred( $log_id, false, $this->prefs, $this->core );
				$merchant_instance->construct_value_array();

				// Validate
				$merchant_instance->validate( $purchase_log );

				// Charge
				do_action_ref_array( 'wpsc_pre_submit_gateway', array( &$merchant_instance ) );
				$merchant_instance->submit();
			}

			/**
			 * Checkout Form
			 * @since 1.3
			 * @version 1.0
			 */
			function checkout_form() {
				$output = '';
				if ( !is_user_logged_in() ) {
					$output .= '<tr><td>' . $this->core->template_tags_general( $this->prefs['visitor'] ) . '</td></tr>';
					return $output;
				}

				$output .= '<tr><td><table width="100%"><thead><th class="cart-item">' . __( 'Item', 'mycred' ) . '</th><th class="cart-item-qt"></th><th class="cart-item-cost">' . $this->core->plural() . '</th></thead><tbody>';
				
				$total = 0;
				while ( wpsc_have_cart_items() ) : wpsc_the_cart_item();
					$price = wpsc_cart_item_price( false );
					if ( $this->prefs['rate'] != 1 ) {
						$price = $this->prefs['rate']*$price;
					}
					$total = $total+$price;
				endwhile;
				
				$output .= '<tr><td colspan="2">' . __( 'Total Cost', 'mycred' ) . '</td><td class="cart-item-cost">' . $this->core->format_creds( $total ) . '</td></tr>';
				$balance = $this->core->get_users_cred( get_current_user_id() );
				
				if ( $balance < $total )
					$highlight = ' style="color:red;"';
				else
					$highlight = '';

				$output .= '<tr><td class="cart-item" colspan="2">' . __( 'Your current balance', 'mycred' ) . '</td><td class="cart-item-cost"' . $highlight . '>' . $this->core->format_creds( $balance ) . '</td></tr></tdody></table></tr>';

				if ( !empty( $this->prefs['message'] ) ) {
					$output .= '<tr><td>' . $this->core->template_tags_general( $this->prefs['message'] ) . '</td></tr>';
				}

				return apply_filters( 'mycred_wpecom_form', $output );
			}

			/**
			 * Parse Custom Template Tags
			 * @since 1.3
			 * @version 1.0
			 */
			function parse_template_tags( $content, $log_entry ) {
				if ( !empty( $log_entry->data ) )
					$content = str_replace( '%order_id%', $log_entry->data, $content );
				else
					$content = str_replace( '%order_id%', 'missing', $content );

				return $content;
			}
		}
		new myCRED_WP_E_Commerce_Gateway();

		class wpsc_merchant_mycred extends wpsc_merchant {

			var $prefs = array();
			var $core = '';
			var $cost = 0;
			var $transaction_id = '';

			/**
			 * Construct
			 */
			function __construct( $purchase_id = NULL, $is_receiving = false, $prefs = NULL, $mycred = NULL ) {
				parent::__construct( $purchase_id, $is_receiving );
				$name = apply_filters( 'mycred_label', myCRED_NAME );
				$this->prefs = $prefs;
				$this->core = $mycred;
			}

			/**
			 * Validate
			 * Checks to make sure the current user can use this gateway.
			 * @since 1.3
			 * @version 1.0
			 */
			function validate( $purchase_log ) {
				$error = false;
				$user_id = get_current_user_id();

				// Get cost
				$cart_total = $this->cart_data['total_price'];
				if ( $this->prefs['rate'] != 1 ) {
					$cart_total = $this->prefs['rate']*$cart_total;
				}
				$cart_total = $this->core->number( $cart_total );
				$this->cost = $cart_total;

				// User is not logged in
				if ( !is_user_logged_in() ) {
					$error = $this->core->template_tags_general( $this->prefs['visitor'] );
				}

				// Else if user is excluded
				elseif ( $this->core->exclude_user( $user_id ) ) {
					$error = __( 'You can not use this gateway.', 'mycred' );
				}

				// Else check balance
				else {
					// Rate
					$balance = $this->core->get_users_cred( $user_id );
					if ( $balance < $this->cost ) {
						$error = $this->core->template_tags_general( $this->prefs['low_funds'] );
					}
				}

				// Exit on errors
				if ( apply_filters( 'mycred_wpcom_validate', $error ) !== false ) {
					wpsc_delete_customer_meta( 'selected_gateway' );
					$this->set_error_message( $error );
					$purchase_log->delete( $this->purchase_id );
					unset( $_SESSION['WpscGatewayErrorMessage'] );
					$this->return_to_checkout();
					exit;
				}

				// Prep for payment
				$this->user_id = $user_id;
				$this->transaction_id = 'MYCRED' . $user_id . time();
			}

			/**
			 * Submit
			 * Charges the user for the purchase and if profit sharing is enabled
			 * each product owner.
			 * @since 1.3
			 * @version 1.0.1
			 */
			function submit() {
				// Since the wpsc_pre_submit_gateway action could change these values, we need to check
				if ( $this->cost > 0 && $this->user_id != 0 && !empty( $this->transaction_id ) ) {
					// Let other play before we start
					do_action_ref_array( 'mycred_wpecom_charg', array( &$this ) );

					// Charge
					$this->core->add_creds(
						'wpecom_payment',
						$this->user_id,
						0-$this->cost,
						$this->prefs['log'],
						'',
						$this->purchase_id
					);

					// Update Order
					$this->set_transaction_details( $this->transaction_id, 3 );
					transaction_results( $this->cart_data['session_id'], false );

					// Payout Share
					if ( $this->prefs['share'] > 0 ) {
						// Loop though items
						foreach ( (array) $this->cart_items as $item ) {
							// Get product
							$product = get_post( (int) $item['product_id'] );

							// Continue if product has just been deleted or owner is buyer
							if ( $product === NULL || $product->post_author == $this->user_id ) continue;

							// Calculate Cost
							$price = $item['price'];
							$quantity = $item['quantity'];
							$cost = $price*$quantity;

							// Calculate Share
							$share = ( $this->prefs['share'] / 100 ) * $cost;

							// Payout
							$this->core->add_creds(
								'store_sale',
								$product->post_author,
								$this->core->number( $share ),
								$this->prefs['share_log'],
								$product->ID,
								array( 'ref_type' => 'post' )
							);
						}
					}

					// Let others play before we end
					do_action_ref_array( 'mycred_wpecom_charged', array( &$this ) );

					// Empty Cart, Redirect & Exit
					wpsc_empty_cart();
					$this->go_to_transaction_results( $this->cart_data['session_id'] );
					exit;
				}

				// Else save this as pending
				elseif ( !empty( $this->transaction_id ) ) {
					$this->set_transaction_details( $this->transaction_id, 2 );
				}
			}
		}
	}
}

/**
 * Gateway Settings
 * @filter mycred_wpecom_settings
 * @since 1.3
 * @version 1.0
 */
if ( !function_exists( 'mycred_wpecom_gateway_settings' ) ) {
	function mycred_wpecom_gateway_settings() {
		global $wpdb, $mycred_wpecom_settings;

		$mycred = mycred_get_settings();

		// Get current currency
		$currency_data = $wpdb->get_results( "SELECT * FROM `" . WPSC_TABLE_CURRENCY_LIST . "` ORDER BY `country` ASC", ARRAY_A );
		$selected_currency = esc_attr( get_option( 'currency_type' ) );
		foreach ( $currency_data as $currency ) {
			if ( $selected_currency != $currency['id'] ) continue;
			else {
				if ( !empty( $currency['symbol_html'] ) )
					$selected_currency = $currency['symbol_html'];
				else
					$selected_currency = $currency['code'];

				break;
			}
		}

		$output = '
		<tr>
			<td colspan="2"><strong>' . __( 'General', 'mycred' ) . '</strong></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Log Template for Payments', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[log]" value="' . esc_attr( $mycred_wpecom_settings['log'] ) . '" style="width:50%;" /><br /><span class="description">' . __( 'Log entry template for successful payments. Available template tags: General, %order_id%', 'mycred' ) . '</span></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Exchange Rate', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[rate]" value="' . esc_attr( $mycred_wpecom_settings['rate'] ) . '" style="width:50px;" /><br /><span class="description">' . sprintf( __( 'How much is 1 %s worth in %s', 'mycred' ), $selected_currency, $mycred->plural() ) . '</span></td>
		</tr>
		<tr>
			<td colspan="2"><strong>' . __( 'Profit Sharing', 'mycred' ) . '</strong></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Payout', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[share]" value="' . esc_attr( $mycred_wpecom_settings['share'] ) . '" style="width:50px;" /> %<br /><span class="description">' . __( 'Option to share a percentage of the sale with the product owner (post author). User zero to disable.', 'mycred' ) . '</span></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Log Template', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[share_log]" value="' . esc_attr( $mycred_wpecom_settings['share_log'] ) . '" style="width:50%;" /><br /><span class="description">' . __( 'Log entry template for profit sharing. Available template tags: General and Post related', 'mycred' ) . '</span></td>
		</tr>
		<tr>
			<td colspan="2"><strong>' . __( 'Messages', 'mycred' ) . '</strong></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Instructions', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[message]" value="' . esc_attr( $mycred_wpecom_settings['message'] ) . '" style="width:50%;" /><br /><span class="description">' . __( 'Optional instructions to show users when selecting this gateway. Leave empty to hide.', 'mycred' ) . '</span></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Visitors', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[visitor]" value="' . esc_attr( $mycred_wpecom_settings['visitor'] ) . '" style="width:50%;" /><br /><span class="description">' . __( 'Message to show visitors who are not logged in.', 'mycred' ) . '</span></td>
		</tr>
		<tr>
			<td width="150">' . __( 'Insufficient Funds', 'mycred' ) . '</td>
			<td><input type="text" name="mycred_gateway[low_funds]" value="' . esc_attr( $mycred_wpecom_settings['low_funds'] ) . '" style="width:50%;" /><br /><span class="description">' . $mycred->template_tags_general( __( 'Message to show when users does not have enough %plural% to pay using this gateway.', 'mycred' ) ) . '</span></td>
		</tr>';

		return apply_filters( 'mycred_wpecom_settings', $output );
	}
}

/**
 * Save Gateway Settings
 * @filter mycred_wpecom_save_settings
 * @since 1.3
 * @version 1.0
 */
if ( !function_exists( 'mycred_wpecom_gateway_settings_save' ) ) {
	function mycred_wpecom_gateway_settings_save() {
		if ( isset( $_POST['mycred_gateway'] ) ) {
			$new_settings = apply_filters( 'mycred_wpecom_save_settings', array(
				'log'       => trim( $_POST['mycred_gateway']['log'] ),
				'share'     => abs( $_POST['mycred_gateway']['share'] ),
				'share_log' => trim( $_POST['mycred_gateway']['share_log'] ),
				'rate'      => trim( $_POST['mycred_gateway']['rate'] ),
				'visitor'   => sanitize_text_field( $_POST['mycred_gateway']['visitor'] ),
				'low_funds' => sanitize_text_field( $_POST['mycred_gateway']['low_funds'] ),
				'message'   => sanitize_text_field( $_POST['mycred_gateway']['message'] )
			) );
			update_option( 'mycred_wpecom_settings', $new_settings );
		}
		return true;
	}
}

/**
 * Parse Email Notice
 * @since 1.2.2
 * @version 1.0
 */
if ( !function_exists( 'mycred_wpecom_parse_email' ) ) {
	add_filter( 'mycred_email_before_send', 'mycred_wpecom_parse_email' );
	function mycred_wpecom_parse_email( $email )
	{
		if ( $email['request']['ref'] == 'wpecom_payment' ) {
			$email['request']['entry'] = str_replace( '%order_id%', $email['request']['data'], $email['request']['entry'] );
		}
		return $email;
	}
}
?>