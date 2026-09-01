@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Advisory Requests</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Advisory Requests</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Advisory Requests List</h4>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="advisoryRequestsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Organization Name</th>
                                <th>Full Name</th>
                                <th>Work Email</th>
                                <th>Service of Interest</th>
                                <th>Fee Amount</th>
                                <th>Payment Status</th>
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
            $('#advisoryRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.advisory-requests.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'organization_name',
                        name: 'organization_name'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'work_email',
                        name: 'work_email'
                    },
                    {
                        data: 'service_of_interest',
                        name: 'service_of_interest'
                    },
                    {
                        data: 'payment_amount',
                        name: 'payment_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
