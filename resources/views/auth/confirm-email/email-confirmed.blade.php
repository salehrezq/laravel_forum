@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Email confirmed</div>
                    <div class="card-body">
                        <div class="card-text">
                            <p>Thank you, your email has been confirmed.</p>
                            <p>Go to the <a href="{{ route('threads.index') }}">home</a> page.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
