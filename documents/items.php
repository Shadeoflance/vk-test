<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
</head>
<a href="create.php">Create new item</a>
</br>
</br>
<table style="width:100%">
  <tr>
    <th>ID</th>
    <th>Name</th>
	<th>Description</th>
    <th>Price</th>
  </tr>
<?php
$mysqli = new mysqli("localhost", "root", "", "db");
$res = $mysqli->query("select * from items");
if(!$res)
	echo "error " . $mysqli->errno . ' ' . $mysqli->error;
while($row = mysqli_fetch_assoc($res)) {
	echo "<tr><td>${row['id']}</td><td>${row['name']}</td><td>${row['description']}</td><td>${row['price']}</td></tr>";
}
?>
</table>