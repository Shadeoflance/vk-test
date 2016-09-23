<a href="create.php">Create new item</a>
</br>
</br>
<?php
$mysqli = new mysqli("localhost", "root", "", "db");
$res = $mysqli->query("select * from items");
if(!$res)
	echo "error " . $mysqli->errno . ' ' . $mysqli->error;
while($row = mysqli_fetch_assoc($res)) {
	echo "${row['id']} ${row['name']} ${row['price']} ${row['description']}</br>";
}
?>