@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Catalogue</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Catalogue List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Catalogue List</h4>
                    <a href="{{ route('admin.catalogues.create') }}" class="btn btn-sm btn-success">
                        <i class="fa-solid fa-plus me-1"></i> Add Catalogue Item
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="cataloguesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Short Title</th>
                                <th>Catalogue Type</th>
                                <th>Service Type</th>
                                <th>Price Regular</th>
                                <th>Price Final</th>
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
            $('#cataloguesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.catalogues.index') }}",
                columns: [
                    { data: 'DT_RowIndex',      name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title',             name: 'title' },
                    { data: 'short_title',       name: 'short_title' },
                    { data: 'catalogue_type',    name: 'catalogue_type' },
                    { data: 'service_type',      name: 'service_type' },
                    { data: 'price_regular',     name: 'price_regular' },
                    { data: 'price_final',       name: 'price_final' },
                    { data: 'features_count',    name: 'features_count', orderable: false, searchable: false },
                    { data: 'status',            name: 'status',         orderable: false, searchable: false },
                    { data: 'action',            name: 'action',         orderable: false, searchable: false },
                ],
            });
        });
    </script>
@endpush
