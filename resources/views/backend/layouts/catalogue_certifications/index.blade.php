@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Catalogue Certifications</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Certifications List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Catalogue Certifications</h4>
                    {{-- 
                    <a href="{{ route('admin.catalogue-certifications.create') }}" class="btn btn-sm btn-success">
                        <i class="fa-solid fa-plus me-1"></i> Add Catalogue Certification
                    </a>
                    --}}
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="examsResourcesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Catalogue Title</th>
                                <th>Short Title</th>
                                <th>Exams</th>
                                <th>Resources</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var table = $('#examsResourcesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.catalogue-certifications.index') }}",
                columns: [
                    { data: 'DT_RowIndex',      name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title',             name: 'title' },
                    { data: 'short_title',       name: 'short_title' },
                    { data: 'exams_count',       name: 'exams_count', orderable: false, searchable: false },
                    { data: 'resources_count',   name: 'resources_count', orderable: false, searchable: false },
                    { data: 'action',            name: 'action',         orderable: false, searchable: false },
                ],
            });

            // Handle delete confirmation dynamically for ajax loaded elements
            $(document).on('click', '.delete-button', function(e) {
                e.preventDefault();
                var form = $(this).closest('class', '.delete-form');
                if (!form.length) {
                    form = $(this).parent('form');
                }
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete all exams and resources for this catalogue? This will also remove the files from server.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
