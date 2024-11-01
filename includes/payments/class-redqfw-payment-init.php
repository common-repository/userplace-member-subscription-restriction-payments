<?php

/**
 * Userplace Payment Handler for WordPress
 */

namespace Userplace;

use RedQ\Payment\Billing;
use Userplace\Provider;
use Exception;

class Payment_Init
{
	use Payment_Info;

	public $gateway;

	public $credentials;

	public $billing;

	public $customerId = false;

	public $cardId = false;

	public $subscriptionId = false;

	public $formDirectory = USERPLACE_DIR;

	public $userId;

	public $customerPlanId;

	public $userEmail;


	public function __construct()
	{
		add_action('init', array($this, 'init'));
		add_action('userplace_webhooks', array($this, 'init'));
	}

	/**
	 * Initialize payment processing
	 * @method init
	 * @return void initiate interface based class & rendering a shortcode
	 */
	public function init()
	{
		$this->gateway 			   = $this->get_payment_gateway();
		if ($this->gateway != 'false') {
			$this->userId 			   = get_current_user_id();
			$this->userEmail 			 = $this->get_user_info('email');
			$this->customerId      = $this->get_user_info('userplace_customer_id');
			$this->subscriptionId  = $this->get_user_info('userplace_subscription_id');
			$this->customerPlanId  = $this->get_user_info('userplace_customer_plan_id');
			$this->customerStatus  = $this->get_user_info('userplace_status');
			$this->credentials 	   = $this->get_payment_gateway_credentials($this->gateway);
			$gateWayClass = "RedQ\\Payment\\" . ucfirst($this->gateway) . 'Gateway';
			$this->billing = new Billing(new $gateWayClass($this->credentials));
			add_shortcode('userplace_payment', array($this, 'paymentForm'));
			add_shortcode('userplace_billing', array($this, 'billingPage'));
			add_shortcode('userplace_single_payment', array($this, 'singlePaymentForm'));
			add_shortcode('userplace_list_cards', array($this, 'listCards'));
			add_shortcode('userplace_remaining_quota', array($this, 'remainingQuota'));
		}
	}


	/**
	 * Shortcode: Payment form
	 * @method paymentForm
	 * @return html different payment gateway form
	 */
	public function paymentForm($atts)
	{
		try {
			if ($_POST) {
				if (isset($_POST['userplace_free_plan'])) {
					$this->handleBuyingFreePlan();
					return;
				}
				if (isset($_POST['redq_payment_form'])) {
					$this->paymentHandler($_POST);
				}
				return;
			}

			$languages = array(
				'panelLabel' => esc_html__('Buy This Plan', 'userplace'),
				'label' => esc_html__('Buy This Plan', 'userplace'),
				'locale' => 'auto',
			);
			extract(shortcode_atts(array(
				'plan' => null,
			), $atts));
			if (is_user_logged_in()) {
				if ($plan != null && $plan != 'free') {
					$planDetails = $this->billing->getPlan($plan);
					if (isset($planDetails->id)) {
						$plandata = array(
							"id"         			=> $planDetails->id,
							"amount"        	=> number_format($planDetails->amount / 100),
							"currency"      	=> $planDetails->currency,
							"interval"      	=> $planDetails->interval,
							"interval_count"	=> $planDetails->interval_count,
							"livemode"      	=> $planDetails->livemode,
							"name"          	=> $planDetails->nickname,
						);

						echo '<div class="up-userplace-plan-page"><div class="up-userplace-plan-card">';

						include_once($this->formDirectory . '/includes/payments/pay/plan.php');
						$differentPlan = false;

						if ($this->customerId) {
							$customerId = $this->customerId;
							if ($plan == $this->customerPlanId && strtolower($this->customerStatus) == 'active') {
								echo '<h3 class="up-userplace-plan-alert">' . esc_html__('Congrats! You are already subscribed to current plan.', 'userplace') . '</h3>';
							} else if ($plan != $this->customerPlanId && strtolower($this->customerStatus) == 'active' && $this->customerPlanId != 'free') {
								$differentPlan = true;
								if ($this->gateway === 'stripe') {
									$default_card = $this->billing->defaultCard($this->customerId);
									if (isset($default_card->default_source->id)) {
										return '<form class="freePlanButton" method="post">
													<input type="hidden" name="redq_payment_form" value="1">
													<input type="hidden" name="redq_payment_plan" value="' . esc_attr($plan) . '">
													<button type="submit" name="button" class="userplace-pay-button">' . esc_html__('Switch Now', 'userplace') . '</button>
												</form></div></div>';
									}
								}
								echo apply_filters('payment_form', $this->billing->showPaymentForm($plan, $languages, $customerId));
							} else if (($this->customerPlanId == 'free' && $plan != $this->customerPlanId) || strtolower($this->customerStatus) != 'active') {
								return apply_filters('payment_form_free', $this->billing->showPaymentForm($plan, $languages));
							}
						} else {
							echo apply_filters('payment_form_general', $this->billing->showPaymentForm($plan, $languages));
						}
						echo '</div></div>';
					}
				} else if ($plan == 'free') {
					if ($plan != $this->customerPlanId) { ?>
						<form class="freePlanButton" method="post">
							<input type="hidden" name="userplace_free_plan" value="1">
							<button type="submit" name="button" class="userplace-pay-button"><?php esc_html_e('Start Free Plan', 'userplace') ?></button>
						</form>
				<?php } else if ($plan == $this->customerPlanId && strtolower($this->customerStatus) == 'active') {
						echo '<div class="userplace-check-ok"><p>'
							. esc_html__('You are already subscribed to ', 'userplace') . esc_html($plan) . esc_html__(' Plan', 'userplace') .
							'</p></div>';
					}
				} else {
					echo '<div class="userplace-check-ok">
						<p>' . esc_html__('Please select a plan before enter this page', 'userplace') . '</p>
					</div>';
				}
			} else {
				echo '<div class="userplace-check-ok">
				<p>' . esc_html__('please login first to buy this plan', 'userplace') . '</p>
				</div>';
			}
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}
	/**
	 * Shortcode: Single Payment form
	 * @method singlePaymentForm
	 * @return html different payment gateway form
	 */
	public function singlePaymentForm($atts)
	{
		try {
			extract(shortcode_atts(array(
				'amount' => null,
				'currency' => '$',
			), $atts));
			echo '<div class="pay-as-go-block">';
			if ($amount != null) { ?>
				<p><?php esc_html_e('This is a pay as you go process and you are going to charge', 'userplace') ?> <span><?php echo esc_html($currency . '' . $amount); ?></span></p>
				<?php
				if (isset($_POST['userplace_single_payment_form'])) {
					if (isset($_POST['userplace_single_payment_form'])) {
						if ($this->gateway === 'stripe') {
							$paymentMethodNonce = sanitize_text_field($_POST['stripeToken']);
						} else if ($this->gateway === 'braintree') {
							$paymentMethodNonce = sanitize_text_field($_POST['payment_method_nonce']);
						}
						if ($this->customerId != 'free' && $this->customerId) {
							$this->billing->createSingleTransaction($amount * 100, $paymentMethodNonce, $this->customerId);
						} else {
							$this->billing->createSingleTransaction($amount * 100, $paymentMethodNonce);
						}
						if (isset($_GET['listing_id'])) {
							$post_id = intval($_GET['listing_id']);
							$post = get_post($post_id);
							if (isset($post->ID)) {
								$listing_data_array = array(
									'ID' => $post->ID,
									'post_status' => 'publish'
								);
								wp_update_post($listing_data_array);
								if ($post->ID) {
									update_post_meta($post->ID, 'is_pay_as_u_go', 'true');
									$redirect_page_url = site_url() . '/console/submission-restrictions/fourth-step/?listing_id=' . $post->ID . '&plan_type=pay_as_go';
									userplace_redirect($redirect_page_url);
								}
							}
						} else {
							$redirect_page_id = userplace_get_settings('successful_payment_redirection');
							if ($redirect_page_id && is_numeric($redirect_page_id)) {
								$url = get_permalink($redirect_page_id);
								userplace_redirect($url);
							}
							userplace_redirect(site_url());
						}
					}
				}
			} else {
				wp_die(esc_attr__('Opps!! Amount can not be null or less that 20 cent.', 'userplace'));
			}

			$languages = array(
				'panelLabel' => esc_html__('Pay Now', 'userplace'),
				'label' => esc_html__('Click here to pay', 'userplace'),
				'locale' => 'auto',
			);
			if ($this->customerId == 'free') {
				return esc_html($this->billing->showSinglePaymentForm($languages, null));
			} else {
				return esc_html($this->billing->showSinglePaymentForm($languages, $this->customerId));
			}
			echo '</div>';
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}

	public function handleBuyingFreePlan()
	{
		try {
			update_user_meta($this->userId, 'userplace_customer_id', 'free');
			update_user_meta($this->userId, 'userplace_customer_plan_id', 'free');
			update_user_meta($this->userId, 'userplace_subscription_id', 'free');
			update_user_meta($this->userId, 'userplace_price', 'free');
			update_user_meta($this->userId, 'userplace_status', 'active');
			update_user_meta($this->userId, 'userplace_expired_at', 'free');
			update_user_meta($this->userId, 'userplace_start_at', 'free');
			update_user_meta($this->userId, 'userplace_interval_in_month', 'free');
			if (isset($_GET['listing_id'])) {
				$post_id = intval($_GET['listing_id']);
				$post = get_post($post_id);
				if (isset($post->ID) && $post->ID) {
					$redirect_page_url = site_url() . '/console/submission-restrictions/fourth-step/?listing_id=' . $post->ID;
					userplace_redirect($redirect_page_url);
				}
			} else {
				$redirect_page_id = userplace_get_settings('successful_payment_redirection');
				if ($redirect_page_id && is_numeric($redirect_page_id)) {
					$url = get_permalink($redirect_page_id);
					userplace_redirect($url);
				}
				userplace_redirect(site_url());
			}
			exit;
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}

	public function getPaymentData($payload)
	{
		try {
			$paymentToken   	= false;
			$paymentEmail   	= false;
			$paymentPlanId  	= false;
			$existingCustomerId = false;

			if (isset($payload['stripeToken'])) {
				$paymentToken = $payload['stripeToken'];
			}
			if (isset($payload['payment_method_nonce'])) {
				$paymentToken = $payload['payment_method_nonce'];
			}

			$paymentEmail  = isset($payload['stripeEmail']) ? $payload['stripeEmail']                   : $this->userEmail;
			$paymentPlanId = isset($payload['redq_payment_plan']) ? $payload['redq_payment_plan']       : false;

			return [
				'token'       => $paymentToken,
				'email'       => $paymentEmail,
				'plan'        => $paymentPlanId,
			];
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}


	public function paymentHandler($payload)
	{
		try {
			$paymentData = $this->getPaymentData($payload);
			extract($paymentData);
			$coupon_id = null;
			$provider = new Provider();
			$restriction_details = $provider->get_plan_restrictions($plan);
			if (isset($restriction_details['general']) && isset($restriction_details['general']['userplace_coupon_post_id']) && $restriction_details['general']['userplace_coupon_post_id'] != 'no_coupon') {
				$coupon_id = userplace_get_coupon_id($restriction_details['general']['userplace_coupon_post_id']);
			}
			$customer = $this->billing->create(
				$token,
				$email,
				$plan,
				($this->subscriptionId && $this->subscriptionId != 'free') ? $this->subscriptionId : false,
				($this->customerId && $this->customerId != 'free') ? $this->customerId : false,
				'',
				$coupon_id
			);
			if (isset($customer['customerId'])) {
				if ($this->gateway === 'braintree') {
					userplace_save_invoices_to_db($customer);
				}
				if (isset($customer['card_details'])) {
					$default_card = null;
					if ($this->gateway === 'stripe') {
						$default_card = $this->billing->defaultCard($customer['customerId']);
					}
					userplace_save_cards_to_db($customer['card_details'], $default_card);
				}
				update_user_meta($this->userId, 'userplace_customer_id', sanitize_text_field($customer['customerId']));
				update_user_meta($this->userId, 'userplace_customer_plan_id', sanitize_text_field($customer['planId']));
				update_user_meta($this->userId, 'userplace_customer_plan_name', sanitize_text_field($customer['planName']));
				update_user_meta($this->userId, 'userplace_subscription_id', sanitize_text_field($customer['subscriptionId']));
				update_user_meta($this->userId, 'userplace_price', sanitize_text_field($customer['price']));
				update_user_meta($this->userId, 'userplace_currency', sanitize_text_field($customer['currency']));
				update_user_meta($this->userId, 'userplace_status', sanitize_text_field($customer['status']));
				update_user_meta($this->userId, 'userplace_expired_at', sanitize_text_field($customer['expired_at']));
				update_user_meta($this->userId, 'userplace_start_at', sanitize_text_field($customer['start_at']));
				update_user_meta($this->userId, 'userplace_interval_in_month', sanitize_text_field($customer['interval_in_month']));
				update_user_meta($this->userId, 'userplace_interval_type', sanitize_text_field($customer['interval']));
				$provider = new Provider();
				$restrictions = $provider->get_plan_restrictions($customer['planId']);
				if (isset($restrictions['general']) && is_array($restrictions['general'])) {
					$selected_roles = isset($restrictions['general']['userplace_plan_role']) &&  $restrictions['general']['userplace_plan_role'] != '' ? explode(',', $restrictions['general']['userplace_plan_role']) : null;
					if (isset($selected_roles) && is_array($selected_roles)) {
						$all_custom_roles = get_posts(array(
							'posts_per_page' => -1,
							'post_type' => 'userplace_role',
							'post_status' => 'publish'
						));
						$user_object = new \WP_User($this->userId);
						foreach ($all_custom_roles as $key => $role) {
							if (in_array($role->post_title, $user_object->roles)) {
								$user_object->remove_role($role->post_title);
							}
						}
						foreach ($selected_roles as $key => $role) {
							$user_object->add_role($role);
						}
					}
				}
				if (isset($_GET['listing_id'])) {
					$post_id = intval($_GET['listing_id']);
					$post = get_post($post_id);
					if (isset($post->ID) && $post->ID) {
						$redirect_page_url = site_url() . '/console/submission-restrictions/fourth-step/?listing_id=' . $post->ID;
						userplace_redirect($redirect_page_url);
					}
				} else {
					userplace_redirect(site_url() . '/console/?welcome=true');
				}
			} else {
				echo esc_html__("Sorry, There is problem", 'userplace');
			}
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}


	public function billingPage($atts, $content = null)
	{
		try {
			extract(shortcode_atts(array(
				'preview_mode' => 'default', // billing, invoice
			), $atts));

			if (isset($_POST['redq_change_payment_method'])) {
				$paymentData = $this->getPaymentData($_POST);
				extract($paymentData);
				$customerData = $this->billing->attachNewCard($token, $this->customerId);
				if (isset($customerData->id) && isset($customerData->sources->data[0])) {
					$default_card = $this->billing->defaultCard($this->customerId);
					userplace_update_cards_to_db($customerData->sources->data[0], $default_card);
					userplace_redirect(site_url('console?card_update=true'));
				}
			}

			ob_start();
			if ($this->customerId != 'free' && $this->customerId != '') {
				try {
					$customer = [];
					$default_card_details = userplace_get_customer_default_card($this->customerId);
					if (!empty($default_card_details[0])) {
						extract($default_card_details[0]);
					}
					$customer['customerId'] 				= get_user_meta($this->userId, 'userplace_customer_id', true);
					$customer['planAmount'] 				= get_user_meta($this->userId, 'userplace_price', true);
					$customer['planCurrency'] 			= get_user_meta($this->userId, 'userplace_currency', true);
					$customer['status'] 						= get_user_meta($this->userId, 'userplace_status', true);
					$customer['start'] 							= get_user_meta($this->userId, 'userplace_start_at', true);
					$customer['currentPeriodEnd'] 	= get_user_meta($this->userId, 'userplace_expired_at', true);
					$customer['planInterval'] 			= get_user_meta($this->userId, 'userplace_interval_type', true);
					$customer['planIntervalCount'] 	= get_user_meta($this->userId, 'userplace_interval_count', true);
					$args = array(
						'post_type' => 'userplace_plan',
						'meta_query' => array(
							array(
								'key' => 'plan_id',
								'value' => get_user_meta($this->userId, 'userplace_customer_plan_id', true),
								'compare' => '=',
							)
						)
					);
					$plan_array =  get_posts($args);
					if (is_array($plan_array)) {
						$customer['planName'] = $plan_array[0]->post_title;
					}

					$invoices = userplace_get_all_invoices($this->customerId, 10);
					if (is_array($customer)) {
						if ($preview_mode == 'default' || $preview_mode == 'billing') {
							if (isset($_GET['card_update']) && $_GET['card_update'] == 'true') {
								echo '<div class="userplace-welcome">
												<p>' . esc_html__('Your Card is updated successfully', 'userplace') . '
													<span class="userplace-close-icon"><i class="icon icon ion-ios-close"></i></span>
												</p>
										</div>';
							}
							require(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'payments/billing/plan.php');

							require(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'payments/billing/card.php');
						}

						if ($preview_mode == 'default' || $preview_mode == 'invoice') {
							require(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'payments/billing/invoices.php');
						}
					}
				} catch (Exception $e) {
					userplace_store_logs($e->getMessage());
				}
			} else {
				$customer = [];
				$invoices = [];
				if (is_array($customer)) {
					if ($preview_mode == 'default' || $preview_mode == 'billing') {
						require(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'payments/billing/plan.php');

						require(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'payments/billing/card.php');
					}

					if ($preview_mode == 'default' || $preview_mode == 'invoice') {
						require(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'payments/billing/invoices.php');
					}
				}
			}
			return ob_get_clean();
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}

	public function addCard($payload)
	{
		try {
			if (isset($payload['stripeToken'])) {
				$this->billing->createCard($payload['stripeToken'], $this->customerId);
			}
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}

	public function listCards()
	{
		try {
			if (isset($_POST['redq_add_card'])) {
				$paymentData = $this->getPaymentData($_POST);
				extract($paymentData);
				$customerData = $this->billing->createCard($token, $this->customerId);
				if (isset($customerData->is_default)) {
					userplace_save_cards_to_db($customerData);
				} else {
					$default_card = $this->billing->defaultCard($this->customerId);
					userplace_save_cards_to_db($customerData, $default_card);
				}

				if (isset($customerData->id)) {
					userplace_redirect(site_url() . '/console');
				}
			}
			$languages = array(
				'panelLabel' => esc_html__('Submit', 'userplace'),
				'label' => esc_html__('Add New Card', 'userplace'),
				'locale' => 'auto',
			);
			if ($this->customerId) { ?>
				<div class="userplace-card-list-wrapper">
					<?php $this->billing->addCardBox($languages); ?>
					<div class="card-list">
						<?php $all_cards = userplace_get_all_cards($this->customerId); ?>
						<?php foreach ($all_cards as $key => $card) { ?>
							<div class="userplace-single-card">
								<div class="userplace-card-name"> <?php echo esc_html($card['card_brand']) ?></div>
								<div class="userplace-card-num">****'<?php echo esc_html($card['last4']) ?></div>
								<div class="userplace-card-valid"> <?php echo esc_html($card['expired_at']) ?></div>
								<?php if ($card['is_default'] != 1) {  ?>
									<div class="userplace-card-btn-wrapper">
										<button type="button" class="userplace-default-card" data-id="<?php echo esc_attr($card['id'])  ?>">Make Default</button>
										<button type="button" class="userplace-card" data-id="<?php echo esc_attr($card['id'])  ?>"><i class="icon ion-trash-b"></i></button>
									</div>

								<?php } ?>
								<?php if ($card['is_default'] == 1) { ?>
									<div class="userplace-card-default-wrap">
										<span class="userplace-card-default"><?php esc_html_e('Default', 'userplace') ?></span>
										<span class="userplace-card-default-nothing"></span>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php }
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}

	public function handleAnyWebhooks()
	{
		try {
			return $this->billing->handleWebhooks();
		} catch (Exception $e) {
			userplace_store_logs($e->getMessage());
		}
	}

	public function remainingQuota($atts)
	{
		$atts = shortcode_atts(array(
			'post_type'     => 'listing',
			'user_id'     => get_current_user_id(),
		), $atts);
		extract($atts);
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		if (!$user_subscribed_plan) { ?>
			<div><?php esc_html_e('You are not subscribed.', 'userplace'); ?></div>
		<?php } else {
			$restriction_details = $this->getRestrictionDetails($user_id, $user_subscribed_plan, $post_type);
			if (isset($restriction_details[$post_type]) && isset($restriction_details[$post_type]['allow_unlimited']) && $restriction_details[$post_type]['allow_unlimited'] === 'true') {
				$remaining_quota = 'unlimited';
			} else {
				$remaining_quota = isset($restriction_details[$post_type]) && isset($restriction_details[$post_type]['max_posts']) && isset($restriction_details[$post_type]['used_quota']) ? $restriction_details[$post_type]['max_posts'] - $restriction_details[$post_type]['used_quota'] : 0;
			}
		?>
			<div class="listbook-userplace-widget up-userplace-widgets widget-4-col">
				<div class="listbook-userplace-widget-icon total-listing">
					<i class="ion ion-ios-hourglass"></i>
				</div>
				<div class="listbook-userplace-widget-data">
					<h3 class='count'>
						<?php if (current_user_can('administrator') || $remaining_quota === 'unlimited') { ?>
							<i class="ion ion-md-infinite" style="font-size: 22px;display: block;"></i>
						<?php } else { ?>
							<?php echo esc_attr($remaining_quota) ?>
						<?php } ?>
					</h3>
					<div><?php esc_html_e('Remaining', 'userplace')  ?></div>
				</div>
			</div>
<?php }
	}
}
