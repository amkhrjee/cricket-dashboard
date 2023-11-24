<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulated Match</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
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
$team_one_id_name = [];
foreach ($team_one_results as $res) {
    $team_one_players[] = $res[0];
    $team_one_id_name[$res[0]] = $res[1];
}

$team_two_query = "SELECT id, name FROM players WHERE team_id=$team_two_id";
$team_two_results = $conn->query($team_two_query);
if (!$team_one_results) {
    echo $conn->error;
}
$team_two_results = $team_two_results->fetch_all();
$team_two_players = [];
$team_two_id_name = [];
foreach ($team_two_results as $res) {
    $team_two_players[] = $res[0];
    $team_two_id_name[$res[0]] = $res[1];
}

$team_one_name = getTeamName($conn, $team_one_id);
$team_two_name = getTeamName($conn, $team_two_id);

$teams_id_name[$team_one_id] = $team_one_name;
$teams_id_name[$team_two_id] = $team_two_name;

$conn->close();

// Player Stats
$batting_team_player_sr = [];
$batting_team_player_econ = [];
$batting_team_player_balls_played = [];
$batting_team_players_overs_delivered = [];

$bowling_team_player_sr = [];
$bowling_team_player_econ = [];
$bowling_team_player_balls_played = [];
$bowling_team_players_overs_delivered = [];


// Coin Toss
$toss_winning_team_id = (rand(0, 1) == 0) ? $team_one_id : $team_two_id;

$batting_team = (rand(0, 1) == 0) ? $team_one_players : $team_two_players;
$batting_team_id = ($batting_team == $team_one_players) ? $team_one_id : $team_two_id;

$bowling_team_id = ($batting_team == $team_one_players) ? $team_two_id : $team_one_id;
$bowling_team = ($batting_team == $team_one_players) ? $team_two_players : $team_one_players;
// First Innings
[$total_runs, $player_runs, $player_wickets, $player_balls_played, $player_overs_delivered] = simulate_innings($batting_team, $bowling_team, $overs);
// Batting Team Batting Stats
$batting_team_player_runs = $player_runs;
$batting_team_player_balls_played = $player_balls_played;
// Bowling Team Bowling Stats
$bowling_team_player_wickets = $player_wickets;
$bowling_team_players_overs_delivered = $player_overs_delivered;

$target = $total_runs + 1;

// Second Innings
$player_runs = [];
$player_wickets = [];
$player_balls_played = [];
$player_overs_delivered = [];

[$total_runs, $player_runs, $player_wickets, $player_balls_played, $player_overs_delivered] = simulate_innings($bowling_team, $batting_team, $overs, $target);

// Bowling Team Batting Stats
$bowling_team_player_runs = $player_runs;
$bowling_team_player_balls_played = $player_balls_played;
// Batting Team Bowling Stats
$batting_team_player_wickets = $player_wickets;
$batting_team_players_overs_delivered = $player_overs_delivered;

if ($total_runs > $target) {
    $winning_team = $bowling_team;
} elseif ($total_runs == $target) {
    $winning_team = 0;
} else {
    $winning_team = $batting_team;
}

function simulate_innings($batting_team_players, $bowling_team_players, $overs, $target = 9999999)
{
    // Initial Variables
    $total_runs = 0;
    $player_runs = [];
    $player_wickets = [];
    $player_balls_played = [];
    $player_overs_delivered = [];

    foreach ($batting_team_players as $player) {
        $player_runs[$player] = 0;
        $player_balls_played[$player] = 0;
    }
    // var_dump($player_runs);
    foreach ($bowling_team_players as $player) {
        $player_wickets[$player] = 0;
        $player_overs_delivered[$player] = 0;
    }
    $batting_order = 0;
    $total_wickets = 0;
    $total_balls = 6 * $overs;
    $wicket_probability = 0.6;
    $batters = [$batting_team_players[$batting_order], $batting_team_players[$batting_order + 1]];
    $bowlers = array_slice($bowling_team_players, -6);
    $bowler = $bowlers[array_rand($bowlers)];

    // Main events loop
    $onstrike = $batters[0];
    for ($ball_num = 1; ($ball_num <= $total_balls) && ($total_wickets < 10) && ($total_runs < $target); $ball_num++) {
        if ($ball_num % 7 == 0) {
            $player_overs_delivered[$bowler] += 1;
            $bowler = $bowlers[array_rand($bowlers)];
        }
        $run = 0;
        $wicket = (rand(0, 100) / 100) < $wicket_probability ? 0 : 1;
        if ($wicket == 0) {
            $run = random_int(0, 6);
            $total_runs += $run;
            $onstrike = ($run % 2 != 0) ? $batters[0] : $batters[1];
            $player_runs[$onstrike] += $run;
            $player_balls_played[$onstrike] += 1;
        } elseif ($wicket == 1) {
            $batting_order += 1;
            $index_of_batter = array_search($onstrike, $batters);
            $batters[$index_of_batter] = $batting_team_players[$batting_order];
            $player_wickets[$bowler] += 1;
            $total_wickets += 1;
        }
        // echo "$ball_num\t\t$run\t\t$wicket\t\t$onstrike\t\t$bowler<br>";
    }
    //TODO: Update player stats

    return [$total_runs, $player_runs, $player_wickets, $player_balls_played, $player_overs_delivered];
}

function getTeamName($conn, $team_id)
{
    $team_name_query = "SELECT name FROM teams WHERE id=$team_id";
    $team_one_name_result = $conn->query($team_name_query);
    if (!$team_name_query) {
        echo $conn->error;
    }
    $team_name_result = $team_one_name_result->fetch_all();
    return $team_name_result[0][0];
}
?>

<body>
    <div class="header" style="text-align: center;">
        <h1>Simulated Match</h1>
    </div>
    <div class="pre_match_details">
        <h3><?php echo "$teams_id_name[$toss_winning_team_id]" ?> won the toss & chose to
            <?php if ($batting_team_id == $toss_winning_team_id) echo "bat";
            else echo "bowl" ?></h3>

    </div>
    <div class="match_stats_container">
        <div class="team_names_tabs_container">
            <div class="team_tab"><?php echo "$teams_id_name[$batting_team_id]" ?></div>
            <div class="team_tab"><?php echo "$teams_id_name[$bowling_team_id]" ?></div>
        </div>
        <div id="batting_team_stats" class="match_stats_body">
            <!-- Batting -->
            <div class="heading_container">
                <span class="main_stat">Batting</span>
                <div class="stats_heading_group">
                    <span>R</span>
                    <span>B</span>
                    <span>SR</span>
                </div>
            </div>
            <br>
            <?php
            $batting_team_names = ($batting_team_id == $team_one_id) ? $team_one_id_name : $team_two_id_name;
            foreach ($batting_team as $player) {
                $run = $batting_team_player_runs[$player];
                $balls_played = $batting_team_player_balls_played[$player];
                $sr =  ($run != 0) ? (int)(($run / $balls_played) * 100) : 0;
                $batting_team_player_sr[$player] = $sr;

                echo <<<EOT
                    <div class="player_stats">
                    <span class="player_name">$batting_team_names[$player]</span>
                    <div class="stats_group">
                        <span>$batting_team_player_runs[$player]</span>
                        <span>$batting_team_player_balls_played[$player]</span>
                        <span>$sr</span>
                    </div>
                </div>
                <br>
            EOT;
            }
            ?>
            <div class="total_runs" style="display: flex; justify-content: space-between; font-weight: 800; border-bottom: white 0.1em dotted; margin-bottom: 0.2em;">
                <span>Total Runs</span>
                <span><?php echo ($target - 1) ?></span>
            </div>

            <!-- Bowling -->
            <div class="heading_container">
                <span class="main_stat">Bowling</span>
                <div class="stats_heading_group">
                    <span>O</span>
                    <span>R</span>
                    <span>Econ.</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>