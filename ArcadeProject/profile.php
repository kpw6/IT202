<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    die(header("Location: login.php"));
}
?>
<a type="button" href="ChangeProfile.php">Edit Profile</a>
<?php
$db = getDB();
$user = get_user_id();
$stmt = $db->prepare("SELECT user_id, score, Users.username FROM CarScores as user JOIN Users on user.user_id = Users.id ORDER BY score LIMIT 10");
 $r = $stmt->execute();
 if ($r) {
 $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 else {
    flash("There was a problem fetching the results");
    }
   if (!$results) {
        echo "No Results";
        }
 else
 {
 echo "<br>";
 safer_echo("Scoreboard");
 echo "<br>";
 foreach ($results as $r)
   {
    if($user == $r["user_id"])
    {
    safer_echo("Username: " . $r["username"] . " User_id: " . $r["user_id"] . " Score: " . $r["score"]);
    echo "<br>";
    }
   }
 }
safer_echo("points: " . getBalance());
?>

<?php require(__DIR__ . "/partials/flash.php");