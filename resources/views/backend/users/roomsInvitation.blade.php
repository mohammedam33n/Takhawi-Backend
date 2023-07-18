@extends('backend.layouts.master')
@section('title')
    {{__('Users')}}
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
                            <strong>{{__('Whoops')}}!</strong>  {{__('problem with delete')}}.<br><br>
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
                                    <th>{{__('User')}}</th>
                                    <th>{{__('Room')}}</th>
                                    <th>{{__('Body')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invitation as $d)
                                <tr>
                                    <td class="col-1">{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($invit == 1)
                                        {{$d->fromUser ? $d->toUser['name'] : '--'}}
                                        @else
                                        {{$d->toUser ? $d->toUser['name'] : '--'}}
                                        @endif
                                    </td>
                                    <td>{{$d->room ? $d->room['name'] : '--'}}</td>
                                    <td>{{$d->body}}</td>
                                    <td >
                                        <form action="{{ route('delRoomsInvitation', $d->id) }}" method="POST" >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
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
