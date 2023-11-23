<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Create Match</title>
</head>

<?php

use LDAP\Result;

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");
$get_teams_query = "SELECT id, name from teams";
$results = $conn->query($get_teams_query);
if (!$results) {
    echo $conn->error;
}
$results = $results->fetch_all();
?>

<body>
    <div class="header" style="text-align: center;">
        <h1>Create Match</h1>
    </div>
    <div class="match_form">
        <form name="create_match">
            <div class="form_label_input_container">
                <label>Team #1</label>
                <select name="team_one">
                    <?php

                    ?>
                </select>
            </div>
            <div class="form_label_input_container">
                <label>Team #2</label>
                <input required type="text">
            </div>
            <div style="text-align: center;margin: 1em;" class="btn_container">
                <button style="font-size: large; font-family: 'Inter';" type="submit">Create</button>
            </div>
        </form>
    </div>
</body>

</html>