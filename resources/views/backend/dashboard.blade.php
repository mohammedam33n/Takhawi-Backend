@extends('backend.layouts.master')
@section('title')
    {{__('message.Dashboard')}}
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                @can('admin-list')
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{count($admins)}}</h3>

                            <p>{{__('message.Admins')}}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{route('admins.index')}}" class="small-box-footer">{{__('message.More info')}} <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                @endcan
                <!-- ./col -->

                @can('user-list')
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{count($users)}}</h3>

                            <p>{{__('message.Users')}}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{route('users.index')}}" class="small-box-footer">{{__('message.More info')}} <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                @endcan
                <!-- ./col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
