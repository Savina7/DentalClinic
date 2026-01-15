<?php
// Kontrollo nëse variabla $activePage është vendosur, nëse jo vendos default
if (!isset($activePage)) {
    $activePage = '';
}
?>

<aside class="layout-sidebar">
    <div class="sidebar-logo-box">
        <span class="clinic-name-text"><i class="fas fa-tooth"></i> Smile Dental Admin</span>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav-links-list">
            <li>
                <a href="home.php" class="nav-item-link <?= ($activePage == 'home') ? 'active-nav-page' : '' ?>">
                    <i class="fas fa-home nav-icon-style"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="Appointments.php" class="nav-item-link <?= ($activePage == 'appointments') ? 'active-nav-page' : '' ?>">
                    <i class="fas fa-calendar-check nav-icon-style"></i> <span>Appointments</span>
                </a>
            </li>
            <li>
                <a href="patients.php" class="nav-item-link <?= ($activePage == 'patients') ? 'active-nav-page' : '' ?>">
                    <i class="fas fa-user-injured nav-icon-style"></i> <span>Patients</span>
                </a>
            </li>
            <li>
                <a href="dentists.php" class="nav-item-link <?= ($activePage == 'dentists') ? 'active-nav-page' : '' ?>">
                    <i class="fas fa-user-md nav-icon-style"></i> <span>Dentists</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="logout-section-bottom">
        <a href="logout.php" class="nav-item-link logout-color">
            <i class="fas fa-sign-out-alt nav-icon-style"></i> <span>Logout</span>
        </a>
    </div>
</aside>
  