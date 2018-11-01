@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Resend Confirmation Link</div>
                    <div class="card-body">
                        <p class="card-text">
                            The confirmation link that directed you to this page is not the correct one for your acount.
                            Click on the resend button below to resend a new confirmation link to your email.
                        </p>
                        <p>Check your email after you click the resend button</p>
                        <form action="{{ route('confirm.user.email.resend') }}" method="GET">
                            @csrf
                            <div class="form-group row mb-0">
                                <div class="col offset-5">
                                    <button type="submit" class="btn btn-primary">
                                        Resend
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
