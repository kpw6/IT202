<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
 $db = getDB();
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
 safer_echo("Scoreboard");
 echo "<br>";
 foreach ($results as $r)
   {
    safer_echo("Username: " . $r["username"] . " User_id: " . $r["user_id"] . " Score: " . $r["score"]);
    echo "<br>";
   }
 }

 ?>

