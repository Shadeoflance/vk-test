<?php
	include 'fcache.php';
	
	if (!array_key_exists('id', $_GET)){
		echo "No id provided!";
		return;
	}
	$id = $_GET['id'];
	$mysqli = new mysqli("localhost", "root", "", "db");
	if (!($pst = $mysqli->prepare("delete from items where id = ?"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return;
	}
	$pst->bind_param('i', $id);
	$pst->execute();
	$pst->close();
	
	reset_cache_ind();
	
	echo "Deleted successfully.<br><br>
		<a href='items.php'>Back to items</a>";
?>