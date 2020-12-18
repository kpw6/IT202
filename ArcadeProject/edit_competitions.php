<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>
<?php
//saving
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$duration = $_POST["duration"];
	$cost = $_POST["cost"];
	$min = $_POST["min_score"];
	$fee = $_POST["fee"];
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Competitions set name=:name,  duration=:duration, cost=:cost, min_score=:min, fee=:fee WHERE id=:id");
		//$stmt = $db->prepare("INSERT INTO Turtles (name, state, base_rate, mod_min, mod_max, next_stage_time, user_id) VALUES(:name, :state, :br, :min,:max,:nst,:user)");
		$r = $stmt->execute([
			":name"=>$name,
			":duration"=>$duration,
			":cost"=>$cost,
			":min"=>$min,
			":fee"=>$fee,
			":id"=>$id
		]);
		if($r){
			flash("Updated successfully with id: " . $id);
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	else{
		flash("ID isn't set, we need an ID in order to update");
	}
}
?>
<?php
//fetching
$result = [];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Competitions where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
	<label>Name</label>
	<input name="name" placeholder="Name" value="<?php echo $result["name"];?>"/>
	<label>Duration</label>
 	<input type="number" min="1" name="duration" value="<?php echo $result["duration"];?>" />
	<label>Cost</label>
	<input type="number" min="1" name="cost" value="<?php echo $result["cost"];?>" />
	<label>Min Score</label>
	<input type="number" min="1" name="min_score" value="<?php echo $result["min_score"];?>" />
	<label>Mod Max</label>
	<input type="number" min="1" name="fee" value="<?php echo $result["fee"];?>" />
	<input type="submit" name="save" value="Update"/>
</form>


<?php require(__DIR__ . "/partials/flash.php");