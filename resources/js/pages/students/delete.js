import $ from 'jquery';
import Swal from 'sweetalert2';
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/alert.js';

$(document).ready(function () {
    $(document).on('student:delete', function (e, id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this! All associated address, marks and files will be deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteStudent(id);
            }
        });
    });

    function deleteStudent(id) {
        showLoadingAlert('Deleting student...');

        $.ajax({
            url: route('students.destroy', id),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                closeAlert();
                if (response.success) {
                    $('#students-table').DataTable().ajax.reload(null, false);
                    showSuccessAlert(response.message, 'Deleted!');
                } else {
                    showErrorAlert(response.message || 'Failed to delete student.');
                }
            },
            error: function (xhr) {
                closeAlert();
                const message = xhr.responseJSON?.message || 'An error occurred while deleting the student.';
                showErrorAlert(message, 'Error');
            }
        });
    }
});

