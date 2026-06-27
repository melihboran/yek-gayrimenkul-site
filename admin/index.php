<?php
session_start();

$adminUser = "admin";
$adminPass = "123456";

$jsonFile = "../data/ilanlar.json";
$uploadDir = "../uploads/ilanlar/";

if (!file_exists("../data")) {
    mkdir("../data", 0777, true);
}

if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, "[]");
}

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ilanlar = json_decode(file_get_contents($jsonFile), true);
if (!is_array($ilanlar)) {
    $ilanlar = [];
}

$error = "";

if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST["login"])) {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION["admin"] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Kullanıcı adı veya şifre hatalı.";
    }
}

if (!isset($_SESSION["admin"])) {
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>YEK Admin Giriş</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #0f1014;
    color: #fff;
}
.login-box {
    max-width: 420px;
    margin: 100px auto;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 22px;
    padding: 30px;
}
input {
    width: 100%;
    padding: 14px;
    margin-bottom: 14px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.08);
    color: #fff;
    box-sizing: border-box;
}
button {
    background: #c8101a;
    color: #fff;
    border: 0;
    padding: 14px 24px;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 700;
}
.error {
    color: #ff6b6b;
}
</style>
</head>
<body>
<div class="login-box">
    <h1>YEK Admin</h1>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input name="username" placeholder="Kullanıcı adı" required>
        <input name="password" type="password" placeholder="Şifre" required>
        <button type="submit" name="login">Giriş Yap</button>
    </form>
</div>
</body>
</html>
<?php
exit;
}

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $newIlanlar = [];

    foreach ($ilanlar as $ilan) {
        if ((string)$ilan["id"] === (string)$id) {
            if (!empty($ilan["gorsel"])) {
                $imgPath = "../" . $ilan["gorsel"];
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }
        } else {
            $newIlanlar[] = $ilan;
        }
    }

    file_put_contents($jsonFile, json_encode($newIlanlar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php");
    exit;
}

if (isset($_POST["save"])) {
    $gorsel = "";

    if (!empty($_FILES["gorsel"]["name"])) {
        $ext = strtolower(pathinfo($_FILES["gorsel"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($ext, $allowed)) {
            die("Sadece JPG, JPEG, PNG veya WEBP yükleyebilirsiniz.");
        }

        $fileName = "ilan_" . time() . "_" . rand(1000, 9999) . "." . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["gorsel"]["tmp_name"], $targetPath)) {
            $gorsel = "uploads/ilanlar/" . $fileName;
        }
    }

    $newIlan = [
        "id" => time(),
        "baslik" => $_POST["baslik"] ?? "",
        "durum" => $_POST["durum"] ?? "",
        "kategori" => $_POST["kategori"] ?? "",
        "fiyat" => $_POST["fiyat"] ?? "",
        "konum" => $_POST["konum"] ?? "",
        "oda" => $_POST["oda"] ?? "",
        "brut" => $_POST["brut"] ?? "",
        "net" => $_POST["net"] ?? "",
        "bina_yasi" => $_POST["bina_yasi"] ?? "",
        "kat" => $_POST["kat"] ?? "",
        "isitma" => $_POST["isitma"] ?? "",
        "banyo" => $_POST["banyo"] ?? "",
        "balkon" => $_POST["balkon"] ?? "",
        "otopark" => $_POST["otopark"] ?? "",
        "aciklama" => $_POST["aciklama"] ?? "",
        "link" => $_POST["link"] ?? "",
        "gorsel" => $gorsel,
        "created_at" => date("Y-m-d H:i:s")
    ];

    array_unshift($ilanlar, $newIlan);

    file_put_contents($jsonFile, json_encode($ilanlar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>YEK Admin Panel</title>
<style>
* {
    box-sizing: border-box;
}
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #0f1014;
    color: #fff;
}
.admin-layout {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 24px;
}
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}
.admin-card {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 22px;
    padding: 26px;
    margin-bottom: 26px;
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}
input, select, textarea {
    width: 100%;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.08);
    color: #fff;
}
select option {
    color: #000;
}
textarea {
    min-height: 130px;
    margin-top: 14px;
}
button, .btn {
    border: 0;
    border-radius: 999px;
    padding: 14px 24px;
    background: #c8101a;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}
.btn-dark {
    background: #272a33;
}
.btn-danger {
    background: #7a1010;
}
.listing-admin-row {
    display: grid;
    grid-template-columns: 130px 1fr auto;
    gap: 18px;
    align-items: center;
    border-top: 1px solid rgba(255,255,255,.1);
    padding: 18px 0;
}
.listing-admin-row img {
    width: 130px;
    height: 92px;
    object-fit: cover;
    border-radius: 14px;
    background: #222;
}
.muted {
    color: rgba(255,255,255,.65);
}
.stats {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.stat-box {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 18px;
    padding: 18px 24px;
    min-width: 180px;
}
.stat-box strong {
    display: block;
    font-size: 30px;
}
@media(max-width:768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 18px;
    }
    .form-grid,
    .listing-admin-row {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>
<div class="admin-layout">

    <div class="admin-header">
        <div>
            <h1>YEK Gayrimenkul Admin</h1>
            <div class="muted">İlan ekle, sil ve yayındaki ilanları yönet.</div>
        </div>

        <a href="?logout=1" class="btn btn-dark">Çıkış Yap</a>
    </div>

    <div class="stats">
        <div class="stat-box">
            <span>Toplam İlan</span>
            <strong><?php echo count($ilanlar); ?></strong>
        </div>
    </div>

    <div class="admin-card">
        <h2>Yeni İlan Ekle</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <input type="text" name="baslik" placeholder="İlan Başlığı" required>
                <input type="text" name="fiyat" placeholder="Fiyat" required>

                <select name="durum" required>
                    <option value="">Durum Seç</option>
                    <option value="satilik">Satılık</option>
                    <option value="kiralik">Kiralık</option>
                </select>

                <select name="kategori" required>
                    <option value="">Kategori Seç</option>
                    <option value="daire">Daire</option>
                    <option value="villa">Villa</option>
                    <option value="arsa">Arsa</option>
                    <option value="ticari">Ticari</option>
                </select>

                <input type="text" name="konum" placeholder="Konum" required>
                <input type="text" name="oda" placeholder="Oda Sayısı">
                <input type="text" name="brut" placeholder="Brüt m²">
                <input type="text" name="net" placeholder="Net m²">
                <input type="text" name="bina_yasi" placeholder="Bina Yaşı">
                <input type="text" name="kat" placeholder="Bulunduğu Kat">
                <input type="text" name="isitma" placeholder="Isıtma">
                <input type="text" name="banyo" placeholder="Banyo Sayısı">
                <input type="text" name="balkon" placeholder="Balkon">
                <input type="text" name="otopark" placeholder="Otopark">
                <input type="url" name="link" placeholder="Sahibinden İlan Linki">
                <input type="file" name="gorsel" accept="image/*" required>
            </div>

            <textarea name="aciklama" placeholder="İlan Açıklaması"></textarea>

            <br><br>
            <button type="submit" name="save">Sitede Göster</button>
        </form>
    </div>

    <div class="admin-card">
        <h2>Mevcut İlanlar</h2>

        <?php if (empty($ilanlar)): ?>
            <p class="muted">Henüz ilan eklenmemiş.</p>
        <?php endif; ?>

        <?php foreach ($ilanlar as $ilan): ?>
            <div class="listing-admin-row">
                <?php if (!empty($ilan["gorsel"])): ?>
                    <img src="../<?php echo htmlspecialchars($ilan["gorsel"]); ?>" alt="">
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <div>
                    <h3><?php echo htmlspecialchars($ilan["baslik"] ?? ""); ?></h3>
                    <div class="muted"><?php echo htmlspecialchars($ilan["fiyat"] ?? ""); ?></div>
                    <div class="muted"><?php echo htmlspecialchars($ilan["konum"] ?? ""); ?></div>
                    <div class="muted">
                        <?php echo htmlspecialchars($ilan["durum"] ?? ""); ?> /
                        <?php echo htmlspecialchars($ilan["kategori"] ?? ""); ?>
                    </div>
                </div>

                <a class="btn btn-danger"
                   href="?delete=<?php echo urlencode($ilan["id"]); ?>"
                   onclick="return confirm('Bu ilan silinsin mi?')">
                    Sil
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>