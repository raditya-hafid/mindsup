<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    require '../head/head.php';
    ?>
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
            <form class="">
            <div class="form-floating mb-3">
                <input type="email" class="form-control rounded-3" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control rounded-3" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Sign up</button>
            <small class="text-body-secondary">By clicking Sign up, you agree to the terms of use.</small>
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