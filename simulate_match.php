<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Simulated Match</title>
</head>
<?php

class Team
{
    public $name = "";
    public $bowlers = [];
    public $id_array = [];
    public $id_sr_map = [];
    public $total_runs = 0;
    public $id_name_map = [];
    public $id_econ_map = [];
    public $id_wickets_map = [];
    public $id_runs_scored_map = [];
    public $id_balls_played_map = [];
    public $id_runs_conceived_map = [];
    public $id_overs_delivered_map = [];

    function __construct($team_id)
    {
        require 'login.php';
        $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error) die("Failed to connect to database");
        $query = "SELECT id, name FROM players WHERE team_id=$team_id";
        $results = $conn->query($query);
        if ($results) echo $conn->error;
        $results = $results->fetch_all();
        foreach ($results as $res) {
            $this->id_array[] = $res[0];
            $this->id_sr_map[$res[0]] = 0;
            $this->id_econ_map[$res[0]] = 0;
            $this->id_wickets_map[$res[0]] = 0;
            $this->id_name_map[$res[0]] = $res[1];
            $this->id_runs_scored_map[$res[0]] = 0;
            $this->id_balls_played_map[$res[0]] = 0;
            $this->id_runs_conceived_map[$res[0]] = 0;
            $this->id_overs_delivered_map[$res[0]] = 0;
        }

        // Setting the bowlers
        $this->bowlers = array_slice($this->id_array, -6);

        // Setting the team name
        $team_name_query = "SELECT name FROM teams WHERE id=$team_id";
        $team_one_name_result = $conn->query($team_name_query);
        if (!$team_name_query) {
            echo $conn->error;
        }
        $team_name_result = $team_one_name_result->fetch_all();
        $this->name = $team_name_result[0][0];
        // Close the connection
        $conn->close();
    }
}

$overs = $_POST['overs'];
$match_id = $_POST['match_id'];
$team_one_id = $_POST['team_one_id'];
$team_two_id = $_POST['team_two_id'];
// $match_id = ($team_one_id << 8) + $team_two_id;

$team_one = new Team($team_one_id);
$team_two = new Team($team_two_id);

// Coin Toss
$toss_winning_team = (rand(0, 1) == 0) ? $team_one : $team_two;

// Choose who bats first
$batting_team = (rand(0, 1) == 0) ? $team_one : $team_two;
$bowling_team = ($batting_team == $team_one) ? $team_two : $team_one;

// First Innings
simulate_innings($batting_team, $bowling_team, $overs);

// Setting the target
$target = $batting_team->total_runs + 1;

// Second Innings
simulate_innings($bowling_team, $batting_team, $overs, $target);

$winning_team = null;
if ($bowling_team->total_runs >= $target) {
    $winning_team = $bowling_team;
} elseif ($bowling_team->total_runs < $target) {
    $winning_team = $batting_team;
}

function simulate_innings(Team $batting_team, Team $bowling_team, int $overs, int $target = 9999999)
{
    // Database Connection
    require 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);

    // Initial Variables
    $batting_order = 0;
    $total_wickets = 0;
    $total_balls = 6 * $overs;
    $wicket_probability = 0.8;
    $bowler = $bowling_team->bowlers[array_rand($bowling_team->bowlers)];
    $batters = [$batting_team->id_array[$batting_order], $batting_team->id_array[$batting_order + 1]];

    // Main events loop
    $onstrike = $batters[0];
    for ($ball_num = 1; ($ball_num <= $total_balls) && ($total_wickets < 10) && ($batting_team->total_runs < $target); $ball_num++) {
        if ($ball_num % 7 == 0) {
            $bowling_team->id_overs_delivered_map[$bowler] += 1;
            $bowler = $bowling_team->bowlers[array_rand(array_filter(
                $bowling_team->bowlers,
                function ($val) use ($bowler) {
                    return $val != $bowler;
                }
            ))];
        }
        $run = 0;
        $wicket = (rand(0, 100) / 100) < $wicket_probability ? 0 : 1;
        if ($wicket == 0) {
            $run = random_int(0, 6);
            $batting_team->total_runs += $run;
            $onstrike = ($run % 2 != 0) ? $batters[0] : $batters[1];
            $batting_team->id_runs_scored_map[$onstrike] += $run;
            $batting_team->id_balls_played_map[$onstrike] += 1;
            $bowling_team->id_runs_conceived_map[$bowler] += $run;
        } elseif ($wicket == 1) {
            $batting_order += 1;
            $total_wickets += 1;
            $bowling_team->id_wickets_map[$bowler] += 1;
            $index_of_batter = array_search($onstrike, $batters);
            $batters[$index_of_batter] = $batting_team->id_array[$batting_order];
        }

        // Updating the "match_summaries" table
        // $match_summary_query = "INSERT INTO match_summaries 
        // (match_id, ball_num, run, wicket, batter, bowler) 
        // VALUES (global $match_id, $ball_num, $run, $wicket, $onstrike, $bowler)";
        // $res = $conn->query($match_summary_query);
        // if (!$res) $conn->error;
    }
}

// function updatePlayer($player_id, $runs_scored, $sr, $wickets_taken, $economy)
// {
//     // Database Connection
//     require 'login.php';
//     $conn = new mysqli($hn, $un, $pw, $db);

//     $innings_query = "SELECT innings_played, max_run, avg_strike_rate, avg_economy FROM players WHERE id=$player_id";
//     $result = $conn->query($innings_query);
//     $result = $result->fetch_all();
//     $innings_played = $result[0][0];
//     $max_runs = $result[0][1];
//     $avg_strike_rate = $result[0][2];
//     $avg_economy = $result[0][3];

//     // Updating the values
//     $century = ($runs_scored >= 100) ? 1 : 0;
//     $half_century = ($runs_scored >= 50) ? 1 :  0;
//     $five_wickets = ($wickets_taken >= 5) ? 1 : 0;
//     $max_runs = ($max_runs > $runs_scored) ? $max_runs : $runs_scored;
//     $avg_economy = $avg_economy + (int)(($economy - $avg_economy) / ($innings_played + 1));
//     $avg_strike_rate = $avg_strike_rate + (int)(($sr - $avg_strike_rate) / ($innings_played + 1));
//     $innings_played += 1;

//     // Updating the "players" table
//     $player_query = "UPDATE players SET 
//     total_runs=total_runs+$runs_scored, 
//     innings_played=$innings_played, 
//     max_run=$max_runs, 
//     centuries=centuries+$century, 
//     half_centuries=half_centuries+$half_century, 
//     avg_strike_rate=$avg_strike_rate, 
//     avg_economy=$avg_economy, 
//     five_wickets=five_wickets+$five_wickets 
//     WHERE id=$player_id";
//     $res = $conn->query($player_query);
//     if (!$res) $conn->error;
// }
?>

<body>
    <div class="header" style="text-align: center;">
        <h1>Simulated Match</h1>
    </div>
    <div class="pre_match_details">
        <h3><?php echo "$toss_winning_team->name" ?> won the toss & chose to
            <?php if ($batting_team == $toss_winning_team) echo "bat";
            else echo "bowl" ?></h3>

    </div>
    <h5 style="text-align: center;"><?php if (!$winning_team) echo "Match Tied";
                                    else echo $winning_team->name . " won the match"; ?></h5>
    <div class="match_stats_container">
        <div class="team_names_tabs_container">
            <div id="batting_team_tab" class="team_tab"><?php echo "$batting_team->name" ?></div>
            <div id="bowling_team_tab" class="team_tab"><?php echo "$bowling_team->name" ?></div>
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
            // $batting_team_names = ($batting_team_id == $team_one_id) ? $team_one_id_name : $team_two_id_name;
            foreach ($batting_team->id_array as $player) {
                $run = $batting_team->id_runs_scored_map[$player];
                $balls_played = $batting_team->id_balls_played_map[$player];
                $sr =  ($run != 0) ? (int)(($run / $balls_played) * 100) : 0;
                $batting_team->id_sr_map[$player] = $sr;
                $name = $batting_team->id_name_map[$player];
                // updatePlayer($conn, $player, $run, $sr, 0, 0);
                echo <<<EOT
                    <div class="player_stats">
                    <span class="player_name">$name</span>
                    <div class="stats_group">
                        <span>$run</span>
                        <span>$balls_played</span>
                        <span>$sr</span>
                    </div>
                </div>
                <br>
            EOT;
            }
            ?>
            <div class="total_runs" style="display: flex; justify-content: space-between; font-weight: 800; border-bottom: white 0.1em dotted; margin-bottom: 0.2em;">
                <span>Total Runs</span>
                <span><?php echo $batting_team->total_runs ?></span>
            </div>

            <!-- Bowling -->
            <div class="heading_container">
                <span class="main_stat">Bowling</span>
                <div class="stats_heading_group">
                    <span>O</span>
                    <span>R</span>
                    <span>W</span>
                    <span>Econ.</span>
                </div>
            </div>

            <?php
            // $bowling_team_names = ($batting_team_id == $team_one_id) ? $team_two_id_name : $team_one_id_name;
            foreach ($bowling_team->bowlers as $player) {
                $wickets = $bowling_team->id_wickets_map[$player];
                $runs_conceived = $bowling_team->id_runs_conceived_map[$player];
                $overs_bowled = $bowling_team->id_overs_delivered_map[$player];
                $economy = ($overs_bowled != 0) ? ((int) ($runs_conceived / $overs_bowled)) : 0;
                $name = $bowling_team->id_name_map[$player];
                // updatePlayer($conn, $player, 0, 0, $wickets, $economy);
                echo <<<EOT
                <div class="player_stats">
                <span class="player_name">$name</span>
                <div class="stats_group">
                    <span>$overs_bowled</span>
                    <span>$runs_conceived</span>
                    <span>$wickets</span>
                    <span>$economy</span>
                </div>
                </div>
                <br>
                EOT;
            }
            ?>
        </div>
        <div id="bowling_team_stats" class="match_stats_body">
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
            // $bowling_team_names = ($batting_team_id == $team_one_id) ? $team_two_id_name : $team_one_id_name;
            foreach ($bowling_team->id_array as $player) {
                $run = $bowling_team->id_runs_scored_map[$player];
                $balls_played = $bowling_team->id_balls_played_map[$player];
                $sr =  ($run != 0) ? (int)(($run / $balls_played) * 100) : 0;
                $bowling_team->id_sr_map[$player] = $sr;
                $name = $bowling_team->id_name_map[$player];
                // updatePlayer($conn, $player, $run, $sr, 0, 0);
                echo <<<EOT
                    <div class="player_stats">
                    <span class="player_name">$name</span>
                    <div class="stats_group">
                        <span>$run</span>
                        <span>$balls_played</span>
                        <span>$sr</span>
                    </div>
                </div>
                <br>
            EOT;
            }
            ?>
            <div class="total_runs" style="display: flex; justify-content: space-between; font-weight: 800; border-bottom: white 0.1em dotted; margin-bottom: 0.2em;">
                <span>Total Runs</span>
                <span><?php echo $bowling_team->total_runs ?></span>
            </div>

            <!-- Bowling -->
            <div class="heading_container">
                <span class="main_stat">Bowling</span>
                <div class="stats_heading_group">
                    <span>O</span>
                    <span>R</span>
                    <span>W</span>
                    <span>Econ.</span>
                </div>
            </div>

            <?php
            // $batting_team_names = ($batting_team_id == $team_one_id) ? $team_one_id_name : $team_two_id_name;
            foreach ($batting_team->bowlers as $player) {
                $wickets = $batting_team->id_wickets_map[$player];
                $runs_conceived = $batting_team->id_runs_conceived_map[$player];
                $overs_bowled = $batting_team->id_overs_delivered_map[$player];
                $economy = ($overs_bowled != 0) ? ((int) ($runs_conceived / $overs_bowled)) : 0;
                $name = $batting_team->id_name_map[$player];
                // updatePlayer($conn, $player, 0, 0, $wickets, $economy);
                echo <<<EOT
                <div class="player_stats">
                <span class="player_name">$name</span>
                <div class="stats_group">
                    <span>$overs_bowled</span>
                    <span>$runs_conceived</span>
                    <span>$wickets</span>
                    <span>$economy</span>
                </div>
                </div>
                <br>
                EOT;
            }
            ?>
        </div>
    </div>
</body>
<script>
    let batting_team_tab = document.getElementById("batting_team_tab")
    let bowling_team_tab = document.getElementById("bowling_team_tab")

    let batting_team_stats = document.getElementById("batting_team_stats")
    let bowling_team_stats = document.getElementById("bowling_team_stats")

    // default behaviour
    batting_team_stats.style.display = "none"
    bowling_team_stats.style.display = "none"

    batting_team_tab.onclick = () => {
        batting_team_tab.style.backgroundColor = "black"
        batting_team_tab.style.color = "white"
        batting_team_stats.style.display = "block"
        bowling_team_stats.style.display = "none"
        bowling_team_tab.style.backgroundColor = "grey"
    }

    bowling_team_tab.onclick = () => {
        bowling_team_tab.style.backgroundColor = "black"
        bowling_team_tab.style.color = "white"
        bowling_team_stats.style.display = "block"
        batting_team_stats.style.display = "none"
        batting_team_tab.style.backgroundColor = "grey"
    }
</script>

</html>