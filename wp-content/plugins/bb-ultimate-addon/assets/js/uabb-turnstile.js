/**
 * UABB Turnstile Integration
 *
 * Handles Cloudflare Turnstile widget initialization and token management
 * for UABB login and registration forms.
 *
 * @package UABB
 * @since 1.36.16
 */

(function() {
	'use strict';

	// Global variables accessible across all scopes
	window.uabbTurnstileWidgets = window.uabbTurnstileWidgets || {};
	window.uabbTurnstileTokens = window.uabbTurnstileTokens || {};

	/**
	 * Turnstile API loaded callback
	 */
	window.uabbTurnstileOnLoad = function() {
		// API loaded, rendering widgets
		// The widget will auto-render due to data attributes
	};

	/**
	 * UABB-specific Turnstile success callback
	 *
	 * @param {string} token - The Turnstile token
	 */
	window.onUABBTurnstileCallback = function(token) {
		// IMPORTANT: Cloudflare doesn't always bind 'this' to the widget element in auto-render mode
		// So we need to find the widget differently - search for all turnstile widgets and find the one that was just completed
		var allWidgets = document.querySelectorAll('.cf-turnstile[data-callback="onUABBTurnstileCallback"]');

		var turnstileWidget = null;
		var widgetId = '';
		var nodeId = '';

		// Try to get from 'this' first (if available)
		if (this && this.dataset && this.dataset.widgetId) {
			widgetId = this.dataset.widgetId;
			nodeId = this.dataset.nodeId || '';
			turnstileWidget = this;
		} else {
			// Fallback: Find the widget by looking for the one that contains the token in its iframe or just completed
			// For simplicity, we'll use the first widget we find (or iterate to find the right one)
			for (var i = 0; i < allWidgets.length; i++) {
				var widget = allWidgets[i];
				// Check if this widget has been processed already
				if (!widget.dataset.processed) {
					turnstileWidget = widget;
					widgetId = widget.dataset.widgetId || widget.id || '';
					nodeId = widget.dataset.nodeId || '';
					widget.dataset.processed = 'true'; // Mark as processed
					break;
				}
			}
		}

		// Store token globally
		if (nodeId) {
			window.uabbTurnstileTokens[nodeId] = token;
		}

		// Find the form containing the Turnstile widget
		var form = turnstileWidget ? turnstileWidget.closest('form') : null;

		if (form) {
			// Remove any existing token inputs first
			var existingInputs = form.querySelectorAll('input[name="cf-turnstile-response"]');
			existingInputs.forEach(function(input) {
				input.remove();
			});

			// Add fresh hidden input
			var hiddenInput = document.createElement('input');
			hiddenInput.type = 'hidden';
			hiddenInput.name = 'cf-turnstile-response';
			hiddenInput.value = token;
			hiddenInput.className = 'uabb-turnstile-token';
			form.appendChild(hiddenInput);

			// Hook into form submission to ensure token persistence
			if (!form.dataset.turnstileHooked) {
				form.dataset.turnstileHooked = 'true';

				// Use both jQuery and native event listeners for broader compatibility
				if (typeof jQuery !== 'undefined') {
					jQuery(form).on('submit', function() {
						ensureTurnstileToken(form, token);
					});
				}

				form.addEventListener('submit', function() {
					ensureTurnstileToken(form, token);
				});
			}
		}
	};

	/**
	 * Helper function to ensure token is in form
	 *
	 * @param {HTMLFormElement} form - The form element
	 * @param {string} token - The Turnstile token
	 */
	function ensureTurnstileToken(form, token) {
		var tokenInput = form.querySelector('input[name="cf-turnstile-response"]');
		if (!tokenInput) {
			tokenInput = document.createElement('input');
			tokenInput.type = 'hidden';
			tokenInput.name = 'cf-turnstile-response';
			tokenInput.className = 'uabb-turnstile-token';
			form.appendChild(tokenInput);
		}
		tokenInput.value = token;
	}

	/**
	 * UABB-specific Turnstile error callback
	 *
	 * @param {string} error - The error message
	 */
	window.onUABBTurnstileError = function(error) {
		// Error callback - can be extended for custom error handling
	};

	// Hook into jQuery AJAX to inject token at the last moment
	if (typeof jQuery !== 'undefined') {
		// Store original jQuery.ajax
		var originalAjax = jQuery.ajax;

		// Override jQuery.ajax
		jQuery.ajax = function(options) {
			// Check if this is a UABB form submission - check multiple possible patterns
			var isUABBForm = false;
			if (options && options.url && typeof options.url === 'string') {
				isUABBForm = options.url.indexOf('uabb_login_form_submit') !== -1 ||
							options.url.indexOf('uabb_registration_form_submit') !== -1 ||
							options.url.indexOf('admin-ajax.php') !== -1;
			}

			// Also check the data for UABB action
			if (!isUABBForm && options && options.data) {
				var dataStr = '';
				if (typeof options.data === 'string') {
					dataStr = options.data;
				} else if (typeof options.data === 'object') {
					dataStr = JSON.stringify(options.data);
				}
				isUABBForm = dataStr.indexOf('uabb_login_form_submit') !== -1 ||
							dataStr.indexOf('uabb_registration_form_submit') !== -1;
			}

			if (isUABBForm) {
				// Find any available Turnstile token
				var availableToken = '';
				for (var nodeId in window.uabbTurnstileTokens) {
					if (window.uabbTurnstileTokens[nodeId]) {
						availableToken = window.uabbTurnstileTokens[nodeId];
						break;
					}
				}

				// Also check for any cf-turnstile-response inputs on the page
				if (!availableToken) {
					var tokenInputs = document.querySelectorAll('input[name="cf-turnstile-response"]');
					if (tokenInputs.length > 0) {
						availableToken = tokenInputs[0].value;
					}
				}

				if (availableToken) {
					// Ensure data object exists
					if (!options.data) {
						options.data = {};
					}

					// Handle different data formats
					if (typeof options.data === 'string') {
						// Data is URL-encoded string
						options.data += '&cf-turnstile-response=' + encodeURIComponent(availableToken);
					} else if (typeof options.data === 'object') {
						// Data is object
						options.data['cf-turnstile-response'] = availableToken;

						// Also add to data sub-object if it exists (for UABB structure)
						if (options.data.data) {
							options.data.data['cf-turnstile-response'] = availableToken;
						}
					}
				}

				// Add nonce for security
				if (typeof uabbTurnstileData !== 'undefined' && uabbTurnstileData.nonce) {
					// Ensure data object exists
					if (!options.data) {
						options.data = {};
					}

					// Handle different data formats
					if (typeof options.data === 'string') {
						// Data is URL-encoded string
						options.data += '&uabb_turnstile_nonce=' + encodeURIComponent(uabbTurnstileData.nonce);
					} else if (typeof options.data === 'object') {
						// Data is object
						options.data['uabb_turnstile_nonce'] = uabbTurnstileData.nonce;
					}
				}
			}

			// Call original AJAX function
			return originalAjax.call(this, options);
		};
	}

	// Fallback: Try to capture token using MutationObserver
	if (typeof MutationObserver !== 'undefined') {
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if (mutation.type === 'childList') {
					mutation.addedNodes.forEach(function(node) {
						if (node.nodeType === 1 && node.tagName === 'INPUT' && node.name === 'cf-turnstile-response') {
							// Token input detected via MutationObserver
						}
					});
				}
			});
		});
		observer.observe(document.body, { childList: true, subtree: true });
	}
})();
