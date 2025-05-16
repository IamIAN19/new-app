<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="/dashboard">
            <span class="align-middle">MyInvoice</span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Pages
            </li>

            <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="/dashboard">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('invoices.index') }}">
                    <i class="align-middle" data-feather="list"></i> <span class="align-middle">Invoice</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('reports.index') }}">
                    <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Reports</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('invoices.deleted-invoices') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('invoices.deleted-invoices') }}">
                    <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Deleted Invoices</span>
                </a>
            </li>

            <li class="sidebar-header">
                Managers
            </li>

            <li class="sidebar-item {{ request()->routeIs('company.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('company.index') }}">
                    <i class="align-middle" data-feather="square"></i> <span class="align-middle">Company</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('sales.index') }}">
                    <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Sales Category</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('accounts.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('accounts.index') }}">
                    <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Account title</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('supplier.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('supplier.index') }}">
                    <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Suppliers</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('department.index') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('department.index') }}">
                    <i class="align-middle" data-feather="align-left"></i> <span class="align-middle">Department</span>
                </a>
            </li>
            @if( auth()->user()->id === 1 )
                <li class="sidebar-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('users.index') }}">
                        <i class="align-middle" data-feather="users"></i> <span class="align-middle">Users</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>