@extends('front.layouts.app')

@section('main')
    <section class="section-5 bg-2">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Account Settings</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    @include('front.account.sidebar')
                </div>
                <div class="col-lg-9">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <p class="mb-0 pb-0">{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <p class="mb-0 pb-0">{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="card border-0 shadow mb-4">
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">My Profile</h3>
                            <form action="{{ route('account.updateProfile') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label for="name" class="mb-2">Name*</label>
                                    <input type="text" name="name" id="name" placeholder="Enter Name"
                                        class="form-control" value="{{ old('name', $user->name) }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="mb-2">Email*</label>
                                    <input type="email" name="email" id="email" placeholder="Enter Email"
                                        class="form-control" value="{{ old('email', $user->email) }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="designation" class="mb-2">Designation</label>
                                    <input type="text" name="designation" id="designation" placeholder="Designation"
                                        class="form-control" value="{{ old('designation', $user->designation) }}">
                                    @error('designation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="mobile" class="mb-2">Mobile</label>
                                    <input type="text" name="mobile" id="mobile" placeholder="Mobile"
                                        class="form-control" value="{{ old('mobile', $user->mobile) }}">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 shadow mb-4">
                        <form action="{{ route('account.updatePassword') }}" method="POST" id="changePasswordForm">
                            @csrf
                            @method('PUT')
                            <div class="card-body p-4">
                                <h3 class="fs-4 mb-1">Change Password</h3>
                                <div class="mb-4">
                                    <label for="old_password" class="mb-2">Old Password*</label>
                                    <input type="password" name="old_password" id="old_password" placeholder="Old Password"
                                        class="form-control">
                                    @error('old_password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="new_password" class="mb-2">New Password*</label>
                                    <input type="password" name="new_password" id="new_password" placeholder="New Password"
                                        class="form-control">
                                    @error('new_password')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="mb-2">Confirm Password*</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        placeholder="Confirm Password" class="form-control">
                                    @error('password_confirmation')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer p-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                        <div id="responseMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
{{-- <script>
    $(document).ready(function() {
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the default way

            $.ajax({
                url: $(this).attr('action'), // Get form action URL
                type: 'POST', // Use POST method
                data: $(this).serialize(), // Serialize form data
                headers: {
                    'X-HTTP-Method-Override': 'PUT' // Override HTTP method to PUT
                },
                success: function(response) {
                    if (response.status) {
                        // Handle success
                        $('#responseMessage').html('<div class="alert alert-success">' +
                            response.message + '</div>');
                        $('#changePasswordForm')[0].reset(); // Reset the form
                    } else {
                        var errors = response.errors;
                        if (errors.old_password) {
                            $('#old_password').addClass('is-invalid');
                            $('#old_passwordError').html(errors.old_password[0]);
                        } else {
                            $('#old_password').removeClass('is-invalid');
                            $('#old_passwordError').html('');
                        }
                        if (errors.new_password) {
                            $('#new_password').addClass('is-invalid');
                            $('#new_passwordError').html(errors.new_password[0]);
                        } else {
                            $('#new_password').removeClass('is-invalid');
                            $('#new_passwordError').html('');
                        }
                        if (errors.confirm_password) {
                            $('#confirm_password').addClass('is-invalid');
                            $('#confirm_passwordError').html(errors.confirm_password[0]);
                        } else {
                            $('#confirm_password').removeClass('is-invalid');
                            $('#confirm_passwordError').html('');
                        }
                    }
                },
                error: function(xhr) {
                    // Handle errors
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $('#old_passwordError').text(errors.old_password ? errors
                            .old_password[0] : '');
                        $('#new_passwordError').text(errors.new_password ? errors
                            .new_password[0] : '');
                        $('#confirm_passwordError').text(errors.confirm_password ? errors
                            .confirm_password[0] : '');
                    }
                }
            });
        });
    });
</script> --}}
