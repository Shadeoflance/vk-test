<?php
	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		if(!array_key_exists('id', $_GET))
			return;
		$id = $_GET['id'];
		$mysqli = new mysqli("localhost", "root", "", "db");
		//load item from DB instead of cache because it's more consistent and CREATE shouldn't be very requested operation
		//use prepared statements in order to prevent injection
		if (!($pst = $mysqli->prepare("select id, price, name, description, img_url from items where id = ?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		$pst->bind_param('i', $id);
		$pst->execute();
		$pst->bind_result($id, $price, $name, $description, $img_url);
		$pst->fetch();
		$pst->close();
		echo "
			<form method='post'>
				ID:<br>
				<input type='text' name='id' value='$id' readonly><br><br>
				Name:<br>
				<input type='text' name='name' value='$name'><br><br>
				Price:<br>
				<input type='text' name='price' value=$price><br><br>
				Description:<br>
				<textarea type='text' rows='3' cols='30' name='description' value='$description'>$description</textarea><br><br>
				Image URL:<br>
				<input type='text' name='img_url' value='$img_url'><br><br>
				<input type='submit' value='Save'>
				<input type='button' onclick=\"location.href='items.php';\" value='Back to items' />
			</form>
		";
	}
	elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
		$id = htmlspecialchars($_POST['id']);
		$name = htmlspecialchars($_POST['name']);
		$price = (int)htmlspecialchars($_POST['price']);
		$description = htmlspecialchars($_POST['description']);
		$img_url = htmlspecialchars($_POST['img_url']);
		$mysqli = new mysqli("localhost", "root", "", "db");
		if (!($pst = $mysqli->prepare("update items set name = ?, price = ?, description = ?, img_url = ? where id = ?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			return;
		}
		$pst->bind_param('sissi', $name, $price, $description, $img_url, $id);
		$pst->execute();
		$pst->close();
		$memcache = memcache_connect('localhost', 11211);
		$memcache->delete($id);//delete item so it will be loaded from DB next time
		echo "Updated successfully.<br><br>
			<a href='items.php'>Back to items</a>";
	}
?>