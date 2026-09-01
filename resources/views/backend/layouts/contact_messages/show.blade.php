@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Contact Messages</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contact-messages.index') }}">Contact Messages</a></li>
                        <li class="breadcrumb-item active">View Message</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Message Details</h4>
                    <span class="badge bg-primary me-2" style="font-size: 14px; padding: 6px 12px;">Ref: {{ $contactMessage->reference_number }}</span>
                    <a href="{{ route('admin.contact-messages.edit', $contactMessage->id) }}" class="btn btn-sm btn-primary">Reply</a>
                </div>

                <div class="card-body">
                    <div class="row gy-4">

                        {{-- Name --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Name</strong></label>
                                <p>{{ $contactMessage->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Email</strong></label>
                                <p><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email ?? 'N/A' }}</a></p>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Phone</strong></label>
                                <p>
                                    @if($contactMessage->phone)
                                        <a href="tel:{{ $contactMessage->phone }}">{{ $contactMessage->phone }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Organization --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Organization</strong></label>
                                <p>{{ $contactMessage->organization ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Service of Interest --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Service of Interest</strong></label>
                                <p>{{ $contactMessage->service_of_interest ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Status</strong></label>
                                <p>
                                    @php
                                        $status = trim($contactMessage->status);
                                        if (empty($status)) $status = 'pending';

                                        $badgeClass = match (strtolower($status)) {
                                            'pending' => 'bg-danger',
                                            'replied' => 'bg-warning',
                                            'canceled' => 'bg-dark',
                                            'completed' => 'bg-success',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Message</strong></label>
                                <div class="border rounded p-3 bg-light">
                                    <p>{!! nl2br(e($contactMessage->message ?? 'N/A')) !!}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Received On</strong></label>
                                <p>{{ $contactMessage->created_at->format('d M Y, H:i A') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Status Update Form --}}
                        <div class="col-xxl-12 col-md-12 mt-3">
                            <form action="{{ route('admin.contact-messages.update-status', $contactMessage->id) }}" method="POST" class="d-flex gap-2 align-items-end">
                                @csrf
                                @method('PATCH')

                                <div class="flex-grow-1">
                                    <label for="statusSelect" class="form-label"><strong>Update Status</strong></label>
                                    <select class="form-select" name="status" id="statusSelect">
                                        <option value="pending" {{ $contactMessage->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="replied" {{ $contactMessage->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                        <option value="canceled" {{ $contactMessage->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                        <option value="completed" {{ $contactMessage->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">Update Status</button>
                            </form>
                        </div>

                        {{-- Back Button --}}
                        <div class="col-xxl-12 col-md-12">
                            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">Back to Messages</a>
                            <form action="{{ route('admin.contact-messages.destroy', $contactMessage->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger delete-button">Delete Message</button>
                            </form>
                        </div>

                        {{-- Reply History --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="mb-3">Reply History</h5>

                            @forelse($contactMessage->replies as $reply)
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>{{ $reply->subject }}</strong>
                                        <span class="badge bg-secondary-subtle text-dark border">{{ $reply->created_at?->format('d M Y, H:i A') }}</span>
                                    </div>
                                    <p class="mb-2">{!! nl2br(e($reply->message)) !!}</p>

                                    @if(!empty($reply->status))
                                        <small class="text-muted">Status after reply: <span class="badge bg-light text-dark">{{ ucfirst($reply->status) }}</span></small>
                                    @endif
                                </div>
                            @empty
                                <div class="alert alert-light border mb-0">
                                    No replies have been sent yet.
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
