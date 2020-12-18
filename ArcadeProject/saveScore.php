<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php 
$db = getDB();
$user = get_user_id();
$name = get_username();
$score = $_POST["scores"];
$stmt = $db->prepare("INSERT INTO CarScores (score, user_id) VALUES(:score ,:user)");
$r = $stmt->execute([
		":score"=>$score,
		":user"=>$user,
	]);
	if($r){
		flash("Created successfully with id");
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}


?>