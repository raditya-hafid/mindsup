<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
  <style>
    body{
      font-family: 'Baloo 2', sans-serif;
    }
    .container-fluid .sisi{
      font-weight: bold;
    }
    .container-fluid .sisi:hover {
      text-decoration: underline !important;
    }
  </style>
</head>
<body>
  
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
          <div class="container-fluid">
              <label for="sidebar-toggle" class="navbar-brand text-primary display-5" style="cursor: pointer;">
                <i class="bi bi-list"></i>
              </label>
              <a href="../landing page/index.php" class="sisi text-decoration-none text-primary fs-5">Minds Up</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav mx-auto">
                      <li class="nav-item">
                          <a class="nav-link" href="../landing page/index.php">Home</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" href="../courses/halaman.php">Courses</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" href="../landing page/index.php#about">About</a>
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
                              if ($_SESSION['role'] === 'mentor'){
                                  echo "<li><a class=\"dropdown-item\" href=\"../admin_mentor/dashboard_mentor.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard </a></li>";
                              } elseif ($_SESSION['role'] === 'admin') {
                                    echo "<li><a class=\"dropdown-item\" href=\"../admin_mentor/dashboard_admin.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard </a></li>";
                              } else {
                                    echo "<li><a class=\"dropdown-item\" href=\"../dashboard/dashboard.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard </a></li>";
                                    echo "<li><a class=\"dropdown-item\" href=\"../simulasi/keranjang.php\"><i class=\"bi bi-cart me-2\"></i> Keranjang</a></li>";
                                }
                              
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
</body>
</html>

