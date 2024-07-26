@extends('front.layouts.app')
@section('main')
    <section class="section-5">
        <div class="container my-5">
            <div class="py-lg-2">&nbsp;</div>
            @if (Session::has('success'))
                <div class="alert alert-success">
                    <p class="mb-0 pb-0">{{ session::get('success') }}</p>
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-danger">
                    <p class="mb-0 pb-0">{{ session::get('error') }}</p>
                </div>
            @endif
            <div class="row d-flex justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow border-0 p-5">
                        <h1 class="h3">Login</h1>
                        <form action="{{ route('account.authenticate') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="mb-2">Email</label>
                                <input type="email" name="email" id="email" placeholder="Email"
                                    class="form-control">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password" class="mb-2">Password</label>
                                <input type="password" name="password" id="password" placeholder="Password"
                                    class="form-control">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="justify-content-between d-flex">
                                <button class="btn btn-primary mt-2">Login</button>
                                <a href="{{ route('account.forgotPassword') }}" class="mt-3">Forgot Password?</a>
                            </div>
                        </form>

                    </div>
                    <div class="mt-4 text-center">
                        <p>Do not have an account? <a href="{{ route('account.registration') }}">Register</a></p>
                    </div>
                </div>
            </div>
            <div class="py-lg-5">&nbsp;</div>
        </div>
    </section>
@endsection
