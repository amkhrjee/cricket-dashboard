<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Team</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Fatal Error");
    ?>
    <div class="header" style="text-align: center;">
        <h1>Register Team</h1>
    </div>
    <div class="reg_form">
        <form method="post" action="team_created.php" name="reg_team">
            <div class="form_label_input_container">
                <label>Name</label>
                <input required name="name" type="text" placeholder="Chennai Super Kings">
            </div>
            <div class="form_label_input_container">
                <label>Label</label>
                <input required name="label" type="text" placeholder="CSK">
            </div>
            <fieldset>
                <legend>Players</legend>
                <?php
                $query = "SELECT * FROM players where contracted='0';";
                $result = $conn->query($query);
                if (!$result) echo $conn->error;
                $resultList = $result->fetch_all();
                for ($i = 1; $i <= 11; $i++) {
                    echo "<div class='form_label_input_container'>";
                    echo "<label>$i</label>";
                    echo "<select class='player_select' id='$i' name='$i'>";
                    echo "<option disabled selected value>Select a player</option>";
                    $player_names = [];
                    foreach ($resultList as $row) {
                        $id = $row[0];
                        $name = $row[1];
                        $player_names[] = $name;
                        echo "<option class='player_option' value='$id'>" . htmlspecialchars($name) . "</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                }
                $conn->close();
                ?>
            </fieldset>
            <script>
                let selects = document.querySelectorAll(".player_select")

                selects.forEach(select => {
                    select.onchange = () => {
                        if (select.value.length > 0) {
                            updateOptions(select.id, select.value)
                        }
                    }
                })

                const updateOptions = (id, value) => {
                    console.log("Selected value " + value);
                    selects.forEach(select => {
                        if (select.id != id) {
                            for (option of select.options) {
                                if (option.value == value) {
                                    select.removeChild(option)
                                }
                            }
                        }
                    })
                }
            </script>
            <?php
            echo "<input type='hidden' name='player_names' value=" . serialize($player_names) . ">";
            ?>
            <div style="text-align: center;margin: 1em;" class="btn_container">
                <button style="font-size: large; font-family: 'Inter';" type="submit">Register</button>
            </div>
        </form>
    </div>
</body>

</html>