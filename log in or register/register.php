<?php
    // Pastikan session_start() jika ada penggunaan sesi di sini, tapi untuk register murni biasanya tidak.
    require '../head/head.php'; //
    require '../komponen/koneksi.php'; //

    $registration_error = ''; // Variabel untuk menyimpan pesan error

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password_input = $_POST['password']; // Ambil password input

        // Validasi dasar (tambahkan lebih banyak jika perlu)
        if (empty($username) || empty($email) || empty($password_input)) {
            $registration_error = "Semua field wajib diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $registration_error = "Format email tidak valid.";
        } else {
            // Cek apakah email atau username sudah ada
            $stmt_check = mysqli_prepare($conn, "SELECT id_siswa FROM siswa WHERE email_siswa = ? OR username = ?");
            mysqli_stmt_bind_param($stmt_check, "ss", $email, $username);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $registration_error = "Email atau Username sudah terdaftar.";
            } else {
                $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);
                $role = 'siswa'; // Default role untuk registrasi

                // Query INSERT dengan kolom 'role'
                $sql = "INSERT INTO siswa (username, email_siswa, password, role) VALUES (?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($conn, $sql);
                // Pastikan tipe data di bind_param sesuai: ssss untuk empat string
                mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $email, $password_hashed, $role);

                if (mysqli_stmt_execute($stmt_insert)){
                    // Registrasi berhasil, arahkan ke login
                    $_SESSION['registration_success'] = "Registrasi berhasil! Silakan login."; // Opsional: pesan sukses di halaman login
                    header("Location: login.php");
                    exit();
                }else{
                    // $registration_error = "Pendaftaran gagal: " . mysqli_error($conn);
                    $registration_error = "Pendaftaran gagal. Silakan coba lagi nanti."; // Pesan lebih umum
                }
                mysqli_stmt_close($stmt_insert);
            }
            mysqli_stmt_close($stmt_check);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php /* head.php sudah di-require di atas */ ?>
    <title>MindsUp - Registrasi</title>
</head>
<body>
<div class="modal modal-sheet position-static d-block bg-body-secondary p-4 py-md-5" tabindex="-1" role="dialog" id="modalSignin">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-4  mb-3 flex-column">
            <h1 class="pb-2 fw-bold mb-0 fs-2 text-center text-primary w-100">MindsUp</h1>
            <h3 class="fw-bold mb-0 ">Sign up for free</h3>
        </div>

        <div class="modal-body p-5 pt-0">
            <?php if(!empty($registration_error)): ?>
                <div class="alert alert-danger"><?php echo $registration_error; ?></div>
            <?php endif; ?>

            <form class="" method="POST" action="register.php">
            <div class="form-floating mb-3">
                <input type="text" class="form-control rounded-3" name="username" id="floatingusername" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <label for="floatingusername">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control rounded-3" name="email" id="floatingemail" placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <label for="floatingemail">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control rounded-3" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Sign up</button>
            <small class="text-body-secondary">Already have an account?<a href="login.php" class="text-primary fw-bold">Back to login</a>.</small>
            <hr class="my-4">
            </form>
        </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>