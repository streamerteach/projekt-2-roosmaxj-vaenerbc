<?php
// This file assumes:
// - $conn is available
// - $_SESSION['user_id'] is set
// - $user is already loaded by the parent file

 $user_id = $_SESSION['user_id'];
?>

<h2>Byt profilbild</h2>

<form class="upload-form" action="" method="post" enctype="multipart/form-data">
    <label for="fileToUpload">Välj bild:</label>
    <input type="file" name="fileToUpload" id="fileToUpload" required>
    <button type="submit" name="submit">Ladda upp</button>
</form>

<?php


if (isset($_POST["submit"])) {

    $target_dir = "./pictures/";
    $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

    $new_filename = "user_" . $user_id . "_" . time() . "." . $imageFileType;
    $target_file = $target_dir . $new_filename;

    $uploadOk = 1;

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "<p style='color:red;'>Filen är inte en bild.</p>";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "<p style='color:red;'>Filen är för stor.</p>";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "<p style='color:red;'>Ogiltig filtyp.</p>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

            // Save filename in DB
            $sql = "UPDATE users SET profile_pic = :pic WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':pic' => $new_filename,
                ':id'  => $user_id
            ]);

            echo "<p style='color:green;'>Profilbild uppdaterad!</p>";

            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        } else {
            echo "<p style='color:red;'>Fel vid uppladdning.</p>";
        }
    }
}


if (!empty($user['profile_pic'])) {
    echo '<h3>Din profilbild:</h3>';
    echo '<img src="./pictures/' . htmlspecialchars($user['profile_pic']) . '" width="200">';
} else {
    echo "<p>Ingen profilbild uppladdad ännu.</p>";
}
?>