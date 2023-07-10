<?php
include 'classes/connect.php';
session_start();

$userID = $_SESSION['userID'];
// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    $_SESSION['loginMessage'] = "Please log in to access this page.";
    header("Location: index.php");
    exit();
}

//$userID = $_SESSION['userID'];
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teamName = $_POST['team_name'];
    $skillLevel = $_POST['skill_level'];
    $gameDay = $_POST['game_day'];

    // Validate the form fields
    $errors = array();

    if (empty($teamName)) {
        $errors[] = "Please enter a team name.";
    }

    if (!is_numeric($skillLevel) || $skillLevel < 1 || $skillLevel > 5) {
        $errors[] = "Please enter a valid skill level (1-5).";
    }

    if (empty($gameDay)) {
        $errors[] = "Please enter a game day.";
    }

    // If there are no errors, insert the team into the database
    if (empty($errors)) {
        try {
            $pdo = db_connect();

            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO team (userID, teamName, skillLevel, gameDay) VALUES (:user_id, :team_name, :skill_level, :game_day)");
            $stmt->bindParam(':user_id', $userID);
            $stmt->bindParam(':team_name', $teamName);
            $stmt->bindParam(':skill_level', $skillLevel);
            $stmt->bindParam(':game_day', $gameDay);

            // Execute the query
            $stmt->execute();

            // Redirect to the dashboard page or any other page you desire
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            // Handle any database connection errors
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Team</title>
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
            <h1>Create New Team</h1>
        </div>

        <div class="new-team">
            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                <table>
                    <tr>
                        <td><label for="team_name">Team Name:</label></td>
                        <td><input type="text" id="team_name" name="team_name" class="creat_team_input" required></td>
                    </tr>
                    <tr>
                        <td><label for="skill_level">Skill Level (1-5):</label></td>
                        <td><input type="range" id="skill_level" name="skill_level" min="1" max="5" class="creat_team_input" required></td>
                    </tr>
                    <tr>
                        <td><label for="game_day">Game Day:</label></td>
                        <td><input type="text" id="game_day" name="game_day" class="creat_team_input" required></td>
                    </tr>
                    <?php if (!empty($errors)) : ?>
                        <tr>
                            <td colspan="2">
                                <div class="error-message">
                                    <?php foreach ($errors as $error) : ?>
                                        <p><?= $error ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tfoot>
                    <tr>
                        <th colspan="2"><input type="submit" value="Create Team"></th>
                    </tr>
                    </tfoot>

                </table>
            </form>
        </div>
    </main>

</div>

<?php include("classes/footer.php"); ?>

</body>

</html>