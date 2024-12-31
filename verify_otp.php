<?php
require 'service/database.php'; // Koneksi ke database

$verify = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $input_otp = trim($_POST['otp']);

    if (!$email || !is_numeric($input_otp)) {
        $verify = '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Data tidak valid!",
                        text: "Silakan periksa input Anda.",
                    });
                  </script>';
    } else {
        // Ambil OTP dari database berdasarkan email
        $sql = $db->prepare("SELECT * FROM pending_users WHERE email = ?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Periksa apakah OTP cocok
            if ($user['otp'] == $input_otp) {
                // Pindahkan data user ke tabel 'users' utama
                $sql_insert = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $sql_insert->bind_param("sss", $user['username'], $user['email'], $user['password']);
                
                if ($sql_insert->execute()) {
                    // Hapus data dari tabel 'pending_users'
                    $sql_delete = $db->prepare("DELETE FROM pending_users WHERE email = ?");
                    $sql_delete->bind_param("s", $email);
                    $sql_delete->execute();

                    $verify = '<script>
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Akun Anda telah diverifikasi.",
                                }).then(() => {
                                    window.location = "login.php";
                                });
                              </script>';
                } else {
                    $verify = '<script>
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    text: "Gagal memindahkan data ke tabel utama.",
                                });
                              </script>';
                }
            } else {
                $verify = '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Kode OTP Salah!",
                                text: "Silakan coba lagi.",
                            });
                          </script>';
            }
        } else {
            $verify = '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Email Tidak Ditemukan!",
                            text: "Email Anda belum terdaftar.",
                        });
                      </script>';
        }
    }
} elseif (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    die("Email tidak valid.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <title>Verifikasi OTP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
</head>
<body class="vrf-otp">
    <form method="POST" class="verify container" action="verify_otp.php">
        <div class="col-lg-12 justify-content-center row">
            <div class="verify-otp justify-content-center row">
                <div class="col-lg-7 justify-content-center row vrf-top">
                    <h2 class="col col-lg-5">Verifikasi Kode OTP</h2>
                    <img class="col-lg-2" src="img/secure-shield.png" alt="">
                </div>
                <div class="col-lg-7 justify-content-center row">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                    <label class="col-lg-3">Kode OTP:</label>
                    <input type="number" name="otp" class="otp col-lg-4" placeholder="Masukkan Kode OTP" required>
                </div><br>
                <div class="col-lg-7 justify-content-center row vrf-bttm">
                    <button type="submit" class="btn3 justify-content-center">Verifikasi</button>
                </div>
            </div>
        </div>
    </form>
    <?= $verify ?>
</body>
</html>
