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
                                <input type="date" class="form-control" id="birth_date" name="birth_date"
                                    max="{{ date('Y-m-d', strtotime('-1 day')) }}" required>
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
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                    accept=".jpeg,.jpg,.png,.gif,.bmp,.webp,.svg">
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
                                    <input type="file" class="form-control" name="marks[0][proof]"
                                        accept=".pdf,.doc,.docx,.txt,.ppt,.pptx,.xls,.xlsx,.odt,.ods,.odp,.jpeg,.jpg,.png,.gif,.bmp,.webp,.svg">
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

    @push('styles')
        @vite(['resources/css/pages/students/index.css'])
    @endpush


    @vite(['resources/js/pages/students/index.js', 'resources/js/pages/students/create.js'])
@endsection