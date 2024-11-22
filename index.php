<?php
session_start();

// Pastikan antrian diset jika belum ada
if (!isset($_SESSION['queue'])) {
    $_SESSION['queue'] = [];
}

function tambahAntrian($nama, $nomor_hp, $jenis, $keluhan, $gambar) {
    $_SESSION['queue'][] = [
        'nama' => $nama,
        'nomor_hp' => $nomor_hp,
        'jenis' => $jenis,
        'keluhan' => $keluhan,
        'gambar' => $gambar
    ];
}

$pesanError = '';
$gambar = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nomor_hp = $_POST['nomor_hp'];
    $jenis = $_POST['jenis'];
    $keluhan = $_POST['keluhan'];

    if (isset($_FILES['gambar_keluhan']) && $_FILES['gambar_keluhan']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar_keluhan']['tmp_name'];
        $fileName = $_FILES['gambar_keluhan']['name'];
        $fileSize = $_FILES['gambar_keluhan']['size'];
        $fileType = $_FILES['gambar_keluhan']['type'];

        $fileInfo = pathinfo($fileName);
        $fileExtension = strtolower($fileInfo['extension']); 

        $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

        if ($fileSize > 5 * 1024 * 1024) {
            $pesanError = "Ukuran file maksimal 5MB.";
        }
        elseif (!in_array($fileExtension, $allowedfileExtensions)) {
            $pesanError = "Hanya file dengan ekstensi .jpg, .jpeg, dan .png yang diperbolehkan.";
        } 
        elseif (!in_array($fileType, ['image/jpeg', 'image/png'])) {
            $pesanError = "Hanya file gambar (.jpg, .jpeg, .png) yang diperbolehkan.";
        } else {
            $safeNama = preg_replace("/[^a-zA-Z0-9\-\_]/", "_", $nama); 
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $safeNama . '.' . $fileExtension;

            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar = basename($dest_path);
            } else {
                $pesanError = "Terjadi kesalahan saat mengunggah file.";
            }
        }
    }

    if (!$pesanError) {
        tambahAntrian($nama, $nomor_hp, $jenis, $keluhan, $gambar);
        header('Location: hasil.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAR Electronic</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Form Service FAR Electronic</h1>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" pattern="[A-Za-z\s]+" required>

            <label for="nomor_hp">Nomor HP</label>
            <input type="tel" id="nomor_hp" name="nomor_hp" pattern="[0-9]{10,13}" required>

            <label for="jenis">Jenis Elektronik</label>
            <select id="jenis" name="jenis" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Televisi">Televisi</option>
                <option value="Kulkas">Kulkas</option>
                <option value="AC">AC</option>
                <option value="Mesin Cuci">Mesin Cuci</option>
                <option value="Kipas Angin">Kipas Angin</option>
                <option value="Rice Cooker">Rice Cooker</option>
                <option value="Komputer">Komputer</option>
                <option value="Oven">Oven</option>
                <option value="Setrika">Setrika</option>
                <option value="Pompa Air">Pompa Air</option>
                <option value="Blender">Blender</option>
                <option value="Speaker">Speaker</option>
            </select>

            <label for="keluhan">Keluhan</label>
            <textarea id="keluhan" name="keluhan" rows="4" placeholder="Jelaskan keluhan sedetail mungkin" required></textarea>

            <label for="gambar_keluhan">Gambar Keluhan (Opsional)</label>
            <input type="file" id="gambar_keluhan" name="gambar_keluhan" accept=".jpg, .jpeg, .png">

            <?php if ($pesanError) { ?>
                <span style="color: red;"><?= $pesanError ?></span>
            <?php } ?>

            <button type="submit">Tambah ke Antrian</button>
        </form>
    </div>
</body>
</html>
