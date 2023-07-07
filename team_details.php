<?php
include 'classes/connect.php';
session_start();
$pdo = db_connect();

// Get the team ID from the URL
// if (isset($_GET['id'])) {
//     $teamId = $_GET['id'];
// } else {
//     die("Team ID is not provided.");
// }
$teamId = $_GET['id'];
// Check if the user is the team creator
$teamCreator = false;
if (isset($_SESSION['userID'])) {
    $userId = $_SESSION['userID'];
    

    // Check if the user is the creator of the team
    $stmt = $pdo->prepare("SELECT userID FROM team WHERE teamID = :team_id");
    $stmt->bindParam(':team_id', $teamId);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row && $row['userID'] == $userId) {
        $teamCreator = true;
    }
}

// Check if the user is adding a new player
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_player'])) {
    $playerName = $_POST['player_name'];

    // Validate the player name (you can add more validation as per your requirements)
    if (empty($playerName)) {
        $error = "Please enter a player name.";
    } else {
        // Check if the team is already full (9 players)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM teamplayers WHERE teamID = :team_id");
        $stmt->bindParam(':team_id', $teamId);
        $stmt->execute();
        $playerCount = $stmt->fetchColumn();

        if ($playerCount >= 9) {
            $error = "The team is already full. No more players can be added.";
        } else {
            // Insert the new player into the database
            $stmt = $pdo->prepare("INSERT INTO teamplayers (teamID, playerName) VALUES (:team_id, :player_name)");
            $stmt->bindParam(':team_id', $teamId);
            $stmt->bindParam(':player_name', $playerName);
            $stmt->execute();
        }
    }
}

// Check if the user is deleting a player
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_player'])) {
    $playerId = $_POST['player_id'];

    // Delete the player from the database
    $stmt = $pdo->prepare("DELETE FROM teamplayers WHERE playerID = :player_id");
    $stmt->bindParam(':player_id', $playerId);
    $stmt->execute();
}


// Fetch team information from the database
$stmt = $pdo->prepare("SELECT * FROM team WHERE teamID = :team_id");
$stmt->bindParam(':team_id', $teamId);
$stmt->execute();
$team = $stmt->fetch();

// Fetch players in the team from the database
$stmt = $pdo->prepare("SELECT * FROM teamplayers WHERE teamID = :team_id");
$stmt->bindParam(':team_id', $teamId);
$stmt->execute();
$players = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Team</title>
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
            <h1><?php echo $team['teamName']; ?></h1>
            <p>Skill Level: <?php echo $team['skillLevel']; ?></p>
            <p>Game Day: <?php echo $team['gameDay']; ?></p>
            <p>Number of Players: <?php echo count($players); ?></p>
        </div>

        <div class="team-players">
            <h2>Players in the Team</h2>
            <?php if ($teamCreator) : ?>
                <a href="update_team.php?id=<?php echo $teamId; ?>" class="btn-update">Update Team</a>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $teamId; ?>" method="post">
                    <input type="text" name="player_name" placeholder="Player Name" required>
                    <input type="submit" name="add_player" value="Add Player">
                </form>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
                <p><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (count($players) > 0) : ?>
                <table>
                    <thead>
                    <tr>
                        <th>Player Name</th>
                        <?php if ($teamCreator) : ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($players as $player) : ?>
                        <tr>
                            <td><?php echo $player['playerName']; ?></td>
                            <?php if ($teamCreator) : ?>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $teamId; ?>" method="post">
                                        <input type="hidden" name="player_id" value="<?php echo $player['playerID']; ?>">
                                        <input type="submit" name="delete_player" value="Delete">
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No players in the team.</p>
            <?php endif; ?>
        </div>
    </main>

</div>




    <?php include("classes/footer.php"); ?>


</body>

</html>