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
                        <li class="breadcrumb-item active">Reply Message</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Reply to Message</h4>
                </div>

                <form action="{{ route('admin.contact-messages.reply', $contactMessage->id) }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Original Message Header --}}
                            <div class="col-xxl-12 col-md-12">
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <strong>Original Message from {{ $contactMessage->name }}</strong>
                                    <p class="mb-0 mt-2">{{ $contactMessage->message }}</p>
                                </div>
                            </div>

                            {{-- Name --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="name" class="form-label"><strong>Sender Name</strong></label>
                                    <input type="text" class="form-control" value="{{ $contactMessage->name }}" readonly>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="email" class="form-label"><strong>Sender Email</strong></label>
                                    <input type="email" class="form-control" value="{{ $contactMessage->email }}" readonly>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="phone" class="form-label"><strong>Phone</strong></label>
                                    <input type="text" class="form-control" value="{{ $contactMessage->phone ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            {{-- Organization --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="organization" class="form-label"><strong>Organization</strong></label>
                                    <input type="text" class="form-control" value="{{ $contactMessage->organization ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            {{-- Service of Interest --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="service_of_interest" class="form-label"><strong>Service of Interest</strong></label>
                                    <input type="text" class="form-control" value="{{ $contactMessage->service_of_interest ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            {{-- Subject field --}}
                            <div class="col-xxl-12 col-md-12">
                                <label for="subject" class="form-label"><strong>Email Subject</strong></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject"
                                    value="{{ old('subject', 'Regarding your contact message') }}">
                                @error('subject')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Reply Message field --}}
                            <div class="col-xxl-12 col-md-12">
                                <label for="reply_message" class="form-label"><strong>Reply Message</strong></label>
                                <textarea class="form-control @error('reply_message') is-invalid @enderror" id="reply_message" name="reply_message" rows="8">{{ old('reply_message') }}</textarea>
                                @error('reply_message')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Status Update --}}
                            <div class="col-xxl-6 col-md-6">
                                <label for="statusSelect" class="form-label"><strong>Status After Reply</strong></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                    <option value="pending" {{ old('status', $contactMessage->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="replied" {{ old('status', $contactMessage->status ?? 'replied') == 'replied' ? 'selected' : '' }}>Replied</option>
                                    <option value="canceled" {{ old('status', $contactMessage->status) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                    <option value="completed" {{ old('status', $contactMessage->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Note about reply --}}
                            <div class="col-xxl-12 col-md-12">
                                <div class="alert alert-warning" role="alert">
                                    <strong>Note:</strong> This form will send the email reply directly to <strong>{{ $contactMessage->email }}</strong> and log it in the system.
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="col-xxl-12 col-md-12">
                                <button type="submit" class="btn btn-primary">Send Reply</button>
                                <a href="{{ route('admin.contact-messages.show', $contactMessage->id) }}" class="btn btn-secondary">Cancel</a>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
