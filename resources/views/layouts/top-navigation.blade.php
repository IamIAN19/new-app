<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    {{-- <img src="img/avatars/avatar.jpg" class="avatar img-fluid rounded me-1" alt="" /> <span class="text-dark">Charles Hall</span> --}}
                    Welcome <span class="text-dark">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                          <button type="submit" class="nav-link w-100" href="/">
                              <span class="menu-title">Logout</span>
                              <i class="mdi mdi-logout menu-icon"></i>
                          </button>  
                      </form>
                </div>
            </li>
        </ul>
    </div>
</nav>