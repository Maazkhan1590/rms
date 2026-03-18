/**
 * Form Validation Script
 * Applies jQuery Validation to all forms with class 'auth-form' or 'needs-validation'
 */

(function($) {
    'use strict';

    // Custom validation methods
    $.validator.addMethod("url", function(value, element) {
        return this.optional(element) || /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/.test(value);
    }, "Please enter a valid URL.");

    $.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
    }, "Please enter only letters, numbers, and spaces.");

    // Build validation rules for a form
    function buildValidationRules($form) {
        const rules = {};
        const messages = {};

        // Find all required fields (including those in hidden steps)
        $form.find('input[required], select[required], textarea[required]').each(function() {
            const $field = $(this);
            const fieldName = $field.attr('name');
            const fieldId = $field.attr('id');
            const fieldType = $field.attr('type');

            if (!fieldName) return;

            // Skip file inputs for now (handled separately)
            if (fieldType === 'file') return;

            // Build rule object
            const rule = { required: true };
            const message = {};

            // Add type-specific rules
            if (fieldType === 'email') {
                rule.email = true;
                message.email = 'Please enter a valid email address.';
            } else if (fieldType === 'url') {
                rule.url = true;
                message.url = 'Please enter a valid URL.';
            } else if (fieldType === 'number') {
                rule.number = true;
                message.number = 'Please enter a valid number.';
                const min = $field.attr('min');
                const max = $field.attr('max');
                if (min !== undefined) {
                    rule.min = parseFloat(min);
                    message.min = 'Please enter a value greater than or equal to ' + min + '.';
                }
                if (max !== undefined) {
                    rule.max = parseFloat(max);
                    message.max = 'Please enter a value less than or equal to ' + max + '.';
                }
            } else if (fieldType === 'date') {
                rule.date = true;
                message.date = 'Please enter a valid date.';
            }

            // Check for pattern attribute
            const pattern = $field.attr('pattern');
            if (pattern) {
                rule.pattern = pattern;
                message.pattern = 'Please match the required format.';
            }

            // Check for minlength/maxlength
            const minlength = $field.attr('minlength');
            const maxlength = $field.attr('maxlength');
            if (minlength) {
                rule.minlength = parseInt(minlength);
                message.minlength = 'Please enter at least ' + minlength + ' characters.';
            }
            if (maxlength) {
                rule.maxlength = parseInt(maxlength);
                message.maxlength = 'Please enter no more than ' + maxlength + ' characters.';
            }

            // Get label text for error message
            let labelText = '';
            // Try to find label by 'for' attribute
            const $label = $form.find('label[for="' + fieldId + '"]');
            if ($label.length) {
                labelText = $label.text().replace(/\s*\*\s*$/, '').trim();
            } else {
                // Try to find label that contains this field
                const $parentLabel = $field.closest('.form-group').find('label').first();
                if ($parentLabel.length) {
                    labelText = $parentLabel.text().replace(/\s*\*\s*$/, '').trim();
                }
            }

            // Set required message
            message.required = labelText ? (labelText + ' is required.') : 'This field is required.';

            // Handle array fields (e.g., authors[0][name])
            rules[fieldName] = rule;
            messages[fieldName] = message;
        });

        return { rules, messages };
    }

    // Initialize validation for all forms
    function initializeValidation() {
        // Validate forms with class 'auth-form'
        $('form.auth-form').each(function() {
            const $form = $(this);
            
            // Skip if already validated
            if ($form.data('validator')) {
                return;
            }

            // Get form ID or create one
            const formId = $form.attr('id') || 'form-' + Math.random().toString(36).substr(2, 9);
            if (!$form.attr('id')) {
                $form.attr('id', formId);
            }

            // Build validation rules
            const { rules, messages } = buildValidationRules($form);

            function getSubmitButtons() {
                return $form.find('button[type="submit"], input[type="submit"]');
            }

            function setSubmitDisabled(disabled) {
                const $btns = getSubmitButtons();
                $btns.prop('disabled', !!disabled);
                $btns.attr('aria-disabled', disabled ? 'true' : 'false');
                if (disabled) {
                    $btns.addClass('is-disabled');
                } else {
                    $btns.removeClass('is-disabled');
                }
            }

            function updateSubmitState(validator) {
                if (!validator) return;
                // checkForm() updates internal invalid list without forcing a full submit
                validator.checkForm();
                setSubmitDisabled(validator.numberOfInvalids() > 0);
            }

            // Initialize validation
            const validator = $form.validate({
                rules: rules,
                messages: messages,
                ignore: ':hidden:not([name*="[]"])', // Ignore hidden fields except array inputs
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: 'div',
                onkeyup: function(element) {
                    // Validate live on typing
                    this.element(element);
                    updateSubmitState(this);
                },
                onfocusout: function(element) {
                    this.element(element);
                    updateSubmitState(this);
                },
                onclick: function(element) {
                    // For selects/checkboxes
                    this.element(element);
                    updateSubmitState(this);
                },
                errorPlacement: function(error, element) {
                    const fieldName = element.attr('name') || element.attr('id') || '';
                    const $group = element.closest('.form-group');

                    // Create/find a per-field inline error container close to the input
                    let $inlineError = null;
                    if (fieldName) {
                        $inlineError = $group.find('.form-error[data-for="' + CSS.escape(fieldName) + '"]').first();
                    } else {
                        $inlineError = $group.find('.form-error').first();
                    }

                    if (!$inlineError || !$inlineError.length) {
                        $inlineError = $('<div class="form-error show" data-for=""></div>');
                        if (fieldName) $inlineError.attr('data-for', fieldName);

                        // Place under the field:
                        // - For evidence URL rows (flex), insert after the whole row so it doesn't appear on the right.
                        // - For input-groups, insert after the group wrapper.
                        const $evidenceRow = element.closest('.evidence-url-item');
                        if ($evidenceRow.length) {
                            $inlineError.insertAfter($evidenceRow);
                        } else {
                            const $wrapper = element.parent('.input-group').length ? element.parent('.input-group') : element;
                            $inlineError.insertAfter($wrapper);
                        }
                    }

                    $inlineError.text(error.text()).addClass('show');
                    return;

                    // Fallback to standard invalid-feedback placement
                    if ($group.length) {
                        error.addClass('invalid-feedback').appendTo($group);
                    } else if (element.parent('.input-group').length) {
                        error.addClass('invalid-feedback').appendTo(element.parent('.input-group').parent());
                    } else {
                        error.addClass('invalid-feedback').insertAfter(element);
                    }
                },
                success: function(label, element) {
                    // Clear inline error container if present
                    const $group = $(element).closest('.form-group');
                    const fieldName = $(element).attr('name') || $(element).attr('id') || '';
                    let $inlineError = null;
                    if (fieldName) {
                        $inlineError = $group.find('.form-error[data-for="' + CSS.escape(fieldName) + '"]').first();
                    } else {
                        $inlineError = $group.find('.form-error').first();
                    }
                    if ($inlineError && $inlineError.length) {
                        $inlineError.text('').removeClass('show');
                    }

                    // Remove generated error label (fallback mode)
                    label.remove();
                    // Add valid class to input
                    $(element).removeClass('is-invalid').addClass('is-valid');
                    updateSubmitState($form.validate());
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                    updateSubmitState($form.validate());
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                    updateSubmitState($form.validate());
                },
                submitHandler: function(form) {
                    // Remove any existing error messages
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.invalid-feedback').remove();
                    $form.find('.form-error').text('').removeClass('show');
                    setSubmitDisabled(true);
                    
                    // Submit the form
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    updateSubmitState(validator);
                    // Scroll to first error
                    const firstError = $form.find('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                        firstError.focus();
                    }
                }
            });

            // Initial state: keep submit disabled until valid
            setSubmitDisabled(true);
            updateSubmitState(validator);

            // Also update state on dynamic changes (e.g. added URL inputs)
            $form.on('input change', 'input, select, textarea', function() {
                updateSubmitState($form.validate());
            });
        });
    }

    // Re-validate form when dynamic fields are added
    function revalidateForm($form) {
        if ($form.length && $form.data('validator')) {
            const { rules, messages } = buildValidationRules($form);
            const validator = $form.validate();
            validator.settings.rules = $.extend(validator.settings.rules || {}, rules);
            validator.settings.messages = $.extend(validator.settings.messages || {}, messages);
        }
    }

    // Export revalidateForm for use in other scripts
    window.revalidateForm = revalidateForm;

    // Handle dynamic fields using MutationObserver (more reliable than DOMNodeInserted)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const $node = $(node);
                        if ($node.is('input, select, textarea') || $node.find('input, select, textarea').length) {
                            const $form = $node.closest('form.auth-form');
                            if ($form.length) {
                                setTimeout(function() {
                                    revalidateForm($form);
                                }, 100);
                            }
                        }
                    }
                });
            });
        });

        // Observe document body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Initialize on document ready
    $(document).ready(function() {
        initializeValidation();
    });

    // Re-initialize when forms are shown (for multi-step forms)
    $(document).on('shown.bs.tab', function() {
        initializeValidation();
    });

    // Re-initialize when step content is shown
    $(document).on('DOMContentLoaded', function() {
        initializeValidation();
    });

    // Add CSS for validation states
    if (!$('#form-validation-styles').length) {
        $('<style id="form-validation-styles">')
            .text(`
                .auth-form .is-disabled {
                    opacity: 0.6;
                    cursor: not-allowed !important;
                }
                .auth-form .form-error.show {
                    display: block !important;
                    color: #ef4444 !important;
                    font-size: 0.875rem;
                    margin-top: 0.5rem;
                }
                .form-control.is-invalid {
                    border-color: #dc3545;
                    padding-right: calc(1.5em + 0.75rem);
                    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
                    background-repeat: no-repeat;
                    background-position: right calc(0.375em + 0.1875rem) center;
                    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                }
                .form-control.is-valid {
                    border-color: #28a745;
                    padding-right: calc(1.5em + 0.75rem);
                    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
                    background-repeat: no-repeat;
                    background-position: right calc(0.375em + 0.1875rem) center;
                    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                }
                .invalid-feedback {
                    display: block;
                    width: 100%;
                    margin-top: 0.25rem;
                    font-size: 0.875rem;
                    color: #dc3545;
                }
                .invalid-feedback::before {
                    content: '⚠ ';
                    margin-right: 0.25rem;
                }
                select.form-control.is-invalid,
                select.form-control.is-valid {
                    padding-right: 2.5rem;
                }
            `)
            .appendTo('head');
    }

})(jQuery);
