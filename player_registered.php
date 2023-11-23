<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Player Registered</title>
</head>

<body>
    <div class="header" style="text-align: center;">
        <h1>Player Registration Successful 🎉</h1>
    </div>
    <?php
    require_once("login.php");
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Fatal Error");
    $namesList = ["name", "age", "total_runs", "highest_runs", "innings_played", "centuries", "half_centuries", "avg_strike_rate", "batting_style", "bowling_style", "avg_economy", "five_wickets", "role", "gender"];
    $valuesList = [];
    foreach ($namesList as $name) {
        if (isset($_POST[$name])) $valuesList[] = $_POST[$name];
    }
    ?>
    <fieldset>
        <legend>Player Details</legend>
        <h4>Name: <?php $i = 0;
                    echo $valuesList[$i++] ?></h4>
        <h4>Age: <?php echo $valuesList[1] ?></h4>
        <h4>Total Runs: <?php echo $valuesList[$i++] ?></h4>
        <h4>Highest Runs: <?php echo $valuesList[$i++] ?></h4>
        <h4>Innings Played: <?php echo $valuesList[$i++] ?></h4>
        <h4>Centuries: <?php echo $valuesList[$i++] ?></h4>
        <h4>Half Centuries: <?php echo $valuesList[$i++] ?></h4>
        <h4>Average Striek Rate: <?php echo $valuesList[$i++] ?></h4>
        <h4>Batting Style: <?php echo $valuesList[$i++] ?></h4>
        <h4>Bowling Style: <?php echo $valuesList[$i++] ?></h4>
        <h4>Average Economy: <?php echo $valuesList[$i++] ?></h4>
        <h4>Five Wickets: <?php echo $valuesList[$i++] ?></h4>
        <h4>Role: <?php echo $valuesList[$i++] ?></h4>
        <h4>Gender: <?php echo $valuesList[$i++] ?></h4>
    </fieldset>
    <?php

    register_player($conn, $valuesList);
    $conn->close();
    function register_player($conn, $valuesList)
    {
        $placeholderString = "";
        for ($i = 0; $i < sizeof($valuesList); $i++) {
            $placeholderString .= "?,";
        }
        $placeholderString = substr($placeholderString, 0, -1);
        $statement = $conn->prepare("INSERT INTO players (name, age, total_runs, innings_played, max_run, centuries, half_centuries, gender, avg_strike_rate, batting_style, bowling_style, avg_economy, five_wickets, role) VALUES($placeholderString)");
        $statement->bind_param("siiiiiiiissiis", ...$valuesList); //Don't touch!
        $statement->execute();
        $statement->close();
    }
    ?>
</body>

</html>