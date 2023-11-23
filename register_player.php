<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Player</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <div class="header" style="text-align: center;">
        <h1>Register Player</h1>
    </div>
    <div class="reg_form">
        <form method="post" action="player_registered.php" name="register_player" class="register_player">
            <div class="form_label_input_container">
                <label>Name</label>
                <input name="name" required type="text">
            </div>
            <div class="form_label_input_container">
                <label>Age</label>
                <input name="age" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Total Runs</label>
                <input name="total_runs" required type="text">
            </div>
            <div class="form_label_input_container">
                <label>Highest Runs</label>
                <input name="highest_runs" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Innings Played</label>
                <input name="innings_played" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Centuries</label>
                <input name="centuries" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Half Centuries</label>
                <input name="half_centuries" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Average Strike Rate</label>
                <input name="avg_strike_rate" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Batting Style</label>
                <input name="batting_style" required type="text">
            </div>
            <div class="form_label_input_container">
                <label>Bowling Style</label>
                <input name="bowling_style" required type="text">
            </div>
            <div class="form_label_input_container">
                <label>Average Economy</label>
                <input name="avg_economy" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Five Wickets</label>
                <input name="five_wickets" required inputmode="numeric" type="number">
            </div>
            <div class="form_label_input_container">
                <label>Role</label>
                <input name="role" required type="text">
            </div>
            <div class="form_label_input_container">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="1">Male</option>
                    <option value="0">Female</option>
                </select>
            </div>
            <div style="text-align: center;" class="btn_container">
                <button style="font-size: large; font-family: 'Inter';" type="submit">Register</button>
            </div>
        </form>
    </div>
</body>

</html>