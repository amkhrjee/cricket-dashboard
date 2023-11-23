<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Team Created</title>
</head>

<body>
    <div class="header" style="text-align: center;">
        <h1>Team Creation Successful 🎉</h1>
    </div>

    <?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Fatal Error");

    function add_team($conn, $team_name, $team_label)
    {
        $statement = $conn->prepare("INSERT INTO teams (name, label) VALUES (?,?)");
        $statement->bind_param("ss", $team_name, $team_label);
        $statement->execute();
        $statement->close();

        $get_id_query = "SELECT id from teams where label='$team_label'";
        $res = $conn->query($get_id_query);
        if (!$res) {
            echo $conn->error;
        }
        $res = $res->fetch_all();
        return $res[0][0];
    }

    function update_player($conn, $player_id, $team_id)
    {
        $updateQuery = ("UPDATE players SET contracted='1', team_id='$team_id' WHERE id='$player_id'");
        $res = $conn->query($updateQuery);
        if (!$res) {
            echo $conn->error . "<br>";
        }
    }

    $player_names = [];
    $players = [];
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    }
    if (isset($_POST['label'])) {
        $label = $_POST['label'];
    }

    $team_id = add_team($conn, $name, $label);

    for ($i = 1; $i <= 11; $i++) {
        if (isset($_POST[strval($i)])) {
            $players[] = $_POST[strval($i)];
        }
        $player_id = $players[$i - 1];
        update_player($conn, $player_id, $team_id);
        $query = "SELECT * FROM players where id=" . $player_id . ";";
        $result = $conn->query($query);
        if (!$result) {
            echo $conn->error;
        }
        $resultList = $result->fetch_all();
        $player_names[] = $resultList[0][1];
    }

    $conn->close();
    ?>

    <div class="display_team">
        <h2 style="text-align: center;"><?php echo "$name" ?></h2>
        <h3 style="text-align: center;"><?php echo "$label" ?></h3>
        <fieldset>
            <legend>Players</legend>
            <?php
            foreach ($player_names as $player) {
                echo "<h4>" . $player . "</h4>";
            }
            ?>
        </fieldset>

    </div>
</body>

</html>