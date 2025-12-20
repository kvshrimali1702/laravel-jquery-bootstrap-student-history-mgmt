import $ from 'jquery';

/**
 * Register common custom validation methods for jQuery Validation plugin
 */
export function registerCustomMethods() {
    // Custom validation for duplicate subjects
    if (!$.validator.methods.uniqueSubject) {
        $.validator.addMethod('uniqueSubject', function (value, element) {
            if (!value) {
                return true;
            }

            const $form = $(element).closest('form');
            const selector = $(element).hasClass('edit-subject-select') ? '.edit-subject-select' : '.subject-select';
            const $selects = $form.find(selector);
            
            let count = 0;
            $selects.each(function () {
                if ($(this).val() == value) {
                    count++;
                }
            });

            return count <= 1;
        }, 'Each subject can only be selected once.');
    }

    // Custom validation for birth date to be before today
    if (!$.validator.methods.beforeToday) {
        $.validator.addMethod('beforeToday', function (value, element) {
            if (!value) {
                return true;
            }

            const inputDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            return inputDate < today;
        }, 'Birth date must be before today.');
    }

    // Custom validation for file size
    if (!$.validator.methods.filesize) {
        $.validator.addMethod('filesize', function (value, element, param) {
            if (!element.files || element.files.length === 0) {
                return true;
            }
            return element.files[0].size <= param;
        }, 'File size must not exceed {0} bytes.');
    }

    // Custom validation for obtained marks <= total marks
    if (!$.validator.methods.maxObtainedMarks) {
        $.validator.addMethod('maxObtainedMarks', function (value, element) {
            if (!value || value === '') {
                return true;
            }

            const obtained = parseInt(value);
            if (isNaN(obtained)) {
                return true;
            }

            const $entry = $(element).closest('[data-repeater-item], .marks-entry');
            const totalSelector = $(element).hasClass('edit-obtained-marks') ? '.edit-total-marks' : '.total-marks';
            const $totalMarks = $entry.find(totalSelector);
            const totalValue = $totalMarks.val();

            if (!totalValue || totalValue === '') {
                return true;
            }

            const total = parseInt(totalValue);
            if (isNaN(total)) {
                return true;
            }

            return obtained <= total;
        }, 'Obtained marks cannot be greater than total marks.');
    }
}

/**
 * Get common validation rules for students
 * @param {string} mode 'create' or 'edit'
 */
export function getCommonRules(mode = 'create') {
    return {
        first_name: {
            required: true,
            minlength: 2,
            maxlength: 50,
        },
        last_name: {
            required: true,
            minlength: 2,
            maxlength: 50,
        },
        birth_date: {
            required: true,
            date: true,
            beforeToday: true,
        },
        standard: {
            required: true,
            digits: true,
            min: 1,
            max: 12,
        },
        status: {
            required: true,
            digits: true,
            range: [0, 1],
        },
        profile_picture: {
            extension: "jpg|jpeg|png|gif|bmp|webp|svg",
            filesize: 2097152, // 2MB
            accept: false,
        },
        full_address: {
            required: true,
            minlength: 3,
            maxlength: 1000,
        },
        street_number: {
            maxlength: 50,
        },
        street_name: {
            maxlength: 50,
        },
        city: {
            required: true,
            minlength: 2,
            maxlength: 50,
        },
        postcode: {
            required: true,
            minlength: 4,
            maxlength: 30,
        },
        state: {
            required: true,
            minlength: 2,
            maxlength: 50,
        },
        country: {
            required: true,
            minlength: 2,
            maxlength: 50,
        },
    };
}

/**
 * Get common validation messages
 */
export function getCommonMessages() {
    return {
        profile_picture: {
            extension: 'Profile picture must be a valid image file (JPG, JPEG, PNG, GIF, BMP, WebP, SVG).',
            filesize: 'Profile picture must not exceed 2MB.',
        },
        first_name: {
            required: 'First name is required.',
            maxlength: 'First name must not exceed 50 characters.',
        },
        last_name: {
            required: 'Last name is required.',
            maxlength: 'Last name must not exceed 50 characters.',
        },
        birth_date: {
            required: 'Birth date is required.',
            date: 'Birth date must be a valid date.',
            beforeToday: 'Birth date must be before today.',
        },
        standard: {
            required: 'Standard is required.',
            number: 'Standard must be a number.',
            min: 'Standard must be at least 1.',
            max: 'Standard must not exceed 12.',
        },
        status: {
            required: 'Status is required.',
        },
        full_address: {
            required: 'Full address is required.',
        },
        city: {
            required: 'City is required.',
            minlength: 'City must be at least 2 characters.',
            maxlength: 'City must not exceed 50 characters.',
        },
        postcode: {
            required: 'Postcode is required.',
            minlength: 'Postcode must be at least 4 characters.',
            maxlength: 'Postcode must not exceed 30 characters.',
        },
        state: {
            required: 'State is required.',
            minlength: 'State must be at least 2 characters.',
            maxlength: 'State must not exceed 50 characters.',
        },
        country: {
            required: 'Country is required.',
            minlength: 'Country must be at least 2 characters.',
            maxlength: 'Country must not exceed 50 characters.',
        },
    };
}

/**
 * Get common validation configuration
 * @param {jQuery} $form 
 */
export function getCommonConfig($form) {
    return {
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        highlight: function (element) {
            const $element = $(element);
            $element.addClass('is-invalid').removeClass('is-valid');

            if ($element.hasClass('select2-hidden-accessible')) {
                $element.next('.select2-container').find('.select2-selection').addClass('is-invalid');
            }
        },
        unhighlight: function (element) {
            const $element = $(element);
            $element.removeClass('is-invalid').addClass('is-valid');

            if ($element.hasClass('select2-hidden-accessible')) {
                $element.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }
        },
        errorPlacement: function (error, element) {
            const $element = $(element);
            const fieldName = $element.attr('name');

            const $entry = $element.closest('[data-repeater-item], .marks-entry');
            if (fieldName && $entry.length) {
                $entry.find('.invalid-feedback[data-field="' + fieldName + '"]').remove();
            } else if (fieldName) {
                $form.find('.invalid-feedback[data-field="' + fieldName + '"]').remove();
            }

            error.attr('data-field', fieldName);
            error.css('display', 'block');

            if ($element.hasClass('select2-hidden-accessible')) {
                const $container = $element.next('.select2-container');
                if ($container.length) {
                    $container.after(error);
                } else {
                    $element.after(error);
                }
            } else {
                $element.after(error);
            }
        },
        onkeyup: false,
        onfocusout: function (element) {
            if (!$(element).hasClass('select2-hidden-accessible')) {
                this.element(element);
            }
        },
    };
}

/**
 * Add rules for a single marks entry
 * @param {object} validator jQuery validator instance
 * @param {string} baseName prefix for the fields (e.g. 'marks[0]')
 * @param {jQuery} $container the container element for this entry
 */
export function addMarkRules(validator, baseName, $container) {
    const isEdit = $container.hasClass('marks-entry') && $container.find('.edit-subject-select').length > 0;
    
    const subjectSelector = isEdit ? '.edit-subject-select' : '.subject-select';
    const totalSelector = isEdit ? '.edit-total-marks' : '.total-marks';
    const obtainedSelector = isEdit ? '.edit-obtained-marks' : '.obtained-marks';

    const $subject = $container.find(subjectSelector);
    const $total = $container.find(totalSelector);
    const $obtained = $container.find(obtainedSelector);
    const $proof = $container.find('input[type="file"]');

    if ($subject.length) {
        $subject.rules('add', {
            required: true,
            uniqueSubject: true,
            messages: {
                required: 'Subject is required.',
                uniqueSubject: 'Each subject can only be selected once.'
            }
        });
    }

    if ($total.length) {
        $total.rules('add', {
            required: true,
            digits: true,
            min: 1,
            max: 1000,
            messages: {
                required: 'Total marks is required.',
                digits: 'Total marks must be a number.',
                min: 'Total marks must be at least 1.',
                max: 'Total marks must not exceed 1000.'
            }
        });
    }

    if ($obtained.length) {
        $obtained.rules('add', {
            required: true,
            digits: true,
            min: 0,
            maxObtainedMarks: true,
            messages: {
                required: 'Obtained marks is required.',
                digits: 'Obtained marks must be a number.',
                min: 'Obtained marks cannot be negative.',
                maxObtainedMarks: 'Obtained marks cannot be greater than total marks.'
            }
        });
    }

    if ($proof.length) {
        $proof.rules('add', {
            extension: "pdf|doc|docx|txt|ppt|pptx|xls|xlsx|odt|ods|odp|jpg|jpeg|png|gif|bmp|webp|svg",
            filesize: 10485760, // 10MB
            accept: false,
            messages: {
                extension: 'Proof must be a valid file type (PDF, Word, Excel, PowerPoint, Text, Images).',
                filesize: 'Proof file must not exceed 10MB.'
            }
        });
    }
}

