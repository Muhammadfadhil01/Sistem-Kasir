<?php

$conn = mysqli_connect('localhost', 'root', '', 'db_kasir');

function readpesanan()
{
    global $conn;
    $query = "SELECT id_pesanan, gambar, nama, qty, FORMAT((qty * harga), 0) as totalharga from pesanan join menu using(id_menu)";
    $akhir = mysqli_query($conn, $query);
    return $akhir;
}

function totalharga()
{
    global $conn;
    $query = "SELECT FORMAT(sum(qty * harga), 0) AS totalharga FROM pesanan JOIN menu USING(id_menu)";
    $akhir = mysqli_query($conn, $query);
    return $akhir;
}


$pembayaran = $_GET['pembayaran'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h2>Kasir fadhil</h2>
        <h2>------------------------------------------</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (readpesanan() as $row) { ?>
                    <tr>
                        <td><?php echo $row['nama'] ?></td>
                        <td><?php echo $row['qty'] ?>X</td>
                        <td>Rp.<?php echo $row['totalharga'] ?> </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <h5>Pembayaran : <?php print_r($pembayaran) ?></h5>
        <?php foreach (totalharga() as $row) { ?>
            <h2>Total harga : Rp.<?php echo $row['totalharga'] ?> </h2>
        <?php } ?>
        <h2>------------------------------------------</h2>
        <h5 style="text-align: center; text-decoration:none">Terimakasih sudah order disini!</h5>
        <h5 style="text-align: center;"><a href="index.php?deletetruncate" style="text-decoration:none; color:blue">sistem kasir</a></h5>
    </div>
    <script>
        window.print()
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>