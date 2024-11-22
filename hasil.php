<?php
session_start();

function hapusAntrian() {
    if (!empty($_SESSION['queue'])) {
        return array_shift($_SESSION['queue']);
    }
    return null;
}

function getAntrian() {
    return $_SESSION['queue'] ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dequeue'])) {
    hapusAntrian();
    header('Location: hasil.php');
    exit;
}

$antrian = getAntrian();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian FAR Electronic</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Daftar Antrian</h1>
        <?php if (empty($antrian)) { ?>
            <p>Tidak ada antrian saat ini.</p>
        <?php } else { ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Nomor HP</th>
                        <th>Jenis</th>
                        <th>Keluhan</th>
                        <th>Gambar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($antrian as $index => $data) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $data['nama'] ?></td>
                            <td><?= $data['nomor_hp'] ?></td>
                            <td><?= $data['jenis'] ?></td>
                            <td><?= $data['keluhan'] ?></td>
                            <td>
                                <?php if ($data['gambar']) { ?>
                                    <img src="uploads/<?= $data['gambar'] ?>" alt="Gambar Keluhan" style="width: 100px; height: auto;">
                                <?php } else { ?>
                                    <span>Tidak ada gambar</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
            <form action="hasil.php" method="POST">
                <button type="submit" name="dequeue">Hapus Antrian Terdepan</button>
            </form>

            <form action="index.php" method="GET">
                <button type="submit">Kembali</button>
            </form>
    </div>
</body>
</html>
