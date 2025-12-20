@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Students Management</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addStudentModal">
                                + Add Student
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="students-table" class="table table-striped table-hover table-bordered align-middle"
                                style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Profile Pic</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>DOB</th>
                                        <th class="text-center">Standard</th>
                                        <th class="text-center">Status</th>
                                        <th>Address</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStudentForm" enctype="multipart/form-data" novalidate action="javascript:void(0);">
                    @csrf
                    <div class="modal-body">
                        <!-- Student Details -->
                        <h6 class="mb-3">Student Details</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Birth Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="birth_date" name="birth_date" readonly required>
                            </div>
                            <div class="col-md-6">
                                <label for="standard" class="form-label">Standard <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="standard" name="standard" min="1" max="12"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                                <small class="form-text text-muted">Accepted formats: JPEG, JPG, PNG, GIF, BMP, WebP, SVG
                                    (Max: 2MB)</small>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <hr class="my-4">
                        <h6 class="mb-3">Address Details</h6>
                        <div class="mb-3">
                            <label for="full_address" class="form-label">Full Address <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="full_address" name="full_address" rows="2"
                                required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="street_number" class="form-label">Street Number</label>
                                <input type="text" class="form-control" id="street_number" name="street_number"
                                    maxlength="50">
                            </div>
                            <div class="col-md-6">
                                <label for="street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control" id="street_name" name="street_name" maxlength="50">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" maxlength="50" required>
                            </div>
                            <div class="col-md-4">
                                <label for="postcode" class="form-label">Postcode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="postcode" name="postcode" maxlength="30"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" maxlength="50" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="country" name="country" maxlength="50" required>
                        </div>

                        <!-- Student Marks -->
                        <hr class="my-4">
                        <h6 class="mb-3">Student Marks</h6>
                        <div id="marks-repeater">
                            <div class="marks-entry mb-3 border p-3 rounded" data-repeater-item>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Subject Marks Entry</h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-entry" data-repeater-delete>
                                        × Remove
                                    </button>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                                        <select class="form-select subject-select" name="marks[0][subject_id]" required>
                                            <option value="">Select Subject</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control total-marks" name="marks[0][total_marks]"
                                            min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Obtained Marks <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control obtained-marks"
                                            name="marks[0][obtained_marks]" min="0" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Proof (Optional)</label>
                                    <input type="file" class="form-control" name="marks[0][proof]">
                                    <small class="form-text text-muted">Accepted formats: PDF, Word, Excel, PowerPoint,
                                        Text, Images (JPEG, JPG, PNG, GIF, BMP, WebP, SVG) (Max: 10MB)</small>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="add-marks-entry">
                            + Add Another Subject
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStudentForm" enctype="multipart/form-data" novalidate action="javascript:void(0);">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_student_id" name="id">
                    <div class="modal-body">
                        <!-- Student Details -->
                        <h6 class="mb-3">Student Details</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_birth_date" class="form-label">Birth Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_birth_date" name="birth_date" readonly
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_standard" class="form-label">Standard <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_standard" name="standard" min="1"
                                    max="12" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_profile_picture" class="form-label">Profile Picture</label>
                                <div id="edit_profile_picture_preview" class="mb-2 d-none">
                                    <img src="" alt="Profile Preview" class="rounded-circle" width="60" height="60">
                                </div>
                                <input type="file" class="form-control" id="edit_profile_picture" name="profile_picture">
                                <small class="form-text text-muted">Leave empty to keep current picture. Accepted formats:
                                    JPEG, JPG, PNG, GIF, BMP, WebP, SVG
                                    (Max: 2MB)</small>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <hr class="my-4">
                        <h6 class="mb-3">Address Details</h6>
                        <div class="mb-3">
                            <label for="edit_full_address" class="form-label">Full Address <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_full_address" name="full_address" rows="2"
                                required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_street_number" class="form-label">Street Number</label>
                                <input type="text" class="form-control" id="edit_street_number" name="street_number"
                                    maxlength="50">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_street_name" class="form-label">Street Name</label>
                                <input type="text" class="form-control" id="edit_street_name" name="street_name"
                                    maxlength="50">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit_city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_city" name="city" maxlength="50" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_postcode" class="form-label">Postcode <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_postcode" name="postcode" maxlength="30"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_state" class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_state" name="state" maxlength="50"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_country" class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_country" name="country" maxlength="50"
                                required>
                        </div>

                        <!-- Student Marks -->
                        <hr class="my-4">
                        <h6 class="mb-3">Student Marks</h6>
                        <div id="edit-marks-repeater">
                            <div id="edit-marks-container">
                                <!-- Existing marks will be loaded here -->
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="edit-add-marks-entry">
                            + Add Another Subject
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Student Modal -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div id="view_profile_picture" class="mb-3">
                                <!-- Profile picture here -->
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h4 id="view_full_name" class="mb-1"></h4>
                            <p id="view_status" class="mb-2"></p>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <strong>DOB:</strong> <span id="view_birth_date"></span>
                                </div>
                                <div class="col-6">
                                    <strong>Standard:</strong> <span id="view_standard"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-geo-alt-fill text-primary me-2 fs-5"></i>
                        <h6 class="mb-0">Address Information</h6>
                    </div>
                    <div class="address-details bg-light p-3 p-md-4 rounded-3 mb-4 border shadow-sm">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="bg-white p-3 rounded-circle border shadow-sm me-3 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-geo-alt text-danger fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Full
                                            Address</label>
                                        <p id="view_full_address" class="mb-0 fw-semibold fs-5 text-dark"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-2 bg-white rounded border shadow-sm h-100">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-house-door fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="text-muted small d-block lh-1 mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.65rem;">Street Info</label>
                                        <div class="text-dark">
                                            <span id="view_street_number" class="fw-medium"></span>
                                            <span id="view_street_name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-2 bg-white rounded border shadow-sm h-100">
                                    <div class="bg-info-subtle text-info p-2 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-mailbox fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="text-muted small d-block lh-1 mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.65rem;">Postcode</label>
                                        <span id="view_postcode" class="fw-medium text-dark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-flex align-items-center p-2 bg-white rounded border shadow-sm h-100">
                                    <div class="bg-success-subtle text-success p-2 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-building fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="text-muted small d-block lh-1 mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.65rem;">City</label>
                                        <span id="view_city" class="fw-medium text-dark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-flex align-items-center p-2 bg-white rounded border shadow-sm h-100">
                                    <div class="bg-warning-subtle text-warning p-2 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-map fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="text-muted small d-block lh-1 mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.65rem;">State</label>
                                        <span id="view_state" class="fw-medium text-dark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-2 bg-white rounded border shadow-sm h-100">
                                    <div class="bg-secondary-subtle text-secondary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-globe fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="text-muted small d-block lh-1 mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.65rem;">Country</label>
                                        <span id="view_country" class="fw-medium text-dark"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6>Academic Performance (Marks)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-center">Total Marks</th>
                                    <th class="text-center">Obtained Marks</th>
                                    <th class="text-center">Percentage</th>
                                    <th class="text-center">Proof</th>
                                </tr>
                            </thead>
                            <tbody id="view_marks_body">
                                <!-- Marks here -->
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td>Total</td>
                                    <td class="text-center" id="view_total_marks_sum">0</td>
                                    <td class="text-center" id="view_obtained_marks_sum">0</td>
                                    <td class="text-center" id="view_total_percentage">0%</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template for new marks in edit mode (Moved outside form to avoid submission) -->
    <template id="edit-mark-template">
        <div class="marks-entry mb-3 border p-3 rounded" data-repeater-item>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Subject Marks Entry</h6>
                <button type="button" class="btn btn-sm btn-danger remove-edit-entry">
                    × Remove
                </button>
            </div>
            <input type="hidden" class="mark-id" name="marks[TEMP_IDX][id]">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                    <select class="form-select edit-subject-select" name="marks[TEMP_IDX][subject_id]" required>
                        <option value="">Select Subject</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                    <input type="number" class="form-control edit-total-marks" name="marks[TEMP_IDX][total_marks]" min="1"
                        required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Obtained Marks <span class="text-danger">*</span></label>
                    <input type="number" class="form-control edit-obtained-marks" name="marks[TEMP_IDX][obtained_marks]"
                        min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Proof (Optional)</label>
                <div class="edit-proof-preview mb-2 d-none">
                    <a href="" target="_blank" class="btn btn-sm btn-outline-secondary">View Current Proof</a>
                </div>
                <input type="file" class="form-control" name="marks[TEMP_IDX][proof]">
                <small class="form-text text-muted">Accepted formats: PDF, Word, Excel, PowerPoint,
                    Text, Images (JPEG, JPG, PNG, GIF, BMP, WebP, SVG) (Max: 10MB)</small>
            </div>
        </div>
    </template>

    @push('styles')
        @vite(['resources/css/pages/students/index.css'])
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    @vite(['resources/js/pages/students/index.js', 'resources/js/pages/students/create.js', 'resources/js/pages/students/edit.js', 'resources/js/pages/students/view.js', 'resources/js/pages/students/delete.js'])
@endsection