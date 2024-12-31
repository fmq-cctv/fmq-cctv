<?php
include "service/database.php";
require 'vendor/autoload.php';
$data_message = "";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if(isset($_SESSION['is_login'])){
    header("location: akun.php");
};

if (isset($_POST['register'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password
    $otp = rand(100000, 999999); // Generate kode OTP (6 digit angka)

    $check_sql = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check_sql->bind_param("ss", $username, $email);
    $check_sql->execute();
    $result = $check_sql->get_result();

    if ($result->num_rows > 0){
        $data_message = '<script>
                            Swal.fire({
                            icon: "warning",
                            title: "Username Atau Email Sudah Terdaftar",
                            text: "Silakan gunakan username atau email lain, atau tidak login dengan username atau email tersebut",
                            });
                        </script>';
    }else{

        $sql = $db->prepare("INSERT INTO pending_users (username, email, password, otp) VALUES (?, ?, ?, ?)");
        $sql->bind_param("sssi", $username, $email, $password, $otp);
    
    
        if ($sql->execute()) {
            // Jika data berhasil disimpan, kirim email kode OTP
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
                $mail->setFrom('fmqcctv@gmail.com', 'FMQ CCTV'); // Pengirim
                $mail->addAddress($email, $username);              // Penerima
                $mail->Subject = 'Kode OTP Anda';
                $mail->Body    = "Halo $username, berikut adalah kode OTP Anda: $otp. Gunakan kode ini untuk memverifikasi akun Anda.";
    
                $mail->send(); // Kirim email
                $data_message = '<script>
                                Swal.fire({
                                icon: "success",
                                title: "Kode OTP Terkirim",
                                text: "Cek email anda, kami sudah mengirim kode OTP anda",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location = "verify_otp.php?email='.urlencode($email).'";
                                    }
                                });
                            </script>';
            } catch (Exception $e) {
                $data_message = '<script>
                                Swal.fire({
                                icon: "error",
                                title: "Ada Kesalahan",
                                text: "Ada sesuatu yang salah sepertinya",
                                });
                            </script>';
                            echo "{$mail->ErrorInfo}";
            }
        } else {
            $data_message = '<script>
                                Swal.fire({
                                icon: "error",
                                title: "Ada Kesalahan",
                                text: "Ada sesuatu yang salah sepertinya",
                                });
                            </script>';
        }
    
        $sql->close();
    }
    $check_sql->close();
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

<div class="hal-log2">
        <div>
            <div class="container hal-log ">
                <h1>Buat Akun Baru Lagi Ya</h1>
                <form action="register.php" onsubmit="register(); return false;" method="POST">
                    <table>
                        <tr>
                            <td>
                            <input type="text" placeholder="Username" name="username">
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <input type="email" placeholder="Email" name="email">
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <input type="password" placeholder="Password" name="password">
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <button class="btn3" type="submit" name="register">Daftar Sekarang</button>
                            </td>
                        </tr>
                    </table>
                </form>
                <p>Sudah punya akun? <i><a href="login.php">Langsung login aja cuy</a></i></p>
            </div>
        </div>
    </div>

    <script
          src="bootstrap/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"
        ></script>

        <?= $data_message ?>
</body>
</html>