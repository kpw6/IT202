<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}
function getIsPrivate() {
    if (is_logged_in() && isset($_SESSION["user"]["private"])) {
        return $_SESSION["user"]["private"];
    }
    return -1;
}

function changeIsPrivate() {
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users SET private = :p WHERE id = :id");
    $params = [":id" => get_user_id()];
    $r = $stmt->execute($params);
    
    if (is_logged_in() && isset($_SESSION["user"]["private"])) {
        if($_SESSION["user"]["private"] === 0) {
          $params = [":id" => get_user_id(), ":p" => 1];
          $r = $stmt->execute($params);
          $_SESSION["user"]["private"] = 1;
        }
        else {
          $params = [":id" => get_user_id(), ":p" => 0];
          $r = $stmt->execute($params);
          $_SESSION["user"]["private"] = 0;
        }
        
    }
}

function createProfileLink($arr) {
  if(isset($arr["user_id"]) && isset($arr["username"])) {
    echo join('', ["<a href='profile.php?id=",$arr["user_id"],">",$arr["username"],"</a>"]);
  }
  else {
    flash("missing id or username");
    }
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//for flash feature
function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

//end flash

function getBalance() {
  $db = getDB();
  $stmt = $db->prepare("SELECT points FROM Users WHERE id = :id");
  $params = [":id" => get_user_id()];
  $r = $stmt->execute($params);
  if ($r) {
     $re = $stmt->fetchAll(PDO::FETCH_ASSOC);
     foreach($re as $r) {
     return $r["points"];
     }
    }
}
function getCompetitionResults(){
  $db = getDB();
  $first = 0;
  $second = 0;
  $third = 0;
  $firstplayer = 0;
  $secondplayer = 0;
  $thirdplayer = 0;
  $reason1 = "1st Place Competition";
  $reason2 = "2nd Place Competition";
  $reason3 = "3rd Place Competition";
  $stmt = $db->prepare("SELECT id, first_place_per, second_place_per, third_place_per, participants, reward, expires, created FROM Competitions WHERE expires > current_timestamp && paid_out = 0");
  $r = $stmt->execute();
  if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $r) {
      if ($r["participants"] > 3) {
        $stmt = $db->prepare("SELECT user_id, comp_id FROM CompetitionParticipants WHERE comp_id = :cid");
        $re = $stmt->execute([":cid" => $r["id"]]);
        if($re) {
          $results2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach($results2 as $re) {
             $stmt = $db->prepare("SELECT user_id, score, created FROM CarScores");
             $res = $stmt->execute();
             if($res){
               $results3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
               foreach($results3 as $res) {
                 if($r["created"] > $res["created"] && $res["created"] > $r["expires"]) {
                   if ($res["score"] < $first) {
                     if($res["score"] < $second) {
                       if ($res["score"] < $third) {
                       
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
    $stmt = $db->prepare("UPDATE Competitions SET paid_out = 1");
    $res = $stmt->execute();
    if ($r) {
      flash("Successful payout of Competition");
    }
    while ($third != 0) {
      $stmt = $db->prepare("INSERT INTO PointsHistory (user_id, points_change, reason) VALUES(:uid, :points_change, :reason)");
      if ($first != 0) {
        $params = [
          ":uid" => $firstplayer,
          ":points_change" => $r["first_place_per"]*$r["reward"],
          ":reason" => $reason1
          ];
          $res = $stmt->execute($params);
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
          $res = $stmt->execute($params);
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
          $res = $stmt->execute($params);
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
}
?>