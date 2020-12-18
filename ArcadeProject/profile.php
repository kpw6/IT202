<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    die(header("Location: login.php"));
}
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}
?>
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
$results = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT username, private, email FROM Users WHERE id = :id");
    $results = $stmt->execute([":id" => $id]);
    if ($results) {
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($results as $r) {
      
              $db = getDB();
              $stmt = $db->prepare("SELECT count(*) as total FROM CarScores WHERE user_id = :id ORDER BY score");
              $scoreResults = $stmt->execute([":id" => $id]);
              $total = 0;
              if ($scoreResults) {
                 $total = (int)$scoreResults["total"];
              }
              else {
              flash("There was a problem fetching the results");
              }
              $total_pages = ceil($total / $per_page);
              $offset = ($page-1) * $per_page;
              $stmt = $db->prepare("SELECT user_id, score, user.created, Users.username FROM CarScores as user JOIN Users on user.user_id = Users.id WHERE user_id = :id ORDER BY score LIMIT :offset, :count");
              $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
              $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
              $stmt->bindValue(":id", $id);
              $stmt->execute();
              $e = $stmt->errorInfo();
              if($e[0] != "00000"){
                  flash(var_export($e, true), "alert");
              }
              $scoreResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
              }
    }
}
else {
flash("Id is not correct");
}
?>
<div class="profile">
  <?php if($id == get_user_id()) : ?>
    <?php foreach($results as $r) : ?>
       <?php safer_echo($r["username"]); ?>
       <?php safer_echo($r["email"]); ?>
       <?php safer_echo("Is Private(0 = no, 1 = yes): " . $r["private"]); ?> 
       <?php safer_echo("points: " . getBalance()); ?> 
       <a href="ChangeProfile.php">Edit Profile</a>
         <?php if($scoreResults) : ?>
               <?php foreach($scoreResults as $sr) : ?>
                <div class="Scoreboard"> 
                  <div class="row">
                    <div class="col">
                      <?php safer_echo(createProfileLink($sr)); ?>
                    </div>
                    <div class="col">
                      <?php safer_echo($sr["score"]); ?>
                    </div>
                     <div class="col">
                      <?php safer_echo($sr["created"]); ?>
                   </div>
                  </div>
               </div>
             <?php endforeach; ?>
            <?php else: ?>
             <?php safer_echo("No Results Found"); ?>
           <?php endif; ?>
    <?php endforeach; ?>
   <?php else: ?>
     <?php if($r["private"] != 1) : ?>
         <?php foreach($results as $r) : ?>
           <?php safer_echo($r["username"]); ?>
             <?php if($scoreResults) : ?>
               <?php foreach($scoreResults as $sr) : ?>
                <div class="Scoreboard"> 
                  <div class="row">
                    <div class="col">
                      <?php safer_echo(createProfileLink($sr)); ?>
                    </div>
                    <div class="col">
                      <?php safer_echo($sr["score"]); ?>
                    </div>
                     <div class="col">
                      <?php safer_echo($sr["created"]); ?>
                   </div>
                  </div>
               </div>
             <?php endforeach; ?>
            <?php else: ?>
             <?php safer_echo("No Results Found"); ?>
           <?php endif; ?>
        <?php endforeach; ?>
     <?php else: ?>
       <?php safer_echo($r["username"] . "is a private account"); ?>
     <?php endif; ?>
    <?php endif; ?>
            <nav aria-label="My Eggs">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                    <a class="page-link" href="?id=<?php echo ($id);?>page=<?php echo ($i+1);?>" tabindex="-1">Previous</a>
                </li>
                <?php for($i = 0; $i < $total_pages; $i++):?>
                <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?id=<?php echo ($id);?>page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
                </li>
            </ul>
        </nav>
</div>



<?php require(__DIR__ . "/partials/flash.php");