import $ from '../../utils/jquery-select2.js';
import { ensureSelect2 } from '../../utils/jquery-select2.js';
import { Modal } from 'bootstrap';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';
import 'jquery-ui/ui/widgets/datepicker';
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/alert.js';
import { registerCustomMethods, getCommonRules, getCommonMessages, getCommonConfig, addMarkRules } from './shared-validation.js';

$(document).ready(async function () {
    await ensureSelect2();
    registerCustomMethods();

    const $form = $('#editStudentForm');
    const $modal = $('#editStudentModal');
    const $marksContainer = $('#edit-marks-container');
    const $markTemplate = $('#edit-mark-template').html();
    let entryIndex = 0;
    let selectedSubjects = new Set();

    function getSelect2Jquery() {
        if (typeof window !== 'undefined' && window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 !== 'undefined') {
            return window.jQuery;
        }
        return (typeof $.fn.select2 !== 'undefined') ? $ : null;
    }

    function syncSelectedSubjects() {
        const ids = [];
        $marksContainer.find('.edit-subject-select').each(function () {
            const value = $(this).val();
            if (value) {
                ids.push(value.toString());
            }
        });
        selectedSubjects = new Set(ids);
    }

    function safelyDestroySelect2($select) {
        if (!$select || $select.length === 0) return;
        const select2Jq = getSelect2Jquery();
        if (!select2Jq || typeof select2Jq.fn.select2 === 'undefined') return;
        const $select2 = $select instanceof select2Jq ? $select : select2Jq($select[0] || $select);
        if ($select2.data('select2')) {
            try { $select2.select2('destroy'); } catch (e) { }
        }
        $select2.removeData().next('.select2-container').remove();
        $select2.removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex');
    }

    function initializeSubjectSelect($select, initialValue = null) {
        const select2Jq = getSelect2Jquery();
        if (!select2Jq) return;

        const $select2 = select2Jq($select[0]);
        safelyDestroySelect2($select2);
        $select2.empty();

        const subjectsUrl = route('subjects.index');

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
                data: (params) => ({ search: params.term || '' }),
                processResults: (data) => {
                    const currentSelections = new Set();
                    $marksContainer.find('.edit-subject-select').each(function() {
                        const val = $(this).val();
                        if (val && val != $select2.val()) currentSelections.add(val.toString());
                    });
                    return {
                        results: (data.results || []).filter(s => !currentSelections.has(s.id.toString()))
                    };
                },
                cache: false
            }
        });

        if (initialValue) {
            const option = new Option(initialValue.name, initialValue.id, true, true);
            $select2.append(option).trigger('change');
        }

        $select2.on('select2:select select2:unselect', () => {
            syncSelectedSubjects();
            const validator = $form.data('validator');
            if (validator) validator.element($select2);
        });
    }

    function addMarkEntry(markData = null) {
        const index = entryIndex++;
        let html = $markTemplate.replace(/TEMP_IDX/g, index);
        const $newEntry = $(html);

        $marksContainer.append($newEntry);

        const $select = $newEntry.find('.edit-subject-select');
        const $idField = $newEntry.find('.mark-id');
        const $totalMarks = $newEntry.find('.edit-total-marks');
        const $obtainedMarks = $newEntry.find('.edit-obtained-marks');
        const $proofPreview = $newEntry.find('.edit-proof-preview');

        if (markData) {
            $idField.val(markData.id);
            $totalMarks.val(markData.total_marks);
            $obtainedMarks.val(markData.obtained_marks);
            if (markData.proof) {
                $proofPreview.removeClass('d-none').find('a').attr('href', markData.proof);
            }
            initializeSubjectSelect($select, markData.subject);
        } else {
            initializeSubjectSelect($select);
        }

        // Add rules for new fields
        const validator = $form.data('validator');
        if (validator) {
            addMarkRules(validator, `marks[${index}]`, $newEntry);
        }

        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const count = $marksContainer.find('.marks-entry').length;
        $marksContainer.find('.remove-edit-entry').toggle(count > 1);
    }

    $(document).on('click', '.remove-edit-entry', function() {
        const $entry = $(this).closest('.marks-entry');
        safelyDestroySelect2($entry.find('.edit-subject-select'));
        $entry.remove();
        updateRemoveButtons();
        syncSelectedSubjects();
    });

    $('#edit-add-marks-entry').on('click', () => addMarkEntry());

    // Initialize jQuery Validation (Bootstrap-friendly)
    const validator = $form.validate({
        ...getCommonConfig($form),
        rules: getCommonRules('edit'),
        messages: getCommonMessages()
    });

    $(document).on('student:edit', async function (e, id) {
        showLoadingAlert('Fetching student details...');
        $.ajax({
            url: route('students.edit', id),
            type: 'GET',
            success: function (response) {
                closeAlert();
                if (response.success) {
                    populateEditForm(response.data);
                    const modal = Modal.getOrCreateInstance($modal[0]);
                    modal.show();
                }
            },
            error: function () {
                closeAlert();
                showErrorAlert('Failed to fetch student details.');
            }
        });
    });

    function populateEditForm(student) {
        $form[0].reset();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        $('#edit_student_id').val(student.id);
        $('#edit_first_name').val(student.first_name);
        $('#edit_last_name').val(student.last_name);

        // Format birth date to YYYY-MM-DD for the datepicker/input
        let birthDateValue = student.birth_date;
        if (student.birth_date) {
            const date = new Date(student.birth_date);
            if (!isNaN(date.getTime())) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                birthDateValue = `${year}-${month}-${day}`;
            }
        }
        $('#edit_birth_date').val(birthDateValue);

        $('#edit_standard').val(student.standard);
        $('#edit_status').val(student.status);

        if (student.profile_picture) {
            $('#edit_profile_picture_preview').removeClass('d-none').find('img').attr('src', student.profile_picture);
        } else {
            $('#edit_profile_picture_preview').addClass('d-none');
        }

        if (student.address) {
            $('#edit_full_address').val(student.address.full_address);
            $('#edit_street_number').val(student.address.street_number);
            $('#edit_street_name').val(student.address.street_name);
            $('#edit_city').val(student.address.city);
            $('#edit_postcode').val(student.address.postcode);
            $('#edit_state').val(student.address.state);
            $('#edit_country').val(student.address.country);
        }

        $marksContainer.empty();
        entryIndex = 0;
        if (student.student_subject_marks && student.student_subject_marks.length > 0) {
            student.student_subject_marks.forEach(mark => addMarkEntry(mark));
        } else {
            addMarkEntry();
        }

        initEditDatepicker();
    }

    function initEditDatepicker() {
        const $birthDate = $('#edit_birth_date');
        if ($birthDate.hasClass('hasDatepicker')) {
            $birthDate.datepicker('destroy');
        }
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        $birthDate.datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:' + new Date().getFullYear(),
            maxDate: yesterday
        });
    }

    // Validate obtained marks <= total marks - trigger validation on blur
    $(document).on('blur change', '.edit-obtained-marks, .edit-total-marks', function () {
        const $entry = $(this).closest('.marks-entry');
        const $obtainedMarks = $entry.find('.edit-obtained-marks');

        const formValidator = $form.data('validator');
        if (formValidator && $obtainedMarks.length) {
            formValidator.element($obtainedMarks);
        }
    });

    $form.on('submit', function (e) {
        e.preventDefault();
        if (!$form.valid()) return;

        const id = $('#edit_student_id').val();
        const formData = new FormData(this);

        showLoadingAlert('Updating student...');
        $.ajax({
            url: route('students.update', id),
            type: 'POST', // Spoofed to PUT via @method('PUT') in form
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                closeAlert();
                if (response.success) {
                    Modal.getInstance($modal[0]).hide();
                    $('#students-table').DataTable().ajax.reload(null, false);
                    showSuccessAlert(response.message, 'Updated!');
                }
            },
            error: function (xhr) {
                closeAlert();
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let msg = Object.values(errors).flat().join('\n');
                    showErrorAlert(msg, 'Validation Error');
                } else {
                    showErrorAlert('Failed to update student.');
                }
            }
        });
    });
});
