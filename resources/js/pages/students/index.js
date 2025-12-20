import $ from '../../utils/jquery-select2.js';
import 'datatables.net';
import 'datatables.net-bs5';

$(document).ready(function () {
    const $table = $('#students-table');
    const studentsTableUrl = route('students.index');

    $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: studentsTableUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        },
        columns: [
            { data: 'profile_picture', name: 'profile_picture', orderable: false, searchable: false },
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'birth_date', name: 'birth_date' },
            { data: 'standard', name: 'standard' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'address', name: 'address', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        pageLength: 10,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: 'No students found',
            zeroRecords: 'No matching students found',
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            },
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)'
        }
    });
});

