<?php
session_start();

require '../komponen/koneksi.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Daftar kupon valid (bisa diambil dari database nantinya)
$validCoupons = [
    'DISKON70' => 70,
    'DISKON20' => 20,
    'GRATIS' => 100
];

$subtotal = 0;
$discountPersen = 0;
$discountAmount = 0;
$total = 0;
$pesanKupon = '';
$pesanErrorPembayaran = '';

// Hitung subtotal
foreach ($_SESSION['keranjang'] as $item) {
    $subtotal += $item['harga'];
}

// Proses kupon jika dikirimkan
if (isset($_POST['applyCoupon']) && !empty($_POST['couponCode'])) {
    $couponCode = strtoupper(trim($_POST['couponCode']));
    if (isset($validCoupons[$couponCode])) {
        $discountPersen = $validCoupons[$couponCode];
        $discountAmount = ($subtotal * $discountPersen) / 100;
        $pesanKupon = "Selamat! Anda mendapatkan diskon {$discountPersen}% dengan kupon {$couponCode}.";
        $_SESSION['kode_kupon_aktif'] = $couponCode; // Simpan kupon aktif
        $_SESSION['diskon_persen_aktif'] = $discountPersen;
    } else {
        $pesanKupon = 'Kode kupon tidak valid.';
        unset($_SESSION['kode_kupon_aktif']); // Hapus jika tidak valid
        unset($_SESSION['diskon_persen_aktif']);
    }
} elseif (isset($_SESSION['kode_kupon_aktif']) && isset($_SESSION['diskon_persen_aktif'])) {
    // Terapkan kembali diskon jika ada di sesi (misalnya setelah menghapus item)
    $discountPersen = $_SESSION['diskon_persen_aktif'];
    $discountAmount = ($subtotal * $discountPersen) / 100;
    $pesanKupon = "Diskon {$discountPersen}% dengan kupon ".$_SESSION['kode_kupon_aktif']." diterapkan.";
}


$total = $subtotal - $discountAmount;

// Proses pembayaran (simulasi, bisa dikembangkan lebih lanjut)
if (isset($_POST['bayar_sekarang'])) {
    if (empty($_SESSION['keranjang'])) {
        $pesanErrorPembayaran = "Keranjang Anda kosong. Silakan tambahkan kursus terlebih dahulu.";
    } elseif (!isset($_POST['payment_method']) || empty($_POST['payment_method'])) {
        $pesanErrorPembayaran = "Silakan pilih metode pembayaran.";
    } else {
        // Simulasi proses pembayaran berhasil
        // Simpan transaksi ke sesi (seperti di simulasi.php)
        if (!isset($_SESSION['transactions'])) {
            $_SESSION['transactions'] = [];
        }
        $selectedItemsForTransaction = [];
        $selectedItemsId = [];
        foreach($_SESSION['keranjang'] as $cartItem) {
            $selectedItemsForTransaction[] = $cartItem['nama'];
            $selectedItemsId[] = (int)$cartItem['id'];
        }

        $transaction = [
            'date' => date('Y-m-d'),
            'items' => json_encode($selectedItemsForTransaction), // Array nama item
            'payment_method' => $_POST['payment_method'],
            'subtotal' => $subtotal,
            'discount_code' => $_SESSION['kode_kupon_aktif'] ?? "",
            'discount_percentage' => $discountPersen,
            'discount_amount' => $discountAmount,
            'total' => $total
        ];

        // Kosongkan keranjang setelah berhasil bayar
        $_SESSION['keranjang'] = [];
        unset($_SESSION['kode_kupon_aktif']);
        unset($_SESSION['diskon_persen_aktif']);

        $stmt = mysqli_prepare($conn, "INSERT INTO `pembelian` (`id_siswa`, `status`, `tanggal_beli`, `items`, `pay_method`, `subtotal`, `discount_code`, `discount_persen`, `discount_amount`, `total`) VALUES (?, 'Pending', ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssisiii", $_SESSION['user_id'], $transaction['date'], $transaction['items'], $transaction['payment_method'], $transaction['subtotal'], $transaction['discount_code'], $transaction['discount_percentage'], $transaction['discount_amount'], $transaction['total']);
        mysqli_stmt_execute($stmt);

        $stmt2 = mysqli_prepare($conn, "SELECT `id_pembelian` FROM `pembelian` ORDER BY `id_pembelian` DESC LIMIT 1");
        mysqli_stmt_execute($stmt2);
        $result = mysqli_stmt_get_result($stmt2);
        $row = mysqli_fetch_assoc($result);

        foreach ($selectedItemsId as $id_kursus_transaksi) {
            $stmt3 = mysqli_prepare($conn, "INSERT INTO `detail_pembelian`(`id_kursus`, `id_pembelian`) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt3, "ii", $id_kursus_transaksi, $row['id_pembelian']);
            mysqli_stmt_execute($stmt3);
        }

        // Redirect ke halaman sukses atau tampilkan pesan sukses
        $_SESSION['pesan_sukses_pembayaran'] = "Pembayaran berhasil! Terima kasih telah melakukan pembelian.";
        header("Location: keranjang.php"); // Kembali ke keranjang untuk lihat pesan sukses / riwayat
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<?php require '../head/head.php'; // Sesuaikan path jika simulasi/keranjang.php ada di root, maka jadi 'head/head.php' ?>
<link rel="stylesheet" href="../css/simulasi.css"> <body>
    <?php require '../komponen/nav.php'; // Sesuaikan path ?>
    <?php require '../komponen/sidebar.php'; // Sesuaikan path ?>

    <div class="container mt-5 pt-5">
        <h2 class="mb-4 text-center">Keranjang Belanja Anda</h2>

        <?php if (isset($_SESSION['pesan_keranjang'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['pesan_keranjang']; unset($_SESSION['pesan_keranjang']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_sukses_pembayaran'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['pesan_sukses_pembayaran']; unset($_SESSION['pesan_sukses_pembayaran']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($pesanErrorPembayaran)): ?>
            <div class="alert alert-danger"><?php echo $pesanErrorPembayaran; ?></div>
        <?php endif; ?>


        <?php if (empty($_SESSION['keranjang'])): ?>
            <div class="alert alert-warning text-center" role="alert">
                Keranjang Anda masih kosong. <a href="../courses/Halaman.php" class="alert-link">Mulai belanja sekarang!</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Detail Kursus di Keranjang</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;">Gambar</th>
                                            <th>Nama Kursus</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($_SESSION['keranjang'] as $id => $item): ?>
                                            <tr>
                                                <td>
                                                    <?php 
                                                    // Path gambar relatif dari keranjang.php ke root, lalu ke gambar
                                                    // Asumsi $item['gambar'] berisi 'uploads/subfolder/file.jpg'
                                                    if (empty($item['gambar']) || !file_exists($item['gambar'])) {
                                                        $gambar_display = '../asset/placeholder_image.png';
                                                    } else {
                                                        $gambar_display = $item['gambar'];
                                                    }
                                                    ?>
                                                    <img src="<?php echo $gambar_display; ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>" style="width: 80px; height: auto; border-radius: .25rem;">
                                                </td>
                                                <td class="align-middle"><?php echo htmlspecialchars($item['nama']); ?></td>
                                                <td class="align-middle text-end">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                                <td class="align-middle text-center">
                                                    <form action="../keranjang_aksi.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="id_kursus" value="<?php echo $id; ?>">
                                                        <input type="hidden" name="action" value="hapus">
                                                        <input type="hidden" name="dari_keranjang" value="true">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Item">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                         <div class="card-footer d-flex justify-content-end">
                            <form action="../keranjang_aksi.php" method="POST">
                                <input type="hidden" name="action" value="kosongkan">
                                <input type="hidden" name="dari_keranjang" value="true">
                                <button type="submit" class="btn btn-outline-danger">Kosongkan Keranjang</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <form method="POST" action="keranjang.php">
                        <div class="card card-custom mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Kode Kupon</h5>
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="couponCode" placeholder="Masukkan kode kupon" value="<?php echo htmlspecialchars($_SESSION['kode_kupon_aktif'] ?? ''); ?>">
                                    <button class="btn btn-secondary" type="submit" name="applyCoupon">Terapkan</button>
                                </div>
                                <?php if (!empty($pesanKupon)): ?>
                                    <div class="alert <?php echo $discountPersen > 0 ? 'alert-success' : 'alert-danger'; ?> mt-2">
                                        <?php echo $pesanKupon; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card card-custom mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Metode Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer_bank" checked>
                                    <label class="form-check-label" for="transfer">Transfer Bank</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="ewallet" value="ewallet">
                                    <label class="form-check-label" for="ewallet">E-Wallet</label>
                                </div>
                                </div>
                        </div>

                        <div class="card card-custom mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Ringkasan Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div>Subtotal: <span class="float-end">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span></div>
                                <?php if ($discountAmount > 0): ?>
                                <div>Diskon (<?php echo $discountPersen; ?>%): <span class="float-end text-danger">- Rp <?php echo number_format($discountAmount, 0, ',', '.'); ?></span></div>
                                <?php endif; ?>
                                <hr>
                                <div><strong>Total Pembayaran: <span class="float-end">Rp <?php echo number_format($total, 0, ',', '.'); ?></span></strong></div>
                            </div>
                        </div>

                        <button type="submit" name="bayar_sekarang" class="btn btn-success w-100 btn-lg">Bayar Sekarang</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['transactions']) && !empty($_SESSION['transactions'])): ?>
        <div class="card card-custom mt-5">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Riwayat Transaksi Terakhir</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Item</th>
                                <th>Metode</th>
                                <th>Subtotal</th>
                                <th>Diskon</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($_SESSION['transactions'], 0, 5) as $transaction): // Tampilkan 5 transaksi terakhir ?>
                                <tr>
                                    <td><?php echo $transaction['date']; ?></td>
                                    <td><?php echo implode(', ', $transaction['items']); ?></td>
                                    <td><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($transaction['payment_method']))); ?></td>
                                    <td>Rp <?php echo number_format($transaction['subtotal'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($transaction['discount_amount'] > 0): ?>
                                            <?php echo $transaction['discount_percentage']; ?>% (Rp <?php echo number_format($transaction['discount_amount'], 0, ',', '.'); ?>)
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>Rp <?php echo number_format($transaction['total'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <?php require '../komponen/footer.php'; // Sesuaikan path ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>