!<!DOCTYPE html>
<html lang="en">
  <head>

    <title>Minds UP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">

    <title>Minds UP</title>

    <style>
      body{
        overflow-x: hidden;
        font-family: 'Baloo 2', sans-serif;
      }
    </style>
  </head>
<?php
    require '../head/head.php';
?>
<body>
    
    <?php
    require '../komponen/sidebar.php';
    ?>
        
    </div>

    <!-- Navigation Bar -->
    <?php
     require '../komponen/nav.php';

    ?>
    <!-- Navigation body  -->
    <main>
    <?php
     require '../body.php';

    ?>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>