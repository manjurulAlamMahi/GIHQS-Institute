@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Email Audit Logs</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Email Audit Logs</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Email Audit Logs List</h4>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="emailLogsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Recipient</th>
                                <th>Subject</th>
                                <th>Stage</th>
                                <th>Sent At</th>
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
            $('#emailLogsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.email-logs.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'recipient',
                        name: 'recipient_email'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'stage',
                        name: 'stage'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                order: [[4, 'desc']] // Sort by Sent At desc by default
            });
        });
    </script>
@endpush
