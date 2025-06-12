<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
  <style>
    body{
      font-family: 'Baloo 2', sans-serif;
    }
  </style>
</head>
<body>
  
  <div class="sidebar-container">
          <input type="checkbox" id="sidebar-toggle" class="sidebar-checkbox">
          
          <div class="sidebar">
              <label for="sidebar-toggle" class="sidebar-close">&times;</label>
              <div class="sidebar-content">
                  <div class="sidebar-heading">Menu</div>
                  <a href="../landing page/index.php" class="sidebar-link"><i class="bi bi-house-door me-2"></i> Home</a>
                  <a href="../courses/halaman.php" class="sidebar-link"><i class="bi bi-book me-2"></i> All Courses</a>
                  <a href="#" class="sidebar-link"><i class="bi bi-person me-2"></i> My Account</a>
              
                  
                  <div class="sidebar-heading">Help</div>
                  <a href="#" class="sidebar-link"><i class="bi bi-question-circle me-2"></i> FAQs</a>
                  <a href="#" class="sidebar-link"><i class="bi bi-headset me-2"></i> Support</a>
                  <a href="#" class="sidebar-link"><i class="bi bi-info-circle me-2"></i> About Us</a>
              </div>
          </div>
          
          <label for="sidebar-toggle" class="sidebar-overlay"></label>
          
      </div>
</body>
</html>
