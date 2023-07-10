<?php
include 'classes/connect.php';
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teamID = $_GET['id'];
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

    // If there are no errors, update the team in the database
    if (empty($errors)) {
        try {
            $pdo = db_connect();

            // Prepare the SQL statement
            $stmt = $pdo->prepare("UPDATE team SET teamName = :team_name, skillLevel = :skill_level, gameDay = :game_day WHERE teamID = :team_id");
            $stmt->bindParam(':team_name', $teamName);
            $stmt->bindParam(':skill_level', $skillLevel);
            $stmt->bindParam(':game_day', $gameDay);
            $stmt->bindParam(':team_id', $teamID);

            // Execute the query
            $stmt->execute();

            // Redirect to the team details page or any other page you desire
            header("Location: team_details.php?id=" . $teamID);
            exit();
        } catch (PDOException $e) {
            // Handle any database connection errors
            die("Database Error: " . $e->getMessage());
        }
    }
}

// Fetch the team details from the database
function fetchTeamDetails($teamID) {
    try {
        $pdo = db_connect();

        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM team WHERE teamID = :team_id");
        $stmt->bindParam(':team_id', $teamID);

        // Execute the query
        $stmt->execute();

        // Fetch the team details
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        return $team;
    } catch (PDOException $e) {
        // Handle any database connection errors
        die("Database Error: " . $e->getMessage());
    }
}

// Get the team ID from the URL parameter
$teamID = $_GET['id'];

// Get the team details
$team = fetchTeamDetails($teamID);
?>



<!DOCTYPE html>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Team</title>
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
            <h1>Update Team</h1>
        </div>

        <div class="update-team">

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $teamID; ?>" method="post">
                <table>
                    <tr>
                        <td><label for="team_name">Team Name:</label></td>
                        <td><input type="text" id="team_name" name="team_name" value="<?php echo $team['teamName']; ?>" class="creat_team_input" required></Continuing from where we left off:

                            ```html
                        </td>
                    </tr>
                    <tr>
                        <td><label for="skill_level">Skill Level (1-5):</label></td>
                        <td><input type="range" id="skill_level" name="skill_level" min="1" max="5" value="<?php echo $team['skillLevel']; ?>" class="creat_team_input" required></td>
                    </tr>
                    <tr>
                        <td><label for="game_day">Game Day:</label></td>
                        <td><input type="text" id="game_day" name="game_day" value="<?php echo $team['gameDay']; ?>" class="creat_team_input" required></td>
                    </tr>
                    <?php if (!empty($errors)) : ?>
                        <tr>
                            <td colspan="2">
                                <div class="error-message">
                                    <?php foreach ($errors as $error) : ?>
                                        <p><?php echo $error; ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tfoot>
                    <tr>
                        <th colspan="2"><input type="submit" value="Update Team"></th>
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