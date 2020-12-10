<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
    <ul class="nav">
        <li><a href="home.php">Home</a></li>
        <?php if (!is_logged_in()): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <?php if (has_role("Admin")): ?>
            <li><a href="create_carscore.php">Create Score</a></li>
            <li><a href="test_list_score.php">View Score</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
            <li><a href="CarGame.php">Car</a></li>
            <li><a href="Scoreboards.php">Scoreboard</a></li>
            <li><a href="create_competitons.php">Create Competition</a></li>
            <li><a href="competitions.php">Competitions</a></li>
            <li><a href="mycompetitions.php">My Competitions</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>