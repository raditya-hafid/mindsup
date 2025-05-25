<?php
// Mulai sesi untuk menyimpan riwayat transaksi
session_start();

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

// Daftar kupon valid
$validCoupons = [
    'JARINGANSALAWASE' => 20,
    'WEBCOURSE' => 20
];

// Variabel dasar 
$subtotal = 0;
$discount = 0;
$discountAmount = 0;
$total = 0;
$discountMessage = '';
$selectedItems = [];

// DAFTAR course
$courses = ['mtk', 'ipa', 'ips'];
$price = 150000;

// Kalau tombol clear_history diklik
if (isset($_POST['clear_history'])) {
    $_SESSION['transactions'] = [];
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Hitung subtotal berdasarkan mata pelajaran yang dipilih
    foreach ($courses as $course) {
        if (isset($_POST[$course])) {
            $selectedItems[] = strtoupper($course);
            $subtotal += $price;
        }
    }

    // Validasi kupon jika ada
    if (!empty($_POST['couponCode'])) {
        $couponCode = strtoupper($_POST['couponCode']);
        if (isset($validCoupons[$couponCode])) {
            $discount = $validCoupons[$couponCode];
            $discountAmount = ($subtotal * $discount) / 100;
            $discountMessage = "Selamat! Anda mendapatkan diskon {$discount}%";
        } else {
            $discountMessage = 'Kode kupon tidak valid';
        }
    }

    $total = $subtotal - $discountAmount;
    
    if(isset($_POST['bayar'])){
        // Cek jika semua data sudah lengkap (pilihan mata pelajaran dan metode pembayaran)
        if (isset($_POST['payment']) && !empty($selectedItems) && $_POST['bayar'] == 'bayar') {
            // Simpan transaksi
            $transaction = [
                'date' => date('Y-m-d H:i:s'),
                'items' => $selectedItems,
                'payment_method' => $_POST['payment'],
                'discount' => $discount,
                'discount_amount' => $discountAmount,
                'total' => $total
            ];
    
            array_unshift($_SESSION['transactions'], $transaction);
            // Redirect untuk mencegah pengulangan transaksi setelah refresh
        } elseif ($_POST['bayar'] == 'bayar') {
            $paymentError = "Silakan pilih mata pelajaran dan metode pembayaran.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
    <?php
    require '../head/head.php';
    ?>
<body>
    <!-- Navigasi utama -->
    <?php
     require '../komponen/nav.php';

    ?>

    <!-- sidebar -->
    <?php
     require '../komponen/sidebar.php';

    ?>




    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <!-- Formulir pembayaran -->
                <form method="POST">
                    <!-- Pilih Mata Pelajaran -->
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Pilih Mata Pelajaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mtk" id="mtk" value="1" 
                                    <?php echo isset($_POST['mtk']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="mtk">Matematika - Rp 150.000</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ipa" id="ipa" value="1"
                                    <?php echo isset($_POST['ipa']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ipa">IPA - Rp 150.000</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ips" id="ips" value="1"
                                    <?php echo isset($_POST['ips']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ips">IPS - Rp 150.000</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kode Kupon -->
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Kode Kupon</h4>
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" class="form-control" name="couponCode" 
                                    placeholder="Masukkan kode kupon" 
                                    value="<?php echo $_POST['couponCode'] ?? ''; ?>">
                                <button class="btn btn-secondary" type="submit" name="applyCoupon">Terapkan Kupon</button>
                            </div>
                            <?php if (!empty($discountMessage)): ?>
                                <div class="alert <?php echo $discount > 0 ? 'alert-success' : 'alert-danger'; ?> mt-2">
                                    <?php echo $discountMessage; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Metode Pembayaran -->
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Metode Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="transfer" 
                                    value="transfer" <?php echo (!isset($_POST['payment']) || $_POST['payment'] == 'transfer') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="transfer">Transfer Bank</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="ewallet" 
                                    value="ewallet" <?php echo isset($_POST['payment']) && $_POST['payment'] == 'ewallet' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ewallet">E-Wallet</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="cash" 
                                    value="cash" <?php echo isset($_POST['payment']) && $_POST['payment'] == 'cash' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="cash">Tunai</label>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Pembayaran -->
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Ringkasan Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div>Subtotal: Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></div>
                            <div>Diskon: Rp <?php echo number_format($discountAmount, 0, ',', '.'); ?> (<?php echo $discount; ?>%)</div>
                            <div><strong>Total Pembayaran: Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></div>
                        </div>
                    </div>

                    <!-- Tombol Bayar -->
                    <button type="submit" name="bayar" value="bayar" class="btn btn-primary w-100">Bayar Sekarang</button>
                    <?php if (isset($paymentError)): ?>
                    <div class="alert alert-danger mt-2"><?php echo $paymentError; ?></div>
                    <?php endif; ?>
                </form>

                <!-- Riwayat Transaksi -->
                <div class="card card-custom mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Riwayat Transaksi</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Diskon</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['transactions'] as $index => $transaction): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $transaction['date']; ?></td>
                                            <td><?php echo implode(', ', $transaction['items']); ?></td>
                                            <td><?php echo strtoupper($transaction['payment_method']); ?></td>
                                            <td><?php echo $transaction['discount']; ?>% (Rp <?php echo number_format($transaction['discount_amount'], 0, ',', '.'); ?>)</td>
                                            <td>Rp <?php echo number_format($transaction['total'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST">
      <button type="submit" name="clear_history" class="btn btn-danger w-100 mt-2">Hapus Riwayat Transaksi</button>
    </form>
    
    <?php
        require '../komponen/footer.php';
        ?>

<!-- Sertakan script Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>