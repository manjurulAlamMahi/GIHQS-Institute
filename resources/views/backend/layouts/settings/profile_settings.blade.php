@extends('backend.app')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Profile Settings</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ session('active_tab') == 'password' ? '' : 'active' }}"
                                data-bs-toggle="tab" href="#editProfile" role="tab">
                                Edit Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ session('active_tab') == 'password' ? 'active' : '' }}"
                                data-bs-toggle="tab" href="#changePassword" role="tab">
                                Change Password
                            </a>
                        </li>
                    </ul>
                </div><!-- end card header -->

                <div class="card-body p-4">
                    <div class="tab-content">
                        {{-- Edit Profile Tab --}}
                        <div class="tab-pane {{ session('active_tab') == 'password' ? '' : 'active' }}" id="editProfile"
                            role="tabpanel">
                            <form action="{{ route('admin.profile-settings.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row gy-4">
                                    {{-- Profile Image --}}
                                    <div class="col-12">
                                        <label for="avatar" class="form-label">Profile Image</label>
                                        <input type="file" name="avatar" id="avatar" class="form-control dropify"
                                            data-default-file="{{ $user->avatar ? asset($user->avatar) : '' }}"
                                            data-allowed-file-extensions="jpg jpeg png gif">
                                        <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
                                        @error('avatar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- First Name --}}
                                    <div class="col-12">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" name="first_name" id="first_name" class="form-control"
                                            value="{{ old('first_name', $user->first_name) }}">
                                        @error('first_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Last Name --}}
                                    <div class="col-12">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" name="last_name" id="last_name" class="form-control"
                                            value="{{ old('last_name', $user->last_name) }}">
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Country --}}
                                    <div class="col-12">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" name="country" id="country" class="form-control"
                                            value="{{ old('country', $user->country) }}">
                                        @error('country')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Username --}}
                                    <div class="col-12">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control"
                                            value="{{ old('username', $user->username) }}">
                                        @error('username')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Change Password Tab --}}
                        <div class="tab-pane {{ session('active_tab') == 'password' ? 'active' : '' }}" id="changePassword"
                            role="tabpanel">
                            <form action="{{ route('admin.profile-settings.change-password') }}" method="POST">
                                @csrf
                                <div class="row gy-4">
                                    {{-- Current Password --}}
                                    <div class="col-12">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control" placeholder="Enter Current Password">
                                        @error('current_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- New Password --}}
                                    <div class="col-12">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Enter New Password">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" placeholder="Confirm Password">
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let drEvent = $('.dropify').dropify({
                messages: {
                    'default': 'Drag or click',
                    'replace': 'Drag to replace',
                    'remove': 'Remove',
                    'error': 'Something went wrong.'
                }
            });

            drEvent.on('dropify.afterClear', function() {
                $('#remove_avatar').val(1);
            });
        });
    </script>
@endpush
