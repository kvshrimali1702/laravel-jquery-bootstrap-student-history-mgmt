import Swal from 'sweetalert2';

/**
 * Show success alert
 *
 * @param {string} message
 * @param {string} title
 * @returns {Promise}
 */
export function showSuccessAlert(message, title = 'Success!') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonColor: '#198754',
        confirmButtonText: 'OK',
    });
}

/**
 * Show error alert
 *
 * @param {string} message
 * @param {string} title
 * @returns {Promise}
 */
export function showErrorAlert(message, title = 'Error!') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'OK',
    });
}

/**
 * Show warning alert
 *
 * @param {string} message
 * @param {string} title
 * @returns {Promise}
 */
export function showWarningAlert(message, title = 'Warning!') {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'OK',
    });
}

/**
 * Show info alert
 *
 * @param {string} message
 * @param {string} title
 * @returns {Promise}
 */
export function showInfoAlert(message, title = 'Info') {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonColor: '#0dcaf0',
        confirmButtonText: 'OK',
    });
}

/**
 * Show confirmation dialog
 *
 * @param {string} message
 * @param {string} title
 * @param {string} confirmText
 * @param {string} cancelText
 * @returns {Promise}
 */
export function showConfirmDialog(message, title = 'Are you sure?', confirmText = 'Yes', cancelText = 'No') {
    return Swal.fire({
        icon: 'question',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#dc3545',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
    });
}

/**
 * Show loading alert
 *
 * @param {string} message
 * @returns {void}
 */
export function showLoadingAlert(message = 'Please wait...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
}

/**
 * Close current alert
 *
 * @returns {void}
 */
export function closeAlert() {
    Swal.close();
}




