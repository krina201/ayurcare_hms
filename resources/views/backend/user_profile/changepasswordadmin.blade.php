@extends('backend.layouts.master')
@section('content')
    <section class="section mt-5">
        <div class="row">
            <div class="col-12">

                <form method="POST" enctype="multipart/form-data" action="{{ route('saveChangePasswordAdmin', $id) }}"
                    class="form d-flex flex-column">
                    @csrf
                    @method('PATCH')

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Details</h5>
                            <div class="row">



                                <!-- General Form Elements -->
                                <div class="mb-3 col-6">
                                    <label for="password" class="col-form-label">New
                                        Password</label>
                                    <input type="password" name="password" id="password" class="form-control mb-2"
                                        placeholder="New Password" value="{{ old('password') }}">

                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="mb-3 col-6">

                                    <label for="password_confirmation" class="col-form-label">Re-enter
                                        Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control mb-2" placeholder="New Password">

                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <a href="{{ route('user') }}" id="kt_ecommerce_add_user_cancel"
                                        class="btn btn-light me-5">Cancel</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <button type="submit" class="btn btn-primary"><span class="indicator-label">Change
                                            Password</span> </button>


                                    <!--end::Button-->
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </section>
@endsection()

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection()
