<?php
include 'classes/connect.php';
session_start();
if(isset($_SESSION['Uname'])){
    header("Location: dashboard.php");
}
// Check if the user clicked on the logout link on the index.php page
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect back to the index.php page
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $_SESSION['Uname']=$email;

    // Check if the "Remember Me" checkbox is checked
    if (isset($_POST['remember'])) {
        // Set a cookie with the user's email and password (replace 3600 with the desired cookie expiration time)
        setcookie('email', $email, time() + 3600*60);
        setcookie('password', $password, time() + 3600*60);
    }

     // Check if the email and password are not empty
     if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            
            try {
                $pdo= db_connect();

                // Prepare the SQL statement
                $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
                $stmt->bindParam(':email', $email);

                // Execute the query
                $stmt->execute();

                // Fetch the user record
                $user = $stmt->fetch();

                // Verify the password
                if ($user && isset($user[3]) && $password == $user[3]) {
                    // Password is correct, store user information in session
                    $_SESSION['userID']= $user[0];
                    $_SESSION['email'] = $user[2];
                    $_SESSION['username']=$user[1];
                    
                    // Redirect to the home page or any other page you desire
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Invalid email or password
                    $error = "Invalid email or password.";
                }
            } catch (PDOException $e) {
                // Handle any database connection errors
                die("Database Error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>

<?php include("classes/header.php"); ?>

<div class="body_container">

    <aside>
        <?php include("nav_bar.php"); ?>
    </aside>

    <main>

        <div class="welcome">
            <h1>Welcome !</h1>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table>
                <thead>
                <tr>
                    <th colspan=2>Log In</th>
                </tr>
                </thead>
                <tfoot>
                <?php if (!empty($error)) : ?>
                    <tr>
                        <td colspan="2">
                            <div class="error-message">
                                <?php echo $error ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th colspan="2">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Remember Me</label>
                    </th>
                </tr>
                <tr>
                    <th colspan=2><input type="submit" value="LogIn"></th>
                </tr>
                <tr>
                    <th colspan=2><a href="signup.php">sign up</a></th>
                </tr>
                </tfoot>
                <tr>
                    <td>Email:</td>
                    <td><label>
                            <input type="email" name="email" value="<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>" >
                        </label></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><label>
                            <input type="password" name="password" value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>">
                        </label></td>
                </tr>
            </table>
        </form>

    </main>

</div>
<?php include("classes/footer.php"); ?>
</body>
</html>