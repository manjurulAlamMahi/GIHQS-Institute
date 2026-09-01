@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Orders</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Order History</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Order History</h4>
                </div>

                <div class="card-body">
                    <table class="table table-bordered dt-responsive nowrap" id="ordersTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Item</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Payment Status</th>
                                <th>Order Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" id="orderIdField">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="orderIdDisplay" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="paymentStatusSelect" class="form-label">Payment Status</label>
                            <select class="form-select" id="paymentStatusSelect" name="payment_status" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="orderStatusSelect" class="form-label">Order Status</label>
                            <select class="form-select" id="orderStatusSelect" name="order_status" required>
                                <option value="pending">Pending</option>
                                <option value="accepted">Accepted</option>
                                <option value="active">Active</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveStatusBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">Issue Stripe Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="refundForm">
                    @csrf
                    <input type="hidden" id="refundPurchaseIdField" name="purchase_id">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Order ID</label>
                                <input type="text" class="form-control-plaintext font-weight-bold text-primary" id="refundOrderIdDisplay" readonly style="font-size: 1.1rem; padding-top: 0; padding-bottom: 0;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Total Paid</label>
                                <input type="text" class="form-control-plaintext font-weight-bold text-success" id="refundTotalAmountDisplay" readonly style="font-size: 1.1rem; padding-top: 0; padding-bottom: 0;">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Total Refunded So Far</label>
                                <input type="text" class="form-control-plaintext text-danger font-weight-bold" id="refundedAmountDisplay" readonly style="padding-top: 0; padding-bottom: 0;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Remaining Refundable</label>
                                <input type="text" class="form-control-plaintext text-warning font-weight-bold" id="remainingRefundableDisplay" readonly style="padding-top: 0; padding-bottom: 0;">
                            </div>
                        </div>
                        
                        <div id="refundRequestSection" class="alert alert-warning mt-3 mb-0" style="display: none;">
                            <h6 class="alert-heading mb-1"><i class="ri-information-line"></i> Customer Refund Request</h6>
                            <p class="mb-1"><strong>Requested On:</strong> <span id="requestDateDisplay"></span></p>
                            <p class="mb-0"><strong>Reason:</strong> <span id="requestReasonDisplay"></span></p>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label for="refundTypeSelect" class="form-label">Refund Type</label>
                            <select class="form-select" id="refundTypeSelect" name="refund_type" required>
                                <option value="full">Full Refund</option>
                                <option value="partial">Partial Refund</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="refundAmountGroup" style="display: none;">
                            <label for="refundAmountInput" class="form-label">Refund Amount ($)</label>
                            <input type="number" class="form-control" id="refundAmountInput" name="refund_amount" step="0.01" min="0.01">
                            <small class="text-muted">Enter the amount you wish to refund in USD.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="refundReasonInput" class="form-label">Refund Reason / Note</label>
                            <textarea class="form-control" id="refundReasonInput" name="reason" rows="3" placeholder="Reason for the refund (will be saved internally and sent to Stripe)"></textarea>
                        </div>

                        <hr>
                        
                        <!-- Refund History Section -->
                        <h6 class="mb-3"><i class="ri-history-line"></i> Previous Refunds</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Refunded By</th>
                                        <th>Stripe Refund ID</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="refundHistoryBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No previous refunds for this order.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" id="rejectRefundBtn" style="display: none;">Reject Request</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" id="submitRefundBtn">Process Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let table = $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.orders.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'order_id',
                        name: 'order_id'
                    },
                    {
                        data: 'user_info',
                        name: 'user.full_name'
                    },
                    {
                        data: 'item_info',
                        name: 'purchase_type'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'order_status',
                        name: 'order_status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Open Update Status Modal
            $(document).on('click', '.update-status-btn', function() {
                let id = $(this).data('id');
                let orderId = $(this).data('order-id');
                let paymentStatus = $(this).data('payment-status');
                let orderStatus = $(this).data('order-status');

                $('#orderIdField').val(id);
                $('#orderIdDisplay').val(orderId);
                $('#paymentStatusSelect').val(paymentStatus);
                $('#orderStatusSelect').val(orderStatus);

                $('#updateStatusModal').modal('show');
            });

            // Save Status Changes
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#orderIdField').val();
                let saveBtn = $('#saveStatusBtn');
                saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                let url = "{{ route('admin.orders.update-status', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: "POST", // handled as PATCH via method override in form
                    data: $(this).serialize(),
                    success: function(response) {
                        saveBtn.prop('disabled', false).html('Save Changes');
                        $('#updateStatusModal').modal('hide');
                        table.ajax.reload(null, false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                    error: function(xhr) {
                        saveBtn.prop('disabled', false).html('Save Changes');
                        let errorMessage = 'Failed to update order status.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            text: errorMessage,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });

            // Open Refund Modal
            $(document).on('click', '.refund-btn', function() {
                let id = $(this).data('id');
                let orderId = $(this).data('order-id');
                let amount = parseFloat($(this).data('amount')) || 0;
                let refundedAmount = parseFloat($(this).data('refunded-amount')) || 0;
                let remaining = amount - refundedAmount;

                $('#refundPurchaseIdField').val(id);
                $('#refundOrderIdDisplay').val(orderId);
                $('#refundTotalAmountDisplay').val('$' + amount.toFixed(2));
                $('#refundedAmountDisplay').val('$' + refundedAmount.toFixed(2));
                $('#remainingRefundableDisplay').val('$' + remaining.toFixed(2));
                
                // Reset form values
                $('#refundTypeSelect').val('full');
                $('#refundAmountGroup').hide();
                $('#refundAmountInput').val(remaining.toFixed(2)).prop('max', remaining.toFixed(2));
                $('#refundReasonInput').val('');

                let requestStatus = $(this).data('refund-request-status') || '';
                let requestReason = $(this).data('refund-request-reason') || '';
                let requestedAt = $(this).data('refund-requested-at') || '';

                if (requestStatus === 'pending') {
                    $('#requestDateDisplay').text(requestedAt);
                    $('#requestReasonDisplay').text(requestReason);
                    $('#refundRequestSection').show();
                    $('#rejectRefundBtn').show();
                    $('#refundReasonInput').val('Approved request: ' + requestReason);
                } else {
                    $('#refundRequestSection').hide();
                    $('#rejectRefundBtn').hide();
                }

                // Load Refund History
                let historyBody = $('#refundHistoryBody');
                historyBody.html('<tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading history...</td></tr>');

                let historyUrl = "{{ route('admin.orders.refund-history', ':id') }}";
                historyUrl = historyUrl.replace(':id', id);

                $.ajax({
                    url: historyUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.refunds && response.refunds.length > 0) {
                            let rows = '';
                            response.refunds.forEach(function(r) {
                                let badgeClass = r.status.toLowerCase() === 'succeeded' ? 'bg-success' : 'bg-warning text-dark';
                                rows += `<tr>
                                    <td>${r.date}</td>
                                    <td class="font-weight-bold text-danger">${r.amount}</td>
                                    <td>${r.reason}</td>
                                    <td>${r.admin}</td>
                                    <td><small class="text-muted">${r.stripe_refund_id}</small></td>
                                    <td><span class="badge ${badgeClass}">${r.status}</span></td>
                                </tr>`;
                            });
                            historyBody.html(rows);
                        } else {
                            historyBody.html('<tr><td colspan="6" class="text-center text-muted">No previous refunds for this order.</td></tr>');
                        }
                    },
                    error: function() {
                        historyBody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load refund history.</td></tr>');
                    }
                });

                $('#refundModal').modal('show');
            });

            // Toggle custom amount field based on refund type
            $('#refundTypeSelect').on('change', function() {
                if ($(this).val() === 'partial') {
                    $('#refundAmountGroup').slideDown();
                    $('#refundAmountInput').prop('required', true);
                } else {
                    $('#refundAmountGroup').slideUp();
                    $('#refundAmountInput').prop('required', false);
                }
            });

            // Submit Refund
            $('#refundForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#refundPurchaseIdField').val();
                let orderId = $('#refundOrderIdDisplay').val();
                let refundType = $('#refundTypeSelect').val();
                
                let totalAmount = parseFloat($('#refundTotalAmountDisplay').val().replace('$', '')) || 0;
                let refundedSoFar = parseFloat($('#refundedAmountDisplay').val().replace('$', '')) || 0;
                let remaining = totalAmount - refundedSoFar;
                
                let refundAmount = refundType === 'full' ? remaining : parseFloat($('#refundAmountInput').val()) || 0;

                if (refundAmount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Refund amount must be greater than $0.00.'
                    });
                    return;
                }

                if (refundAmount > remaining) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Refund amount cannot exceed remaining refundable balance of $' + remaining.toFixed(2) + '.'
                    });
                    return;
                }

                // Show confirmation before submitting refund
                Swal.fire({
                    title: 'Confirm Refund',
                    html: `Are you sure you want to issue a <strong>${refundType} refund</strong> of <strong class="text-danger">$${refundAmount.toFixed(2)}</strong> for order <strong>${orderId}</strong>?<br><br><span class="text-warning font-weight-bold">This action will charge the Stripe API and is irreversible!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Issue Refund',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let submitBtn = $('#submitRefundBtn');
                        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing Refund...');

                        let refundUrl = "{{ route('admin.orders.refund', ':id') }}";
                        refundUrl = refundUrl.replace(':id', id);

                        $.ajax({
                            url: refundUrl,
                            type: 'POST',
                            data: $('#refundForm').serialize(),
                            success: function(response) {
                                submitBtn.prop('disabled', false).html('Process Refund');
                                $('#refundModal').modal('hide');
                                table.ajax.reload(null, false);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Refunded',
                                    text: response.message
                                });
                            },
                            error: function(xhr) {
                                submitBtn.prop('disabled', false).html('Process Refund');
                                let errorMessage = 'Failed to process refund via Stripe.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Refund Failed',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

            // Reject Refund Request
            $(document).on('click', '#rejectRefundBtn', function() {
                let id = $('#refundPurchaseIdField').val();
                let orderId = $('#refundOrderIdDisplay').val();

                Swal.fire({
                    title: 'Reject Refund Request',
                    text: `Are you sure you want to reject the refund request for order ${orderId}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e09a0b',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Reject Request',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let rejectBtn = $('#rejectRefundBtn');
                        rejectBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Rejecting...');

                        let rejectUrl = "{{ route('admin.orders.reject-refund', ':id') }}";
                        rejectUrl = rejectUrl.replace(':id', id);

                        $.ajax({
                            url: rejectUrl,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                rejectBtn.prop('disabled', false).text('Reject Request');
                                $('#refundModal').modal('hide');
                                table.ajax.reload(null, false);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Rejected',
                                    text: response.message
                                });
                            },
                            error: function(xhr) {
                                rejectBtn.prop('disabled', false).text('Reject Request');
                                let errorMessage = 'Failed to reject refund request.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
