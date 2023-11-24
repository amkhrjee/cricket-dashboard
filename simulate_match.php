<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Simulated Match</title>
</head>
<?php
$overs = $_POST['overs'];
$match_id = $_POST['match_id'];
$team_one_id = $_POST['team_one_id'];
$team_two_id = $_POST['team_two_id'];

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");
$team_one_query = "SELECT id, name FROM players WHERE team_id=$team_one_id";
$team_one_results = $conn->query($team_one_query);
if (!$team_one_results) {
    echo $conn->error;
}
$team_one_results = $team_one_results->fetch_all();
$team_one_players = [];
foreach ($team_one_results as $res) {
    $team_one_players[] = $res[0];
}

$team_two_query = "SELECT id, name FROM players WHERE team_id=$team_two_id";
$team_two_results = $conn->query($team_two_query);
if (!$team_one_results) {
    echo $conn->error;
}
$team_two_results = $team_two_results->fetch_all();
$team_two_players = [];
foreach ($team_two_results as $res) {
    $team_two_players[] = $res[0];
}
$conn->close();

$total_runs = simulate_innings($team_one_players, $team_two_players, $overs);

echo "Runs Made = $total_runs";

// function simulate_match();
function simulate_innings($batting_team_players, $bowling_team_players, $overs, $target = 9999999)
{
    // Initial Variables
    $total_runs = 0;
    $player_runs = [];
    $player_wickets = [];
    foreach ($batting_team_players as $player) {
        $player_runs[$player] = 0;
    }
    foreach ($bowling_team_players as $player) {
        $player_wickets[$player] = 0;
    }
    $batting_order = 0;
    $total_wickets = 0;
    $total_balls = 6 * $overs;
    $wicket_probability = 0.7;
    $batters = [$batting_team_players[$batting_order], $batting_team_players[$batting_order + 1]];
    $bowlers = array_slice($bowling_team_players, -6);
    $bowler = $bowlers[array_rand($bowlers)];

    echo "Team 1 Batting<br>";
    echo "Ball No.\t\tRuns\t\tWicket\t\tBatter ID\t\tBowler ID<br>";

    // Main events loop
    $onstrike = $batters[0];
    for ($ball_num = 1; ($ball_num <= $total_balls) && (($total_wickets < 10) || ($total_runs > $target)); $ball_num++) {
        if ($ball_num % 7 == 0) {
            $bowler = $bowlers[array_rand($bowlers)];
        }
        $run = 0;
        $wicket = (rand(0, 100) / 100) < $wicket_probability ? 0 : 1;
        if ($wicket == 0) {
            $run = random_int(0, 6);
            $total_runs += $run;
            $onstrike = ($run % 2 != 0) ? $batters[0] : $batters[1];
            $player_runs[$onstrike] += $run;
        } elseif ($wicket == 1) {
            $batting_order += 1;
            $index_of_batter = array_search($onstrike, $batters);
            $batters[$index_of_batter] = $batting_team_players[$batting_order];
            $player_wickets[$bowler] += 1;
            $total_wickets += 1;
        }
        echo "$ball_num\t\t$run\t\t$wicket\t\t$onstrike\t\t$bowler<br>";
    }

    return $total_runs;
}

?>

<body>
    <div class="header" style="text-align: center;">
        <h1>Simulated Match</h1>
    </div>

</body>

</html>