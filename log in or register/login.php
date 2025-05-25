<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    session_start();
    require '../head/head.php';
    require '../komponen/koneksi.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE email_siswa = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                $_SESSION['id_siswa'] = $row['id_siswa'];
                $_SESSION['username'] = $row['username'];
                header("Location: ../landing page/pertama.php");
                exit();
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!');</script>";
        }
    }

    ?>

</head>
<body>
<div class="modal modal-sheet position-static d-block bg-body-secondary p-4 py-md-5" tabindex="-1" role="dialog" id="modalSignin">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-4  mb-3 flex-column">
            <h1 class="pb-2 fw-bold mb-0 fs-2 text-center text-primary w-100">MindsUp</h1>
            <h3 class="fw-bold mb-0 ">Log in and let's get started!</h3>
        </div>

        <div class="modal-body p-5 pt-0">
            <form class="" method="post" action="">
            <div class="form-floating mb-3">
                <input type="email" class="form-control rounded-3" name="email" id="floatingemail" placeholder="name@example.com" required>
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control rounded-3" name="password" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Log In</button>
            <small class="text-body-secondary">Don’t have an account?<a href="register.php" class="text-primary fw-bold">Click here</a></small>
            <hr class="my-4">
            <h2 class="fs-5 fw-bold mb-3">Or use a third-party</h2>
            <button class="w-100 py-2 mb-2 btn btn-outline-secondary rounded-3" type="submit">
                
                Sign up with Google
            </button>
            <button class="w-100 py-2 mb-2 btn btn-outline-primary rounded-3" type="submit">
                
                Sign up with Facebook
            </button>
            <button class="w-100 py-2 mb-2 btn btn-outline-secondary rounded-3" type="submit">

                Sign up with GitHub
            </button>
            </form>
        </div>
        </div>
    </div>
</div>
</body>
</html>