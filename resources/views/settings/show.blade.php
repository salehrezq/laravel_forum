@extends('layouts.app')

@section('content')
   <div class="container">
       <div class="card" style="width: 18rem;">
           <img class="card-img-top" src="{{ asset('storage/avatars/'.$avatar_path) }}" alt="">
           <div class="card-body">
               <p class="card-text">Current profile picture</p>
           </div>
       </div>
    <form action="{{ route('users.settings.post', $username) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="exampleFormControlFile1">Upload your avatar</label>
            <input type="file" name="user_avatar" class="form-control-file">
        </div>
        <input class="btn btn-primary" type="submit" value="Submit">
    </form>
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
@endsection