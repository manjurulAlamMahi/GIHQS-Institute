@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Membership Packages</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Membership Packages List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Membership Packages List</h4>
                    <a href="{{ route('admin.membership-packages.create') }}" class="btn btn-sm btn-success">
                        <i class="fa-solid fa-plus me-1"></i> Add Package
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="membershipPackagesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Discount (%)</th>
                                <th>Exam Limit</th>
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
            $('#membershipPackagesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.membership-packages.index') }}",
                columns: [
                    { data: 'DT_RowIndex',          name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name',                 name: 'name' },
                    { data: 'title',                name: 'title' },
                    { data: 'price',                name: 'price' },
                    { data: 'discount_percentage',  name: 'discount_percentage' },
                    { data: 'exam_attempt_limit',   name: 'exam_attempt_limit' },
                    { data: 'features_count',       name: 'features_count', orderable: false, searchable: false },
                    { data: 'status',               name: 'status',  orderable: false, searchable: false },
                    { data: 'action',               name: 'action',  orderable: false, searchable: false },
                ],
            });
        });
    </script>
@endpush
