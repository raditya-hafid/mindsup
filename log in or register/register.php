<head>
    <?php
    require '../head/head.php'; //
    require '../komponen/koneksi.php'; //

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'siswa'; // Default role untuk registrasi

        
        $cek = mysqli_prepare($conn, "SELECT username from siswa where username =?");
        $cek2 =mysqli_prepare($conn, "SELECT  email_siswa from siswa where email_siswa =?");

        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        $result = mysqli_stmt_get_result($cek);
        

        mysqli_stmt_bind_param($cek2, "s", $email);
        mysqli_stmt_execute($cek2);
        $result2 = mysqli_stmt_get_result($cek2);

        if (mysqli_num_rows($result)>0) {
            $error = "username telah dipakai!";
        }elseif(mysqli_num_rows($result2)>0){
            $error2 = "email telah dipakai!";
        }
        else{

            $stmt = mysqli_prepare($conn, "INSERT INTO siswa (username, email_siswa, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $role);

            if (mysqli_stmt_execute($stmt)){
                header("Location: login.php");
                exit();
            }else{
                echo "Pendaftaran gagal: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
        mysqli_stmt_close($cek2);
        
        
        
    }
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <style>
        /* CSS UNTUK IKON MATA */
        .password-container {
            position: relative;
            width: 100%; /* Penting agar ikon bisa diposisikan relatif terhadap input */
        }
        .password-container input[type="password"],
        .password-container input[type="text"] { /* Sesuaikan padding untuk memberi ruang ikon */
            padding-right: 40px !important; /* Gunakan !important jika diperlukan untuk menimpa Bootstrap */
        }
        .toggle-password {
            position: absolute;
            right: 10px; /* Jarak dari kanan input */
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d; /* Warna ikon, sesuaikan dengan tema Bootstrap Anda */
            z-index: 10; /* Pastikan ikon di atas input */
        }
    </style>
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
            <form class="" method="POST" action="">

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    
                </div>
            <?php endif; ?>


            <div class="form-floating mb-3">
                <input type="text" class="form-control rounded-3" name="username" id="floatingusername" placeholder="Username" required>
                <label for="floatingusername">username</label>
            </div>

            <?php if (!empty($error2)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error2; ?>
                    
                </div>
            <?php endif; ?>
        
            <div class="form-floating mb-3">
                <input type="email" class="form-control rounded-3" name="email" id="floatingemail" placeholder="name@example.com" required>
                <label for="floatingemail">Email address</label>
            </div>
            <div class="form-floating mb-3 password-container"> <input type="password" class="form-control rounded-3" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
                <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('floatingPassword', this)"></i> </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Sign up</button>
            <small class="text-body-secondary">Already have an account?<a href="login.php" class="text-primary fw-bold">Back to login</a>.</small>
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
<script>
// JavaScript untuk fungsi tampil/sembunyi password
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