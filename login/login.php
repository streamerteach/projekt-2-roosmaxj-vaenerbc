<?php
include "./randPassFunc.php";

$file = __DIR__ . "/users.json";

// Load users safely
$users = [];
if (file_exists($file)) {
    $users = json_decode(file_get_contents($file), true);
    if (! is_array($users)) {
        $users = [];
    }
}

// REGISTER
if (! empty($_POST['form']) && $_POST['form'] === "register") {

    $username = test_input($_POST['username']);
    $email    = test_input($_POST['email']);

    // Check for duplicate username
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            die("Username already exists!");
        }
    }

    // Generate random password
    $password       = generatePassword();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Add user to array
    $users[] = [
        "username" => $username,
        "email"    => $email,
        "password" => $hashedPassword,
    ];

    // Save to JSON
    if (! file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT))) {
        die("Cannot write to users.json. Check file permissions.");
    }

    // Show the password directly
    echo "<h2>Registration successful!</h2>";
    echo "<p>Your account has been created.</p>";
    echo "<p><strong>Username:</strong> $username</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p>Use these credentials to log in below.</p>";
}

// LOGIN
if (! empty($_POST['form']) && $_POST['form'] === "login") {

    $username = test_input($_POST['username']);
    $password = $_POST['password'];

    $found = false;
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            echo "<h2>Login successful!</h2>";
            echo "<p>Welcome back, $username!</p>";
            $found = true;
            break;
        }
    }

    if (! $found) {
        echo "<p>Invalid username or password.</p>";
    }
}
