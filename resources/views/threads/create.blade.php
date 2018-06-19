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
                        <input type="text" name="threadTitle" value="{{old('threadTitle')}}" class="form-control" placeholder="Title...">
                    </div>
                    <div class="form-group">
                        <textarea rows="5" name="threadBody" class="form-control">{{old('threadBody')}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="channels">Select a channel</label>
                        <select id="channels" name="channelId" value="{{old('channelId')}}" class="form-control">
                            @foreach($channels as $channel)
                            <option
                                {{(intval(old('channelId')) === $channel->id)? 'selected' : ''}} 
                                value="{{$channel->id}}">{{$channel->slug}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
                <div class="errors-create-thread">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


