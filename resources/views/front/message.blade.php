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
