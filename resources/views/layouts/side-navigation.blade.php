<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item nav-profile">
        <a href="#" class="nav-link">
          <div class="nav-profile-image">
            <img src="{{ asset('assets/images/faces/face1.png')  }}" alt="profile" />
            <span class="login-status online"></span>
            <!--change to offline or busy as needed-->
          </div>
          <div class="nav-profile-text d-flex flex-column">
            <span class="font-weight-bold mb-2">Welcome {{ Auth::user()->name }}</span>
          </div>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/dashboard">
          <span class="menu-title">Dashboard</span>
          <i class="mdi mdi-home menu-icon"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('invoices.index') }}">
          <span class="menu-title">Invoice</span>
          <i class="mdi mdi-file menu-icon"></i>
        </a>
      </li>
      <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Manager</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-folder menu-icon"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('company.index')}}">Company</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('sales.index')}}">Sales Category</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{route('accounts.index')}}">Account title</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('supplier.index')}}">Supplier</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('reports.index')}}">
            <span class="menu-title">Reports</span>
            <i class="mdi mdi-chart-bar menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
              <button type="submit" class="nav-link w-100" href="/">
                  <span class="menu-title">Signout</span>
                  <i class="mdi mdi-logout menu-icon"></i>
              </button>  
          </form>
        </li>
    </ul>
  </nav>