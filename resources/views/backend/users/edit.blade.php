@extends('backend.layouts.master')
@section('title')
    {{__('message.Edit')}} {{__('message.Users')}}
@endsection
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
            @if(Session::has('success'))
            <div class="card-header">
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                    @php
                        Session::forget('success');
                    @endphp
                </div>
            </div>
            @endif

            <!-- Way 1: Display All Error Messages -->
            @if (Session::has('errors'))
            <div class="card-header">
                <div class="alert alert-danger">
                    <strong>{{__('message.Whoops')}}!</strong> {{__('message.There were some problems with your input')}}.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            <!-- /.card-header -->
            <form method="POST" action="{{route('users.update', $user->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputFile">{{__('message.Image')}}</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="pic" class="custom-file-input" id="exampleInputFile" value="{{$user->pic}}">
                                <label class="custom-file-label" for="exampleInputFile">{{__('message.Choose file')}}</label>
                            </div>
                            <img src="{{asset($user->pic)}}" alt="Not Photo" style="width: 8%;border-radius: 50%;padding-left: 10px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Name')}}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$user->name}}" placeholder="Enter Name">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Level')}}</label>
                        <select class="form-control select2" name="level_id" style="width: 100%;">
                            @foreach ($levels as $l)
                            <option value="{{$l->id}}" @if($l->id == $user->level_id) selected @endif>{{$l->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Email')}}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{$user->email}}" placeholder="Enter Email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Mobile')}}</label>
                        <input type="text" name="mobile" class="form-control" placeholder="{{__('message.Enter')}} {{__('message.Email')}}" value="{{$user->mobile}}">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Password')}}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{__('message.Enter')}} {{__('message.Password')}}">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Gender')}}</label>
                        <select class="form-control select2" name="gender" style="width: 100%;">
                            <option value="m" @if($user->gender == 'male') selected="selected" @endif>{{__('message.Male')}}</option>
                            <option value="f" @if($user->gender == 'female') selected="selected" @endif>{{__('message.Female')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Birth Date')}}</label>
                        <input type="date" name="birth_date" class="form-control" placeholder="{{__('message.Enter')}} {{__('message.Birth Date')}}" value="{{$user->birth_date}}">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">{{__('message.Country')}}</label>
                        <input type="text" name="country" class="form-control" placeholder="{{__('message.Enter')}} {{__('message.Country')}}" value="{{$user->country}}">
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{__('message.Save')}}</button>
                </div>
              </form>
            <!-- /.row -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
