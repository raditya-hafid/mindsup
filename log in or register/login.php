<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    session_start();
    require '../head/head.php';
    require '../komponen/koneksi.php'; //

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = $_POST['email_or_username']; // Ganti input name di form menjadi 'email_or_username'
    $password = $_POST['password'];
    $login_success = false;

    // 1. Coba login sebagai Siswa
    // Asumsi siswa login dengan email_siswa
    $stmt_siswa = mysqli_prepare($conn, "SELECT id_siswa, username, email_siswa, password FROM siswa WHERE email_siswa = ? OR username = ?");
    mysqli_stmt_bind_param($stmt_siswa, "ss", $email_or_username, $email_or_username);
    mysqli_stmt_execute($stmt_siswa);
    $result_siswa = mysqli_stmt_get_result($stmt_siswa);

    if (mysqli_num_rows($result_siswa) === 1) {
        $row = mysqli_fetch_assoc($result_siswa);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id_siswa'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email_siswa'];
            $_SESSION['role'] = 'siswa';
            $login_success = true;
            header("Location: ../dashboard/dashboard.php"); // Halaman dashboard siswa
            exit();
        }
    }
    mysqli_stmt_close($stmt_siswa);

    // 2. Jika tidak berhasil sebagai siswa, coba login sebagai Mentor
    // Asumsi mentor login dengan email_mentor atau username
    if (!$login_success) {
        $stmt_mentor = mysqli_prepare($conn, "SELECT id_mentor, username, email_mentor, password FROM mentor WHERE email_mentor = ? OR username = ?");
        mysqli_stmt_bind_param($stmt_mentor, "ss", $email_or_username, $email_or_username);
        mysqli_stmt_execute($stmt_mentor);
        $result_mentor = mysqli_stmt_get_result($stmt_mentor);

        if (mysqli_num_rows($result_mentor) === 1) {
            $row = mysqli_fetch_assoc($result_mentor);
            // Untuk mentor, kita asumsikan password belum di-hash jika mengikuti contoh gambar
            // IDEALNYA SEMUA PASSWORD HARUS DI-HASH!
            // Jika password mentor sudah di-hash, gunakan password_verify()
            if ($password === $row['password']) { // Ganti dengan password_verify jika sudah di-hash
            // if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id_mentor'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email_mentor'];
                $_SESSION['role'] = 'mentor';
                $login_success = true;
                header("Location: ../admin_mentor/dashboard_mentor.php");
                exit();
            }
        }
        mysqli_stmt_close($stmt_mentor);
    }

    // 3. Jika tidak berhasil sebagai mentor, coba login sebagai Admin
    // Asumsi admin login dengan username
    if (!$login_success) {
        $stmt_admin = mysqli_prepare($conn, "SELECT id_admin, username, password FROM admin WHERE username = ?");
        mysqli_stmt_bind_param($stmt_admin, "s", $email_or_username);
        mysqli_stmt_execute($stmt_admin);
        $result_admin = mysqli_stmt_get_result($stmt_admin);

        if (mysqli_num_rows($result_admin) === 1) {
            $row = mysqli_fetch_assoc($result_admin);
            // IDEALNYA SEMUA PASSWORD HARUS DI-HASH!
            // Jika password admin sudah di-hash, gunakan password_verify()
            if ($password === $row['password']) { // Ganti dengan password_verify jika sudah di-hash
            // if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id_admin'];
                $_SESSION['username'] = $row['username'];
                // Admin mungkin tidak punya email di tabelnya
                $_SESSION['role'] = 'admin';
                $login_success = true;
                // Buat halaman dashboard admin jika perlu, misal: ../admin_panel/dashboard_admin.php
                header("Location: ../admin_panel/dashboard_admin.php"); // Ganti ke dashboard admin
                exit();
            }
        }
        mysqli_stmt_close($stmt_admin);
    }

    // Jika semua percobaan login gagal
    if (!$login_success) {
        echo "<script>alert('Email/Username atau password salah!');</script>";
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* CSS tambahan untuk menata ikon mata */
    .password-container {
        position: relative;
        width: 100%; /* Pastikan ini sesuai dengan lebar input Anda */
    }
    .password-container .form-control { /* Menargetkan input Bootstrap di dalam container */
        padding-right: 40px !important; /* Menambahkan padding di sisi kanan input agar ada ruang untuk ikon. !important mungkin diperlukan untuk menimpa gaya Bootstrap. */
    }
    .toggle-password {
        position: absolute;
        right: 10px; /* Jarak ikon dari kanan input */
        top: 50%;
        transform: translateY(-50%); /* Menyesuaikan posisi vertikal agar tepat di tengah */
        cursor: pointer; /* Mengubah kursor menjadi pointer saat diarahkan ke ikon */
        color: #6c757d; /* Warna ikon, disesuaikan dengan warna teks sekunder Bootstrap */
        z-index: 10; /* Memastikan ikon berada di atas input */
    }
</style>
</head>
<body>
<div class="modal modal-sheet position-static d-block bg-body-secondary p-4 py-md-5" tabindex="-1" role="dialog" id="modalSignin">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header p-4 mb-3 flex-column">
                <h1 class="pb-2 fw-bold mb-0 fs-2 text-center text-primary w-100">MindsUp</h1>
                <h3 class="fw-bold mb-0 ">Log in and let's get started!</h3>
            </div>
            <div class="modal-body p-5 pt-0">
                <form class="" method="post" action="">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3" name="email_or_username" id="floatingInput" placeholder="Email atau Username" required>
                        <label for="floatingInput">Email atau Username</label>
                    </div>
                    <div class="form-floating mb-3 password-container">
                        <input type="password" class="form-control rounded-3" name="password" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                        <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('floatingPassword', this)"></i>
                    </div>
                    <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Log In</button>
                    <small class="text-body-secondary">Don’t have an account? <a href="register.php" class="text-primary fw-bold">Click here</a></small>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>

</body>
</html>