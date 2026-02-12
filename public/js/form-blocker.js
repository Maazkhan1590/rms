/**
 * Form Submit Blocker
 * Prevents duplicate form submissions by disabling submit buttons during processing
 * Works with both regular form submissions and AJAX requests
 * Automatically re-enables buttons on completion or error
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        loadingText: 'Processing...',
        spinnerClass: 'form-blocker-spinner',
        disabledClass: 'form-blocker-disabled',
        dataAttr: 'data-original-text',
        excludeClass: 'no-block', // Add this class to forms/buttons that should not be blocked
        timeout: 30000 // 30 seconds timeout to re-enable button if something goes wrong
    };

    // CSS for loading spinner
    const style = document.createElement('style');
    style.textContent = `
        .form-blocker-disabled {
            opacity: 0.6;
            cursor: not-allowed !important;
            pointer-events: none;
        }
        
        .form-blocker-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: form-blocker-spin 0.8s linear infinite;
            margin-right: 6px;
            vertical-align: middle;
        }
        
        @keyframes form-blocker-spin {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    // Store active buttons with their timeout IDs
    const activeButtons = new Map();

    /**
     * Disable a submit button and show loading state
     */
    function disableButton(button) {
        if (!button || button.classList.contains(CONFIG.disabledClass)) {
            return; // Already disabled
        }

        // Store original text
        const originalText = button.innerHTML;
        button.setAttribute(CONFIG.dataAttr, originalText);

        // Disable button
        button.disabled = true;
        button.classList.add(CONFIG.disabledClass);

        // Add spinner and loading text
        const spinner = `<span class="${CONFIG.spinnerClass}"></span>`;
        button.innerHTML = spinner + CONFIG.loadingText;

        // Set timeout to re-enable button if something goes wrong
        const timeoutId = setTimeout(() => {
            enableButton(button);
        }, CONFIG.timeout);

        activeButtons.set(button, timeoutId);
    }

    /**
     * Re-enable a submit button and restore original state
     */
    function enableButton(button) {
        if (!button) return;

        // Clear timeout
        const timeoutId = activeButtons.get(button);
        if (timeoutId) {
            clearTimeout(timeoutId);
            activeButtons.delete(button);
        }

        // Restore original state
        const originalText = button.getAttribute(CONFIG.dataAttr);
        if (originalText) {
            button.innerHTML = originalText;
            button.removeAttribute(CONFIG.dataAttr);
        }

        button.disabled = false;
        button.classList.remove(CONFIG.disabledClass);
    }

    /**
     * Get all submit buttons in a form
     */
    function getSubmitButtons(form) {
        const buttons = [];
        
        // Get button[type="submit"]
        buttons.push(...form.querySelectorAll('button[type="submit"]'));
        
        // Get input[type="submit"]
        buttons.push(...form.querySelectorAll('input[type="submit"]'));
        
        // Get buttons without explicit type (default to submit in forms)
        buttons.push(...Array.from(form.querySelectorAll('button:not([type])')));
        
        return buttons.filter(btn => !btn.classList.contains(CONFIG.excludeClass));
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(event) {
        const form = event.target;
        
        // Skip if form has exclude class
        if (form.classList.contains(CONFIG.excludeClass)) {
            return;
        }

        // Disable all submit buttons in the form
        const buttons = getSubmitButtons(form);
        buttons.forEach(button => disableButton(button));

        // Re-enable buttons after a delay (for regular form submissions that navigate away)
        // For AJAX forms, they should be re-enabled in the AJAX callback
        setTimeout(() => {
            // Only re-enable if we're still on the same page
            if (document.body.contains(form)) {
                buttons.forEach(button => enableButton(button));
            }
        }, 2000);
    }

    /**
     * Setup form blocker on page load
     */
    function init() {
        // Handle all form submissions
        document.addEventListener('submit', handleFormSubmit);

        // If jQuery is available, integrate with jQuery AJAX
        if (typeof jQuery !== 'undefined') {
            setupJQueryIntegration();
        }

        // If axios is available, integrate with axios
        if (typeof axios !== 'undefined') {
            setupAxiosIntegration();
        }
    }

    /**
     * Setup jQuery AJAX integration
     */
    function setupJQueryIntegration() {
        const $ = jQuery;

        // Track AJAX requests initiated from forms
        $(document).on('click', 'button[type="submit"], input[type="submit"]', function(e) {
            const button = this;
            const form = $(button).closest('form')[0];
            
            if (form && !form.classList.contains(CONFIG.excludeClass) && !button.classList.contains(CONFIG.excludeClass)) {
                // Store reference for AJAX complete handlers
                $(button).data('formBlockerButton', button);
            }
        });

        // Re-enable buttons on AJAX complete
        $(document).ajaxComplete(function(event, xhr, settings) {
            // Try to find any disabled buttons and re-enable them
            setTimeout(() => {
                $('.' + CONFIG.disabledClass).each(function() {
                    enableButton(this);
                });
            }, 500);
        });

        // Re-enable on AJAX error
        $(document).ajaxError(function() {
            setTimeout(() => {
                $('.' + CONFIG.disabledClass).each(function() {
                    enableButton(this);
                });
            }, 500);
        });
    }

    /**
     * Setup Axios integration
     */
    function setupAxiosIntegration() {
        // Store original axios functions
        const originalPost = axios.post;
        const originalGet = axios.get;
        const originalPut = axios.put;
        const originalPatch = axios.patch;
        const originalDelete = axios.delete;

        // Helper to re-enable all disabled buttons
        function reEnableAll() {
            document.querySelectorAll('.' + CONFIG.disabledClass).forEach(button => {
                enableButton(button);
            });
        }

        // Wrap axios methods to re-enable buttons on completion
        ['post', 'get', 'put', 'patch', 'delete'].forEach(method => {
            axios[method] = function(...args) {
                const originalMethod = method === 'post' ? originalPost :
                                     method === 'get' ? originalGet :
                                     method === 'put' ? originalPut :
                                     method === 'patch' ? originalPatch :
                                     originalDelete;

                return originalMethod.apply(this, args)
                    .then(response => {
                        reEnableAll();
                        return response;
                    })
                    .catch(error => {
                        reEnableAll();
                        throw error;
                    });
            };
        });
    }

    /**
     * Public API for manual control
     */
    window.FormBlocker = {
        disable: disableButton,
        enable: enableButton,
        disableForm: function(form) {
            const buttons = getSubmitButtons(form);
            buttons.forEach(button => disableButton(button));
        },
        enableForm: function(form) {
            const buttons = getSubmitButtons(form);
            buttons.forEach(button => enableButton(button));
        },
        config: CONFIG
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
