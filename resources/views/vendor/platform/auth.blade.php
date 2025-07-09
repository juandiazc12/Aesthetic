@extends('platform::app')

@section('body')

    <div class="container-md">
        <div class="form-signin h-full min-vh-100 d-flex flex-column justify-content-center">

            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-6 col-xxl-5 px-md-5">

                    <div class="bg-white p-4 p-sm-5 rounded shadow-sm text-center">

                        <h1 class="h4 fw-light">
                            Bienvenidos a Aestectic
                        </h1>
                        <p class="text-muted mt-2 mb-4">
                            Tu panel administrativo
                        </p>

                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
