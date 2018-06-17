@extends('layouts.app')

@section('content')
<div class="container">
    <div class="div-left">
        <a href="{{route('threads.index')}}">All threads</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card thread-reply-form">
                <form method="POST" action="{{route('threads.store')}}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="threadTitle" class="form-control" placeholder="Title...">
                    </div>
                    <div class="form-group">
                        <textarea rows="5" name="threadBody" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


