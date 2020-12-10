<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
  $first = 0;
  $second = 0;
  $third = 0;
  $firstplayer = 0;
  $secondplayer = 0;
  $thirdplayer = 0;
  $reason1 = "1st Place Competition";
  $reason2 = "2nd Place Competition";
  $reason3 = "3rd Place Competition";
  $stmt = $db->prepare("SELECT id, first_place_per, second_place_per, third_place_per, participants, reward, expired, created FROM Competitions WHERE expired > current_timestamp && paid_out = 0 SORT BY id");
  $r = $db=>execute();
  if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $r) {
      if {$r["participants"] > 3) {
        $stmt = $db->prepare("SELECT user_id, comp_id FROM CompetitionParticipants WHERE comp_id = :cid");
        $re = $db=>execute([":cid" = $r["id"]]);
        if($re) {
          $results2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach($results2 as $re) {
             $stmt = $db->prepare("SELECT user_id, score, created FROM CarScores");
             $res = $db=>execute();
             if($res){
               $results3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
               foreach($results3 as $res) {
                 if($r["created"] > $res["created"] && $res["created"] > $r["expired"] {
                   if ($res["score"] < $first) {
                     if($res["score"] < $second) {
                       if ($res["score" < $third) {
                       
                       }
                       else {
                         $third = $res["score"];
                         $thirdplayer = $res["user_id"];
                       }
                     }
                     else {
                       $second = $res["score"];
                       $secondplayer = $res["user_id"];
                     }
                   }
                   else {
                     $first = $res["score"];
                     $firstplayer = $res["user_id"];
                   }
                 }
               }
             }            
          }
        }
        else {
          flash("error");
        }
      }
      else {
        flash("Not enough participants reward wasn't giving");
      }
    }
    $stmt = $stmt = $db->prepare("UPDATE Competitions SET paid_out = 1");
    $res = $db=>execute();
    if ($r) {
      flash("Successful payout of Competition");
    }
    while ($third != 0) {
      $stmt = $stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason) VALUES(:uid, :points_change, :reason)");
      if ($first != 0) {
        $params = [
          ":uid" => $firstplayer,
          ":points_change" => $r["first_place_per"]*$r["reward"],
          ":reason" => $reason1
          ];
          $res = $db=>execute($params);
          if($res) {
            flash("PointsHistory Successfully made");
          }
          else {
            flash("Error making pointshistory");
          }
          $stmt = $db->prepare("UPDATE Users SET points = (SELECT SUM(points_change) FROM PointsHistory WHERE user_id = :uid) WHERE id = :uid");
          $res = $stmt->execute([":uid" => $firstplayer]);
          if($res) {
            flash("Points Successfully changed");
          }
          else {
            flash("Error updating points");
          }
          $first = 0;
      }
       elseif ($second != 0) {
        $params = [
          ":uid" => $secondplayer,
          ":points_change" => $r["second_place_per"]*$r["reward"],
          ":reason" => $reason2
          ];
          $res = $db=>execute($params);
          if($res) {
            flash("PointsHistory Successfully made");
          }
          else {
            flash("Error making pointshistory");
          }
          $stmt = $db->prepare("UPDATE Users SET points = (SELECT SUM(points_change) FROM PointsHistory WHERE user_id = :uid) WHERE id = :uid");
          $res = $stmt->execute([":uid" => $secondplayer]);
          if($res) {
            flash("Points Successfully changed");
          }
          else {
            flash("Error updating points");
          }
          $second = 0;
      }
       else {
        $params = [
          ":uid" => $thirdplayer,
          ":points_change" => $r["third_place_per"]*$r["reward"],
          ":reason" => $reason3
          ];
          $res = $db=>execute($params);
          if($res) {
            flash("PointsHistory Successfully made");
          }
          else {
            flash("Error making pointshistory");
          }
          $stmt = $db->prepare("UPDATE Users SET points = (SELECT SUM(points_change) FROM PointsHistory WHERE user_id = :uid) WHERE id = :uid");
          $res = $stmt->execute([":uid" => $thirdplayer]);
          if($res) {
            flash("Points Successfully changed");
          }
          else {
            flash("Error updating points");
          }
          $third = 0;
      }
    }
    
    
  }
  else {
   flash("Error getting results");
   }
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>