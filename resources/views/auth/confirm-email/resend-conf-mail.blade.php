@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Resend Confirmation Link</div>
                    <div class="card-body">
                        <div class="card-text">
                            <p>You are redirected to this page because you haven't confirmed your email yet. Go to your
                                email and open the message named "Forum Confirmation Link" from us and follow the
                                instruction in the message.
                            </p>
                            <p>If you don't find the email message or you have already followed the instructions but
                                still redirected to this page, then
                                click on the resend button below to resend a new confirmation link message to your
                                email.
                            </p>
                            <p>Check your email after clicking the resend button</p>
                        </div>
                        <form action="{{ route('confirm.user.email.resend.post') }}" method="POST">
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
