<?php
/**
 * Portmone gateaway Class
 */


// Doing by action to make shure woocommerce is already run

add_action( 'plugins_loaded', 'wc_portmone_gateway_init', 11 );

function wc_portmone_gateway_init() {

    class WC_Gateway_Portmone extends WC_Payment_Gateway {

    	public function __construct() {

    		$this->gateway = 'https://www.portmone.com.ua/gateway/';

			$this->id = "prtportmone";
			$this->icon = '';
			$this->has_fields = false;
			$this->method_title = "Splitpay Portmone";
			$this->method_description = __("Безготівкова оплата за допомогою платіжної системи Portmone", 'wc-gateway-portmone' );

			$this->owners_keys = array();

			$this->init_form_fields();
			

			// Saving options fields
			$this->init_settings();
			// //General options for payment getaway
			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions' );
			$this->enabled = $this->get_option( 'enabled' );
			// //Options from portmone account
			$this->portmone_login = $this->get_option( 'portmone_login' );
			$this->portmone_password = $this->get_option( 'portmone_password' );
			$this->payee_id = $this->get_option( 'payee_id' );
			$this->success_url = $this->get_option( 'success_url' );

			$this->succeed_pay = $this->get_option( 'succeed_pay' );
			$this->waiting_pay = $this->get_option( 'waiting_pay' );
			$this->failed_pay = $this->get_option( 'failed_pay' );

			// Saving owners_keys
			$terms = get_terms( 'bill', array( 'hide_empty' => false ) );
			foreach ($terms as $term) {
				
				$this->owners_keys['owner_key_'. $term->term_id] = $this->get_option('owner_key_'. $term->term_id);
				
			}


			// Do not allow the gateway be enabled if shop currency isn't avilible for portmone
			if (!$this->curr_check()){
                $this->enabled = false;
            }




            // Actions, that should fire on start
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			// Display the $instructions on thankyou page
			add_action( 'woocommerce_thankyou', array( $this, 'thankyou_page' ) );
			// Adding a button to checkout
			add_action( 'woocommerce_receipt_'.$this->id , array( $this, 'receipt_page' ), 10, 1);
			

		} 


		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {

			$default_fields =  array(
		          
		        'enabled' => array(
		            'title'   => __('Увімкнути/Вимкнути', 'wc-gateway-portmone' ),
		            'type'    => 'checkbox',
		            'label'   => __('Увімкнути метод оплати Splitpay', 'wc-gateway-portmone' ),
		            'default' => 'yes'
		        ),

		        'portmone_login' => array(
		            'title'       => __( 'Login до Portmone', 'wc-gateway-portmone' ),
		            'type'        => 'text',
		            'description' => __( '', 'wc-gateway-portmone' ),
		            'default'     => '',
		            'desc_tip'    => true,
		        ),

		        'portmone_password' => array(
		            'title'       => __( 'Пароль до Portmone', 'wc-gateway-portmone' ),
		            'type'        => 'text',
		            'description' => __( '', 'wc-gateway-portmone' ),
		            'default'     => '',
		            'desc_tip'    => true,
		        ),

		        'payee_id' => array(
		            'title'       => __( 'payee_id ключ', 'wc-gateway-portmone' ),
		            'type'        => 'text',
		            'description' => __( '', 'wc-gateway-portmone' ),
		            'default'     => '',
		            'desc_tip'    => true,
		        ),

		        'success_url' => array(
		            'title'       => __( 'Посилання на сторінку після оплати', 'wc-gateway-portmone' ),
		            'type'        => 'url',
		            'description' => __( 'Виберіть посилання на сторінку, на яку буде редіректити Лікпей після завершення процесу оплати. Або залиште порожнім, щоб повертало на сторінку замовлення по замовчуванню.', 'wc-gateway-portmone' ),
		            'default'     => '',
		            'desc_tip'    => true,
		        ),

		        'title' => array(
		            'title'       => __( "Заголовок", 'wc-gateway-portmone' ),
		            'type'        => 'text',
		            'description' => __( 'Цей заголовок бачить користувач під час вибору платіжного методу', 'wc-gateway-portmone' ),
		            'default'     => __( 'Оплата карткою', 'wc-gateway-portmone' ),
		            'desc_tip'    => true,
		        ),

		        'description' => array(
		            'title'       => __( "Опис (необов'язково)", 'wc-gateway-portmone' ),
		            'type'        => 'textarea',
		            'description' => __( 'Опис даного платіжного методу для користувача', 'wc-gateway-portmone' ),
		            'default'     => '',
		            'desc_tip'    => true,
		        ),

		        'instructions' => array(
		            'title'       => __( "Інструкції (необов'язково)", 'wc-gateway-portmone' ),
		            'type'        => 'textarea',
		            'description' => __( 'Інструкції, що будуть додані на сторінку подяки та в емейли.', 'wc-gateway-portmone' ),
		            'default'     => '',
		            'desc_tip'    => true,
		        ),

		        'succeed_pay' => array(
		            'title'       => __( 'Статус змовлення з успішною оплатою', 'wc-gateway-portmone' ),
		            'type'        => 'select',
		            'description'       => __('Призначити статус замовлення, оплата якого пройшла успішно', 'wc-gateway-portmone' ),
		            'options'     => wc_get_order_statuses(),
				    'default'     => 'wc-processing',
				    'desc_tip'    => true,
		        ),
		        'waiting_pay' => array(
		            'title'       => __( 'Статус змовлення з очікуванням оплати', 'wc-gateway-portmone' ),
		            'type'        => 'select',
		            'description'       => __('Призначити статус замовлення, оплата якого ще не пройшла', 'wc-gateway-portmone' ),
		            'options'     => wc_get_order_statuses(),
				    'default'     => 'wc-pending',
				    'desc_tip'    => true,
		        ),
		        'failed_pay' => array(
		            'title'       => __( 'Статус змовлення з помилкою оплати', 'wc-gateway-portmone' ),
		            'type'        => 'select',
		            'description'       => __('Призначити статус замовлення, оплата якого повернула помилку', 'wc-gateway-portmone' ),
		            'options'     => wc_get_order_statuses(),
				    'default'     => 'wc-failed',
				    'desc_tip'    => true,
		        ),

		    );

		    $terms = get_terms( 'bill', array( 'hide_empty' => false ) );
			foreach ($terms as $term) {
				if ($term->slug != 'owner') {
					$owner_tip = __( 'Ви можете його отримати в адмін-панелі кабінету Лікпей', 'wc-gateway-portmone' );
				} else {
					$owner_tip = __( 'ВАЖЛИВО! Публічний ключ рахунку власника сайту, на який повинні зараховуватись кошти не повинен співпадати з основним ключем магазину', 'wc-gateway-portmone' );
				}
				$default_fields['owner_payeeid_'. $term->term_id] = array(
		            'title'       => __( 'Payee_id власника ', 'wc-gateway-portmone' ) . $term->name,
		            'type'        => 'text',
		            'description' => $owner_tip,
		            'default'     => '',
		            'desc_tip'    => true,
		        );
			}
		      
		    $this->form_fields = apply_filters( 'wc_portmone_form_fields', $default_fields);
		    
		   
		}

		// Function, that checks if woocommerce has valid currency for portmone
		public function curr_check(){
            if (!in_array(get_option('woocommerce_currency'), array('RUB', 'UAH', 'USD', 'EUR'))){
                return false;
            }
            return true;
        }


		// Forming Portmone checkout link to redirect on placing order
        public function portmone_redirect_link($order_id){
			global $woocommerce;
			
            $order = new WC_Order( $order_id );
			
            switch (get_woocommerce_currency()) {
				case 'UAH':
					$currency = 'UAH';
					break;
				case 'USD':
					$currency = 'USD';
					break;
				case 'RUB':
					$currency = 'RUB';
					break;
				case 'EUR':
					$currency = 'EUR';
					break;   
				case 'GBP':
					$currency = 'GBP';
					break;   
				case 'BYN':
					$currency = 'BYN';
					break;   
				case 'KZT':
					$currency = 'KZT';
					break;   
				default: 
					$currency = 'UAH';
			}


			// ///////////////////////// FORM HTML
			// $portmone_args = array(
		 //        'payee_id'           => $this->payee_id,
		 //        'shop_order_number'  => $order->get_id(),
		 //        'bill_amount'        => $order->get_total(),
		 //        'bill_currency'      => $currency,
		 //        'success_url'        => $this->success_url . '&status=success',
		 //        'failure_url'        => $this->success_url . '&status=failure',
		 //        'cms_module_name'    => json_encode(['name' => 'WordPress', 'v' => '1']), //!!!!!!!!!! ЗМІНИИТИ ВЕРСІЮ
		 //        'encoding'           => 'UTF-8'
		 //    );
		 //    $out = '';
			
			// foreach ($portmone_args as $key => $value) {
	  //           $portmone_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
	  //       }
	  //       $out .= '<form action="' . $this->gateway . '" method="post" id="portmone_payment_form">
	  //           ' . implode('', $portmone_args_array) . '
	  //       <input type="submit" id="submit_portmone_payment_form" value="' . 'PAY TEST' . '" /></form>';


			$source = '{
				"v":"2",
				"payeeId":"'. $this->payee_id .'",
				"lang":"uk",
				"amount":"'. $order->get_total() .'",
				"billCurrency":"'. $currency .'",
				"description":"'. __( 'Оплата за замовлення №', 'wc-gateway-portmone' ) . ' ' . $order->get_id() .'",
				"emailAddress":"test@test.com",
				"successUrl":"'. $order->get_checkout_order_received_url() .'",
				"billNumber":"'. $order->get_id() .'"
			}';

			$i = base64_encode(gzencode($source));
			$url = 'https://www.portmone.com.ua/r3/uk/autoinsurance?i=' . $i;
			//$url тепер містить адресу структурованого посилання для видачі клієнту



			// $raw_url = $portmone->cnb_form_raw(array(
			// 	'public_key'     => $public_key,
			// 	'action'         => 'pay',
			// 	'amount'         => $order->get_total(),
			// 	'currency'       => $currency,
			// 	'split_rules'    => $split_rules,
			// 	'description'    => __( 'Оплата за замовлення №', 'wc-gateway-portmone' ) . ' ' . $order->get_id(),
			// 	'order_id'       => $order->get_id(),
			// 	'result_url'     => $success_url,
   //          	'server_url'     => $result_url,
			// 	'version'        => '3'
			// ));

			// $url_query_params = array(
			// 	'data' => $raw_url['data'],
			// 	'signature' => $raw_url['signature']
			// );

			// $url = $raw_url['url'] . '?' . http_build_query($url_query_params);

			return $url;
        }

        public function receipt_page($order_id){
        	echo '<br><br><br><br>kljasdklsfjskdfjsdlf<br><br><br>';
        	global $woocommerce;
			
            $order = new WC_Order( $order_id );
			
            switch (get_woocommerce_currency()) {
				case 'UAH':
					$currency = 'UAH';
					break;
				case 'USD':
					$currency = 'USD';
					break;
				case 'RUB':
					$currency = 'RUB';
					break;
				case 'EUR':
					$currency = 'EUR';
					break;   
				case 'GBP':
					$currency = 'GBP';
					break;   
				case 'BYN':
					$currency = 'BYN';
					break;   
				case 'KZT':
					$currency = 'KZT';
					break;   
				default: 
					$currency = 'UAH';
			}


			///////////////////////// FORM HTML
			$portmone_args = array(
		        'payee_id'           => $this->payee_id,
		        'shop_order_number'  => $order->get_id(),
		        'bill_amount'        => $order->get_total(),
		        'bill_currency'      => $currency,
		        'success_url'        => $order->get_checkout_order_received_url() . '&status=success',
		        'failure_url'        => $order->get_checkout_order_received_url() . '&status=failure',
		        'encoding'           => 'UTF-8'
		    );
		    $out = '';
			
			foreach ($portmone_args as $key => $value) {
	            $portmone_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
	        }
	        $out .= '<form action="' . $this->gateway . '" method="post" id="portmone_payment_form">
	            ' . implode('', $portmone_args_array) . '
	        <input type="submit" id="submit_portmone_payment_form" value="' . 'PAY TEST' . '" /></form>';
	        echo $out;
        }

        // Main function that processing order ad redirecting to Portmone checkout page
		public function process_payment( $order_id ) {
    
		    $order = wc_get_order( $order_id );

		    // $pay_url = $this->portmone_redirect_link( $order_id );

		    $pay_url = $order->get_checkout_payment_url(true);
		            
		    // Mark as on-hold (we're awaiting the payment)
		    $order->update_status( $this->get_option( 'waiting_pay' ) );
		            
		    // Reduce stock levels
		    $order->reduce_order_stock();
		            
		    // Remove cart
		    WC()->cart->empty_cart();

		            
		    // Return thankyou redirect
		    // 'result' here is  
		    return array(
		        'result'    => 'success',
		        'redirect'  => $pay_url
		    );

		}



		public function curlRequest($url, $data) {
	        $ch = curl_init($url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $response = curl_exec($ch);
	        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        curl_close($ch);
	        if (200 !== intval($httpCode)) {
	            return false;
	        }
	        return $response;
	    }



		/**
		 * Output for the order received page.
		 */
		public function thankyou_page($order_id) {

			

			$order = wc_get_order( $order_id );
			$data = array(
		        "method" => "result",
		        "payee_id" => $this->payee_id,
		        "login" => $this->portmone_login,
		        "password" => $this->portmone_password,
		        "shop_order_number" => $order_id,
		    );

		    // getting result of payment by order_id on portmone
			$result_portmone = curlRequest($this->gateway, $data);
			// parsing xml response from portmone
			$parseXml = simplexml_load_string($result_portmone, 'SimpleXMLElement', LIBXML_NOCDATA);

			if ($parseXml->orders->order->status=="PAYED") {
				$order->update_status($this->get_option( 'succeed_pay' ));
			} else {
				$order->update_status($this->get_option( 'failed_pay' ));
			}

			var_dump($parseXml->orders->order->status);

			

		    if ( $this->instructions ) {
		        echo wpautop( wptexturize( $this->instructions ) );
		    }
		}
		    
		    
		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		        
		    if ( $this->instructions && ! $sent_to_admin && 'prt_portmone' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
		        echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
		    }
		}


		
	

    } // end \WC_Gateway_Portmone class
}
