<style>
body {
    background: #f8f9fa;
}

.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
}

.sidebar {
    min-height: calc(100vh - 56px);
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 12px 20px;
    border-radius: 8px;
    margin: 2px 10px;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.sidebar .nav-link i {
    margin-right: 10px;
    width: 20px;
}

.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
}

.navbar-dark .navbar-nav .nav-link {
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
}

.navbar-dark .navbar-nav .nav-link:hover,
.navbar-dark .navbar-nav .nav-link:focus {
    color: white;
    transform: translateX(5px);
}

.dropdown-item i {
    width: 20px;
    margin-right: 10px;
}
</style>