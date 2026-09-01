@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Members</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Members list</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Members list</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex align-items-center gap-2">
                            <label for="roleFilter" class="form-label mb-0 text-muted">Filter by Membership:</label>
                            <select id="roleFilter" class="form-select form-select-sm" style="width: 220px;">
                                <option value="">All Members</option>
                                @foreach($membershipPackages as $package)
                                    <option value="{{ $package->id }}">{{ $package->title ?: $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="membersTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role / Package</th>
                                <th>Joined Date</th>
                                <th>Expiry Date</th>
                                <th>Status (Active)</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Member Status & Expiry Modal -->
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="editMemberModalLabel">Edit Member Membership & Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMemberForm">
                    @csrf
                    <input type="hidden" id="edit_member_id" name="member_id">
                    <div class="modal-body">
                        <!-- Member Name & Email Display -->
                        <div class="mb-3">
                            <label class="form-label text-muted mb-1">Member Info:</label>
                            <div class="p-2 border rounded bg-light">
                                <strong id="modal_member_name" class="d-block text-dark"></strong>
                                <small id="modal_member_email" class="text-muted"></small>
                            </div>
                        </div>

                        <!-- User Active Status -->
                        <div class="mb-3">
                            <label for="modal_status" class="form-label fw-bold">Account Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive / Suspended</option>
                            </select>
                        </div>

                        <!-- Membership Package -->
                        <div class="mb-3">
                            <label for="modal_membership_package_id" class="form-label fw-bold">Membership Package</label>
                            <select class="form-select" id="modal_membership_package_id" name="membership_package_id" required>
                                @foreach($membershipPackages as $package)
                                    <option value="{{ $package->id }}">{{ $package->title ?: $package->name }} ({{ $package->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Expiry Date -->
                        <div class="mb-3">
                            <label for="modal_expires_at" class="form-label fw-bold">Membership Expiry Date</label>
                            <input type="date" class="form-control" id="modal_expires_at" name="expires_at">
                            <div class="form-text">Leave blank for lifetime validity or when choosing "No Active Membership".</div>
                        </div>

                        <!-- Stripe Subscription Details -->
                        <div id="stripeSubscriptionSection" class="mb-3 border p-3 rounded bg-light" style="display: none;">
                            <h6 class="fw-bold text-primary mb-2"><i class="ri-secure-payment-line me-1"></i> Stripe Subscription Details</h6>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Subscription ID</span>
                                    <strong id="modal_stripe_sub_id" class="text-dark" style="font-size: 12px; word-break: break-all;"></strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Status</span>
                                    <span id="modal_stripe_sub_status" class="badge"></span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Next Renewal</span>
                                    <strong id="modal_stripe_next_renewal" class="text-dark" style="font-size: 12px;"></strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Auto Renew</span>
                                    <strong id="modal_stripe_auto_renew" class="text-dark" style="font-size: 12px;"></strong>
                                </div>
                            </div>
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-warning flex-grow-1" id="btnCancelSubAtPeriodEnd" style="font-size: 11px;">
                                    Cancel at Period End
                                </button>
                                <button type="button" class="btn btn-sm btn-danger flex-grow-1" id="btnCancelSubImmediately" style="font-size: 11px;">
                                    Cancel Immediately
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveMemberBtn">
                            <i class="ri-save-line me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let table = $('#membersTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('admin.members.index') }}",
                    data: function(d) {
                        d.role = $('#roleFilter').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'joined_date',
                        name: 'joined_date'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date',
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
                    }
                ]
            });

            $('#roleFilter').on('change', function() {
                table.draw();
            });

            // Open Edit Member Modal
            $(document).on('click', '.edit-member-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let email = $(this).data('email');
                let status = $(this).data('status');
                let packageId = $(this).data('package-id');
                let expiresAt = $(this).data('expires-at');
                
                let stripeSubId = $(this).data('stripe-sub-id');
                let stripeSubStatus = $(this).data('stripe-sub-status');
                let stripeNextRenewal = $(this).data('stripe-next-renewal');
                let stripeCancelAtPeriodEnd = $(this).data('stripe-cancel-at-period-end');

                $('#edit_member_id').val(id);
                $('#modal_member_name').text(name);
                $('#modal_member_email').text(email);
                $('#modal_status').val(status);
                $('#modal_membership_package_id').val(packageId || 'none');
                $('#modal_expires_at').val(expiresAt);

                if (stripeSubId) {
                    $('#modal_stripe_sub_id').text(stripeSubId);
                    
                    let badgeClass = 'bg-secondary';
                    if (stripeSubStatus === 'active' || stripeSubStatus === 'trialing') {
                        badgeClass = 'bg-success text-white';
                    } else if (stripeSubStatus === 'past_due' || stripeSubStatus === 'incomplete') {
                        badgeClass = 'bg-warning text-dark';
                    } else if (stripeSubStatus === 'canceled' || stripeSubStatus === 'unpaid') {
                        badgeClass = 'bg-danger text-white';
                    }
                    
                    $('#modal_stripe_sub_status').text(stripeSubStatus.toUpperCase()).removeClass().addClass('badge ' + badgeClass);
                    $('#modal_stripe_next_renewal').text(stripeNextRenewal || 'N/A');
                    
                    if (stripeCancelAtPeriodEnd == '1') {
                        $('#modal_stripe_auto_renew').text('NO (Ends ' + (stripeNextRenewal || '') + ')').removeClass().addClass('text-danger');
                        $('#btnCancelSubAtPeriodEnd').hide();
                    } else {
                        $('#modal_stripe_auto_renew').text('YES').removeClass().addClass('text-success');
                        $('#btnCancelSubAtPeriodEnd').show();
                    }
                    
                    if (stripeSubStatus === 'canceled') {
                        $('#btnCancelSubAtPeriodEnd').hide();
                        $('#btnCancelSubImmediately').hide();
                    } else {
                        $('#btnCancelSubImmediately').show();
                    }
                    
                    $('#stripeSubscriptionSection').show();
                } else {
                    $('#stripeSubscriptionSection').hide();
                }

                $('#editMemberModal').modal('show');
            });

            // Handle Form Submit for Member Membership & Status Update
            $('#editMemberForm').on('submit', function(e) {
                e.preventDefault();

                let memberId = $('#edit_member_id').val();
                let saveBtn = $('#saveMemberBtn');
                saveBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Saving...');

                $.ajax({
                    url: "/admin/members/" + memberId + "/update-membership",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        saveBtn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Changes');
                        $('#editMemberModal').modal('hide');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        saveBtn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Changes');
                        let errorMsg = 'Failed to update member status.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });

            // Handle Stripe Subscription Cancellation
            function cancelStripeSubscription(mode) {
                let memberId = $('#edit_member_id').val();
                let confirmText = mode === 'at_period_end' 
                    ? 'Are you sure you want to cancel the auto-renewal at the end of the billing period?' 
                    : 'Are you sure you want to cancel this subscription immediately? The member will be demoted to default Standard package.';

                Swal.fire({
                    title: 'Confirm Cancellation',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/admin/members/" + memberId + "/cancel-subscription",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                cancel_mode: mode
                            },
                            success: function(response) {
                                $('#editMemberModal').modal('hide');
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                let errorMsg = 'Failed to cancel subscription.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg
                                });
                            }
                        });
                    }
                });
            }

            $('#btnCancelSubAtPeriodEnd').on('click', function() {
                cancelStripeSubscription('at_period_end');
            });

            $('#btnCancelSubImmediately').on('click', function() {
                cancelStripeSubscription('immediately');
            });
        });
    </script>
@endpush
