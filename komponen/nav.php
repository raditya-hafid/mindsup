<?php
session_start();
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
        <div class="container-fluid">
            <label for="sidebar-toggle" class="navbar-brand text-primary" style="cursor: pointer;">Minds Up</label>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../landing page/pertama.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../landing page/pertama.php#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Programs/Content</a>
                    </li>
                </ul>
                <div>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Login'; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                        if (isset($_SESSION['username'])) {
                            if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'mentor'){
                                echo "<li><a class=\"dropdown-item\" href=\"../admin_mentor/dashboard_mentor.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard </a></li>";
                            } else {
                                echo "<li><a class=\"dropdown-item\" href=\"../dashboard/dashboard.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard </a></li>";
                            }
                            
                            echo "<li><a class=\"dropdown-item\" href=\"../input page/input.php\"><i class=\"bi bi-box-arrow-up\"></i> Upload</a></li>";
                            echo "<li><a class=\"dropdown-item\" href=\"../simulasi/simulasi.php\"><i class=\"bi bi-cart me-2\"></i> Keranjang</a></li>";
                            echo "<li><hr class=\"dropdown-divider\"></li>";
                            echo "<li><a class=\"dropdown-item\" href=\"../log in or register/logout.php\"><i class=\"bi bi-box-arrow-right me-2\"></i> Log out </a></li>";
                        }else {
                            //  <!-- contoh simulasi -->
                            echo "<li><a class=\"dropdown-item\" href=\"../log in or register/login.php\"><i class=\"bi bi-box-arrow-right me-2\"></i> Log in </a></li>";
                        }
                        ?>
                        
                        
                        
                       
                        
                        
                    </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav> 