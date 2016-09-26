<form method="post">
  Name:<br>
  <input type="text" name="name"><br>
  Price:<br>
  <input type="text" name="price"><br><br>
  Description:<br>
  <input type="text" name="description"><br><br>
  Image URL:<br>
  <input type="text" name="img_url"><br><br>
  <input type="submit" value="Submit">
  <input type="button" onclick="location.href='items.php';" value="Back to items">
</form>
<?php
	include 'fcache.php';
	
	if(!array_key_exists("name", $_POST) || !array_key_exists("price", $_POST)) {//check if form is filled
		return;
	}
	$mysqli = new mysqli("localhost", "root", "", "db");
	if (!($pst = $mysqli->prepare("INSERT INTO items(name, price, description, img_url) VALUES (?, ?, ?, ?)"))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return;
	}
	$pst->bind_param('siss', $name, $price, $description, $img_url);
	$name = htmlspecialchars($_POST['name']);
	$price = (int)htmlspecialchars($_POST['price']);
	$description = htmlspecialchars($_POST['description']);
	$img_url = htmlspecialchars($_POST['img_url']);
	$pst->execute();
	$pst->close();
	
	reset_cache_ind();
?>
<p>New item successfully added!</p>