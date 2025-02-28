<?php
    include "service/database.php";

    session_start();

    if(isset($_SESSION['is_login'])){
        header("location: akun.php");
    };

    $login_message = "";

    if (isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password']; // Jangan hash ulang di sini
    
        // Query hanya berdasarkan username atau email
        $sql = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $sql->bind_param("ss", $username, $email);
        $sql->execute();
        $result = $sql->get_result();
    
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
    
            // Verifikasi password
            if (password_verify($password, $data['password'])) {
                $_SESSION["username"] = $data["username"];
                $_SESSION["is_login"] = true;
                header("location: akun.php");
                exit();
            } else {
                $login_message = '<script>
                                    Swal.fire({
                                    icon: "error",
                                    title: "Gagal Login",
                                    text: "Sepertinya Password Salah, Coba Cek Lagi Cuy",
                                    });
                                  </script>';
            }
        } else {
            $login_message = '<script>
                                Swal.fire({
                                icon: "error",
                                title: "Gagal Login",
                                text: "Akun tidak ditemukan, pastikan username atau email sudah benar",
                                });
                              </script>';
        }
    };    
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
<body class="ipa">

    <div class="hal-log1">
        <div>
            <div class="container hal-log">
                <h1>Silakan Login Terlebih Dahulu</h1>
                <form action="login.php" method="POST">
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
                            <button class="btn3" type="submit" name="login">Masuk Sekarang</button>
                            </td>
                        </tr>
                    </table>
                </form>
                <p>Belum punya akun? <i><a href="register.php">Daftar dulu lah cuy</a></i></p>
                <p>Lanjutkan tanpa akun? <i><a href="index.php">Nanti aja lah akunnya</a></i></p>
            </div>
        </div>
    </div>

    <script
          src="bootstrap/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"
        ></script>

        <?= $login_message ?>
</body>
</html>