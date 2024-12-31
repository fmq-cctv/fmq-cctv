<?php

    session_start();
    include 'service/database.php'; // Menghubungkan ke database
    require "vendor/autoload.php";
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $psn_message = "";

    if (!isset($_SESSION['username'])) {
        echo "";
    }

    // Ambil username dari session
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;


    // Query data sesuai username
    $sql = "SELECT * FROM order_users WHERE username = '$username'";
    $result = $db->query($sql);

    if (isset($_POST['logout_confirmed'])){
        session_destroy(); // Hapus semua session
        header("Location: index.php"); // Redirect ke halaman login
    }

    if (isset($_POST['btl_psn_confirmed']) && isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];
    
        // Cek apakah pesanan sudah lebih dari 3 hari
        $stmt = $db->prepare("SELECT created_at FROM order_users WHERE id = ? AND username = ?");
        $stmt->bind_param("is", $order_id, $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $created_at = new DateTime($row['created_at']);
            $now = new DateTime();
            $interval = $created_at->diff($now);
    
            if ($interval->days > 3) {
                // Pesanan lebih dari 3 hari
                $psn_message = '<script>
                Swal.fire({
                icon: "warning",
                title: "Pesanan Tidak Bisa Dibatalkan",
                text: "Pesanan ini sudah lebih dari 3 hari, jadi tidak bisa dibatalkan",
                confirmButtonText: "OK",
                }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "akun.php";
                        }
                        });
                </script>';
            } else {
                // Hapus pesanan jika kurang dari 3 hari
                $stmt = $db->prepare("DELETE FROM order_users WHERE id = ? AND username = ?");
                $stmt->bind_param("is", $order_id, $username);
                if ($stmt->execute()) {
                    $psn_message = '<script>
                    Swal.fire({
                    icon: "success",
                    title: "Pesanan Berhasil Dibatalkan",
                    text: "Pesanan anda sudah berhasil dibatalkan",
                    confirmButtonText: "OK",
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "akun.php";
                            }
                            });
                    </script>';
                } else {
                    $psn_message = "<script>
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat membatalkan pesanan.', 'error');
                    </script>";
                }
            }
        } else {
            $psn_message = "<script>
                Swal.fire('Gagal!', 'Pesanan tidak ditemukan.', 'error');
            </script>";
        }
        $stmt->close();
    }    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="akun">
    <div class="container">
        <div class="container fixed-top">
            <nav class="navbar navbar-expand-lg ">
                <div class="container-fluid ">
                  <a class="navbar-brand" href="#"><img src="img/logop.png" alt=""></a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav ms-auto">
                      <li><a class="nav-link" href="index.php">Beranda</a></li>
                      <li><a class="nav-link" href="tentang_kami.php">Tentang Kami</a></li>
                      <li><a class="nav-link" href="paket.php">Paket</a></li>
                      <li><a class="nav-link" href="kontak.php">Kontak</a></li>
                      <li class="dropdown">
                        <a href="#" class="dropbtn active1">Lainnya</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Artikel</a></li>
                            <li><a href="akun.php">Akun Saya</a></li>
                        </ul>
                      </li>
                    </div>
                  </div>
                </div>
            </nav>
        </div>
    </div>
    <br><br>
    <div class="hal2 container">
        <div class=" row">
                <div class="hal-log3">
                    <div class="data-akun">
                        <div>
                            <img src="img/account.png " alt="">
                        </div>
                        <div class="data-akun2">
                            <a href="login.php">
                                Login
                            </a>
                            <span>|</span>
                            <a href="register.php">
                                Daftar
                            </a>
                            <?php if (isset($_SESSION["username"])): ?>
                                <h3><?= htmlspecialchars($_SESSION["username"]); ?></h3>
                            <?php else: ?>
                                <h3>Anda Belum Login Nih</h3>
                            <?php endif; ?>

                            <form action="akun.php" method="POST" id="logoutForm">
                                <button type="button" class="btn3" id="logoutButton" name="logout" <?php echo isset($_SESSION['username']) ? '' : 'disabled'; ?>>Logout</button>
                                <input type="hidden" name="logout_confirmed" value="1">
                            </form>
                        </div>
                    </div>

                    <div> 
                        <div>
                            <h1>
                                Data Produk untuk <?php echo $username; ?>
                            </h1>
                        </div>
                            <div class="data-pkt">
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<h4>" . $row["paket"] . "</h4>";
                                        echo "<p>Harga: " . $row["price"] . "</p>";
                                        echo "<p>No. WA: " . $row["notel"] . "</p>";
                                        echo '<p class="alamat">Alamat: ' . $row["alamat"] . "</p>";
                                        echo '<form action="akun.php" method="POST" id="btl_psnForm_' . $row["id"] . '">
                                                <button type="button" class="btn3" id="btl_psnButton_' . $row["id"] . '" data-order-id="' . $row["id"] . '" name="btl_psn">Batalkan Pesanan</button>
                                                <input type="hidden" name="btl_psn_confirmed" value="1">
                                                <input type="hidden" name="order_id" value="' . $row["id"] . '">
                                            </form>';
                                    }
                                } else {
                                    echo "<p>Tidak ada data.</p>";
                                }
                                ?>
                            </div>
                    </div>
                </div>
        </div>
    </div>

    <script
          src="bootstrap/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"
        ></script>
    <script>
        document.getElementById('logoutButton').addEventListener('click', function (e) {
            Swal.fire({
                title: 'Apakah Anda yakin ingin logout?',
                text: "Data sesi Anda akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Gak Jadi'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        });

        document.querySelectorAll("[id^='btl_psnButton']").forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.getAttribute('data-order-id'); // Ambil ID pesanan dari atribut data
                Swal.fire({
                    title: 'Apakah Anda yakin ingin membatalkan pesanan?',
                    text: "Pesanan ini akan dihapus",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Batalkan Pesanan',
                    cancelButtonText: 'Gak Jadi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('btl_psnForm_' + orderId).submit();
                    }
                });
            });
        });


    </script>

    <?= $psn_message ?>
</body>
</html>
