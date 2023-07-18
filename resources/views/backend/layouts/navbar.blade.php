<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{route('admin.logout')}}" class="nav-link">{{__('message.Logout')}}</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    @if(app()->getLocale() == 'ar')
    <ul class="navbar-nav mr-auto-navbav">
    @else
    <ul class="navbar-nav ml-auto">
    @endif
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            @if(app()->getLocale() == 'en')
            <a class="nav-link" href="{{route('changeLang')}}">
                عربي
            </a>
            @endif

            @if(app()->getLocale() == 'ar')
            <a class="nav-link" href="{{route('changeLang')}}">
                English
            </a>
            @endif
        </li>

        <!-- <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li> -->
    </ul>
</nav>
