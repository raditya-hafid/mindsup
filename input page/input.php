<!DOCTYPE html>
<html lang="en">
<?php
    require '../head/head.php';
?>
<body>
    <!-- CSS-only Sidebar implementation -->
    <div class="sidebar-container">
        <input type="checkbox" id="sidebar-toggle" class="sidebar-checkbox">
        
        <div class="sidebar">
            <label for="sidebar-toggle" class="sidebar-close">&times;</label>
            <div class="sidebar-content">
                <div class="sidebar-heading">Menu</div>
                <a href="#" class="sidebar-link"><i class="bi bi-house-door me-2"></i> Home</a>
                <a href="#" class="sidebar-link"><i class="bi bi-book me-2"></i> All Courses</a>
                <a href="#" class="sidebar-link"><i class="bi bi-star me-2"></i> Featured Courses</a>
                <a href="#" class="sidebar-link"><i class="bi bi-person me-2"></i> My Account</a>
                
                <div class="sidebar-heading">Categories</div>
                <a href="#" class="sidebar-link">Programming</a>
                <a href="#" class="sidebar-link">Data Science</a>
                <a href="#" class="sidebar-link">Web Development</a>
                <a href="#" class="sidebar-link">Digital Marketing</a>
                <a href="#" class="sidebar-link">Design</a>
                
                <div class="sidebar-heading">Help</div>
                <a href="#" class="sidebar-link"><i class="bi bi-question-circle me-2"></i> FAQs</a>
                <a href="#" class="sidebar-link"><i class="bi bi-headset me-2"></i> Support</a>
                <a href="#" class="sidebar-link"><i class="bi bi-info-circle me-2"></i> About Us</a>
            </div>
        </div>
        
        <label for="sidebar-toggle" class="sidebar-overlay"></label>
    </div>

    <!-- Navigation Bar -->
    <?php
     require '../komponen/nav.php';

    ?>

    <main class="mt-5">
        <div class="main-content">
            <form action="hasil.php" method="POST" enctype="multipart/form-data">
                <br>
                <h2 style="text-align: center;">Course Data Input</h2>

                <table class="table table-bordered">
                    <tr>
                        <td><label for="nim">Nama Course</label></td>

                        <td><input type="text" class="form-control" id="nim" name="namacourse" required></td>
                    </tr>
        
                    <tr>
                        <td><label for="nama">Deskripsi</label></td>

                        <td><textarea name="deskripsi" class="form-control" rows="10" cols="30" required></textarea></td>
                    </tr>
        
                    <tr>
                        <td>Kategori Materi</td>

                        <td>
                            <select name="kategori_materi" class="form-select" required>
                                <option value="Matematika">Matematika</option>
                                <option value="IPA">IPA</option>
                                <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                <option value="Bahasa Inggris">Bahasa Inggris</option>
                            </select>
                        </td>
                    </tr>
                    
                    <div class="form-check">
                        <tr>
                            <td rowspan="2">Metode Pembelajaran</td>

                            <td><input type="checkbox" class="form-check-input" name="metode[]" value="Video" >Video</td>
                        </tr>

                        <tr>
                            <td><input type="checkbox" class="form-check-input" name="metode[]" value="deskripsi" >Deskripsi</td>
                        </tr>
            
                        <tr>
                            <td rowspan="2">Jenis Kursus</td>

                            <td><input type="radio" class="form-check-input" name="jenis_kursus" value="Gratis" required>Gratis</td>
                        </tr>

                        <tr><td><input type="radio" class="form-check-input" name="jenis_kursus" value="Berbayar" required>Berbayar</td></tr>
                    </div>
        
                    <tr>
                        <td>Harga Kursus(Isi jika berbayar)</td>

                        <td><input type="text" class="form-control" name="harga" placeholder="Isi jika kursus berbayar"></td>
                    </tr>
        
                    <tr>
                        <td><label for="image">Upload Thumbnail:</label></td>

                        <td><input type="file" class="form-control" id="image" name="image" accept="image/*" required></td>
                    </tr>
        
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="kategori" value="Kursus SD">

                            <button type="submit" class="btn btn-primary">Submit</button>

                            <input type="reset" class="btn btn-danger" value="Reset Data">
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        
    </main>

    <?php
        require '../komponen/footer.php';
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>