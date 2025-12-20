import $ from '../../utils/jquery-select2.js';
import { ensureSelect2 } from '../../utils/jquery-select2.js';
import { Modal } from 'bootstrap';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';
import 'jquery.repeater';
import 'select2/dist/css/select2.min.css';
import 'jquery-ui/ui/widgets/datepicker';
import 'jquery-ui/themes/base/core.css';
import 'jquery-ui/themes/base/theme.css';
import 'jquery-ui/themes/base/datepicker.css';
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/alert.js';
import { registerCustomMethods, getCommonRules, getCommonMessages, getCommonConfig, addMarkRules } from './shared-validation.js';

$(document).ready(async function () {
    await ensureSelect2();
    registerCustomMethods();

    const $form = $('#addStudentForm');
    const $modal = $('#addStudentModal');
    const $marksRepeater = $('#marks-repeater');
    let entryIndex = 0;
    let selectedSubjects = new Set();
    const $marksEntryTemplate = (() => {
        const $template = $marksRepeater.find('[data-repeater-item]').first().clone(false, false);

        // Ensure the template is clean (no Select2 containers/data and no validation artifacts)
        $template.find('.select2-container').remove();
        $template.find('.select2-hidden-accessible').removeClass('select2-hidden-accessible');
        $template.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $template.find('.invalid-feedback').remove();

        const $templateSelect = $template.find('.subject-select').first();
        if ($templateSelect.length) {
            $templateSelect
                .removeAttr('data-select2-id')
                .removeAttr('tabindex')
                .removeAttr('aria-hidden')
                .removeAttr('aria-disabled')
                .removeAttr('aria-label')
                .removeAttr('aria-labelledby')
                .removeAttr('aria-controls')
                .removeData()
                .empty()
                .val(null);
        }

        $template.find('input').val('');
        $template.find('input[type="file"]').val('');

        return $template;
    })();

    function getSelect2Jquery() {
        if (typeof window !== 'undefined' && window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 !== 'undefined') {
            return window.jQuery;
        }

        if (typeof $.fn.select2 !== 'undefined') {
            return $;
        }

        return null;
    }

    function getSelectedSubjectIds() {
        const ids = [];

        $marksRepeater.find('.subject-select').each(function () {
            const value = $(this).val();
            if (value) {
                ids.push(value.toString());
            }
        });

        return new Set(ids);
    }

    function syncSelectedSubjects() {
        selectedSubjects = getSelectedSubjectIds();
    }

    function isSubjectAlreadySelected(subjectId, currentElement) {
        // Use selectedSubjects Set for O(1) lookup instead of O(n) iteration
        const subjectIdStr = subjectId?.toString();
        if (!subjectIdStr) {
            return false;
        }

        // Check if already in the Set, but exclude the current element
        if (selectedSubjects.has(subjectIdStr)) {
            // Verify it's not just the current element
            const $currentSelect = $(currentElement);
            const currentValue = $currentSelect.val()?.toString();
            return currentValue !== subjectIdStr || selectedSubjects.size > 1;
        }

        return false;
    }

    // Helper function to safely destroy Select2 instance
    function safelyDestroySelect2($select) {
        if (!$select || $select.length === 0) {
            return;
        }

        const select2Jq = getSelect2Jquery();
        if (!select2Jq || typeof select2Jq.fn.select2 === 'undefined') {
            return;
        }

        // Handle both jQuery object and raw element
        const $select2 = $select instanceof select2Jq ? $select : select2Jq($select[0] || $select);

        // Check if Select2 is actually initialized by checking for Select2 data
        // Select2 stores its instance data on the element via jQuery data API
        const select2Data = $select2.data('select2');
        if (select2Data) {
            try {
                $select2.select2('destroy');
            } catch (e) {
                // Silently ignore if destroy fails - element might not be fully initialized
                console.warn('Failed to destroy Select2 instance:', e);
            }
        }

        // Remove all jQuery data to ensure clean state
        try {
            $select2.removeData();
        } catch (e) {
            // Ignore if removeData fails
        }

        // Remove any Select2 containers that might be left behind
        $select2.next('.select2-container').remove();

        // Remove Select2 classes and attributes that might be left behind
        $select2
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('aria-hidden')
            .removeAttr('tabindex');
    }

    // Helper function to reset form and marks repeater
    function resetFormAndMarks() {
        $form[0].reset();
        $form.validate().resetForm();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.is-valid').removeClass('is-valid');
        $form.find('.invalid-feedback').remove();
        $marksRepeater.find('[data-repeater-item]').not(':first').remove();
        $marksRepeater.find('[data-repeater-item]').first().find('input, select').val('');
        $marksRepeater.find('[data-repeater-item]').first().find('input[type="file"]').val('');
        $marksRepeater.find('.subject-select').each(function () {
            $(this).val(null).trigger('change');
        });
        selectedSubjects.clear();
        syncSelectedSubjects();
        entryIndex = 0;
        updateRemoveButtons();
    }

    function hideStudentModal() {
        const modalEl = $modal[0];
        if (!modalEl) {
            return;
        }

        try {
            // Bootstrap 5 (ESM-friendly)
            const instance = Modal.getInstance(modalEl) ?? Modal.getOrCreateInstance(modalEl);
            instance.hide();
        } catch (e) {
            // Final fallback: attempt jQuery plugin if present
            if (typeof $modal.modal === 'function') {
                $modal.modal('hide');
            }
        }
    }

    function reloadStudentsDataTable() {
        if (!$.fn.DataTable || typeof $.fn.DataTable.isDataTable !== 'function') {
            return;
        }

        if ($.fn.DataTable.isDataTable('#students-table')) {
            $('#students-table').DataTable().ajax.reload(null, false);
        }
    }

    // Check if form exists
    if ($form.length === 0) {
        console.error('Form #addStudentForm not found');
        return;
    }

    // Check if jQuery validation is available
    if (typeof $.fn.validate === 'undefined') {
        console.error('jQuery Validation plugin is not loaded');
        return;
    }

    // Initialize Select2 for subjects with AJAX
    function initializeSubjectSelect($select) {
        if (!$select || $select.length === 0) {
            console.warn('Select element not found for Select2 initialization');
            return;
        }

        const select2Jq = getSelect2Jquery();
        if (!select2Jq) {
            console.error('Select2 is not available (no jQuery instance has $.fn.select2).');
            return;
        }

        const $select2 = select2Jq($select[0]);

        // Safely destroy existing Select2 instance if any
        safelyDestroySelect2($select2);

        // Get current value to preserve it
        const currentValue = $select2.val();

        // Clear the select element completely to remove any cloned options
        $select2.empty();

        // Get the route URL - use window.route if available (Ziggy), otherwise use direct URL
        const subjectsUrl = (typeof route !== 'undefined' && typeof route === 'function')
            ? route('subjects.index')
            : '/subjects';

        try {
            $select2.select2({
                theme: 'bootstrap-5',
                placeholder: 'Select Subject',
                allowClear: true,
                width: '100%',
                dropdownParent: $modal,
                ajax: {
                    url: subjectsUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term || '',
                        };
                    },
                    processResults: function (data) {
                        const currentSelections = getSelectedSubjectIds();
                        // Filter out already selected subjects
                        const filteredResults = (data.results || []).filter(function (subject) {
                            // Don't filter the currently selected value in this select
                            if (subject.id == currentValue) {
                                return true;
                            }
                            return !currentSelections.has(subject.id.toString());
                        });

                        return {
                            results: filteredResults,
                        };
                    },
                    cache: false, // Disable cache to ensure fresh results every time
                },
                minimumInputLength: 0,
            });
        } catch (error) {
            console.error('Error initializing Select2:', error);
        }

        // Only pre-populate options if there's a current value (existing entry)
        // For new entries, let Select2 handle it via AJAX only
        if (currentValue) {
            $.ajax({
                url: subjectsUrl,
                dataType: 'json',
                data: {
                    search: '',
                },
            }).done(function (data) {
                if (data.results && data.results.length > 0) {
                    // Filter out already selected subjects
                    const currentSelections = getSelectedSubjectIds();
                    const availableSubjects = data.results.filter(function (subject) {
                        if (subject.id == currentValue) {
                            return true;
                        }
                        return !currentSelections.has(subject.id.toString());
                    });

                    // Use DocumentFragment for better performance when adding multiple options
                    const fragment = document.createDocumentFragment();
                    availableSubjects.forEach(function (subject) {
                        const option = new Option(subject.text, subject.id, false, false);
                        fragment.appendChild(option);
                    });
                    $select2.append(fragment);

                    // Set the current value
                    $select2.val(currentValue).trigger('change');
                }
            }).fail(function (xhr, status, error) {
                console.error('Failed to load subjects:', error);
            });
        }

        // Track selected subjects
        $select2.on('select2:select', function (e) {
            const subjectId = e.params.data.id?.toString();
            if (!subjectId) {
                return;
            }

            if (isSubjectAlreadySelected(subjectId, this)) {
                // Prevent duplicate selection
                e.preventDefault();
                $select2.val(null).trigger('change');
                showErrorAlert('This subject has already been selected in another entry.', 'Duplicate Subject');
                syncSelectedSubjects();
                return false;
            }

            syncSelectedSubjects();
            updateSubjectOptions();
        });

        $select2.on('select2:unselect', function (e) {
            const subjectId = e.params.data.id?.toString();
            if (!subjectId) {
                return;
            }

            syncSelectedSubjects();
            updateSubjectOptions();
        });

        // Add validation on Select2 close event to clear errors when valid
        $select2.on('select2:close', function () {
            const formValidator = $form.data('validator');
            if (formValidator) {
                // Validate the select element
                formValidator.element(this);
            }
        });

        // Add validation on Select2 select event
        $select2.on('select2:select', function () {
            const formValidator = $form.data('validator');
            if (formValidator) {
                // Validate the select element to clear any errors
                formValidator.element(this);
            }
        });
    }

    // Update subject options to exclude already selected subjects
    // Note: The filtering is handled in the AJAX processResults function
    // This function just ensures Select2 refreshes when subjects are added/removed
    function updateSubjectOptions() {
        syncSelectedSubjects();
        // The AJAX processResults already filters out selected subjects
        // No additional action needed as Select2 will reload via AJAX when opened
    }

    // Initialize jQuery UI Datepicker for birth_date when modal is shown
    function initDatepicker() {
        const $birthDate = $('#birth_date');
        if ($birthDate.length && !$birthDate.hasClass('hasDatepicker')) {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);

            $birthDate.datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '1900:' + new Date().getFullYear(),
                maxDate: yesterday, // Birth date must be before today
            });
        }
    }

    // Initialize datepicker and Select2 when modal is shown
    $modal.on('shown.bs.modal', function () {
        initDatepicker();

        // Wait a bit for modal to fully render, then initialize Select2
        setTimeout(async function () {
            if (!getSelect2Jquery()) {
                await ensureSelect2();
            }

            if (!getSelect2Jquery()) {
                console.error('Select2 is not available');
                return;
            }

            $marksRepeater.find('.subject-select').each(function () {
                const $select = $(this);
                // Safely destroy any existing instance first
                safelyDestroySelect2($select);
                // Initialize Select2
                initializeSubjectSelect($select);
            });
        }, 400);
    });

    // Initialize first subject select - wait for Select2 to be available
    function initFirstSelect() {
        if (getSelect2Jquery()) {
            const $firstSelect = $marksRepeater.find('.subject-select').first();
            if ($firstSelect.length && !$firstSelect.hasClass('select2-hidden-accessible')) {
                initializeSubjectSelect($firstSelect);
            }
        } else {
            // Retry after a short delay if Select2 isn't loaded yet
            setTimeout(initFirstSelect, 50);
        }
    }
    initFirstSelect();

    // Add marks entry
    $('#add-marks-entry').on('click', function () {
        entryIndex++;
        // Always clone from the original captured template (avoids cloning a row whose index isn't [0])
        const $newEntry = $marksEntryTemplate.clone(false, false);

        // Remove any cloned Select2 containers and restore the original select element
        $newEntry.find('.select2-container').remove();

        // Get the select element and completely clean it
        const $newSelect = $newEntry.find('.subject-select').first();

        // Remove all Select2-related attributes and data
        $newSelect
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('tabindex')
            .removeAttr('aria-hidden')
            .removeAttr('aria-disabled')
            .removeAttr('aria-label')
            .removeAttr('aria-labelledby')
            .removeAttr('aria-controls');

        // Remove all jQuery data (including Select2 internal data)
        $newSelect.removeData();

        // Clear the select element's options and value
        $newSelect.empty().val(null);

        // Clear all other input values
        $newEntry.find('input').val('');
        $newEntry.find('input[type="file"]').val('');

        // Update names with new index (replace any existing marks[<n>] index)
        $newEntry.find('[name]').each(function () {
            const $field = $(this);
            const name = $field.attr('name');
            if (!name) {
                return;
            }

            const newName = name.replace(/marks\[\d+\]/, `marks[${entryIndex}]`);
            $field.attr('name', newName);
        });

        // Remove validation classes and errors
        $newEntry.find('.is-invalid').removeClass('is-invalid');
        $newEntry.find('.is-valid').removeClass('is-valid');
        $newEntry.find('.invalid-feedback').remove();

        // Append the entry first, then initialize Select2
        $marksRepeater.append($newEntry);

        // Initialize Select2 for new entry after it's in the DOM
        if (getSelect2Jquery()) {
            // Small delay to ensure DOM is ready
            setTimeout(function () {
                initializeSubjectSelect($newSelect);
            }, 50);
        }

        // Add validation rules for the new entry fields
        const validator = $form.data('validator');
        if (validator) {
            addMarkRules(validator, `marks[${entryIndex}]`, $newEntry);
        }

        updateRemoveButtons();
        syncSelectedSubjects();
        updateSubjectOptions();
    });

    // Remove marks entry
    $(document).on('click', '.remove-entry', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $entry = $(this).closest('[data-repeater-item]');
        const $select = $entry.find('.subject-select');
        const selectedValue = $select.val();
        const fieldName = $select.attr('name');

        // Remove from selected subjects if it was selected
        if (selectedValue) {
            selectedSubjects.delete(selectedValue);
        }

        // Remove validation rules for this entry
        const validator = $form.data('validator');
        if (validator && fieldName) {
            const baseName = fieldName.replace(/\[subject_id\]$/, '');
            const subjectName = fieldName;
            const totalMarksName = baseName + '[total_marks]';
            const obtainedMarksName = baseName + '[obtained_marks]';
            const proofName = baseName + '[proof]';

            // Remove rules and messages
            delete validator.settings.rules[subjectName];
            delete validator.settings.messages[subjectName];
            delete validator.settings.rules[totalMarksName];
            delete validator.settings.messages[totalMarksName];
            delete validator.settings.rules[obtainedMarksName];
            delete validator.settings.messages[obtainedMarksName];
            delete validator.settings.rules[proofName];
            delete validator.settings.messages[proofName];

            // Remove any error messages for these fields
            $form.find('.invalid-feedback[data-field="' + subjectName + '"]').remove();
            $form.find('.invalid-feedback[data-field="' + totalMarksName + '"]').remove();
            $form.find('.invalid-feedback[data-field="' + obtainedMarksName + '"]').remove();
            $form.find('.invalid-feedback[data-field="' + proofName + '"]').remove();
        }

        // Safely destroy Select2 if it's initialized
        safelyDestroySelect2($select);

        // Remove the entry
        $entry.remove();

        // Re-initialize all remaining Select2 instances to ensure they work correctly
        setTimeout(function () {
            $marksRepeater.find('.subject-select').each(function () {
                const $select = $(this);
                // Only re-initialize if not already initialized
                if (!$select.hasClass('select2-hidden-accessible')) {
                    initializeSubjectSelect($select);
                }
            });
        }, 100);

        // Update UI
        updateRemoveButtons();
        syncSelectedSubjects();
        updateSubjectOptions();

        return false;
    });

    // Update remove buttons visibility - optimized with cached selector
    function updateRemoveButtons() {
        const entryCount = $marksRepeater.find('[data-repeater-item]').length;
        const $removeButtons = $marksRepeater.find('.remove-entry'); // Cache selector
        $removeButtons.toggle(entryCount > 1); // Use toggle for cleaner code
    }

    // Validate obtained marks <= total marks - trigger validation on blur
    $(document).on('blur change', '.obtained-marks, .total-marks', function () {
        const $entry = $(this).closest('[data-repeater-item]');
        const $obtainedMarks = $entry.find('.obtained-marks');

        // Trigger jQuery Validation on the obtained marks field
        const formValidator = $form.data('validator');
        if (formValidator && $obtainedMarks.length) {
            formValidator.element($obtainedMarks);
        }
    });

    // Initialize jQuery Validation (Bootstrap-friendly)
    const validator = $form.validate({
        ...getCommonConfig($form),
        rules: getCommonRules('create'),
        messages: getCommonMessages()
    });

    // Single submit handler: validate (jQuery Validate) then AJAX submit; never refresh.
    $form.off('submit.addStudent').on('submit.addStudent', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Validate form using jQuery Validation (includes all marks entries)
        const formIsValid = $form.valid();

        if (!formIsValid) {
            return false;
        }

        submitForm(this);
        return false;
    });


    // Submit form via AJAX
    function submitForm(form) {
        const formData = new FormData(form);
        showLoadingAlert('Creating student...');

        $.ajax({
            url: route('students.store'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                closeAlert();
                if (response.success) {
                    // After successful creation: clear form, close modal, refresh datatable
                    hideStudentModal();
                    reloadStudentsDataTable();

                    showSuccessAlert(response.message, 'Success!');
                } else {
                    showErrorAlert(response.message || 'Failed to create student.');
                }
            },
            error: function (xhr) {
                closeAlert();
                let errorMessage = 'Failed to create student. Please try again.';

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    const errorMessages = [];

                    // Collect all validation errors
                    Object.keys(errors).forEach(function (key) {
                        errors[key].forEach(function (message) {
                            errorMessages.push(message);
                        });
                    });

                    if (errorMessages.length > 0) {
                        errorMessage = errorMessages.join('\n');
                    }

                    // Show field-specific errors
                    Object.keys(errors).forEach(function (key) {
                        const $field = $form.find(`[name="${key}"]`);
                        if ($field.length) {
                            $field.addClass('is-invalid');
                            if (!$field.next('.invalid-feedback').length) {
                                $field.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                            }
                        }
                    });
                }

                showErrorAlert(errorMessage, 'Validation Error');
            },
        });
    }

    // Reset form when modal is closed
    $modal.on('hidden.bs.modal', function () {
        resetFormAndMarks();

        // Destroy datepicker if it exists
        const $birthDate = $('#birth_date');
        if ($birthDate.hasClass('hasDatepicker')) {
            $birthDate.datepicker('destroy');
        }
    });

    // Initialize remove buttons
    updateRemoveButtons();
    syncSelectedSubjects();
});
