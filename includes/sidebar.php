<div class="col-md-2 sidebar py-3">
    <nav class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"
                href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'team.php' ? 'active' : ''; ?>"
                href="team.php">
                <i class="fas fa-users"></i>
                Our Team
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>"
                href="services.php">
                <i class="fas fa-cogs"></i>
                Services
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>"
                href="projects.php">
                <i class="fas fa-project-diagram"></i>
                Projects
            </a>
        </li> -->
        <!-- <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>"
                href="analytics.php">
                <i class="fas fa-chart-bar"></i>
                Analytics
            </a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"
                href="settings.php">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </li>
    </nav>
</div>