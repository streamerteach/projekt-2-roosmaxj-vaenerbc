<?php
    //Skickar användaren till login om inte inloggad

    if (empty($_SESSION['username'])) {
    header("Location: ../register/");
    } elseif (! empty($_SESSION['username'])) {
    echo "<h2>Profile user: {$_SESSION['username']}</h2>";
    ?>

    <!-- Html koden för profil bilden -->
     <br>
    <p>Change your profile picture here:</p>
    <br>
    <form class="upload-form" action="./" method="post" enctype="multipart/form-data">
        <label for="fileToUpload">Select image to upload:</label>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <button type="submit" name="submit">Upload Image</button>
    </form>


    <?php
       


        if (isset($_POST["submit"])) {

            $user_id = $_SESSION['user_id'];
            $target_dir = "./pictures/";
            $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

            // Create unique filename
            $new_filename = "user_" . $user_id . "_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $new_filename;

            $uploadOk = 1;

            // Validate image
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "File too large.";
                $uploadOk = 0;
            }

            if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                echo "Invalid file type.";
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

                // 🔥 Reload updated user data
                $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                echo "Profile picture updated!";
            } else {
                echo "Error uploading file.";
            }
        }
    }

            // Visa profilbilden
            if (!empty($user['profile_pic'])) {
                echo '<h3>Your Profile Picture:</h3>';
                echo '<img src="./pictures/' . $user['profile_pic'] . '" width="200">';
            } else {
                echo "No profile picture yet.";
            }

            //Print_R sriver ut innehållet av en array
        // print_r($a);
    }


        $user_id = $_SESSION['user_id'];

        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // UPDATE PROFILE
        if (!empty($_POST['form']) && $_POST['form'] === "update") {

            $sql = "UPDATE users SET 
                real_name = :real_name,
                email = :email,
                city = :city,
                ad_text = :ad_text,
                salary = :salary,
                preference = :preference
                WHERE id = :id";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ':real_name'  => $_POST['real_name'],
                ':email'      => $_POST['email'],
                ':city'       => $_POST['city'],
                ':ad_text'    => $_POST['ad_text'],
                ':salary'     => $_POST['salary'],
                ':preference' => $_POST['preference'],
                ':id'         => $_SESSION['user_id']
            ]);

            echo "<p style='color:green;'>Profile updated!</p>";
        }

        // DELETE PROFILE
        if (!empty($_POST['form']) && $_POST['form'] === "delete") {

         // Fetch user
            $sql = "SELECT password FROM users WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($user && password_verify($_POST['password'], $user['password'])) {

                // Delete user
                $sql = "DELETE FROM users WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $_SESSION['user_id']]);

                // Destroy session
                session_destroy();

                header("Location: ../index.php?deleted=1");
                exit;

            } else {
                echo "<p style='color:red;'>Wrong password. Profile NOT deleted.</p>";
            }
        }