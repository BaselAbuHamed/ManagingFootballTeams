<?php
session_start();
include 'classes/connect.php';

// Define variables and set to empty values
$username = $email = $password = $confirmPassword = "";
$usernameErr = $emailErr = $passwordErr = $confirmPasswordErr = "";

// Function to sanitize user input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = sanitizeInput($_POST["username"]);
        // Check if the username is valid (e.g., alphanumeric with underscores)
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            $usernameErr = "Invalid username format";
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = sanitizeInput($_POST["email"]);
        // Check if the email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = sanitizeInput($_POST["password"]);
        // Check if the password meets the requirements (e.g., minimum length)
        if (strlen($password) < 8) {
            $passwordErr = "Password must be at least 8 characters long";
        }
    }

    // Validate confirm password
    if (empty($_POST["confirm_password"])) {
        $confirmPasswordErr = "Please confirm your password";
    } else {
        $confirmPassword = sanitizeInput($_POST["confirm_password"]);
        // Check if the confirm password matches the password
        if ($confirmPassword !== $password) {
            $confirmPasswordErr = "Passwords do not match";
        }
    }

    // If all inputs are valid, proceed with inserting data into the database
    if (empty($usernameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        // Connect to the database
        $pdo = db_connect();

        // Check if email is unique
        $query = "SELECT * FROM user WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $emailErr = "Email already exists";
        } else {
            // Insert user data into the database
            // Prepare the INSERT statement
            $stmt = $pdo->prepare("INSERT INTO user (userName, email, `passward`) VALUES (?, ?, ?)");

            // Bind the parameters and execute the statement
            $stmt->execute([$username, $email, $password]);

            // Redirect to success page
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            <h1>Welcome!</h1>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table class="registration-table">
                <thead>
                <tr>
                    <th colspan="2">Registration</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th colspan="2"><input type="submit" value="Register"></th>
                </tr>
                </tfoot>
                <tr>
                    <td>Username:</td>
                    <td>
                        <label>
                            <input type="text" name="username" value="<?php echo $username; ?>"  class="registration-input">
                        </label>
                        <span class="error"><?php echo $usernameErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <label>
                            <input type="email" name="email" value="<?php echo $email; ?> "  class="registration-input">
                        </label>
                        <span class="error"><?php echo $emailErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td>
                        <label>
                            <input type="password" name="password" value="<?php echo $password; ?>"  class="registration-input">
                        </label>
                        <span class="error"><?php echo $passwordErr; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Confirm Password:</td>
                    <td>
                        <label>
                            <input type="password" name="confirm_password" value="<?php echo $confirmPassword; ?>"  class="registration-input">
                        </label>
                        <span class="error"><?php echo $confirmPasswordErr; ?></span>
                    </td>
                </tr>
            </table>
        </form>
    </main>


</div>


    <?php include("classes/footer.php"); ?>
</body>

</html>