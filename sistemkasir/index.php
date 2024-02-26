<?php

$conn = mysqli_connect('localhost', 'root', '', 'db_kasir');

function readmenu()
{
    global $conn;
    $query = "SELECT id_menu,gambar, nama, FORMAT(harga, 0) as harga FROM menu";
    $akhir = mysqli_query($conn, $query);
    return $akhir;
}

function addmenu()
{
    if (isset($_POST['submit1'])) {
        global $conn;
        $gambar = $_FILES['gambar']['name'];
        $temp = $_FILES['gambar']['tmp_name'];
        $folder = 'images/';

        $random = uniqid();
        $namafile = $random . '_' . $gambar;

        $maxSize = 5 * 1024 * 1024; // 5MB 
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        $fileExtension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);

        if ($_FILES['gambar']['size'] > $maxSize) {
            echo "<script> 
                alert('Ukuran file terlalu besar. Maksimum 5MB.');
                window.history.back();
              </script>";
            exit;
        }

        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            echo "<script> 
                alert('Jenis file tidak diizinkan. Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.');
                window.history.back();
              </script>";
            exit;
        }

        move_uploaded_file($temp, $folder . $namafile);

        $nama = htmlspecialchars($_POST['nama']);
        $harga = htmlspecialchars($_POST['harga']);

        $query = "INSERT INTO menu(gambar, nama, harga) VALUES(?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $namafile, $nama, $harga);
        $akhir = mysqli_stmt_execute($stmt);

        if ($akhir) {
            echo "<script> 
                alert('Menu berhasil ditambahkan');
                document.location.href = 'index.php';
              </script>";
        } else {
            echo "<script> 
                alert('Menu gagal ditambahkan');
                document.location.href = 'index.php';
              </script>";
        }
    }
}



function deletemmenu()
{
    if (isset($_GET['deletemenu'])) {
        global $conn;
        $deletemenu = $_GET['deletemenu'];
        $query = "DELETE FROM menu WHERE id_menu = $deletemenu";
        $akhir = mysqli_query($conn, $query);

        if ($akhir > 0) {
            echo "
            <script>
            alert('menu berhasil dihapus');
            document.location.href='index.php';
            </script>
            ";
        }
    }
}

function addpesanan()
{
    if (isset($_GET['submit3'])) {
        global $conn;
        $id_menu = $_GET['submit3'];
        $qty = $_GET['qty'];

        $query = "INSERT INTO pesanan(id_menu, qty) VALUES($id_menu ,$qty)";
        $akhir = mysqli_query($conn, $query);

        if ($akhir > 0) {
            echo "
            <script>
            document.location.href='index.php';
            </script>
            ";
        } else {
            echo "
                <script>
                alert('pesanan gagal ditambahkan :(');
                document.location.href='index.php';
                </script>
                ";
        }
    }
}

function readpesanan()
{
    global $conn;
    $query = "SELECT id_pesanan, gambar, nama, qty, FORMAT((qty * harga), 0) as totalharga from pesanan join menu using(id_menu)";
    $akhir = mysqli_query($conn, $query);
    return $akhir;
}

function deletepesanan()
{
    if (isset($_GET['deletepesanan'])) {
        global $conn;
        $deletepesanan = $_GET['deletepesanan'];
        $query = "DELETE FROM pesanan WHERE id_pesanan = $deletepesanan";
        $akhir = mysqli_query($conn, $query);

        if ($akhir > 0) {
            echo "
            <script>
            alert('pesanan berhasil dihapus');
            document.location.href='index.php';
            </script>
            ";
        }
    }
}

function totalharga()
{
    global $conn;
    $query = "SELECT FORMAT(sum(qty * harga),0) AS totalharga FROM pesanan JOIN menu USING(id_menu)";
    $akhir = mysqli_query($conn, $query);
    return $akhir;
}

function bayar()
{
    if (isset($_GET['submit4'])) {
        global $conn;
        $pembayaran = $_GET['pembayaran'];
        echo "
        <script>
        document.location.href = 'invoice.php?pembayaran=$pembayaran';
        </script>
        ";

        $query2 = "UPDATE pesanan SET status = 'dibayar'";
        $update = mysqli_query($conn, $query2);
       
    }
}

function truncate()
{
    if (isset($_GET['deletetruncate'])) {
        global $conn;
        $query = "TRUNCATE pesanan";
        $akhir = mysqli_query($conn, $query);
        if ($akhir > 0) {
            echo "
            <script>
            document.location.href='index.php';
            </script>
            ";
        }
    }
}

readmenu();
addmenu();
deletemmenu();

addpesanan();
readpesanan();
deletepesanan();

bayar();
truncate();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem kasir php</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1"><a href="index.php" style="text-decoration: none; color:white">Sistem kasir</a></span>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 bg-primary text-white">
                <!-- main content 1 start-->
                <h2>Sistem kasir</h2>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Tambah menu
                </button>
                <br><br>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Gambar</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (readmenu() as $row) { ?>
                            <tr>
                                <td><img src="images/<?php echo $row['gambar'] ?>" width="100px" alt=""></td>
                                <td><?php echo $row['nama'] ?></td>
                                <td>Rp.<?php echo $row['harga'] ?></td>
                                <td>
                                    <form action="" method="get">
                                        <input type="number" class="form-control" id="qty" name="qty" placeholder="Qty" min="1" required>
                                        <a href="index.php?deletemenu=<?php echo $row['id_menu'] ?>" style="text-decoration: none;">delete</a>
                                        <button type="number" class="btn btn-success" name="submit3" value="<?php echo $row['id_menu'] ?>">+</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- main content 1 end -->
            </div>
            <div class="col-md-6 bg-danger text-white">
                <!-- main content 2 start-->
                <h2>Pesanan</h2>
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Gambar</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (readpesanan() as $row) { ?>
                            <tr>
                                <td><img src="images/<?php echo $row['gambar'] ?>" width="40px" alt=""></td>
                                <td><?php echo $row['nama'] ?></td>
                                <td><?php echo $row['qty'] ?>x</td>
                                <td>Rp.<?php echo $row['totalharga'] ?> </td>
                                <td>
                                    <a href="index.php?deletepesanan=<?php echo $row['id_pesanan'] ?>" style="text-decoration:none;">delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="card">
                    <div class="card-body bg-dark text-white">
                        <?php foreach (totalharga() as $row) { ?>
                            <h2>Total harga : Rp.<?php echo $row['totalharga'] ?> </h2>
                        <?php } ?>
                        <form action="" method="get">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pembayaran" value="tunai" id="tunai" required>
                                <label class="form-check-label" for="tunai">
                                    Tunai
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pembayaran" value="gopay" id="gopay" required>
                                <label class="form-check-label" for="gopay">
                                    Gopay
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pembayaran" value="bni" id="bni" required>
                                <label class="form-check-label" for="bni">
                                    Bni
                                </label>
                            </div><br>
                            <button type="number" class="btn btn-success" name="submit4" value="submit4">Bayar & cetak struk</button>
                        </form>
                    </div>
                </div>
                <br><br><br><br><br><br>
                <!-- main content 2 end -->
            </div>
        </div>
    </div>

    <!-- Modal start -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah pesanan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <p style="color:blue">Ukuran maksimal gambar 5mb (JPEG, JPG, PNG, dan GIF)</p>
                            <label for="gambar" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="gambar" name="gambar">
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama produk</label>
                            <input type="text" class="form-control" id="nama" name="nama">
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="text" class="form-control" id="harga" value="1000" min="1000" name="harga">
                        </div>
                        <button type="number" class="btn btn-primary" name="submit1" value="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->


    <!-- footer start -->
    <nav class="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Developed by @Muhammad fadhil naim</span>
        </div>
    </nav>
    <!-- footer end -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>