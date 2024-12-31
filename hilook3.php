<?php
$data_message = "";
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    // Jika belum login, arahkan ke halaman login
    $data_message = '<script>
            Swal.fire({
            icon: "warning",
            title: "Anda Belum Login",
            text: "Anda Belum Login, Silakan Login Terlebih Dahulu",
            confirmButtonText: "OK",
            }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = "login.php";
                    }
                    });
            </script>';
}

include "service/database.php";
require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (isset($_POST['order'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $notel = trim($_POST['notel']);
    $alamat = trim($_POST['alamat']);
    $paket = trim($_POST['paket']);
    $price = trim($_POST['price']);

    $sql = "INSERT INTO order_users (username, email, notel, alamat, paket, price) VALUE ('$username', '$email', '$notel', '$alamat', '$paket', '$price')";

    if ($db->query($sql)) {
        try {
            $mail = new PHPMailer(true);

            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fmqcctv@gmail.com'; // Ganti dengan email Anda
            $mail->Password   = 'mktm ygbe jifh jfnb';   // Ganti dengan App Password Gmail
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Pengaturan email
            $mail->isHTML(true);
            $mail->setFrom('fmqcctv@gmail.com', 'FMQ CCTV'); // Pengirim
            $mail->addAddress($email, $username);              // Penerima
            $mail->Subject = 'Konfirmasi Pesanan FMQ CCTV';
            $mail->Body    = "
            Hai, $username! <br><br>
            Terima kasih telah melakukan pemesanan paket CCTV Hilook.<br>
            <strong>Detail Pesanan Anda:</strong><br>
            - Paket: $paket<br>
            - Harga: $price<br>
            - Alamat: $alamat<br><br>
            Balas email ini dengan YA agar kami memprosesnya.<br>
            Jika email ini tidak ada balasan selama 24 jam, data yang ada di kami akan kami hapus.<br>
            Salam,<br>
            <strong>Tim FMQ CCTV</strong>
            ";

            $mail->send(); // Kirim email
            $data_message = '<script>
            Swal.fire({
            icon: "success",
            title: "Pesanan Berhasil Disimpan",
            text: "Email Konfirmasi Berhasil Dikirim, Silakan Cek Email Anda",
            confirmButtonText: "OK",
            }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = "paket.php";
                    }
                    });
            </script>';
        } catch (Exception $e) {
            $data_message = '<script>
            Swal.fire({
            icon: "warning",
            title: "Pesanan Berhasil Disimpan",
            text: "Tapi Gagal Mengirim Email, Pastikan Email-nya Benar",
            confirmButtonText: "OK",
            })
            </script>';

        };
    }else{
        $data_message = '<script>
            Swal.fire({
            icon: "error",
            title: "Pesanan Tidak Disimpan",
            text: "Sepertinya Ada Kesalahan",
            });
            </script>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda FMQ CCTV</title>
    <link rel="stylesheet" href="style.css">
    <link
      href="bootstrap/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
</head>
<body>
<div class="container psn-ezviz1">
    <div class="psn justify-content-center">
        <h1>Pesan Paket CCTV 6 Channel Hilook</h1>
        <div class="row justify-content-center">
            <div class="col-lg-3 psnn">
              <img src="img/hilook3.png" alt="Ezviz CCTV">
              <h3 id="price">Rp6.699.000,00</h3>
            </div>
            <div class="col-lg-3 data">
                <form action="hilook2.php" onsubmit="order();return false;" method="POST">
                    <table>
                        <tr>
                            <td>
                                <input type="text" id="username" name="username" placeholder="Masukkan Username" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="tel" id="notel" name="notel" placeholder="Masukkan Nomor WA" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea name="alamat" id="alamat" name="alamat" placeholder="Masukkan Alamat"></textarea>
                            </td>
                        </tr>
                        <tr>
                          <td>
                            <label>
                                <input class="checkbox" type="checkbox" id="paket" name="paket" value="Paket 6 CCTV Hilook" required>
                                <span class="span">Paket 6 CCTV Hilook</span>
                            </label>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <label>
                                <input class="checkbox" type="checkbox" id="price" name="price" value="Rp6.699.000,00" required>
                                <span class="span">Rp6.699.000,00</span>
                            </label>
                          </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn3" type="submit" name="order">Pesan Sekarang</button>
                            </td>
                        </tr>                        
                        <tr>
                            <td>
                                <a class="btn3" href="paket.php#hilook" role="button">Batalkan Pesanan</a>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

    <?= $data_message ?>
</body>
</html>