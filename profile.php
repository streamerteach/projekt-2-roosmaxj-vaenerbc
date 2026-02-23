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
        //Granskar om bilden är riktig eller en fake 
            if (isset($_POST["submit"])) {

                //Profil bilds upplägg

                $target_dir    = "./pictures/";
                $target_file   = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $uploadOk      = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if ($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }

                // Granskar om likadan fil finns ren!
                if (file_exists($target_file)) {
                    echo "Sorry, file already exists.";
                    $uploadOk = 0;
                }

                // Granskar filstorlek! 
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    echo "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                // Låter bara använda vissa filer!
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif") {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }

                // Granskar om $uploadOk är satt 0 av en error!
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                    // om allt är ok lägger den upp bilden! 
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }
            }

            // Skriver ut vilka file som har laddats up
            $dir = "./pictures/";
            $a   = scandir($dir);

            // Hitta första riktiga filen (hoppa över . och ..)
            foreach ($a as $file) {
                if ($file !== "." && $file !== "..") {
                    $profilePic = $file;
                    break;
                }
            }

            // Visa profilbilden
            if (isset($profilePic)) {
                echo '<h3>Your Profile pic:</h3>';
                echo '<img src="./pictures/' . $profilePic . '" alt="Profile picture" width="200">';
            } else {
                echo "No profile picture yet.";
            }

            //Print_R sriver ut innehållet av en array
        // print_r($a);
    }