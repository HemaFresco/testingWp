/* global toastr, rtcl_stripe_params, Stripe, rtcl_make_checkout_request */
(function ($) {
	'use strict';

	try {
		var stripe = Stripe(rtcl_stripe_params.key, {locale: rtcl_stripe_params.locale || 'auto'});
	} catch (error) {
		console.log(error);
		return;
	}
	var stripe_elements_options = Object.keys(rtcl_stripe_params.elements_options).length ? rtcl_stripe_params.elements_options : {},
		elements = stripe.elements(stripe_elements_options), stripe_card, stripe_exp, stripe_cvc;

	/**
	 * Object to handle Stripe elements payment form.
	 */
	var rtcl_stripe_update = {

		/**
		 * Unmounts all Stripe elements when the checkout page is being updated.
		 */
		unmountElements: function () {
			if (rtcl_stripe_params.inline_cc_form) {
				stripe_card.unmount('#stripe-card-element');
			} else {
				stripe_card.unmount('#stripe-card-element');
				stripe_exp.unmount('#stripe-exp-element');
				stripe_cvc.unmount('#stripe-cvc-element');
			}
		},

		/**
		 * Mounts all elements to their DOM nodes on initial loads and updates.
		 */
		mountElements: function () {
			if (!$('#stripe-card-element').length) {
				return;
			}

			if (rtcl_stripe_params.inline_cc_form) {
				stripe_card.mount('#stripe-card-element');
				return;
			}

			stripe_card.mount('#stripe-card-element');
			stripe_exp.mount('#stripe-exp-element');
			stripe_cvc.mount('#stripe-cvc-element');
		},

		/**
		 * Creates all Stripe elements that will be used to enter cards or IBANs.
		 */
		createElements: function () {
			var elementStyles = {
				base: {
					iconColor: '#666ee8', color: '#31325f', fontSize: '15px', '::placeholder': {
						color: '#cfd7e0',
					}
				}
			};

			var elementClasses = {
				focus: 'focused', empty: 'empty', invalid: 'invalid',
			};

			elementStyles = rtcl_stripe_params.elements_styling ? rtcl_stripe_params.elements_styling : elementStyles;
			elementClasses = rtcl_stripe_params.elements_classes ? rtcl_stripe_params.elements_classes : elementClasses;

			if (rtcl_stripe_params.inline_cc_form) {
				stripe_card = elements.create('card', {style: elementStyles, hidePostalCode: true});
				stripe_card.addEventListener('change', function (event) {
					rtcl_stripe_update.onCCFormChange(event);

					if (event.error) {
						$(document.body).trigger('stripeError', event);
					}
				});
			} else {
				stripe_card = elements.create('cardNumber', {style: elementStyles, classes: elementClasses});
				stripe_exp = elements.create('cardExpiry', {style: elementStyles, classes: elementClasses});
				stripe_cvc = elements.create('cardCvc', {style: elementStyles, classes: elementClasses});

				stripe_card.addEventListener('change', function (event) {
					rtcl_stripe_update.onCCFormChange(event);

					rtcl_stripe_update.updateCardBrand(event.brand);

					if (event.error) {
						$(document.body).trigger('stripeError', event);
					}
				});

				stripe_exp.addEventListener('change', function (event) {
					rtcl_stripe_update.onCCFormChange(event);

					if (event.error) {
						$(document.body).trigger('stripeError', event);
					}
				});

				stripe_cvc.addEventListener('change', function (event) {
					rtcl_stripe_update.onCCFormChange(event);

					if (event.error) {
						$(document.body).trigger('stripeError', event);
					}
				});
			}
			$(document.body)
				.on('rtcl_stripe_update_cc_form_init', function () {
					// Don't re-mount if already mounted in DOM.
					if ($('#stripe-card-element').children().length) {
						return;
					}

					// Unmount prior to re-mounting.
					if (stripe_card) {
						rtcl_stripe_update.unmountElements();
					}

					rtcl_stripe_update.mountElements();
				});
		},

		/**
		 * Updates the card brand logo with non-inline CC forms.
		 *
		 * @param {string} brand The identifier of the chosen brand.
		 */
		updateCardBrand: function (brand) {
			var brandClass = {
				'visa': 'stripe-visa-brand',
				'mastercard': 'stripe-mastercard-brand',
				'amex': 'stripe-amex-brand',
				'discover': 'stripe-discover-brand',
				'diners': 'stripe-diners-brand',
				'jcb': 'stripe-jcb-brand',
				'unknown': 'stripe-credit-card-brand'
			};

			var imageElement = $('.stripe-card-brand'), imageClass = 'stripe-credit-card-brand';

			if (brand in brandClass) {
				imageClass = brandClass[brand];
			}

			// Remove existing card brand class.
			$.each(brandClass, function (index, el) {
				imageElement.removeClass(el);
			});

			imageElement.addClass(imageClass);
		},

		/**
		 * Initialize event handlers and UI state.
		 */
		init: function () {

			var $subForm = $('.rtcl-sub-item[data-gateway=stripe] form#rtcl-sub-update-payment');

			console.log(this.form)
			if ($subForm.length) {
				this.form = $subForm;
				rtcl_stripe_update.createElements();
				$subForm.find('.sub-update-btn').addClass('disabled');
				$subForm.validate({
					submitHandler: function (form) {
						rtcl_stripe_update.block();
						rtcl_stripe_update.createCardUpdateMethod()
						return false;
					}
				});
				$(document.body).trigger('rtcl_stripe_update_cc_form_init');
				return;
			}
		},

		/**
		 * Blocks payment forms with an overlay while being submitted.
		 */
		block: function () {
			rtcl_stripe_update.form && rtcl_stripe_update.form.rtclBlock();
		},

		/**
		 * Removes overlays from payment forms.
		 */
		unblock: function () {
			rtcl_stripe_update.form && rtcl_stripe_update.form.rtclUnblock();
		},

		createCardUpdateMethod: function () {
			var that = this;
			return stripe
				.createPaymentMethod({
					type: 'card', card: stripe_card, billing_details: {
						name: rtcl_stripe_params.billing.name, email: rtcl_stripe_params.billing.email
					}
				})
				.then(function (response) {
					if (response.error) {
						toastr.error(response.error.message);
						return;
					}
					response.paymentMethod.id;
					that.unblock();
					const formData = new FormData(that.form[0]);
					formData.append('action', 'rtcl_subscription_update_payment');
					formData.append('pm_id', response.paymentMethod.id);
					formData.append('__rtcl_wpnonce', rtcl.__rtcl_wpnonce);
					const wrap = $(that.form).parents('.rtcl-sub-item');
					$.ajax({
						url: rtcl.ajaxurl,
						data: formData,
						type: 'POST',
						dataType: 'json',
						cache: false,
						processData: false,
						contentType: false,
						beforeSend: function () {
							wrap.rtclBlock();
						},
						success: function (res) {
							console.log(res);
							if (res.success) {
								that.unmountElements();
								that.mountElements()
								wrap.find('.sub-payment-update-wrap').slideUp('slow');
								wrap.find('.rtcl-subi-cc .cc-type').text(res.data.type);
								wrap.find('.rtcl-subi-cc .cc-number').text(res.data.last4);
								wrap.find('.rtcl-subi-cc .cc-expiry').text('(' + res.data.expiry + ')');
								toastr.success(res.data.message);
							} else {
								toastr.error(res.data);
							}
						},
						complete: function () {
							wrap.rtclUnblock();
						},
						error: function (request, status, error) {
							toastr.error('Error while updating subscription');
						},
					});

					return false;
				});
		},


		/**
		 * If a new credit card is entered, reset sources.
		 */
		onCCFormChange: function (event) {
			if (event.complete) {
				$('#rtcl-stripe-cc-form').parents('#rtcl-sub-update-payment').find('.sub-update-btn').removeClass('disabled');
			}
		},

		/**
		 * Displays stripe-related errors.
		 *
		 * @param {Event}  e      The jQuery event.
		 * @param {Object} result The result of Stripe call.
		 */
		onError: function (e, result) {
			var message = result.error.message;

			// Notify users that the email is invalid.
			if ('email_invalid' === result.error.code) {
				message = rtcl_stripe_params.i18.email_invalid;
			} else if (/*
				 * Customers do not need to know the specifics of the below type of errors
				 * therefore return a generic localizable error message.
				 */
				'invalid_request_error' === result.error.type || 'api_connection_error' === result.error.type || 'api_error' === result.error.type || 'authentication_error' === result.error.type || 'rate_limit_error' === result.error.type) {
				message = rtcl_stripe_params.i18.invalid_request_error;
			}

			if ('card_error' === result.error.type && rtcl_stripe_params.i18.hasOwnProperty(result.error.code)) {
				message = rtcl_stripe_params.i18[result.error.code];
			}

			if ('validation_error' === result.error.type && rtcl_stripe_params.i18.hasOwnProperty(result.error.code)) {
				message = rtcl_stripe_params.i18[result.error.code];
			}
			toastr.error(message);
		}
	};

	rtcl_stripe_update.init();
	window.rtcl_stripe_update = rtcl_stripe_update;
})(jQuery);
