<div class="d-flex justify-content-center gap-2">
    <button type="button" class="btn btn-sm btn-outline-info view-student" data-id="{{ $student->id }}" title="View Details">
        <i class="bi bi-eye"></i>
    </button>
    <button type="button" class="btn btn-sm btn-outline-primary edit-student" data-id="{{ $student->id }}" title="Edit Student">
        <i class="bi bi-pencil"></i>
    </button>
    <button type="button" class="btn btn-sm btn-outline-danger delete-student" data-id="{{ $student->id }}" title="Delete Student">
        <i class="bi bi-trash"></i>
    </button>
</div>

