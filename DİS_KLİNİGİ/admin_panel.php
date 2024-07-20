<?php
session_start();

// Oturum kontrolü yap
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zeybus";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Randevu ekleme işlemini kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ekle'])) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $tel = $conn->real_escape_string($_POST['tel'] ?? '');
    $date = $conn->real_escape_string($_POST['date'] ?? '');
    $time = $conn->real_escape_string($_POST['time'] ?? '');

    // Randevu ekleme sorgusu
    $sql = "INSERT INTO zeybus_randevu (name, email, tel, date, time) 
            VALUES ('$name', '$email', '$tel', '$date', '$time')";

    if ($conn->query($sql) === TRUE) {
        // Yeni randevuyu ekledikten sonra pop-up mesajı göstermek için bir oturum değişkeni ayarlıyoruz
        $_SESSION['ekle'] = true;
        // Başarılı eklemeden sonra kullanıcıyı tekrar admin_panel.php sayfasına yönlendir
        header('Location: admin_panel.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Randevu listeleme
$sql = "SELECT * FROM zeybus_randevu ORDER BY date ASC";
$result = $conn->query($sql);
?> 

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <style>
        /* CSS kodları */
      body {            
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }
th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        form button {
            padding: 10px;
            background-color: #6666FF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #FF00FF;
        }
.btn {
            text-decoration: none;
            padding: 6px 10px;
            background-color: #6666FF;
            color: #fff;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #FF00FF;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        form label {
            display: block;
            margin-bottom: 5px;
        }  form input[type="text"],
        form input[type="email"],
        form input[type="tel"],
        form input[type="date"],
        form input[type="time"],
        form textarea {
            width: calc(100% - 18px);
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        form button[type="submit"],
        form button[name="listele"] {
            width: calc(100% - 18px);
            padding: 10px;
            background-color: #6666FF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button[type="submit"]:hover,
        form button[name="listele"]:hover {
            background-color: #FF00FF;
        }


    </style>
</head>
<body>
    <div class="container">
        <h2>Randevular</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <button type="submit" name="listele">Listele</button>
        </form>
        <table>
            <tr>
                <th>ID</th>
                <th>Adı</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Tarih</th>
                <th>Saat</th>
                <th>İşlemler</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["id"]."</td>";
                    echo "<td>".$row["name"]."</td>";
                    echo "<td>".$row["email"]."</td>";
                    echo "<td>".$row["tel"]."</td>";
                    echo "<td>".$row["date"]."</td>";
                    echo "<td>".$row["time"]."</td>";
                    echo "<td><a class='btn' href='update.php?id=".$row["id"]."'>Güncelle</a> | <a class='btn' href='delete.php?id=".$row["id"]."'>Sil</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Randevu bulunamadı.</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="container">
        <h2>Randevu Ekle</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="ekle" value="1">
            <div>
                <label for="name">Adı:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="tel">Telefon:</label>
                <input type="tel" id="tel" name="tel" required>
            </div>
            <div>
                <label for="date">Tarih:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div>
                <label for="time">Saat:</label>
                <input type="time" id="time" name="time" required>
            </div>
            <button type="submit">Randevu Ekle</button>
        </form>
    </div>

    <?php
    // Eğer yeni randevu eklendiyse pop-up mesajını göster
    if (isset($_SESSION['ekle'])) {
        echo "<script>alert('Randevunuz başarıyla eklenmiştir');</script>";
        // Pop-up mesajı gösterildikten sonra oturum değişkenini temizle
        unset($_SESSION['ekle']);
    }
    ?>
</body>
</html>

<?php
$conn->close();
?>
