<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    die(header("Location: login.php"));
}

$db = getDB();
$stmt = $db->prepare("SELECT id, score FROM Scores LIMIT 10");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$score = "score";

?>

<form method="POST">
    <div>
        <a type="button" href="ChangeProfile.php">Edit Profile</a>
    </div>
    <label for="Scores">Top Ten Scores</label>
</form>
<?php require(__DIR__ . "/partials/flash.php");