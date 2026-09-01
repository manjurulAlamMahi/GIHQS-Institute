@extends('backend.app')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Manage Managers</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item active">Manage Managers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Manager List</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManagerModal">
                            <i class="ri-add-line align-middle me-1"></i> Add New Manager
                        </button>
                    </div>
                </div><!-- end card header -->

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap align-middle" id="adminTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Manager Modal -->
    <div class="modal fade" id="addManagerModal" tabindex="-1" aria-labelledby="addManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <form id="addManagerForm">
                    @csrf
                    <div class="modal-header p-3 bg-soft-info">
                        <h5 class="modal-title" id="addManagerModalLabel">Add New Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter Last Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" id="country" placeholder="Enter Country">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Manager Modal -->
    <div class="modal fade" id="editManagerModal" tabindex="-1" aria-labelledby="editManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <form id="editManagerForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_manager_id">
                    <div class="modal-header p-3 bg-soft-primary">
                        <h5 class="modal-title" id="editManagerModalLabel">Edit Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#editProfile" role="tab">
                                    Edit Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                    Change Password
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="editProfile" role="tabpanel">
                                <div class="mb-3">
                                    <label for="edit_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="Enter First Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="Enter Last Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_country" class="form-label">Country</label>
                                    <input type="text" class="form-control" name="country" id="edit_country" placeholder="Enter Country">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="edit_username" placeholder="Enter Username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="edit_email" placeholder="Enter Email" required>
                                </div>
                            </div>
                            <div class="tab-pane" id="changePassword" role="tabpanel">
                                <div class="mb-3">
                                    <label for="edit_current_password" class="form-label">Current Password (Your Admin
                                        Password)</label>
                                    <input type="password" class="form-control" name="current_password" id="edit_current_password" placeholder="Enter Your Admin Password">
                                    <small class="text-muted">Enter your own password to authorize this change.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="password" id="edit_password" placeholder="Enter New Password">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_password_confirmation" class="form-label">Confirm New
                                        Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" id="edit_password_confirmation" placeholder="Confirm Password">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let table = $('#adminTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.managers.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Add Manager
            $('#addManagerForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.managers.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addManagerModal').modal('hide');
                            $('#addManagerForm')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                        });
                    }
                });
            });

            // Edit Manager - Populate Modal
            $(document).on('click', '.edit-button', function() {
                let id = $(this).data('id');
                let first_name = $(this).data('first_name');
                let last_name = $(this).data('last_name');
                let country = $(this).data('country');
                let username = $(this).data('username');
                let email = $(this).data('email');

                $('#edit_manager_id').val(id);
                $('#edit_first_name').val(first_name);
                $('#edit_last_name').val(last_name);
                $('#edit_country').val(country);
                $('#edit_username').val(username);
                $('#edit_email').val(email);
                $('#edit_password').val('');
                $('#edit_password_confirmation').val('');
                $('#edit_current_password').val('');

                // Reset to first tab
                $('.nav-tabs a[href="#editProfile"]').tab('show');

                $('#editManagerModal').modal('show');
            });

            // Update Manager
            $('#editManagerForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_manager_id').val();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ url('admin/settings/managers') }}/" + id,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#editManagerModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                        });
                    }
                });
            });

            // Delete Manager
            $(document).on('click', '.delete-button', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/settings/managers') }}/" + id,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                    table.ajax.reload();
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
