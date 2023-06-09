<nav class="navbar navbar-expand-md navbar-light  bg-header shadow-sm"  >
    <div class="container">
       
            <img src="{{asset('img/logo.png')}}" width="15%"> 
            <a class="navbar-brand title ml-4" href="{{ url('/') }}">
               Delivery Tracking
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <a class="navbar-brand round ml-5"  >
                    <i class="fa-solid fa-headphones-simple fa-xs"></i>
                 </a>
                 <div  >
                    <ul class="text-ul" >
                        <li>Hỗ trợ khách hàng</li>
                        <li>0966073640</li>
                      </ul>
                 </div>
                 @guest
                    <a class="navbar-brand round ml-5"  >
                        <i class="fa-solid fa-user fa-xs"></i>
                    </a>
                    <div  >
                        <ul class="text-ul" >
                            <li> <a  href="{{ route('login') }}">{{ __('Đăng nhập') }}</a></li>
                        </ul>
                    </div>
                    <a class="navbar-brand round ml-5"  >
                        <i class="fa-solid fa-key fa-xs"></i>
                    </a>
                    <div  >
                        <ul class="text-ul" >
                            @if (Route::has('register'))
                            <li  >
                                <a   href="{{ route('register') }}">{{ __('Đăng ký') }}</a>
                            </li>
                        @endif
                        </ul>
                    </div>
                 @else
                    <a class="navbar-brand round ml-5"  >
                        <i class="fa-solid fa-user fa-xs"></i>
                    </a>
                    <div  >
                        <ul class="text-ul" >
                            <li>
                                <a  href="#">
                                   Xin chào, {{ Auth::user()->name }} 
                                   @php
                                        echo Auth::user()->load('customer')->customer==null?"":"(customer)";
                                    @endphp
                                </a>
                                
                            </li>
                            
                            <li>
                                <a  href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    Đăng xuất
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                             
                        </ul>
                    </div>
                    <a class="navbar-brand round ml-5"  >
                        <i class="fa-solid fa-cart-shopping fa-xs"></i>
                    </a>
                    <div  >
                        <ul class="text-ul" >
                            <li  >
                                <a   href="#">Đơn hàng</a>
                            </li>
                       
                        </ul>
                    </div>
                 @endguest
                <!-- Right Side Of Navbar -->
                {{-- <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Đăng nhập') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Đăng ký') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul> --}}
            </div>
        </div>
 
</nav>