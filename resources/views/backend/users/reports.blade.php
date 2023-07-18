@extends('backend.layouts.master')
@section('title')
    {{__('message.Users')}} {{__('message.Reports')}}
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
                        <th>{{__('message.Media')}}</th>
                        <th>{{__('message.By')}} {{__('message.User')}}</th>
                        <th>{{__('message.Reason')}}</th>
                        <th>{{__('message.Describe')}}</th>
                        <th>{{__('message.Options')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users as $d)
                    <tr>
                        <td class="col-1">{{ $loop->iteration }}</td>
                        <td class="col-2"><img src="{{asset('media/'.$d->pic)}}" alt="Not Photo" style="width: 25%;border-radius: 50%;"> </td>
                        <td>{{$d->fromUser['name']}}</td>
                        <td>{{$d->reason}}</td>
                        <td>{{$d->describe}}</td>
                        <td >
                            <form action="{{ route('usersReport.destroy', $d->id) }}" method="POST" >
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
