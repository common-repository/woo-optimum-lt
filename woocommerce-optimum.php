<?php
/*
Plugin Name: WooCommerce Optimum.lt
Plugin URI: http://wordpress.org/plugins/woocommerce-optimum/
Description: Optimum.lt verslo apskaitos sistemos integracija
Author: UAB "Optimum Software"
Version: 1.1.1
Author URI: http://www.optimum.lt/
*/

class WooCommerceOptimumLt {
	
	const options_slug = 'woocommerce-optimumlt-options.php';
	
	const wsdl_url = 'http://ws.optimum.lt/OptimumWS/EShopService.asmx?wsdl';
	
	const soap_host = 'http://www.optimum.lt/';
	

	

	
	public static function init() {
		
		ini_set('default_socket_timeout', 1200);

		add_action( 'admin_head', function() { ?>
			
			<style>
				
				#woocommerce-optimumlt-log {
					position: relative;
					margin: 0 0 150px 0;
				}
				
				#woocommerce-optimumlt-log > pre {
					position: relative;
					padding-top: 30px;
				}
				
				#woocommerce-preloader {
					content: " ";
					background: transparent url('data:image/gif;base64,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') no-repeat center left;
					width: 100%;
					height: 32px;
					position: relative;
					top: 0;
					left: 0;
					display: none;
					margin: 10px 0 10px 0;
				}
				
				#woocommerce-preloader.preloading {
					display: block;
				}
				
			</style>
			
			<script type="text/javascript" >

				jQuery(document).ready(function($){
					
					
					function woocommerce_log_animation_enable() {
						
						$('#woocommerce-preloader').addClass('preloading');
						
					}
					
					
					function woocommerce_log_animation_disable() {
						
						$('#woocommerce-preloader').removeClass('preloading');
						
					}
					
					function fix_wpml() {
			
						jQuery.post('<?php echo admin_url() ?>admin.php?page=sitepress-multilingual-cms/menu/troubleshooting.php&debug_action=icl_fix_terms_count&nonce=<?php echo wp_create_nonce('icl_fix_terms_count'); ?>', function () {
							
							console.log('term counts fixed');

							jQuery.post('<?php echo admin_url() ?>admin.php?page=sitepress-multilingual-cms/menu/troubleshooting.php&debug_action=cache_clear&nonce=<?php echo wp_create_nonce('cache_clear'); ?>', function () {
								
								console.log('wpml cache cleared');
								
							});
			
						});
						
			
					}
					
					
					$('[data-click="woocommerce-optimumlt-import-all"]').click(function(){

						woocommerce_log_animation_enable();
						
						$('#woocommerce-optimumlt-log').html('');
						
						var data = {
							action: 'woocommerce_optimumlt_import_all'
						};

						
						$.post(ajaxurl, data, function(response) {
							
							$('#woocommerce-optimumlt-log').html('<pre>' + response + '</pre>');
							
							fix_wpml();
							
							woocommerce_log_animation_disable();
							
						});
						
					

					});
					
					
					
					$('[data-click="woocommerce-optimumlt-import-articles"]').click(function(){

						var data = {
							action: 'woocommerce_optimumlt_import_articles'
						};

						$.post(ajaxurl, data, function(response) {
							
							$('#woocommerce-optimumlt-log').html('<pre>' + response + '</pre>');
							
							fix_wpml();
							
						});

					});
					
					

					$('[data-click="woocommerce-optimumlt-import-categories"]').click(function(){

						var data = {
							action: 'woocommerce_optimumlt_import_categories'
						};

						$.post(ajaxurl, data, function(response) {
							
							$('#woocommerce-optimumlt-log').html('<pre>' + response + '</pre>');
							
							fix_wpml();
							
						});

					});
					
					
					
					
					$('[data-click="woocommerce-optimumlt-import-client-prices"]').click(function(){

						var data = {
							action: 'woocommerce_optimumlt_import_client_prices'
						};
						
						woocommerce_log_animation_enable();

						$.post(ajaxurl, data, function(response) {

							$('#woocommerce-optimumlt-log').html('<pre>' + response + '</pre>');
														
							woocommerce_log_animation_disable();
							
						});
						
					});
					
					
					$('[data-click="woocommerce-optimumlt-test"]').click(function(){

						var data = {
							action: 'woocommerce_optimumlt_test'
						};

						$.post(ajaxurl, data, function(response) {

							$('#woocommerce-optimumlt-log').html('<pre>' + response + '</pre>');
							
						});
						
					});
					
					
					
				});

			</script>

		<?php } );
		
		
		
		
		
		add_action( 'admin_init', function() {

			register_setting( 'woocommerce-optimumlt-settings', 'api_username' );
			register_setting( 'woocommerce-optimumlt-settings', 'api_password' );
			register_setting( 'woocommerce-optimumlt-settings', 'api_client_id' );
			register_setting( 'woocommerce-optimumlt-settings', 'api_article_file_dir' );
			register_setting( 'woocommerce-optimumlt-settings', 'api_category_file_dir' );
			register_setting( 'woocommerce-optimumlt-settings', 'order_prefix' );
			register_setting( 'woocommerce-optimumlt-settings', 'order_delivery_article_sku' );
			register_setting( 'woocommerce-optimumlt-settings', 'use_vat_price' );
			register_setting( 'woocommerce-optimumlt-settings', 'enable_company_data_collection' );
			register_setting( 'woocommerce-optimumlt-settings', 'enable_shipping_data_collection' );
			
			register_setting( 'woocommerce-optimumlt-settings', 'language_matrix' );
			register_setting( 'woocommerce-optimumlt-settings', 'disable_stock_import' );
			register_setting( 'woocommerce-optimumlt-settings', 'use_cron' );
			register_setting( 'woocommerce-optimumlt-settings', 'cron_hour' );
			
			register_setting( 'woocommerce-optimumlt-settings', 'selected_cats' );
			
		});
			
		
				
	
			
			
		add_action( 'admin_menu', function(){

			add_options_page( 
				__('Optimum.lt nustatymai', 'woocommerce_optimumlt'),
				__('Optimum.lt', 'woocommerce_optimumlt'),
				'manage_options',
				self::options_slug,
				array( __CLASS__, 'options_page' )
			);
		
		});
		
		

		add_action('wp_ajax_woocommerce_optimumlt_import_all', array( __CLASS__, 'import_all') );
		
		add_action('wp_ajax_woocommerce_optimumlt_import_categories', array( __CLASS__, 'import_categories') );
		
		add_action('wp_ajax_woocommerce_optimumlt_import_articles', array( __CLASS__, 'import_articles') );
		
		add_action('wp_ajax_woocommerce_optimumlt_import_client_prices', array( __CLASS__, 'import_client_prices') );
		
		add_action('wp_ajax_woocommerce_optimumlt_test', array( __CLASS__, 'test') );
		
		add_action('wp_ajax_woocommerce_optimumlt_send_ajax_order', array( __CLASS__, 'send_ajax_order') );
		
		add_action ( 'woocommerce_optimum_task', array( __CLASS__, 'crontask' ) );
		
		add_action( 'update_option_cron_hour', function(){
		
			self::setup_cron();
			
		});
	
	
	
		add_filter( 'woocommerce_checkout_fields' , array( __CLASS__, 'custom_override_checkout_fields' ) );
		
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( __CLASS__, 'extend_admin_order_billing' ) );
		
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( __CLASS__, 'extend_admin_order_shipping' ) );

	}
	
	
	

	public static function custom_override_checkout_fields( $fields ) {
		
		 if (get_option( 'enable_company_data_collection' ) == '1') {
				 
			 $fields['billing']['billing_company_code'] = array(
				'label'     => __('Company code', 'woocommerce'),
				'placeholder'   => _x('Company code', 'placeholder', 'woocommerce'),
				'required'  => false,
				'class'     => array('form-row-wide'),
				'clear'     => true
			 );
			
			 $fields['billing']['billing_vat_id'] = array(
				'label'     => __('VAT id', 'woocommerce'),
				'placeholder'   => _x('VAT id', 'placeholder', 'woocommerce'),
				'required'  => false,
				'class'     => array('form-row-wide'),
				'clear'     => true
			 );
			 
			 
			 
			 unset($fields['billing']['billing_state']);
			 
			 $fields['shipping']['shipping_phone'] = array(
				'label'     => __('Phone', 'woocommerce'),
				'placeholder'   => '',
				'required'  => true,
				'class'     => array('form-row-wide'),
				'clear'     => true
			 );
			
			 $fields['shipping']['shipping_email'] = array(
				'label'     => __('Email', 'woocommerce'),
				'placeholder'   => '',
				'required'  => false,
				'class'     => array('form-row-wide'),
				'clear'     => true
			 );
			 
			 
			 unset($fields['shipping']['shipping_state']);
			 
		 }
		 
		 
		 return $fields;
		 
		 
	}
	

	public static function extend_admin_order_billing($order){
		
		if (get_option( 'enable_company_data_collection' ) == '1') {
			
			echo '<div style="clear:both;display:block;"></div>';
			
			$company_code = get_post_meta( $order->get_id(), '_billing_company_code', true );
			
			if (!empty($company_code))
			echo '<p><strong>'.__('Company code').':</strong> ' . $company_code . '</p>';
			
			
			$vat_id = get_post_meta( $order->get_id(), '_billing_vat_id', true );
			
			if (!empty($vat_id))
			echo '<p><strong>'.__('VAT id').':</strong> ' . $vat_id . '</p>';
			
		}
		
	}

	

	public static function extend_admin_order_shipping($order){
		
		if (get_option( 'enable_shipping_data_collection' ) == '1') {
			
			echo '<div style="clear:both;display:block;"></div>';
			
			$shipping_email = get_post_meta( $order->get_id(), '_shipping_email', true );
				
			if (!empty($shipping_email))
			echo '<p><strong>'.__('Email').':</strong> ' . $shipping_email . '</p>';
			
			
			
			$shipping_phone = get_post_meta( $order->get_id(), '_shipping_phone', true );
			
			if (!empty($shipping_phone))
			echo '<p><strong>'.__('Phone').':</strong> ' . $shipping_phone . '</p>';
		
			
		}
		
	}



	
	public static function register_action() {
		
		update_option( 'enable_company_data_collection', 1 );
		update_option( 'enable_shipping_data_collection', 1 );
		
	}

	
		
	public static function array_insert_after($key, array &$array, $new_key, $new_value) {
	  if (array_key_exists ($key, $array)) {
		$new = array();
		foreach ($array as $k => $value) {
		  $new[$k] = $value;
		  if ($k === $key) {
			$new[$new_key] = $new_value;
		  }
		}
		return $new;
	  }
	  return FALSE;
	}
	


	public static function options_page(){
		
		?>
		
		<div class="wrap">
		
			<h2 class="title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
			
			
			
			<?php if (self::is_wpml_mode()) { ?>
		
			
			
			<form autocomplete="off" action="options.php" method="post">
			
			<?php
			  
			  settings_fields( 'woocommerce-optimumlt-settings' );
			  
			  do_settings_sections( 'woocommerce-optimumlt-settings' );
			  
			?>

			<table class="form-table">
				
				<tr>
					<th><?php _e('API prisijungimo vardas', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="api_username" value="<?php echo esc_attr( get_option( 'api_username' ) ); ?>" /></td>
				</tr>
				
				<tr>
					<th><?php _e('API slaptažodis', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="api_password" value="<?php echo esc_attr( get_option( 'api_password' ) ); ?>" /></td>
				</tr>

				<tr>
					<th><?php _e('Prekių priedų adresas', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="api_article_file_dir" value="<?php echo esc_attr( self::get_api_article_file_dir() ); ?>" /></td>
				</tr>

				<tr>
					<th><?php _e('Prekių grupių priedų adresas', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="api_category_file_dir" value="<?php echo esc_attr( self::get_api_category_file_dir() ); ?>" /></td>
				</tr>
				
				
				
				<tr>
					<th><?php _e('Kliento ID', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="api_client_id" value="<?php echo esc_attr( get_option( 'api_client_id' )  ); ?>" /></td>
				</tr>
				
				
				
				<tr>
					<th><?php _e('Importuoti kainas', 'woocommerce_optimumlt'); ?></th>
					<td>
						<label><input type="radio" <?php if (get_option( 'use_vat_price' ) == '1') { ?> checked <?php } ?> name="use_vat_price" value="1" /><span>Su PVM</span></label>
						&nbsp; <label><input type="radio" <?php if (get_option( 'use_vat_price' ) != '1') { ?> checked <?php } ?> name="use_vat_price" value="0" /><span>Be PVM</span></label>
					</td>
				</tr>

				<tr>
					<th><?php _e('Atnaujini prekių likučius', 'woocommerce_optimumlt'); ?></th>
					<td>
						
						<label><input type="radio" <?php if (get_option( 'disable_stock_import' ) != '1') { ?> checked <?php } ?> name="disable_stock_import" value="0" /><span>Taip</span></label>
						&nbsp; 
						<label><input type="radio" <?php if (get_option( 'disable_stock_import' ) == '1') { ?> checked <?php } ?> name="disable_stock_import" value="1" /><span>Ne</span></label>
						
					</td>
				</tr>
				
				
				<tr>
					<th><?php _e('Atnaujinti duomenis nustatytą valandą', 'woocommerce_optimumlt'); ?></th>
					<td>
					
						<?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) { ?>
						
							<select name="cron_hour">
							
								<option value=""></option>
								
								<?php
								
									for ($h = 0; $h < 24; $h++) { 
									
										$hh = ($h < 10) ? '0'.$h : $h; 
									
								?>
										<option <?php if (get_option( 'cron_hour' ) == $hh) { ?> selected <?php } ?> value="<?php echo $hh ?>"><?php echo $hh ?>:00</option>
								
								<?php }	?>
								
							</select>
							
							<?php echo get_option('timezone_string'); ?>
							
						<?php } else { ?>
						
							<p>
								<?php _e('Norėdami naudotis šia funkcija privalote išjungti WP Cron funkciją.', 'woocommerce_optimumlt'); ?><br />
								<?php _e('wp-config.php faile įrašykite eilutę:', 'woocommerce_optimumlt'); ?>
							</p>
							<p class="description">
								define('DISABLE_WP_CRON', true);
							</p>
						
						<?php } ?>
						
					</td>
				</tr>
				
				<?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) { ?>
					<?php if (!empty(get_option( 'cron_hour' ))) { ?>
						
						<tr>
							<th><?php _e('Crontab komanda', 'woocommerce_optimumlt'); ?></th>
							<td>
								
								<p class="description">
									*/10 * * * * wget http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-cron.php?doing_wp_cron
								</p>
							
							</td>
						</tr>
					
					<?php } ?>
				<?php } ?>
				

				
			</table>
			
			
			
			
			
			
			
			<h2 class="title"><?php _e('Checkout puslapis', 'woocommerce_optimumlt'); ?></h2>
			
			<table class="form-table">		
				
				<tr>
					<th><?php _e('Įjungti įmonės ir PVM kodo laukelius', 'woocommerce_optimumlt'); ?></th>
					<td>
						<label><input type="radio" <?php if (get_option( 'enable_company_data_collection' ) == '1') { ?> checked <?php } ?> name="enable_company_data_collection" value="1" /><span>Taip</span></label>
						&nbsp; <label><input type="radio" <?php if (get_option( 'enable_company_data_collection' ) != '1') { ?> checked <?php } ?> name="enable_company_data_collection" value="0" /><span>Ne</span></label>
					</td>
				</tr>
				
				<tr>
					<th><?php _e('Įjungti pristatymo telefono ir el. pašto laukelius', 'woocommerce_optimumlt'); ?></th>
					<td>
						<label><input type="radio" <?php if (get_option( 'enable_shipping_data_collection' ) == '1') { ?> checked <?php } ?> name="enable_shipping_data_collection" value="1" /><span>Taip</span></label>
						&nbsp; <label><input type="radio" <?php if (get_option( 'enable_shipping_data_collection' ) != '1') { ?> checked <?php } ?> name="enable_shipping_data_collection" value="0" /><span>Ne</span></label>
					</td>
				</tr>
				
				
				
				<tr>
					<th><?php _e('Optimum užsakymo proceso pavadinimas', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="order_prefix" value="<?php echo esc_attr( self::get_order_prefix() ); ?>" /></td>
				</tr>
				
				<?php /*
							
				<tr>
					<th><?php _e('Optimum pristatymo paslaugos prekės kodas', 'woocommerce_optimumlt'); ?></th>
					<td><input class="regular-text" type="text" name="order_delivery_article_sku" value="<?php echo get_option( 'order_delivery_article_sku' ) ?>" /></td>
				</tr>
				
				*/ ?>
				
			</table>
			
			
			
			
			
			
			
			
			<h2 class="title"><?php _e('Kalbos ir vertimai', 'woocommerce_optimumlt'); ?></h2>

			<table class="form-table">		

				<?php
				
					if (self::is_wpml_mode()) { ?>
						
						<?php foreach (icl_get_languages() as $lng) { ?>
							
							<tr>
								
								<th>
									<?php echo $lng['native_name'] ?>
								</th>
								
								<td>
					
									<select name="language_matrix[<?php echo $lng['code'] ?>]">
										<option <?php if (self::get_dest_language($lng['code']) == 'main') { ?> selected <?php } ?> value="main"><?php _e('Naudoti vietinius Optimum pavadinimus', 'woocommerce_optimumlt'); ?></option>
										<option <?php if (self::get_dest_language($lng['code']) == 'int') { ?> selected <?php } ?> value="int"><?php _e('Naudoti tarptautinius Optimum pavadinimus', 'woocommerce_optimumlt'); ?></option>
									</select>
									
								</td>
								
							</tr>
							
						<?php } ?>
						
					<?php } else { ?>
												
							<tr>
								
								<th>
									<?php 
										
										echo self::get_non_wpml_language_name();
										
									?>
										
								</th>
								
								<td>
					
									<select name="language_matrix[<?php echo self::get_non_wpml_language_code() ?>]">
										<option <?php if (self::get_dest_language(self::get_non_wpml_language_code()) == 'main') { ?> selected <?php } ?> value="main"><?php _e('Naudoti vietinius Optimum pavadinimus', 'woocommerce_optimumlt'); ?></option>
										<option <?php if (self::get_dest_language(self::get_non_wpml_language_code()) == 'int') { ?> selected <?php } ?> value="int"><?php _e('Naudoti tarptautinius Optimum pavadinimus', 'woocommerce_optimumlt'); ?></option>
									</select>
									
								</td>
								
							</tr>
							
				<?php } ?>

			</table>
			
			

			<p class="description">
			
				<?php
				

					if (empty(esc_attr( get_option( 'api_username' ) )) || empty(esc_attr( get_option( 'api_password') ))) {

						_e('Norėdami naudotis Optimum integracijos moduliu privalote įvesti API prisijungimo vardą ir slaptažodį', 'woocommerce_optimumlt');

					} else {
						
						$api_test = self::api_test();

						_e('Tikrinamas prisijungimas prie Optimum serverio', 'woocommerce_optimumlt');
						
						if ( $api_test ) {
							
							echo '... <kbd>'.__('OK', 'woocommerce_optimumlt').'</kbd><br />';

							_e('Surasta prekių', 'woocommerce_optimumlt');
							echo ': '.count(self::api_article_list()).'<br />';
							
							_e('Surasta prekių kategorijų', 'woocommerce_optimumlt');
							echo ': '.count(self::api_category_list()).'<br />';
							
							?>

							<?php
							
						} else {
							
							echo '... <kbd>'.__('Nepavyko prisijungti', 'woocommerce_optimumlt').'</kbd><br />';
							
							_e('Prašome patikrinti API prisijungimo vardą ir slaptažodį', 'woocommerce_optimumlt');
							
						}
				
					}

				?>
				
			</p>
			
			<br />
			
			
			<?php if (isset($api_test) && $api_test == true) { ?>
				
				<h2 class="title"><?php _e('Importuoti tik pažymėtas kategorijas', 'woocommerce_optimumlt'); ?></h2>

				<?php 

				
				$parent_cats = self::get_list_of_parent_cats(); 
				
				$selected_cats = get_option( 'selected_cats' );
				
				if (!is_array($selected_cats)) $selected_cats = [];
				
				?>
				
				
				<select style="min-height:200px;min-width:500px;" autocomplate="off" name="selected_cats[]" multiple>

					<?php foreach ($parent_cats as $parent_cat_item) { ?>
						<option <?php if (in_array($parent_cat_item['id'], $selected_cats)) { ?> selected="selected" <?php } ?> value="<?php echo $parent_cat_item['id'] ?>"><?php echo $parent_cat_item['name'] ?></option>
					<?php } ?>
					
				</select> 

				<p class="description">
					<?php _e('Nieko nepažymėjus bus importuojamos visos kategorijos', 'woocommerce_optimumlt'); ?>
				</p>
				
				
			<?php } ?>
			
			
			<?php
			
			submit_button( __('Saugoti nustatymus', 'woocommerce_optimumlt') );

			?>
			
			<br /><br />
			
			
			<?php if (isset($api_test) && $api_test == true) { ?>
			
				<button type="button" class="button" data-click="woocommerce-optimumlt-import-all"><?php _e('Importuoti duomenis', 'woocommerce_optimumlt'); ?></button>
				
				<?php if ( !empty( get_option( 'api_client_id' ) )) { ?>
				<button type="button" class="button" data-click="woocommerce-optimumlt-import-client-prices"><?php _e('Importuoti kliento kainas pagal KLIENTO ID', 'woocommerce_optimumlt'); ?></button>
				<?php } ?>
				
				<?php if (in_array($_SERVER['REMOTE_ADDR'], array('78.56.216.106', '10.5.55.251', '192.168.0.102', '192.168.0.103'))) { ?>
				
					<button type="button" class="button" data-click="woocommerce-optimumlt-import-categories"><?php _e('Importuoti kategorijas', 'woocommerce_optimumlt'); ?></button>
				
					<button type="button" class="button" data-click="woocommerce-optimumlt-import-articles"><?php _e('Importuoti prekes', 'woocommerce_optimumlt'); ?></button>
				
					<button type="button" class="button" data-click="woocommerce-optimumlt-test">Test</button>
					
				<?php } ?>
				
			<?php } ?>
			
				<div id="woocommerce-preloader"></div>
	
				<div id="woocommerce-optimumlt-log"></div>
				
			</form>
			
			
			
			<?php } else { ?>
			
				<?php __('Modulio veikimui būtinas WooCommerce ir WPML Multilingual CMS modulis.', 'woocommerce_optimumlt'); ?>
			
			<?php } ?>
			
			
			
		</div>
		
		<?php
		
	}

	
	
	
	
	public static function api_make_service() {

		$apiauth = array('UserName'=> get_option( 'api_username' ), 'Password' => get_option( 'api_password' ));
		
		$service = new SoapClient(self::wsdl_url, array('exceptions' => 1));  
		
		$header = new SoapHeader(self::soap_host, 'User', $apiauth, false);
		
		$service->__setSoapHeaders($header);
		
		return $service;
		
	}
	
	
	
	public static function api_get_settings() {
		
		$service = self::api_make_service();
		
		$data = $service->GetSettings()->GetSettingsResult;
		
		return $data;

	}
	
	
	public static function api_test() {
		
		try {
			
			$service = self::api_make_service();
			
			$data = $service->GetSettings();
			
			return true;
			
		} catch (SoapFault $fault) {
			
		//	trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
			
			return false;
			
		}
		

	}
	
	
	
	
	public static function api_store_list() {
		
		$service = self::api_make_service();

		$result = $service->GetStores()->GetStoresResult->Store;

		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if (isset($result->Id))
			$result = array($result);
			
		}
		
		return $result;
		
	}
	
	
	
	public static function api_get_measures($measureId) {
		
		$service = self::api_make_service();

		$result = $service->GetMeasures(array('measureId' => $measureId))->GetMeasuresResult->Mesasure;
		
		if (is_array($result) && isset($result[0])) {
				
			$result = $result[0];
				
		}
		
		return $result;
		
	}
	
	
	
	public static function api_get_stock() {
		
		$service = self::api_make_service();

		$result = $service->GetStock()->GetStockResult->Stock;

		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if (isset($result->ArticleId))
			$result = array($result);
			
		}
		
		return $result;
		
	}
	
	public static function api_category_list2() {
		
		$service = self::api_make_service();

		$result = $service->GetArtGroups()->GetArtGroupsResult;

		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;
		
	}
	
	public static function api_category_list() {
		
		$service = self::api_make_service();

		$result = $service->GetArtGroups()->GetArtGroupsResult->ArtGroup;

		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;
		
	}
	
	
	public static function api_category_files($category_id) {

		$service = self::api_make_service();

		$result = $service->GetArtGroupFiles(array('artGroupId' => $category_id))->GetArtGroupFilesResult->ArtGroupFile;

		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;

	}
	
	
	public static function api_article_list() {
		
		$service = self::api_make_service();

		$result = $service->GetArticles()->GetArticlesResult->Article;
		
		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;

	}
	
	
	public static function api_client_price_list($companyId) {
		
		$service = self::api_make_service();
		
		$result = $service->GetCustomerArticlePrices(array('cstCompanyId' => $companyId))->GetCustomerArticlePricesResult->CustomerArticlePrice;
		
		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;
		
	}
	
	
	public static function api_article_files($article_id) {

		$service = self::api_make_service();

		$result = $service->GetArticleFiles(array('articleId' => $article_id))->GetArticleFilesResult->ArticleFile;

		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;

	}
	

	

	
	public static function api_register_order($data) {
	
		/*
		
		print_r ($data);
		
		$service = self::api_make_service();

		$result = $service->RegisterOrder($data);
		
		return true;
		
		*/
		
		try {
			
			$service = self::api_make_service();

			$result = $service->RegisterOrder($data);
			
			return array('status' => true);
			
		} catch (SoapFault $fault) {
			
		//	trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
		
			return array('status' => false, 'message' => "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");

		}
		
		
	}
	
	
	
	public static function api_get_customers($data = null) {
		
		$service = self::api_make_service();

		$result = $service->GetCustomers($data)->GetCustomersResponse->GetCustomersResult;
		
		if (is_array($result) && count($result) > 0) {
			
		} else {
			
			if ($result != '') {
				$result = array($result);
			} else {
				$result = array();
			}
			
		}
		
		return $result;
		
	}
	
	
	public static function api_insert_customer($data) {
		
		$service = self::api_make_service();

		$result = $service->InsertCustomer($data);
		
		if (isset($result->InsertCustomerResult)) {
			return $result->InsertCustomerResult;
		}
		
		return $result;
		
	}
	
	

	
	
	public static function import_all() {
		
		self::clean_wp_cache();
		
		self::import_categories();
		
		self::import_articles();
		
		self::clean_wp_cache(); 
		
		die();
		
	}
	
	
	
	
	public static function is_this_cat_part_of_selected_tree($list_of_all_cats = array(), $cat_id) {
		
		$selected_top_cats = get_option('selected_cats');
		
		if (!is_array($selected_top_cats) || count($selected_top_cats) == 0) {
			return true;
		}
		
		
		
		
		$new_array = array();
		
		foreach ($selected_top_cats as $selected_cat_id) {
			
			$this_tree_cat_ids = self::get_this_tree_cat_ids($list_of_all_cats, $selected_cat_id);
			
			$new_array = array_merge($new_array, $this_tree_cat_ids);
			
		}
		
	
		if (in_array($cat_id, $new_array)) {
			
			return true;
			
		}
		
		return false;
		
	}
	
	
	
	public static function is_this_cat_one_of_the_selected_cats($list_of_all_cats = array(), $cat_id) {
		
		$selected_top_cats = get_option('selected_cats');
		
		if (!is_array($selected_top_cats) || count($selected_top_cats) == 0) {
			return true;
		}
	
		if (in_array($cat_id, $selected_top_cats)) {
			return true;
		}
		
		return false;
		
	}
	
	
	public static function find_top_most_cat($list_of_all_cats = array(), $cat_id) {
		
		foreach ($list_of_all_cats as $item) {
			
			if ($item->Id == $cat_id) {
				
				if ($item->ParentId == 0 || empty($item->ParentId)) {
					
					return $cat_id;
					
				} else {
					
					return self::find_top_most_cat($list_of_all_cats, $item->ParentId);
					
				}
				
			}
			
		}
		
		return false;
		
	}
	
	
	
	public static function filter_out_cats($given_cat_list, $selected_cat_ids = array()) {
		
		if (is_array($selected_cat_ids) == false || count($selected_cat_ids) == 0) {
			
			return $given_cat_list;
			
		}
		
		
		
		$new_array = array();
		
		foreach ($selected_cat_ids as $selected_cat_id) {
			
			$this_tree_cat_ids = self::get_this_tree_cat_ids($given_cat_list, $selected_cat_id);
			
			$new_array = array_merge($new_array, $this_tree_cat_ids);
			
		}
		
		
		
		
		$result = array();
		
		foreach ($given_cat_list as $list_item) {
			
			if (in_array($list_item->Id, $new_array))
			$result[] = $list_item;
		
		}
		
		return $result;
		
	}
	
	
	
	
	
	
	public static function get_this_tree_cat_ids($given_cat_list, $lowest_cat_id = array()) {

		$new_array = array();
		
		foreach ($given_cat_list as $gk => $gi) {
			
			if ($gi->Id == $lowest_cat_id) {
				
				$new_array[] = $gi->Id;
				
				foreach ($given_cat_list as $gk2 => $gi2) {
					
					if (strpos($gi->FllCode, $gi2->FllCode) === 0 && $gi->Id != $gi2->Id) {

						$new_array[] = $gi2->Id;

					}

				}

				return $new_array;

			}

		}
		
	}
	
	
	
	
	
	
	
	public static function import_categories() {
		
		global $wpdb;
		
		$list = self::api_category_list();
		
		// only keep user selected top cats
		$list = self::filter_out_cats($list, get_option('selected_cats'));
		
		
		$current_term_ids = self::get_optimum_term_ids();
		
		$new_term_ids = array();
		
		$source_language = self::get_source_language();
		
		$active_languages = self::get_active_languages();
		
		foreach ($list as $list_k => $list_item) {

			foreach ($active_languages as $lang_code) {
				
				$dest_language = self::get_dest_language($lang_code);
				
				if (empty($list_item->IntName)) {
					
					$list_item->name = $list_item->Name;
					
				} else {
					
					$list_item->name = $dest_language == 'int' ? $list_item->IntName : $list_item->Name;
					
				}

				$list_item->lang = $lang_code;
				$list_item->order = 10 + $list_k;
				$list_item->files = self::api_category_files($list_item->Id);

				self::category_iu($list_item);
				
				$list[$list_k] = $list_item;

			}
			
		}
		
		
		
		
		
		foreach ($list as $list_k => $list_item) {

			foreach ($active_languages as $lang_code) {

				$this_term_id = self::get_term_id_by_optimum_id_and_lang($list_item->Id, $lang_code);
				
				$new_term_ids[] = $this_term_id;
				
				$this_tt = self::get_term_taxonomy_id($this_term_id, 'product_cat');
				
				$parent_term_id = self::get_term_id_by_optimum_id_and_lang($list_item->ParentId, $lang_code);

				$parent_term_id = empty($parent_term_id) ? 0 : $parent_term_id;
				
				$wpdb->update( $wpdb->term_taxonomy, array('parent' => $parent_term_id), array('term_taxonomy_id' => $this_tt), array('%d'), array('%d') );
				
				update_term_meta($this_term_id, '_last_update', date('Y-m-d H:i:s'));
				
			}
			
		}
		

		
		
		
		$dead_terms = array_diff($current_term_ids, $new_term_ids);

		if (isset($dead_terms) && count($dead_terms) > 0) {
			
			foreach ($dead_terms as $dead_term_id) {
				
				wp_delete_term( $dead_term_id, 'product_cat' );

				self::log('Pašaliname anksčiau per Optimum sukurtą kategoriją, term_id = '.$dead_term_id);
			
			}
			
		}
		
		
		
		
		
	}
	
	         








	public static function get_list_of_parent_cats() {
		
		global $wpdb;
		
		$result = array();
		$list = self::api_category_list();
		
		$tree = array();
		
		foreach ($list as $k => $item) {
			
			$tree_sign = str_replace('.', '-', preg_replace('/[A-Z0-9]+/', '', $item->FllCode)).' ';
			
			$tree[$item->FllCode] = array('id' => $item->Id, 'name' => $tree_sign.$item->Name);
			
		}
		
		return $tree;
		
	}
	




	
	public static function the_term_slug_exists($slug) {
			
		global $wpdb;
		
		if ($wpdb->get_row("SELECT slug FROM ".$wpdb->terms." WHERE slug = '" . $slug . "'", 'ARRAY_A')) {
			
			return true;
		
		} else {
			
			return false;
			
		}
	
	}
	
	
	
	public static function category_iu($data) {


		global $wpdb;
		
		if (self::is_wpml_mode()) {
			global $sitepress;
		}
		
		
		$source_lang = self::get_source_language();
		$this_source_lang = $source_lang != $data->lang ? $source_lang : '';
		
		
	
		$term_id = self::get_term_id_by_optimum_id_and_lang($data->Id, $data->lang);
		
		self::log(date('Y-m-d H:i:s').' Importuojamas kategorijos optimum_id = '.$data->Id.', pavadinimas "'.$data->name.'", kalba "'.$data->lang.'"....');
		
		
		$slug = sanitize_title($data->name);

		
		
		

		if (empty($term_id)) {
			
			$wpdb->insert(
				$wpdb->prefix."terms",
				array(
					'name' => $data->name,
					'slug' => $slug,
					'term_group' => 0
				),
				array(
					'%s',
					'%s',
					'%d'
				)
			);
			
			$term_id = $wpdb->insert_id;
			
			
			self::log('Sukurtas term_id = '.$term_id.', ');
			
			
			$wpdb->insert(
				$wpdb->prefix."term_taxonomy",
				array(
					'term_id' => $term_id,
					'taxonomy' => 'product_cat',
					'parent' => 0,
					'count' => 0
				),
				array(
					'%d',
					'%s',
					'%d',
					'%d'
				)
			);
			
			$new_tt_id = $wpdb->insert_id;
			
			$order = $data->order;
			
			update_term_meta($term_id, 'order', $order);
			update_term_meta($term_id, 'display_type', '');
			update_term_meta($term_id, 'thumbnail_id', '');
			update_term_meta($term_id, '_optimum_category_id', $data->Id);
			update_term_meta($term_id, '_optimum_lang', $data->lang);
			
			
			
			// wpml
			if (self::is_wpml_mode()) {
					
				$optimum_trid = self::get_category_trid_by_optimum_id($data->Id);
				
				if (empty($optimum_trid)) {
					
					$max_trid = self::get_max_trid();
					$this_trid = $max_trid + 1;
					
				} else {
					
					$this_trid = $optimum_trid;
					
				}

				$sitepress->set_element_language_details($new_tt_id, 'tax_product_cat', $this_trid, $data->lang, $this_source_lang);
				
			}
			
		}

		else {
			
			self::log('Atnaujintas term_id = '.$term_id.', ');
			
			
			$wpdb->update( $wpdb->terms , array( 'name' => $data->name ), array('term_id' => $term_id), array('%s'), array('%d') );
			
			$wpdb->update( $wpdb->terms , array( 'slug' => $slug ), array('term_id' => $term_id), array('%s'), array('%d') );
			
			//wp_update_term($term_id, 'product_cat', array('name' => $data->name) );
			//wp_update_term($term_id, 'product_cat', array('slug' => sanitize_title($data->name)) );
			
		}
		
		
		
		
		
		if (self::the_term_slug_exists($slug)) {
			
			$slug = $slug.'-'.$term_id;
			
			$wpdb->update( $wpdb->terms , array( 'slug' => $slug ), array('term_id' => $term_id), array('%s'), array('%d') );
			
		}
		
		
		
		
		
		
		self::log('surasta priedų: '.count($data->files));
		
		if (count($data->files) > 0) {
			
			foreach ($data->files as $file_key => $file_val) {
				
				if (isset($file_val->FileName) && isset($file_val->ArtGroupId) && isset($file_val->DateTime)) {

					$file_url = self::get_category_file_url($file_val->ArtGroupId, $file_val->FileName);
					$unique_filename = sanitize_file_name('artgroup-'.$file_val->ArtGroupId.'-'.$data->lang.'-'.$file_val->FileName);
					
					$attachment_id = self::handle_media_upload($file_url, $unique_filename, $file_val->DateTime, $data->lang);
					update_term_meta($term_id, 'thumbnail_id', $attachment_id);

				}
				
			}
		
		}
		
		
		
		
		
		
		
		self::log(PHP_EOL);
		
		
		
	}
	
	
	
	
	

	public static function get_attachment_by_title( $title ) {
		
		global $wpdb;

		$query = $wpdb->prepare("SELECT * FROM ".$wpdb->posts." WHERE post_title = %s AND post_type = 'attachment' ", $title);
		
		return $wpdb->get_row( $query, OBJECT );
		
	}
		
		
		
		
		

		
	
	
	public static function handle_media_upload($file_url, $unique_filename, $file_modified_datetime, $lang_code = null) {

		$optimum_timezone = self::get_optimum_timezone();
		
		$tz_obj = new DateTimeZone($optimum_timezone);
		
		$today = new DateTime("now", $tz_obj);
		
		$now_time = $today->format('Y-m-d').'T'.$today->format('H:i:s');
		
		
		//
		// check if attachment exists
		//
		$attachment = self::get_attachment_by_title($unique_filename);
		
		self::log('Apdorojamas priedas: '.$file_url.'...', false);
		
		
		
		if (!empty($attachment->post_modified_gmt)) {

			$date = new DateTime($attachment->post_modified_gmt, new DateTimeZone('UTC'));
			
			$date->setTimezone(new DateTimeZone($optimum_timezone));
			
			$post_datetime = $date->format('Y-m-d').'T'.$date->format('H:i:s');
			
			if ($file_modified_datetime > $post_datetime) {
					
				self::upload_attachment($file_url, $unique_filename, $attachment->ID, $lang_code);
				
				self::log('attachment_id = '.$attachment->ID.' siunčiama atnaujinta versija iš serverio');
				
			} else {
				
				self::log('attachment_id = '.$attachment->ID.' jau turima naujausia versija, nedaroma nieko');
				
			}
			
			self::log('server file datetime: '.$file_modified_datetime.', local last modified: '.(empty($post_datetime) ? ' - ' : $post_datetime).', ');
			
			$attachment_id = $attachment->ID;
			
		} else {
			
		//	echo $unique_filename.' not found wp post';
		
			$attachment_id = self::upload_attachment($file_url, $unique_filename, null, $lang_code);
			
			self::log('siunčiama iš serverio ir sukuriamas naujas attachment_id = '.$attachment_id);
		
		}
		
		
		
		return $attachment_id;
		
		
	}
	
	
	
	
	public static function get_curl_contents($url) {
		
		$url = str_replace(' ', '%20', $url);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;

	}
	
	
	public static function upload_attachment($image_url, $filename, $update_attachment_post_id = null, $lang = null) {

		global $wpdb;
		
		if (self::is_wpml_mode()) {
			global $sitepress;
		}
		
		$upload_dir = wp_upload_dir();
		
		$image_data = self::get_curl_contents($image_url);
		
		$filename = empty($filename) ? basename($image_url) : $filename;

		if (wp_mkdir_p($upload_dir['path'])) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		
		file_put_contents($file, $image_data);
		
		$wp_filetype = wp_check_filetype($filename, null);

		$attachment = new stdClass();
		$attachment->post_type = 'attachment';
		$attachment->post_mime_type = $wp_filetype['type'];
		$attachment->name = $filename;
		$attachment->post_status = 'inherit';

		require_once(ABSPATH . 'wp-admin/includes/image.php');

		if (isset($update_attachment_post_id) && !empty($update_attachment_post_id)) {
			
			$attach_id = $update_attachment_post_id;
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);
			
			/*
			$guid = !empty($wp_filetype['type']) ? trim($upload_dir['url'], '/').'/'.$url.'/' : trim(get_site_url(), '/').'/'.$url.'/';
			
			self::post_update_field($update_attachment_post_id, 'post_type', 'attachment');
			self::post_update_field($update_attachment_post_id, 'post_mime_type', $wp_filetype['type']);
			self::post_update_field($update_attachment_post_id, 'guid', $guid);
			*/
			
		} else {

			$attach_id = self::_wp_insert_attachment( $attachment );
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);
			
			$subdir = trim($upload_dir['subdir'], '/');
			$attached_file = empty($subdir) ? $filename : $subdir.'/'.$filename;
			
			update_post_meta($attach_id, '_wp_attached_file', $attached_file);
			
			//1
			
			if (self::is_wpml_mode()) {
					
				$this_trid = self::get_max_trid() + 1;
				$source_lang = self::get_source_language();
				$this_source_lang = $source_lang != $lang ? $source_lang : '';
				$sitepress->set_element_language_details($attach_id, 'post_attachment', $this_trid, $lang, $this_source_lang);

			}
			
		}

		return $attach_id;
		
	}
	

	public static function _wp_insert_attachment($data) {
		
		return self::post_iu($data);
		
	}
	
	
	
	public static function import_articles() {

		
		if (get_option( 'disable_stock_import' ) != '1') {

			$stock_array = array();
			$stock = self::api_get_stock();
			
			if (is_array($stock) && count($stock) > 0)
			foreach ($stock as $stock_item) {
				$stock_array[$stock_item->ArticleId] = $stock_item->Quantity;
			}
			
		}
		
		
		$source_language = self::get_source_language();
		
		$current_product_ids = self::get_optimum_product_ids();
		$new_product_ids = [];
		
		
		
		$list = self::api_article_list();


		$active_languages = self::get_active_languages();
		
		
		
		
		$list_of_all_cats = self::api_category_list();
		$user_selected_cats = get_option('selected_cats');

		foreach ($list as $list_k => $list_item) {
			
	
			$product = new stdClass();
			
			$product->optimum_id = $list_item->Id;
			$product->sku = $list_item->Code;
			$product->regular_price = get_option( 'use_vat_price' ) == '1' ? $list_item->SlsUntPriceWithVat : $list_item->SlsUntPrice;
			$product->weight = $list_item->NetWeight;
			$product->vat_tariff = $list_item->VatTariff;
			
			
			
			
			if (get_option( 'disable_stock_import' ) != '1') {
				
				$product->_manage_stock = isset($stock_array[$product->optimum_id]) ? 'yes' : 'no';
				
				if ($product->_manage_stock == 'yes') {
					
					$product->_stock = isset($stock_array[$product->optimum_id]) && !empty($stock_array[$product->optimum_id]) ? $stock_array[$product->optimum_id] : '0';
					
					$product->_stock_status = $product->_stock > 0 ? 'instock' : 'outofstock';
	
				}
				
			}
			
			
			$product->files = self::api_article_files($list_item->Id);
			$product->categories = array();
			
			if (!empty($list_item->ArtGroupId)) $product->categories[] = $list_item->ArtGroupId;
			
			if (isset($list_item->AdtArtGroups->AdtArtGroup) && is_array($list_item->AdtArtGroups->AdtArtGroup) && count($list_item->AdtArtGroups->AdtArtGroup) > 0) {
				
				foreach ($list_item->AdtArtGroups->AdtArtGroup as $ArtGroup) {
					$product->categories[] = $ArtGroup->Id;
				}
				
			} else if (isset($list_item->AdtArtGroups->AdtArtGroup) && is_object($list_item->AdtArtGroups->AdtArtGroup) && isset($list_item->AdtArtGroups->AdtArtGroup->Id)) {
				
				$product->categories[] = $list_item->AdtArtGroups->AdtArtGroup->Id;
				
			}
			
			
			$filter_pcats = array();
			
			foreach ($product->categories as $pck => $pc_id) {
				
				//$yes = self::is_this_cat_part_of_selected_tree($list_of_all_cats, $pc_id);
				$yes = self::is_this_cat_one_of_the_selected_cats($list_of_all_cats, $pc_id);
				
				if ($yes) {
					$filter_pcats[] = $pc_id;
				}
				
			}
			
			
			
			$belongs_to_filtered = count($filter_pcats) > 0 || (count($filter_pcats) == 0 && (!is_array($user_selected_cats) || count($user_selected_cats) == 0));
			
			
			
			foreach ($active_languages as $language_code) {
				
				$product->lang = $language_code;
				$product->name = $language_code != $source_language ? $list_item->FllIntName : $list_item->FllName;
				
				$dest_language = self::get_dest_language($language_code);
				
				if (empty($list_item->FllIntName)) {
					
					$product->name = $list_item->FllName;
					
				} else {
					
					$product->name = $dest_language == 'int' ? $list_item->FllIntName : $list_item->FllName;
					
				}
				
				
				$product->custom_attributes = array();
				
				if (!empty($list_item->MeasureId)) {
					
					$measures = self::api_get_measures($list_item->MeasureId);

					if (isset($measures->Id)) {
						
						if (empty($measures->IntName)) {
							
							$measures_name = $measures->Name;
							
						} else {
							
							$measures_name = $dest_language == 'int' ? $measures->IntName : $measures->Name;
							
						}
										
						
						$product->custom_attributes['measure'] = array('name' => __('Packaging', 'woocommerce_optimumlt'), 'value' => $measures_name, 'is_visible' => 1, 'is_variation' => 0, 'is_taxonomy' => 0);
				
					}

				}
				
				if ($belongs_to_filtered) {
					
					$new_product_ids[] = self::product_iu($product);
					
				}
				
			}
			
		}
		
		
		
		
		
		
		
		
		$dead_product_ids = array_diff($current_product_ids, $new_product_ids);

		
		if (isset($dead_product_ids) && count($dead_product_ids) > 0) {
			
			foreach ($dead_product_ids as $dead_product_id) {
				
				self::log(date('Y-m-d H:i:s').' paslepiamas senas post_id = '.$dead_product_id);
				
				self::post_update_field($dead_product_id, 'post_status', 'pending');
				
			}
			
		}
		
		
		
		
		
		
		self::update_term_counts();
		


	}
	


	

	public static function import_client_prices() {
		
		$client_id = get_option( 'api_client_id' );
		
		if ($client_id > 0 == false) {
			
			_e('Nenurodytas kliento ID', 'woocommerce_optimumlt');
			
			return;
			
		}
		
		// $items = self::api_article_list();
		
		// print_r ($items);
		
		$client_prices = self::api_client_price_list($client_id);

		$active_languages = self::get_active_languages();
		
		foreach ($client_prices as $cp) {
			
			$discount = isset($cp->Discount) && $cp->Discount > 0 ? number_format($cp->Discount, 2, '.', '') : 0;
			
			$price = isset($cp->UntPrice) && $cp->UntPrice > 0 ? number_format($cp->UntPrice, 2, '.', '') : 0;
			
			if (get_option( 'use_vat_price' ) == '1') {
				
				$price = $price + ($price * $cp->VatTariff);
				
			}
			
			
			$price_after_discount = $price * (1 - $discount);

			_e('Prekės ID: ', 'woocommerce_optimumlt');
			echo $cp->ArticleId.', ';
			_e('Standartinė kaina: ', 'woocommerce_optimumlt');
			echo $price.', ';
			_e('Kaina po kliento nuolaidos: ', 'woocommerce_optimumlt');
			echo $price_after_discount;
			echo PHP_EOL.PHP_EOL;
			
			foreach ($active_languages as $language_code) {
				
				$post_id = self::get_product_id_by_optimum_id_and_lang($cp->ArticleId, $language_code);

				update_post_meta($post_id, '_regular_price', $price_after_discount);
				update_post_meta($post_id, '_price', $price_after_discount);
				
				update_post_meta($post_id, '_vat_tariff', isset($cp->VatTariff) ? number_format($cp->VatTariff, 2, '.', '') : '0');
				
				if ($cp->VatTariff >= 0.210) {
					update_post_meta($post_id, '_tax_class', '');
				} else if ($cp->VatTariff < 0.210 && $cp->VatTariff > 0.00) {
					update_post_meta($post_id, '_tax_class', 'reduced-rate');
				} else if ($cp->VatTariff < 0.01 || empty($cp->VatTariff)) {
					update_post_meta($post_id, '_tax_class', 'zero-rate');
				} else {
					update_post_meta($post_id, '_tax_class', '');
				}

					
			}
			
		
		}
		
		self::clean_wp_cache(); 
		
	}
	



	public static function update_term_counts() {
		
		global $wpdb;
		
		self::log(date('Y-m-d H:i:s').' atnaujinami kategorijų product count');
		
		$update_taxonomy = 'product_cat';
		
		$query = "SELECT term_id FROM ".$wpdb->term_taxonomy." WHERE taxonomy = 'product_cat' ";
		
		$taxonomy_ids = $wpdb->get_col( $query );
		
		wp_update_term_count_now($taxonomy_ids, $update_taxonomy);

	}
	
	
	
	
	
	
	
	
	public static function product_iu($data) {
		

		
		global $wpdb;
		
		if (self::is_wpml_mode()) {
			global $sitepress;
		}
		
		self::log(date('Y-m-d H:i:s').' Importuojamas produkto optimum_id = '.$data->optimum_id.', pavadinimas "'.$data->name.'", kalba "'.$data->lang.'"....');
		
		$post_id = self::get_product_id_by_optimum_id_and_lang($data->optimum_id, $data->lang);

		if (!empty($post_id)) {
			
			$data->ID = $post_id;
			self::post_iu($data);
			
			self::log('Atnaujintas post_id = '.$post_id, false);

		} else {
			
			$post_id = self::post_iu($data);
			
			self::log('Sukurtas naujas post_id = '.$post_id, false);
	
		}
		
		
		
		

		update_post_meta($post_id, '_optimum_product_id', $data->optimum_id);
		update_post_meta($post_id, '_optimum_lang', $data->lang);
		
		
		/*
			update_post_meta($post_id, '_product_version', '3.2.5');
			update_post_meta($post_id, '_stock', null);
			update_post_meta($post_id, '_tax_class', '');
			update_post_meta($post_id, '_backorders', 'no');
			update_post_meta($post_id, '_manage_stock', 'no');
			update_post_meta($post_id, '_sold_individually', 'no');
			update_post_meta($post_id, '_downloadable', 'no');
			update_post_meta($post_id, '_virtual', 'no');
		*/
		
		
		if (isset($data->_manage_stock))
		update_post_meta($post_id, '_manage_stock', $data->_manage_stock);
	
		if (isset($data->_stock))
		update_post_meta($post_id, '_stock', $data->_stock);
	
		if (isset($data->_stock_status))
		update_post_meta($post_id, '_stock_status', $data->_stock_status);
		
		update_post_meta($post_id, '_tax_status', 'taxable');
		update_post_meta($post_id, '_stock_status', 'instock');
		update_post_meta($post_id, '_sku', isset($data->sku) ? $data->sku : '');
		
		
		update_post_meta($post_id, '_product_attributes', $data->custom_attributes);
		
		update_post_meta($post_id, '_weight', isset($data->weight) ? number_format($data->weight, 3, '.', '') : '');
		update_post_meta($post_id, '_regular_price', isset($data->regular_price) ? number_format($data->regular_price, 2, '.', '') : '0');
		update_post_meta($post_id, '_price', isset($data->regular_price) ? number_format($data->regular_price, 2, '.', '') : '0');
		
		update_post_meta($post_id, '_vat_tariff', isset($data->vat_tariff) ? number_format($data->vat_tariff, 2, '.', '') : '0');
		
		if ($data->vat_tariff >= 0.210) {
			update_post_meta($post_id, '_tax_class', '');
		} else if ($data->vat_tariff < 0.210 && $data->vat_tariff > 0.00) {
			update_post_meta($post_id, '_tax_class', 'reduced-rate');
		} else if ($data->vat_tariff < 0.01 || empty($data->vat_tariff)) {
			update_post_meta($post_id, '_tax_class', 'zero-rate');
		} else {
			update_post_meta($post_id, '_tax_class', '');
		}

		
		self::log(', kaina = '.$data->regular_price, false);
		self::log(', VAT % = '.($data->vat_tariff * 100), false);

		
		
		// wpml
		
		if (self::is_wpml_mode()) {
			
				
			$optimum_trid = self::get_product_trid_by_optimum_id($data->optimum_id);
			
			if (empty($optimum_trid)) {
				
				$max_trid = self::get_max_trid();
				$this_trid = $max_trid + 1;
				
			} else {
				
				$this_trid = $optimum_trid;
				
			}
			
			
			
			$source_lang = self::get_source_language();
			$this_source_lang = $source_lang != $data->lang ? $source_lang : '';
			$sitepress->set_element_language_details($post_id, 'post_product', $this_trid, $data->lang, $this_source_lang);

		
		}
		
		
		
		
		$gallery_ids = array();
		
		$optimum_attachments = array();
		
		$content = "";
		
		
		
		self::log(', surasta priedų = '.count($data->files));
	
		

		if (isset($data->files) && count($data->files) > 0) {
			
			
			

			$optimum_timezone = self::get_optimum_timezone();
			$tz_obj = new DateTimeZone($optimum_timezone);
			$today = new DateTime("now", $tz_obj);
			$now_time = $today->format('Y-m-d').'T'.$today->format('H:i:s');


			$active_languages = self::get_active_languages();
			
			
			foreach ($data->files as $file) {
				
				if (!empty($file->FileName) && !empty($file->ArticleId)) {
					
					if (preg_match('/\.pdf$/i', $file->FileName) || preg_match('/\.doc$/i', $file->FileName) || preg_match('/\.zip$/i', $file->FileName) || preg_match('/\.rar$/i', $file->FileName)) {
						
						$filename_parts = explode('.', $file->FileName);
						$filename_parts_count = count($filename_parts);
						$filename_parts_lang_code = mb_strtolower($filename_parts[$filename_parts_count-2]);
						
						if (
							($filename_parts_count <= 2) ||
							($filename_parts_count >= 3 && in_array($filename_parts_lang_code, $active_languages) && $filename_parts_lang_code == $data->lang) || 
							($filename_parts_count >= 3 && !in_array($filename_parts_lang_code, $active_languages))
						) {
							
							$file_url = self::get_product_file_url($file->ArticleId, $file->FileName);
							$unique_filename = 'attachment-'.$file->ArticleId.'-'.$data->lang.'--'.sanitize_file_name($file->FileName);
							$attachment_id = self::handle_media_upload($file_url, $unique_filename, $file->DateTime, $data->lang);
							$optimum_attachments[] = $attachment_id;
							
						}

					} else if (preg_match('/descriptionshort\.'.$data->lang.'\.txt/i', $file->FileName)) {
						
						$file_url = self::get_product_file_url($file->ArticleId, $file->FileName);
						
						self::log('Apdorojamas priedas: '.$file_url.'...', false);
						
						$short_description_last_modified = get_post_meta($post_id, '_optimum_short_description_last_modified');
						if (is_array($short_description_last_modified)) $short_description_last_modified = $short_description_last_modified[0];
						
						self::log('server file datetime: '.$file->DateTime.', local last modified: '.(empty($short_description_last_modified) ? ' - ' : $short_description_last_modified).', ');

						if (empty($short_description_last_modified) || $file->DateTime > $short_description_last_modified) {

							$content = self::download_contents($file_url);
							//$content = iconv('Windows-1252', 'UTF-8', $content);
							$content = str_replace(PHP_EOL, PHP_EOL.PHP_EOL, $content);
							
							self::post_update_field($post_id, 'post_excerpt', $content);
							
							update_post_meta($post_id, '_optimum_short_description_last_modified', $now_time);
							
							self::log('siunčiama iš serverio ir atnaujinamas post_excerpt');
							
						} else {
							
							self::log('jau turima naujausia versija, nedaroma nieko');
							
						}

						
					} else if (preg_match('/description\.'.$data->lang.'\.txt/i', $file->FileName)) {
						
						$file_url = self::get_product_file_url($file->ArticleId, $file->FileName);
						
						self::log('Apdorojamas priedas: '.$file_url.'...', false);
						
						$description_last_modified = get_post_meta($post_id, '_optimum_description_last_modified');
						
						if (is_array($description_last_modified)) $description_last_modified = $description_last_modified[0];
						
						self::log('server file datetime: '.$file->DateTime.', local last modified: '.(empty($description_last_modified) ? ' - ' : $description_last_modified).', ');

						if (empty($description_last_modified) || $file->DateTime > $description_last_modified) {

							$content = self::download_contents($file_url);
						//	$content = iconv('Windows-1252', 'UTF-8', $content);
							$content = str_replace(PHP_EOL, PHP_EOL.PHP_EOL, $content);
							
							self::post_update_field($post_id, 'post_content', $content);
							
							update_post_meta($post_id, '_optimum_description_last_modified', $now_time);
							
							self::log('siunčiama iš serverio ir atnaujinamas post_content');
							
						} else {
							
							self::log('jau turima naujausia versija, nedaroma nieko');
							
						}
						
						
						
					} else if (preg_match('/\.jpg$/i', $file->FileName) || preg_match('/\.png$/i', $file->FileName)) {
					
						$file_url = self::get_product_file_url($file->ArticleId, $file->FileName);
						
						$unique_filename = sanitize_file_name('art-'.$file->ArticleId.'-'.$data->lang.'-'.$file->FileName);
						
						$attachment_id = self::handle_media_upload($file_url, $unique_filename, $file->DateTime, $data->lang);
						
						update_post_meta($post_id, '_thumbnail_id', $attachment_id);
						
						$gallery_ids[] = $attachment_id;

					}
					
				}
				
			
			}
			
		}
		
		
		
		
		
		$_meta_val = implode(',', $optimum_attachments);
		
		if (strlen($_meta_val) > 0) {

			update_post_meta($post_id, '_optimum_attachments', implode(',', $optimum_attachments) );
		
		} else {
			
			delete_post_meta($post_id, '_optimum_attachments' );
			
		}
		
		
		
		
		
		
		array_shift($gallery_ids);

		
		$_meta_val = implode(',', $gallery_ids);
		
		if (strlen($_meta_val) > 0) {
		
			update_post_meta($post_id, '_product_image_gallery', $_meta_val );
		
		} else {
		
			delete_post_meta($post_id, '_product_image_gallery' );
		
		}
		
		
		
		
	
		$term_ids = array();
		
		if (isset($data->categories) && is_array($data->categories) && count($data->categories) > 0) {
				
			foreach ($data->categories as $optimum_category_id) {
			
				$term_id = self::get_term_id_by_optimum_id_and_lang($optimum_category_id, $data->lang);
				
				if (!empty($term_id))
				$term_ids[] = (int)$term_id;

			}
			
		}
		
		wp_set_object_terms( $post_id, $term_ids, 'product_cat' );
		
	
		
		self::log('');
		
		return $post_id;
			
	}
		
		

		

	
	
	
	
	
	
	public static function download_contents($url) {
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$data = curl_exec($curl);
		curl_close($curl);
		
		return $data;
		
	}
	
	
	
	
	
	
	
	public static function the_slug_exists($post_name) {
			
		global $wpdb;
		
		if ($wpdb->get_row("SELECT post_name FROM ".$wpdb->posts." WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
			
			return true;
		
		} else {
			
			return false;
			
		}
	
	}
	
	
	public static function post_iu($data) {
		

		
		global $wpdb;
		
		$time = date('Y-m-d H:i:s');
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone('GMT'));
		$time_gmt = $date->format('Y-m-d H:i:s');
		
		$title = isset($data->name) ? $data->name : '';
		
		if (empty($title)) $title = $data->sku.' [no title] ['.$data->lang.']';
		
		$url = sanitize_title($title);
		
		$mime_type = isset($data->post_mime_type) ? $data->post_mime_type : '';

		$upload_dir = wp_upload_dir();
		
		$guid = !empty($mime_type) ? trim($upload_dir['url'], '/').'/'.$url.'/' : trim(get_site_url(), '/').'/'.$url.'/';
		
		
		
		if (!isset($data->ID) || empty($data->ID)) {
			
			$new = array(
			
				'post_author' => self::get_post_author_id(),
				
				'post_date' => $time,
				'post_date_gmt' => $time_gmt,
			//	'post_content' => isset($data->post_content) ? $data->post_content : '',
				'post_title' => $title,
				
				'post_status' => isset($data->post_status) ? $data->post_status : 'publish',
				'comment_status' => isset($data->comment_status) ? $data->comment_status : 'open',
				'ping_status' => isset($data->ping_status) ? $data->ping_status : 'closed',
				
				'post_name' => $url,
				'post_modified' => $time,
				'post_modified_gmt' => $time_gmt,
				
				'post_parent' => isset($data->post_parent) ? $data->post_parent : '0',
				'guid' => $guid,
				'menu_order' => 0,
				'post_type' => isset($data->post_type) ? $data->post_type : 'product',
				'post_mime_type' => $mime_type,
				'comment_count' => 0
				
			);
			
			$new_format = array(
			
				'%d',
				
				'%s',
				'%s',
			//	'%s',
				'%s',
				
				'%s',
				'%s',
				'%s',
				
				'%s',
				'%s',
				'%s',
				
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d'
				
			);
			
			$wpdb->insert( $wpdb->posts, 
				$new,
				$new_format
			);
		
			$id = $wpdb->insert_id;
			
			
		} else {
			
			$new = array(
			
				'post_author' => self::get_post_author_id(),
				
			//	'post_content' => isset($data->post_content) ? $data->post_content : '',
				'post_title' => $title,
				
				'post_status' => isset($data->post_status) ? $data->post_status : 'publish',
				'comment_status' => isset($data->comment_status) ? $data->comment_status : 'open',
				'ping_status' => isset($data->ping_status) ? $data->ping_status : 'closed',
				
				'post_name' => $url,
				'post_modified' => $time,
				'post_modified_gmt' => $time_gmt,
				
				'post_parent' => isset($data->post_parent) ? $data->post_parent : '0',
				'guid' => $guid,
				
				'post_type' => isset($data->post_type) ? $data->post_type : 'product',
				'post_mime_type' => $mime_type,

			);
			
			$new_format = array(
			
				'%d',
				
			//	'%s',
				'%s',
				
				'%s',
				'%s',
				'%s',
				
				'%s',
				'%s',
				'%s',
				
				'%d',
				'%s',
				
				'%s',
				'%s'
				
			);
			
			$update_where = array(
				'ID' => $data->ID
			);
			
			$update_where_format = array(
				'%d'
			);
			
			$wpdb->update( $wpdb->posts, 
				$new,
				$update_where,
				$new_format,
				$update_where_format
			);
		
			$id = $data->ID;
			
		}
		
		
		
		
		
		$exists = self::the_slug_exists($url);
		
		if ($exists && isset($id) && !empty($id)) {
				
			$url = $url.'-'.$id;

			$guid = !empty($mime_type) ? trim($upload_dir['url'], '/').'/'.$url.'/' : trim(get_site_url(), '/').'/'.$url.'/';

			$wpdb->update( $wpdb->posts , array( 'post_name' => $url, 'guid' => $guid ), array('ID' => $id), array('%s', '%s'), array('%d') );
			
			
			get_permalink( $id );
			
		}
		
		
		
		
		return $id;
		
	}

	
	public static function post_update_field($post_id, $key, $value) {
		
		global $wpdb;

		$q = $wpdb->prepare('UPDATE '.$wpdb->posts.' SET '.$key.' = %s WHERE ID = %d', $value, $post_id);

		$wpdb->query($q);
	
	}
	
	
	public static function get_term_id_by_optimum_id_and_lang($optimum_id, $lang) {
		
		global $wpdb;
		
		$query = $wpdb->prepare( " SELECT a1.term_id FROM ".$wpdb->prefix."termmeta a1 WHERE a1.term_id IN (
			
			SELECT a2.term_id FROM ".$wpdb->prefix."termmeta a2 WHERE a2.meta_key = '_optimum_category_id' AND a2.meta_value = %s
			
		) AND a1.meta_key = '_optimum_lang' AND a1.meta_value = %s ", $optimum_id, $lang );

		return $wpdb->get_var( $query );
		
	}
	
	
	public static function get_term_id_by_term_taxonomy_id($term_taxonomy_id) {
		
		global $wpdb;
		
		$query = $wpdb->prepare( " SELECT term_id FROM ".$wpdb->prefix."term_taxonomy WHERE term_taxonomy_id = %d ", $term_taxonomy_id );

		return $wpdb->get_var( $query );
		
	}
	
	public static function get_term_taxonomy_id($term_id, $taxonomy) {
		
		global $wpdb;
		
		$query = $wpdb->prepare( " SELECT term_taxonomy_id FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = %d AND taxonomy = %s ", $term_id, $taxonomy );

		return $wpdb->get_var( $query );
		
	}
	
	
	
	public static function get_category_trid_by_optimum_id($optimum_id) {
		
		global $wpdb;
		
		$query = $wpdb->prepare( " SELECT term_id FROM ".$wpdb->prefix."termmeta WHERE meta_key = '_optimum_category_id' AND meta_value = %d ", $optimum_id );
		$rows = $wpdb->get_results( $query );
		
		if (count($rows) > 0) {
			
			foreach ($rows as $rows_key => $rows_item) {
				
				$tt_id = self::get_term_taxonomy_id($rows_item->term_id, 'product_cat');
				
				
				$query = $wpdb->prepare( " SELECT trid FROM ".$wpdb->prefix."icl_translations WHERE element_type = 'tax_product_cat' AND element_id = %d ", $tt_id );
				$trid = $wpdb->get_var( $query );
				
				if (!empty($trid)) return $trid;
				
			}
			
		}
		
	}
	
	
	
	public static function get_product_trid_by_optimum_id($optimum_id) {
		
		global $wpdb;
		
		$query = $wpdb->prepare( " SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_optimum_product_id' AND meta_value = %d ", $optimum_id );
		
		$rows = $wpdb->get_results( $query );

		if (count($rows) > 0) {

			foreach ($rows as $rows_key => $rows_item) {

				$query = $wpdb->prepare( " SELECT trid FROM ".$wpdb->prefix."icl_translations WHERE element_type = 'post_product' AND element_id = %d ", $rows_item->post_id );
				
				$trid = $wpdb->get_var( $query );
				
				if (!empty($trid)) return $trid;
				
			}
			
		}
		
	}
	
	
	public static function get_max_trid() {
		
		global $wpdb;
		
		$query =  " SELECT MAX(trid) FROM ".$wpdb->prefix."icl_translations " ;

		return $wpdb->get_var( $query );
		
	}
	
	
	
	
	
	
	
	public static function get_optimum_term_ids() {
		
		global $wpdb;
		
		$query =  " SELECT term_id FROM ".$wpdb->prefix."termmeta WHERE meta_key = '_optimum_category_id' AND meta_value <> '' AND meta_value IS NOT NULL " ;

		return $wpdb->get_col( $query );
		
	}
	
	
	
	public static function get_optimum_product_ids() {
		
		global $wpdb;
		
		$query = " SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_optimum_product_id' AND meta_value <> '' AND meta_value IS NOT NULL " ;

		return $wpdb->get_col( $query );
		
	}
	
	

	
	
	
	
	
	public static function get_api_article_file_dir(){
		
		if (empty(get_option( 'api_article_file_dir'))) {
			
			return 'http://wa.optimum.lt/optimum_demo_lt';
			
		} else {
			
			return get_option( 'api_article_file_dir' );
			
		}
		
	}
	
	
	public static function get_product_file_url($product_id, $filename){

		return trim(self::get_api_article_file_dir(), '/').'/'.$product_id.'/'.$filename;

	}
	
	
	public static function get_api_category_file_dir(){
		
		if (empty(get_option( 'api_category_file_dir' ))) {
			
			return 'http://wa.optimum.lt/optimum_demo_lt_2';
			
		} else {
			
			return get_option( 'api_category_file_dir' );
			
		}

	}
	
	
	
	public static function get_order_prefix(){
		
		if (empty(get_option( 'order_prefix' ))) {
			
			return 'ESHOP-';
			
		} else {
			
			return get_option( 'order_prefix' );
			
		}

	}
	
	
	
	
	
	
	public static function get_category_file_url($category_id, $filename){

		return trim(self::get_api_category_file_dir(), '/').'/'.$category_id.'/'.$filename;

	}
	
	
	
	
	
	
	
	
	
	//
	//
	//
	
	
	public static function get_product_id_by_optimum_id_and_lang($optimum_id, $lang) {
		
		global $wpdb;
		
		$query = $wpdb->prepare( " SELECT a1.post_id FROM ".$wpdb->prefix."postmeta a1 WHERE a1.post_id IN (
			
			SELECT a2.post_id FROM ".$wpdb->prefix."postmeta a2 WHERE a2.meta_key = '_optimum_product_id' AND a2.meta_value = %s
			
		) AND a1.meta_key = '_optimum_lang' AND a1.meta_value = %s ", $optimum_id, $lang );

		return $wpdb->get_var( $query );
		
	}
	
	
	
	
	
	
	
	
	
	public static function is_wpml_mode() {
		
		return function_exists('icl_object_id');
		
	}
	
	
	
	public static function get_source_language() {
		
		if (self::is_wpml_mode()) {
					
			$wpml_options = get_option( 'icl_sitepress_settings' );

			return $wpml_options['default_language'];
									
		} else {
		
			return self::get_non_wpml_language_code();
		
		}
		
	}
	
	
	
	public static function get_dest_language($source_lang_code) {

		$saved_matrix = get_option('language_matrix');
		
		if (isset($saved_matrix[$source_lang_code]) == false || empty($saved_matrix[$source_lang_code])) {
			
			if ($source_lang_code == 'lt') {
				return 'main';
			} else {
				return 'int';
			}
			
		} else {
			
			return $saved_matrix[$source_lang_code];
			
		}
		
	}
	
	
	public static function get_non_wpml_language_code() {

		$locale = str_replace('-', '_', get_bloginfo("language"));
		
		if ($locale == 'en_US') return 'en';
		
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		
		$translations = wp_get_available_translations();
		
		return $translations[$locale]['iso'][1];
		
	}
	
	
	
	public static function get_non_wpml_language_name() {

		$locale = str_replace('-', '_', get_bloginfo("language"));
		
		if ($locale == 'en_US') return 'English';
		
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		
		$translations = wp_get_available_translations();
		
		return $translations[$locale]['native_name'];
		
	}
	
	
	
	
	public static function get_post_author_id() {
		
		global $wpdb;
		
		$id = $wpdb->get_var( 'SELECT ID FROM '.$wpdb->users.' ORDER BY ID ASC LIMIT 1');
		
		return $id;
		
	}
	
	
	
	public static function get_active_languages() {
		
		$list = array();
		
		if (self::is_wpml_mode()) {
			
			foreach (icl_get_languages() as $lang) {
				
				$list[] = $lang['code'];
				
			}
			
		} else {
			
			$list[] = self::get_non_wpml_language_code();
			
		}
		
		return $list;
		
	}
	
	

	
		

	

	public static function crontask() {
		
		if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) {
			
			echo 'Starting crontab...'.PHP_EOL;
			
			self::import_all();
			
		}
		
	}

	
	
	public static function setup_cron() {
		
		wp_clear_scheduled_hook( 'woocommerce_optimum_task' );
		
		if ( !wp_next_scheduled('woocommerce_optimum_task') ) {
			
			$hour = get_option('cron_hour');
			
			if (!empty($hour)) {

				if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) {
					
					if (date('Y-m-d').' '.$hour.':00:00' < date('Y-m-d H:i:s')) {
						
						$time = date('Y-m-d', strtotime(' +1 day')).' '.$hour.':00:00';
				
					} else {
						
						$time = date('Y-m-d').' '.$hour.':00:00';
						
					}
					
					$user_timezone = new DateTimeZone(get_option('timezone_string'));
					$datetime = new DateTime($time, $user_timezone);
					$utc_timezone = new DateTimeZone('UTC');
					$datetime->setTimezone($utc_timezone);
					$time = $datetime->format('Y-m-d H:i:s');
					
					$timestamp = strtotime($time);
					
					wp_schedule_event( $timestamp, 'daily', 'woocommerce_optimum_task' );
					
				}
				
			}
			
		}

	}
	
	
	
	
	
	
	
	public static function register_optimum_order($data) {


		
		$order_info = array(
		
			'orderNo' => $data['order_no'],
			
			'notes' => $data['notes'],
			
			'pmnMethod' => $data['pmnMethod'],
			'dlvMethod' => $data['dlvMethod'],

			'cstFrsName' => $data['billing']['name'],
			'cstLstName' => $data['billing']['lastname'],
			'cstEMail' => $data['billing']['email'],
			'cstPhone' => $data['billing']['phone'],
			'cstAddress' => $data['billing']['address'],
			'cstPstCode' => $data['billing']['post_code'],
			'cstCtName' => $data['billing']['city'],
			'cstCountryId' => $data['billing']['country_code'],
			
			
			'invCmpCode' => $data['billing']['company_code'],
			'invCmpVatCode' => $data['billing']['vat_id'],
			'invCmpName' => $data['billing']['company_name'],
			'invCmpAddress' => $data['billing']['address'],
			'invCmpPstCode' => $data['billing']['post_code'],
			'invCmpCtName' => $data['billing']['city'],
			'invCmpCountryId' => $data['billing']['country_code'],
					
			'rcvFrsName' => $data['shipping']['name'],
			'rcvLstName' => $data['shipping']['lastname'],
			'rcvEMail' => $data['shipping']['email'],
			'rcvPhone' => $data['shipping']['phone'],
			'dlvAddress' => $data['shipping']['address'],
			'dlvPstCode' => $data['shipping']['post_code'],
			'dlvCtName' => $data['shipping']['city'],
			'dlvCountryId' => $data['shipping']['country_code'],
			'dlvNotes' => $data['shipping']['notes'],
			
			'articles' => $data['articles']
			
		);
		
		
		if (empty($order_info['invCmpName']) || empty($order_info['invCmpCode'])) {
			
			unset($order_info['invCmpCode']);
			unset($order_info['invCmpVatCode']);
			unset($order_info['invCmpName']);
			unset($order_info['invCmpAddress']);
			unset($order_info['invCmpPstCode']);
			unset($order_info['invCmpCtName']);
			unset($order_info['invCmpCountryId']);
			
		}
		
		if ($data['dlvMethod'] == 'Delivery') {
			
			$settings = self::api_get_settings();
			
			$order_info['crrCode'] = $settings->CmpCrrCode;
			
			
		} else if ($data['dlvMethod'] == 'PickUp') {
			

			
			$order_info['pckUpStoreId'] = self::get_main_store_for_pick_up();
			
			unset($order_info['dlvAddress'], $order_info['dlvPstCode'], $order_info['dlvCtName'], $order_info['dlvCountryId'], $order_info['dlvNotes']);
			
		}
		
	

		return self::api_register_order($order_info);
		
	}
	
	
	
	
	
	
	
	
	public static function get_main_store_for_pick_up() {

		$stores = self::api_store_list();
		
		$id = 1;
		
		if (isset($stores) && count($stores) > 0) {
		
			foreach ($stores as $store) {
				
				if (preg_match('/parduotuv/i', $store->Name)) {
					
					$id = $store->Id;
					
				} 
				
			}
		
		}
		
		return $id;
		
	}
	
	
	
	
	public static function test() {
		
		global $wpdb;
		
		$list = self::api_category_list();
		
		print_r ($list);
		
		$ids = self::get_this_tree_cat_ids($list, 313);
		
		print_r ($ids);
		
		$cats = self::filter_out_cats($list, get_option('selected_cats'));
		
		print_r ($cats);
		
		
		
		$is_this_part_if = self::is_this_cat_part_of_selected_tree($list, 308);
		
		echo $is_this_part_if ? 'yes' : 'no';
		
		
		
		
			
//		$list = self::get_list_of_parent_cats();

//		print_r ($list);
		
		/*
		$sel_cats = self::filter_out_cats($list, get_option('selected_cats'));
		
		print_r ($sel_cats);
		*/
		
		die();
		
	}
	
	

	
	
	public static function send_ajax_order() {
		
		echo json_encode(self::send_order((int)$_GET['order_id']));
		
		die();
		
	}
	
	
	
	
	
	
	
	
	
	
	public function send_order($order_id) {
		
		$order = new WC_Order($order_id);
		
		$order_data = $order->get_data();
		
		$order_meta = get_post_meta($order_id);
		
		$articles = $order->get_items();
		
	
		
		if ($order_data['payment_method'] == 'cod') {
			$pmnMethod = 'COD';
		}
		
		else if ($order_data['payment_method'] == 'bacs') {
			$pmnMethod = 'PaymentOrder';
		} 
		
		else {
			$pmnMethod = 'BankLink';
		}
			
		
		$o = array(
		
			'order_no' => self::get_order_prefix().str_pad($order_data['id'], 8, "0", STR_PAD_LEFT),
			
			'notes' => $order_data['customer_note'],
			
			// 'notes' => '',
			
			'pmnMethod' => $pmnMethod,
			
			'customer' => array(
				
				'email' => $order_data['billing']['email'],
				'phone' => $order_data['billing']['phone'],
				
				'name' => $order_data['billing']['first_name'],
				'lastname' => $order_data['billing']['last_name'],
				
				'company_name' => $order_data['billing']['company'],
				'company_code' => isset($order_meta['_billing_company_code'][0]) ? $order_meta['_billing_company_code'][0] : '',
				'vat_id' => isset($order_meta['_billing_vat_id'][0]) ? $order_meta['_billing_vat_id'][0] : '',
				
				'address' => $order_data['billing']['address_1'].(isset($order_data['billing']['address_2']) && !empty($order_data['billing']['address_2']) ? ', '.$order_data['billing']['address_2'] : ''),
				'city' => $order_data['billing']['city'].(isset($order_data['billing']['state']) && !empty($order_data['billing']['state']) ? ', '.$order_data['billing']['state'] : ''),
				'post_code' => $order_data['billing']['postcode'],
				'country_code' => $order_data['billing']['country']

			),
			
			'shipping' => array(
			
				'name' => $order_data['shipping']['first_name'],
				'lastname' => $order_data['shipping']['last_name'],
				
				'company_name' => $order_data['shipping']['company'],
				
				'address' => $order_data['shipping']['address_1'].(isset($order_data['shipping']['address_2']) && !empty($order_data['shipping']['address_2']) ? ', '.$order_data['shipping']['address_2'] : ''),
				'city' => $order_data['shipping']['city'].(isset($order_data['shipping']['state']) && !empty($order_data['shipping']['state']) ? ', '.$order_data['shipping']['state'] : ''),
				'post_code' => $order_data['shipping']['postcode'],
				'country_code' => $order_data['shipping']['country'],
				
				'phone' => isset($order_meta['_shipping_phone'][0]) ? $order_meta['_shipping_phone'][0] : '',
				'email' => isset($order_meta['_shipping_email'][0]) && $order_meta['_shipping_email'][0] != '' ? $order_meta['_shipping_email'][0] : $order_data['billing']['email'],
				
				'notes' => $order_data['customer_note']
				
			),
			
			'billing' => array(
			
				'name' => $order_data['billing']['first_name'],
				'lastname' => $order_data['billing']['last_name'],
				
				'company_name' => $order_data['billing']['company'],
				'company_code' => isset($order_meta['_billing_company_code'][0]) ? $order_meta['_billing_company_code'][0] : '',
				'vat_id' => isset($order_meta['_billing_vat_id'][0]) ? $order_meta['_billing_vat_id'][0] : '',
				
				'address' => $order_data['billing']['address_1'].(isset($order_data['billing']['address_2']) && !empty($order_data['billing']['address_2']) ? ', '.$order_data['billing']['address_2'] : ''),
				'city' => $order_data['billing']['city'].(isset($order_data['billing']['state']) && !empty($order_data['billing']['state']) ? ', '.$order_data['billing']['state'] : ''),
				'post_code' => $order_data['billing']['postcode'],
				'country_code' => $order_data['billing']['country'],
				
				'email' => $order_data['billing']['email'],
				'phone' => $order_data['billing']['phone']
				
			),
			
			'articles' => array()
			
		);
		
		

	
	
	
	
		if (isset($articles) && count($articles) > 0) {
			
			foreach ($articles as $article_key => $article) {
				
				$item_data = $article->get_data();

				$article_meta = get_post_meta($article['product_id']);
				
				$unit_price = $item_data['subtotal'] / $item_data['quantity'];
				$vat_tariff = $item_data['subtotal_tax'] / $item_data['subtotal'];
				
				$ext_price = $item_data['subtotal'] + $item_data['subtotal_tax'];
				
				$a = array(
					
					'ArticleId' => $article_meta['_optimum_product_id'][0],
					'Quantity' => number_format($item_data['quantity'], 2, '.', ''),
					'UntPrice' => number_format($unit_price, 2, '.', ''),
					'Discount' => number_format(0.00, 2, '.', ''),
					'VatTariff' => number_format($vat_tariff, 2, '.', ''),
					'ExtPrice' => number_format($ext_price, 2, '.', '')
					
				);
				
				$o['articles'][] = $a;
				
			}
		
		}
		
		
		
		
		
		
		
		if ( $order->has_shipping_method('local_pickup') ) { 
			
			$o['dlvMethod'] = 'PickUp';
			
		} else if ( $order->has_shipping_method('free_shipping') ) { 
			
			$o['dlvMethod'] = 'Delivery';
			
		} else {
			
			$o['dlvMethod'] = 'Delivery';
			
			$settings = self::api_get_settings();
			
			$аrticle_sku = $settings->DlvArticleId;
			
			$current_shipping_cost = $order->get_total_shipping();
			
			$current_shipping_tax = $current_shipping_cost > 0 ? $order->get_shipping_tax() / $current_shipping_cost : 0;
	
			$delivery_item = array(
				
				'ArticleId' => $аrticle_sku,
				'Quantity' => 1,
				'UntPrice' => number_format($current_shipping_cost, 2, '.', ''),
				'Discount' => number_format(0.00, 2, '.', ''),
				'VatTariff' => number_format($current_shipping_tax, 2, '.', ''),
				'ExtPrice' => number_format($current_shipping_cost * 1.21, 2, '.', '')
				
			);
		
			$o['articles'][] = $delivery_item;
		
		}
		

		
		
		$api_result = self::register_optimum_order($o);
		
		update_post_meta($order_id, '_order_sent_to_optimum', $api_result['status']);
		
		return $api_result;
		

		
		
	}
	
	
	
	public static function get_optimum_timezone(){

		return 'Europe/Vilnius';

	}
	
	
	
	
	

	
	
	public static function clean_wp_cache() {
		
		global $wpdb;

		self::log('wp_cache_flush()...');
		wp_cache_flush();
		
		self::log('clean_taxonomy_cache()...');
		clean_taxonomy_cache('product_cat');
		
		self::log('deleting options...');
		$wpdb->query( ' DELETE FROM '.$wpdb->options.' WHERE option_name LIKE "product_cat_children_%" ');
		
		
		self::log('deleting transients...');
				
		$wpdb->query( " DELETE FROM ".$wpdb->options." WHERE `option_name` LIKE ('_transient_%') ") ;
		
		$wpdb->query( " DELETE FROM ".$wpdb->options." WHERE `option_name` LIKE ('_site_transient_%') " );
	
	
	
	
	
	
		self::log('fixing wpml counts...');



		foreach ( get_taxonomies( array(), 'names' ) as $taxonomy ) {

			$terms_objects = get_terms( $taxonomy, 'hide_empty=0'  );
			if ( $terms_objects ) {
				$term_taxonomy_ids = array_map( 'get_term_taxonomy_id_from_term_object', $terms_objects );
				wp_update_term_count( $term_taxonomy_ids, $taxonomy, true );
			}

		}

	
	
	
	
		self::log('');
		
	}
	
	

	public static function log($string, $new_line = true) {
		
		echo $string.( $new_line ? PHP_EOL : null );
		
	}
	
}


WooCommerceOptimumLt::init();

add_action( 'woocommerce_order_status_processing', 'woocommerce_new_order_optimumlt_hook', 10, 1);
//add_action( 'woocommerce_order_status_pending', 'woocommerce_new_order_optimumlt_hook', 10, 1);
add_action( 'woocommerce_order_status_completed', 'woocommerce_new_order_optimumlt_hook', 10, 1);
add_action( 'woocommerce_order_status_on-hold', 'woocommerce_new_order_optimumlt_hook', 10, 1);
add_action( 'woocommerce_order_status_on_hold', 'woocommerce_new_order_optimumlt_hook', 10, 1);


function woocommerce_new_order_optimumlt_hook($order_id){
	
	WooCommerceOptimumLt::send_order($order_id);
	
}


register_deactivation_hook(__FILE__, function(){
	
	wp_clear_scheduled_hook( 'woocommerce_optimum_task' );
	
});


register_activation_hook(__FILE__, function(){
	
	WooCommerceOptimumLt::register_action();
	
});


add_filter( 'woocommerce_admin_order_actions', 'add_optimum_lt_order_buttons', 100, 2 );

function add_optimum_lt_order_buttons( $actions, $order ) {
	
  //  if ( $order->has_status( array( 'processing' ) ) ) {
        
		$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
		
        $actions['parcial'] = array(
            'url'       =>  admin_url( 'admin-ajax.php?action=woocommerce_optimumlt_send_ajax_order&order_id=' . $order_id ) ,
            'name'      => __( 'Persiųsti užsakymą į Optimum.lt sistemą', 'woocommerce' ),
            'action'    => "view sendtooptimumlt"
        );
		
   // }
    
	return $actions;
	
}

add_action( 'admin_head', 'add_optimum_lt_order_buttons_head' );

function add_optimum_lt_order_buttons_head() {
	
	?>
	
	<style>
	
	.view.sendtooptimumlt::after { font-family: woocommerce; content: "OP" !important; }

	.widefat .column-order_actions, .widefat .column-user_actions, .widefat .column-wc_actions {
		width: 125px;
	}
	
	</style>
	
	<script>
	
		jQuery(document).ready(function($){
					
			$('.sendtooptimumlt').click(function(ev){
				
				console.log('Send to Optimum.lt...');

				ev.preventDefault();
			
				var _href = $(this).attr('href');

				$.get(_href, {}, function(response) {
					
					console.log(response);
					
					if (response.status == false) {
						alert(response.message);
						console.log(response.message);
					} else {
						alert('Order sent!');
					}
					
					console.log('sent!');
					
				}, 'json');
				
			});
		
		});
					
	</script>
	
	<?php
					
}


?>