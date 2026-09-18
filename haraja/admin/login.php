<?php
require_once __DIR__ . "/../db.php";

function clean($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$message = "";
if (isset($_POST["login"])) {
    $username = clean($_POST['username']);
    $password = clean($_POST['password']);

    $sql = "SELECT * FROM admin_users WHERE username='$username'"; // CHECK FOR USER
    $result = mysqli_query($conn, $sql);

    if (empty($username) && empty($password)) {
        $message = "Username and Password required";
    } elseif (empty($username)) {
        $message = "Username is required";
    } elseif (empty($password)) {
        $message = "Password required";
    } elseif (mysqli_num_rows($result) == 0) {
        $message = "Account does not exist";
    } else {
        $row = mysqli_fetch_assoc($result); // FETCH PARA KUNIN AND MA COMPARE
        if ($password == $row['password']) {
            $_SESSION['username'] = $row['username'];

            // // Check if "Remember Me" was ticked
            // if (isset($_POST['remember'])) {
            //     // 1. Generate a secure, unique random token
            //     $token = bin2hex(random_bytes(32));

            //     // 2. Save this token to the database for this specific user
            //     $userEmail = $row['email'];
            //     mysqli_query($conn, "UPDATE users SET remember_token = '$token' WHERE email = '$userEmail'") or die("Database Error: " . mysqli_error($conn));

            //     // 3. Drop the cookie on their computer (lasts for 30 days)
            //     setcookie("remember_me", $token, time() + (60 * 60 * 24 * 30), "/", "", false, true);
            // }

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Incorrect password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Harajā Resort</title>
    <style>
        :root {
            --primary-gold: #C8A54B;
            --bg-color: #121212;
            --card-bg: #1A1A1A;
            --text-color: #E0E0E0;
            --border-color: #333;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Lato', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: url('https://images.unsplash.com/photo-1561501878-aabd62634533?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .login-card {
            position: relative;
            z-index: 2;
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            border-top: 4px solid var(--primary-gold);
            text-align: center;
        }

        .login-card h2 {
            font-family: var(--font-heading);
            color: #fff;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .login-card p {
            color: var(--text-color);
            margin-bottom: 2rem;
            opacity: 0.8;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border-color);
            color: #fff;
            border-radius: 4px;
            transition: 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-gold);
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 12px;
            background: var(--primary-gold);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #b08d3e;
        }
    </style>
</head>

<body>
    <div class="login-overlay"></div>
    <div class="login-card">
        <h2>Harajā Portal</h2>
        <p>Owner & Management Access</p>
        <p style="color: white;"><?php echo $message; ?></p>
        <!-- Form redirects to dashboard.php for now -->
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="admin">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-primary" name="login">Secure Login</button>
        </form>
    </div>
</body>

</html>