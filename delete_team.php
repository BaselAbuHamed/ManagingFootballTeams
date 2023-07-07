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

// Fetch teams created by the user from the database
function fetchUserTeams($userID) {
    try {
        $pdo = db_connect();

        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM team WHERE userID = :user_id");

        // Bind the user ID parameter
        $stmt->bindParam(':user_id', $userID);

        // Execute the query
        $stmt->execute();

        // Fetch all the team records
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $teams;
    } catch (PDOException $e) {
        // Handle any database connection errors
        die("Database Error: " . $e->getMessage());
    }
}

// Get the teams created by the user
$teams = fetchUserTeams($userID);

// Delete functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $teamID = $_POST['team_id'];

    try {
        $pdo = db_connect();

        // Prepare the SQL statement
        $stmt = $pdo->prepare("DELETE FROM team WHERE teamID = :team_id AND userID = :user_id");

        // Bind the team ID and user ID parameters
        $stmt->bindParam(':team_id', $teamID);
        $stmt->bindParam(':user_id', $userID);

        // Execute the query
        $stmt->execute();

        // Redirect to the delete page or any other page you desire
        header("Location: delete_team.php");
        exit();
    } catch (PDOException $e) {
        // Handle any database connection errors
        die("Database Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Team</title>
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
        <h1>Delete Team</h1>
    </div>

    <div class="team-list">
        <?php if (count($teams) > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Team Name</th>
                        <th>Skill Level</th>
                        <th>Game Day</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team) : ?>
                        <tr>
                            <td><?php echo $team['teamName']; ?></td>
                            <td><?php echo $team['skillLevel']; ?></td>
                            <td><?php echo $team['gameDay']; ?></td>
                            <td>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="team_id" value="<?php echo $team['teamID']; ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No teams found.</p>
        <?php endif; ?>
    </div>

</main>

</div> 
    

    <?php include("classes/footer.php"); ?>
</body>

</html>