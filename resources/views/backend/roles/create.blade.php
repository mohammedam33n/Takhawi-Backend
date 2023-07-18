@extends('backend.layouts.master')
@section('title')
    {{__('message.Create')}} {{__('message.Roles')}}
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
            <form method="POST" action="{{route('roles.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="card-body row">
                    <div class="form-group col-12">
                        <label for="exampleInputEmail1">{{__('message.Name')}}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{__('message.Enter')}} {{__('message.Name')}}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group row col-12">
                        <label for="exampleInputEmail1">{{__('message.Roles')}}</label>
                    </div>
                    <div class="form-group row col-12">
                        @foreach($permission as $value)

                        <div class="icheck-primary row col-3">
                            <input type="checkbox" name="permission[]" class="name" id="{{$value->id}}" value="{{$value->id}}">
                            <label for="{{$value->id}}">{{ $value->name }}</label>
                        </div>
                        @endforeach
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
