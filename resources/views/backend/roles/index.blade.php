@extends('backend.layouts.master')
@section('title')
    {{__('message.Roles')}}
@endsection
@section('content')
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
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
                    @if (Session::has('erorrs'))
                    <div class="card-header">
                        <div class="alert alert-danger">
                            <strong>{{__('message.Whoops')}}!</strong>  {{__('message.problem with delete')}}.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th class="col-1">#</th>
                                    <th>{{__('message.Name')}}</th>
                                    <th>{{__('message.Options')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $d)
                                <tr>
                                    <td class="col-1">{{ $loop->iteration }}</td>
                                    <td>{{$d->name}}</td>
                                    <td >
                                        <form action="{{ route('roles.destroy',$d->id) }}" method="POST" >
                                            @csrf
                                            @method('DELETE')
                                            @can('role-edit')
                                            <a href="{{route('roles.edit', $d->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                            @endcan
                                            @can('role-delete')
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
    <!-- /.content -->
@endsection
