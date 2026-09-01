@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Packages</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Packages List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Packages List</h4>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-sm btn-success">
                        <i class="fa-solid fa-plus me-1"></i> Add Package
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="packagesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Features</th>
                                <th>Status</th>
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
            $('#packagesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.packages.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name',           name: 'name' },
                    { data: 'features_count', name: 'features_count', orderable: false, searchable: false },
                    { data: 'status',         name: 'status',  orderable: false, searchable: false },
                    { data: 'action',         name: 'action',  orderable: false, searchable: false },
                ],
            });
        });
    </script>
@endpush
