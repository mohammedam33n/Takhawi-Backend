<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('defult.jpg') }}" alt="Takhawi Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Takhawi</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset(Auth::user()->pic) }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('dashboard.index') }}"
                        class="nav-link @if (Route::is('dashboard*')) active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            {{ __('message.dashboard') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item @if (Route::is('users*') || Route::is('admins*') || Route::is('family*') || Route::is('roles*') || Route::is('page*')) menu-open @endif">
                    <a href="#" class="nav-link @if (Route::is('users*') || Route::is('admins*') || Route::is('family*') || Route::is('roles*') || Route::is('page*')) active @endif">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            {{__('message.settings')}}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item @if (Route::is('admins*')) menu-open @endif">
                            <a href="#" class="nav-link @if (Route::is('admins*')) active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    {{__('message.admins')}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @can('admin-list')
                                    <li class="nav-item ">
                                        <a href="{{ route('admins.index') }}"
                                            class="nav-link @if (Route::is('admins.index') || Route::is('admins.edit')) active @endif">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>{{__('message.admins')}}</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('admin-create')
                                    <li class="nav-item">
                                        <a href="{{ route('admins.create') }}"
                                            class="nav-link @if (Route::is('admins.create')) active @endif">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>{{__('message.add')}} {{__('message.admins')}}</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                        @can('user-list')
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link @if (Route::is('users*')) active @endif">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        {{__('message.users')}}
                                    </p>
                                </a>
                            </li>
                        @endcan

                        <li class="nav-item @if (Route::is('roles*')) menu-open @endif">
                            <a href="#" class="nav-link @if (Route::is('roles*')) active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    {{__('message.roles')}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @can('role-list')
                                    <li class="nav-item">
                                        <a href="{{ route('roles.index') }}"
                                            class="nav-link @if (Route::is('roles.index') || Route::is('roles.edit')) active @endif">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>{{__('message.roles')}}</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('role-create')
                                    <li class="nav-item">
                                        <a href="{{ route('roles.create') }}"
                                            class="nav-link @if (Route::is('roles.create')) active @endif">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>{{__('message.add')}} {{__('message.roles')}}</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
