@extends('backend.layouts.master')
@section('title')
    {{__('message.Edit')}} {{__('message.Admins')}}
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
            <form method="POST" action="{{route('admins.update', $admin->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body row">
                    <div class="form-group col-6">
                        <label for="exampleInputFile">{{__('message.File input')}}</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="pic" class="custom-file-input" id="exampleInputFile" value="{{$admin->pic}}">
                                <label class="custom-file-label" for="exampleInputFile">{{__('mesage.Choose file')}}</label>
                            </div>
                            <img src="{{asset($admin->pic)}}" alt="Not Photo" style="width: 8%;border-radius: 50%;">
                        </div>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">{{__('message.Name')}}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$admin->name}}" placeholder="{{__('message.Enter')}} {{__('message.Name')}}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">{{__('message.Email')}}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{$admin->email}}" placeholder="{{__('message.Enter')}} {{__('message.Email')}}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">{{__('message.Password')}}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{__('message.Enter')}} {{__('message.Password')}}">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">{{__('message.Mobile')}}</label>
                        <input type="text" name="mobile" class="form-control" placeholder="{{__('message.Enter')}} {{__('message.Mobile')}}" value="{{$admin->mobile}}">
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">{{__('message.Gender')}}</label>
                        <select class="form-control select2" name="gender" style="width: 100%;">
                            <option value="m" @if($admin->gender == 'male') selected="selected" @endif>{{__('message.Male')}}</option>
                            <option value="f" @if($admin->gender == 'female') selected="selected" @endif>{{__('message.Female')}}</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">{{__('message.Roles')}}</label>
                        {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control select2','multiple')) !!}
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
