<?php
include 'classes/connect.php';
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

$firstLogin = false;
if (isset($_SESSION['first_login'])) {
    $_SESSION['first_login'] = true;
    $firstLogin = true;
}

// Get the username from the session


// Fetch teams from the database
function fetchTeams()
{
    try {
        $pdo = db_connect();

        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT team.*, COUNT(teamplayers.playerID) AS playerCount 
                               FROM team 
                               LEFT JOIN teamplayers ON team.teamID = teamplayers.teamID 
                               GROUP BY team.teamID");

        // Execute the query
        $stmt->execute();

        // Fetch all the team records
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle any database connection errors
        die("Database Error: " . $e->getMessage());
    }
}

// Get the teams data
$teams = fetchTeams();
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
            <h1>Team Dashboard</h1>
            <?php if ($firstLogin) : ?>
                <h1>Welcome, <?php echo $username; ?>!</h1>
            <?php else : ?>
                <h1>Welcome back, <?php echo $username; ?>!</h1>
            <?php endif; ?>
        </div>

        <div class="dashboard">
            <table>
                <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Skill Level</th>
                    <th>Number of Players</th>
                    <th>Game Day</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($teams as $team) : ?>
                    <tr>
                        <td><a href="team_details.php?id=<?php echo $team['teamID']; ?>"><?php echo $team['teamName']; ?></a></td>
                        <td><?php echo $team['skillLevel']; ?></td>
                        <td><?php echo $team['playerCount']; ?></td>
                        <td><?php echo $team['gameDay']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (isset($_SESSION['username'])) : ?>
                <a href="create_team.php" class="btn-create">Create New Team</a>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include("classes/footer.php"); ?>
</body>

</html>