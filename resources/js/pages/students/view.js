import $ from '../../utils/jquery-select2.js';
import { Modal } from 'bootstrap';

$(document).ready(function () {
    const $modal = $('#viewStudentModal');

    $(document).on('student:view', function (e, id) {
        $.ajax({
            url: route('students.show', id),
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    populateViewModal(response.data);
                    const modal = new Modal($modal[0]);
                    modal.show();
                }
            },
            error: function () {
                alert('Failed to fetch student details.');
            }
        });
    });

    function populateViewModal(student) {
        $('#view_full_name').text(`${student.first_name} ${student.last_name}`);

        // Format birth date to a more readable format (e.g., Dec 19, 2025)
        let formattedBirthDate = student.birth_date;
        if (student.birth_date) {
            const date = new Date(student.birth_date);
            if (!isNaN(date.getTime())) {
                formattedBirthDate = date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        }
        $('#view_birth_date').text(formattedBirthDate);

        $('#view_standard').text(student.standard);

        const statusLabel = student.status === 1 ? 'Active' : 'Inactive';
        const statusClass = student.status === 1 ? 'bg-success' : 'bg-danger';
        $('#view_status').html(`<span class="badge ${statusClass}">${statusLabel}</span>`);

        if (student.profile_picture) {
            $('#view_profile_picture').html(`<img src="${student.profile_picture}" alt="Profile" class="rounded-circle img-thumbnail" width="120" height="120">`);
        } else {
            const initial = student.first_name.charAt(0).toUpperCase();
            $('#view_profile_picture').html(`<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto" style="width: 120px; height: 120px; font-size: 48px;">${initial}</div>`);
        }

        if (student.address) {
            $('#view_full_address').text(student.address.full_address || 'N/A');
            $('#view_street_number').text(student.address.street_number || 'N/A');
            $('#view_street_name').text(student.address.street_name || 'N/A');
            $('#view_city').text(student.address.city || 'N/A');
            $('#view_state').text(student.address.state || 'N/A');
            $('#view_postcode').text(student.address.postcode || 'N/A');
            $('#view_country').text(student.address.country || 'N/A');
        }

        let marksHtml = '';
        let totalMarksSum = 0;
        let obtainedMarksSum = 0;

        if (student.student_subject_marks && student.student_subject_marks.length > 0) {
            student.student_subject_marks.forEach(mark => {
                const percentage = ((mark.obtained_marks / mark.total_marks) * 100).toFixed(2);
                totalMarksSum += parseInt(mark.total_marks);
                obtainedMarksSum += parseInt(mark.obtained_marks);

                let proofHtml = 'No Proof';
                if (mark.proof) {
                    proofHtml = `<a href="${mark.proof}" target="_blank" class="btn btn-sm btn-outline-secondary">View Proof</a>`;
                }

                marksHtml += `
                    <tr>
                        <td>${mark.subject.name}</td>
                        <td class="text-center">${mark.total_marks}</td>
                        <td class="text-center">${mark.obtained_marks}</td>
                        <td class="text-center">${percentage}%</td>
                        <td class="text-center">${proofHtml}</td>
                    </tr>
                `;
            });
        } else {
            marksHtml = '<tr><td colspan="5" class="text-center">No marks recorded</td></tr>';
        }

        $('#view_marks_body').html(marksHtml);
        $('#view_total_marks_sum').text(totalMarksSum);
        $('#view_obtained_marks_sum').text(obtainedMarksSum);

        const totalPercentage = totalMarksSum > 0 ? ((obtainedMarksSum / totalMarksSum) * 100).toFixed(2) : 0;
        $('#view_total_percentage').text(`${totalPercentage}%`);
    }
});

