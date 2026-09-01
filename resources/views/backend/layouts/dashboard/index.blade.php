@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 mb-1">Welcome back, {{ auth()->user()->first_name }}!</h4>
                                <p class="text-muted mb-0">Here's a premium summary of your platform statistics today.</p>
                            </div>
                        </div><!-- end card header -->
                    </div>
                </div>
                <!--end row-->

                <!-- stats row -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Net Revenue</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">$<span class="counter-value" data-target="{{ $netRevenue }}">{{ number_format($netRevenue, 2) }}</span></h4>
                                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-underline text-muted">View all sales</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                            <i class="ri-money-dollar-circle-line text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Orders</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalOrders }}">{{ $totalOrders }}</span></h4>
                                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-underline text-muted">Manage orders</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                            <i class="ri-shopping-cart-2-line text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Registered Users</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalUsers }}">{{ $totalUsers }}</span></h4>
                                        <a href="{{ route('admin.managers.index') }}" class="text-decoration-underline text-muted">View all users</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                            <i class="ri-user-line text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Pending Refunds</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $pendingRefunds }}">{{ $pendingRefunds }}</span></h4>
                                        <a href="{{ route('admin.orders.refund-requests') }}" class="text-decoration-underline text-muted">Review refund requests</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                            <i class="ri-refund-2-line text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->

                <!-- chart & breakdown row -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Revenue Overview ({{ date('Y') }})</h4>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-soft-primary text-primary">Monthly Sales</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="revenue-chart" style="min-height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Platform Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                                            <i class="ri-book-open-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Catalogues</h6>
                                        <p class="text-muted mb-0">Total program catalogues</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-light text-body fs-12 fw-semibold">{{ $totalCatalogues }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <div class="avatar-title bg-warning-subtle text-warning rounded-circle fs-16">
                                            <i class="ri-message-2-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Messages</h6>
                                        <p class="text-muted mb-0">Support & contact inbox</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-light text-body fs-12 fw-semibold">{{ $totalMessages }}</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center mt-4">
                                    <h6 class="text-muted mb-3">Quick Shortcuts</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('admin.catalogues.index') }}" class="btn btn-outline-light w-100 py-2 text-dark">
                                                <i class="ri-add-line d-block fs-16 mb-1 text-primary"></i> Catalogues
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-light w-100 py-2 text-dark">
                                                <i class="ri-mail-line d-block fs-16 mb-1 text-warning"></i> Messages
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- recent entries row -->
                <div class="row">
                    <!-- recent orders -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-soft-primary btn-sm">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <thead class="text-muted bg-light-subtle">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOrders as $order)
                                                <tr>
                                                    <td>
                                                        <span class="fw-semibold text-primary">{{ $order->order_id }}</span>
                                                    </td>
                                                    <td>
                                                        @if($order->user)
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="fs-13 mb-0">{{ $order->user->full_name }}</h6>
                                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold">${{ number_format($order->amount, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = strtolower($order->payment_status ?? 'pending');
                                                            $badge = match($status) {
                                                                'paid' => 'bg-success-subtle text-success',
                                                                'pending' => 'bg-warning-subtle text-warning',
                                                                'refunded' => 'bg-danger-subtle text-danger',
                                                                default => 'bg-secondary-subtle text-secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badge }}">{{ ucfirst($status) }}</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $order->created_at ? $order->created_at->format('M d, Y') : '-' }}</small>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">No orders found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- recent contact messages -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Contact Messages</h4>
                                <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-soft-primary btn-sm">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <thead class="text-muted bg-light-subtle">
                                            <tr>
                                                <th>Name</th>
                                                <th>Subject</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentMessages as $msg)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <h6 class="fs-13 mb-0">{{ $msg->name }}</h6>
                                                                <small class="text-muted">{{ $msg->email }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $msg->subject }}</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $msg->created_at ? $msg->created_at->format('M d, Y') : '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-link btn-sm p-0">View</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">No messages found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end .h-100-->
        </div> <!-- end col -->
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            // ApexCharts Line/Area options
            var options = {
                series: [{
                    name: 'Net Revenue',
                    data: @json($chartData)
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#405189'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    },
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return "$" + parseFloat(value).toFixed(2);
                        }
                    },
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return "$" + parseFloat(value).toFixed(2);
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
            chart.render();
        });
    </script>
@endpush
