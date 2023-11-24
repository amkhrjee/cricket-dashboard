<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Match Created</title>
</head>

<?php
session_start();
$teams = $_SESSION['teams'];
$num_of_overs = $_POST['overs'];
$team_one_id = $_POST['team_one'];
$team_two_id = $_POST['team_two'];
$team_one_name = $teams[$team_one_id][0];
$team_two_name = $teams[$team_two_id][0];
$team_one_label = $teams[$team_one_id][1];
$team_two_label = $teams[$team_two_id][1];

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");
$add_match_query = "INSERT INTO matches (team_a, team_b, overs) VALUES ($team_one_id, $team_two_id, $num_of_overs)";
$results = $conn->query($add_match_query);
if (!$results) {
    echo $conn->error;
}
$match_id = $conn->insert_id;
$conn->close();
?>

<body>
    <div class="header" style="text-align: center;">
        <h1>Match Created</h1>
    </div>
    <div class="match_created">
        <h3 style="text-align: center;"><?php echo "$num_of_overs" ?>-Overs Match</h3>
        <div class="team_names_container">
            <div class="team_container">
                <h4><?php echo "$team_one_name" ?></h4>
                <h5><em><?php echo "$team_one_label" ?></em></h5>
            </div>
            <h5>vs.</h5>
            <div class="team_container">
                <h4><?php echo "$team_two_name" ?></h4>
                <h5><em><?php echo "$team_two_label" ?></em></h5>
            </div>
        </div>
    </div>
    <form method="post" action="simulate_match.php" name="simulate" style="text-align: center;">
        <?php echo "<input name='match_id' type='hidden' value='$match_id'>" ?>
        <?php echo "<input name='overs' type='hidden' value='$num_of_overs'>" ?>
        <?php echo "<input name='team_one_id' type='hidden' value='$team_one_id'>" ?>
        <?php echo "<input name='team_two_id' type='hidden' value='$team_two_id'>" ?>
        <button type="submit">Simulate Match 🤖</button>
    </form>
    <h3 style="text-align: center;">OR</h3>
    <form name="manual" style="text-align: center;">
        <button type="submit">Enter Manually 📝</button>
    </form>
</body>

</html>