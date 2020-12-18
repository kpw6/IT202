<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])) {
    $email = $_SESSION["user"]["email"];
}
?>
    <p>Welcome, <?php echo $email; ?></p>
    <a href="CarGame.php">Game</a>
<?php

 $counter = 1;
 $one_week_ago = strtotime('-1 week');
 $one_month_ago = strtotime('-1 month');
 $db = getDB();
 $stmt = $db->prepare("SELECT user_id, score, scores.created, Users.username FROM CarScores as scores JOIN Users on scores.user_id = Users.id ORDER BY score DESC");
 $r = $stmt->execute();
 if ($r) {
 $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 else {
    flash("original message" . var_export($stmt->errorInfo(), true));
    }

 if (!$results) {
        echo "No Results";
        }
 else
 {
 safer_echo("Lifetime Scoreboard");
 echo "<br>";
 foreach ($results as $r)
   {
   if ($counter < 11)
   {
    echo "[" . $counter . "]" . "User: " . createProfileLink($r) . " User_id: " . $r["user_id"] . " Score: " . $r["score"];
    echo "<br>";
    $counter++;
    }
   }
 $counter = 1;
 safer_echo("Weekly Scoreboard");
 echo "<br>";
 foreach ($results as $r)
   {
   if (strtotime($r["created"]) > $one_week_ago && $counter < 11)
     {
      echo "[" . $counter . "]" . "User: " . createProfileLink($r) . " User_id: " . $r["user_id"] . " Score: " . $r["score"];
      echo "<br>";
      $counter++;
     }
   }
 $counter = 1;
 safer_echo("Monthly Scoreboard");
 echo "<br>";
 foreach ($results as $r)
   {
   if (strtotime($r["created"]) > $one_month_ago && $counter < 11)
     {
      echo "[" . $counter . "]" . "User: " . createProfileLink($r) . " User_id: " . $r["user_id"] . " Score: " . $r["score"];
      echo "<br>";
      $counter++;
     }
   }
 }
?>
<?php require(__DIR__ . "/partials/flash.php");