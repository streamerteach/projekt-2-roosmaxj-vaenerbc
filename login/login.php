<?php
include_once "../methods.php";

// -------------------------
// REGISTER
// -------------------------
if (!empty($_POST['form']) && $_POST['form'] === "register") {

    $username   = $_POST['username'];
    $real_name  = $_POST['real_name'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email      = $_POST['email'];
    $city       = $_POST['city'];
    $ad_text    = $_POST['ad_text'];
    $salary     = $_POST['salary'];
    $preference = $_POST['preference'];

    $sql = "INSERT INTO users 
            (username, real_name, password, email, city, ad_text, salary, preference)
            VALUES 
            (:username, :real_name, :password, :email, :city, :ad_text, :salary, :preference)";

    $stmt = $conn->prepare($sql);

    $success = $stmt->execute([
        ':username'   => $username,
        ':real_name'  => $real_name,
        ':password'   => $password,
        ':email'      => $email,
        ':city'       => $city,
        ':ad_text'    => $ad_text,
        ':salary'     => $salary,
        ':preference' => $preference
    ]);

    if ($success) {
        echo "Registration successful!";
    } else {
        echo "Error: Could not register user.";
    }
}



// -------------------------
// LOGIN
// -------------------------
if (!empty($_POST['form']) && $_POST['form'] === "login") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: index.php");
        exit;
    }

    echo "Invalid username or password.";
}

?>