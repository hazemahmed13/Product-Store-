<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-store"></i> {{ config('app.name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/even') }}">
                        <i class="fas fa-calculator"></i> Even Numbers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/prime') }}">
                        <i class="fas fa-square-root-alt"></i> Prime Numbers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/multable') }}">
                        <i class="fas fa-table"></i> Multiplication Table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products_list') }}">
                        <i class="fas fa-shopping-bag"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('purchases_list') }}">
                        <i class="fas fa-shopping-cart"></i> My Purchases
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users') }}">
                        <i class="fas fa-users"></i> Customers
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('do_logout') }}">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>