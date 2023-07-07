<?php
include 'classes/connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    $_SESSION['loginMessage'] = "Please log in to access this page.";
    header("Location: index.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page or any other page you desire
    header("Location: login.php");
    exit();
}

// Retrieve the teams created by the user from the database
$userId = $_SESSION['userID'];
$teams = fetchUserTeams($userId);

// Function to fetch the teams created by the user from the database
function fetchUserTeams($userId) {
    // Replace the database credentials with your own


    try {
        // Create a new PDO instance
        $pdo = db_connect();
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM team WHERE userID = :user_id");

        // Bind the user ID parameter
        $stmt->bindParam(':user_id', $userId);

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("classes/header.php"); ?>

<div class="body_container">
    <aside>
        <?php include("nav_bar.php"); ?>
    </aside>
    <main>
        <div class="team-list">
        <h2>Edit Team</h2>
        <table>
            <thead>
            <tr>
                <th>Team Name</th>
                <th>Skill Level</th>
                <th>Game Day</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($teams as $team) : ?>
                <tr>
                    <td><?php echo $team['teamName']; ?></td>
                    <td><?php echo $team['skillLevel']; ?></td>
                    <td><?php echo $team['gameDay']; ?></td>
                    <td><a href="update_team.php?id=<?php echo $team['teamID']; ?>">Update</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
</div>
    </main>

</div>
<?php include("classes/footer.php"); ?>
</body>
</html>