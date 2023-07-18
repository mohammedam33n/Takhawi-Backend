@extends('backend.layouts.master')
@section('title')
    {{__('message.Admins')}}
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
                                    <th>{{__('message.Photo')}}</th>
                                    <th>{{__('message.Name')}}</th>
                                    <th>{{__('message.Mobile')}}</th>
                                    <th>{{__('message.Email')}}</th>
                                    <th>{{__('message.Gender')}}</th>
                                    <th>{{__('message.Options')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $d)
                                <tr>
                                    <td class="col-1">{{ $loop->iteration }}</td>
                                    <td class="col-2"><img src="{{asset($d->pic)}}" alt="Not Photo" style="width: 25%;border-radius: 50%;"> </td>
                                    <td>{{$d->name}}</td>
                                    <td>{{$d->mobile}}</td>
                                    <td>{{$d->email}}</td>
                                    <td><i class="@if($d->gender == 'f') fa fa-female @else fa fa-male  @endif"></i> </td>
                                    <td >
                                        <form action="{{ route('admins.destroy',$d->id) }}" method="POST" >
                                            @csrf
                                            @method('DELETE')
                                            @can('admin-edit')
                                            <a href="{{route('admins.edit', $d->id)}}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                            @endcan
                                            @can('admin-delete')
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
