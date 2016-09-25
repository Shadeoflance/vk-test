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
<form>
	Sorting
	<select name="column">
		<option value="id">ID</option>
		<option value="price">Price</option>
	</select>
	<select name="order">
		<option value="asc">Asc</option>
		<option value="desc">Desc</option>
	</select>
	<input type="submit" value="Sort">
</form>
<table style="width:100%">
  <tr>
	<th>Image</th>
    <th>ID</th>
    <th>Name</th>
	<th>Description</th>
    <th>Price</th>
	<th>Delete</th>
  </tr>
<?php
function print_row($row) {
	echo "
		<tr>
			<td><img src='${row['img_url']}' style='width:64px;height:64px;'></td>
			<td>${row['id']}</td>
			<td><a href='item.php?id=${row['id']}'>${row['name']}</a></td>
			<td>${row['description']}</td>
			<td>${row['price']}</td>
			<td><a href='delete.php?id=${row['id']}'>Delete</a></td>
		</tr>";
}

$order = array_key_exists('order', $_GET) ? $_GET['order'] : 'asc';
$column = array_key_exists('column', $_GET) ? $_GET['column'] : 'id';
$page = array_key_exists('page', $_GET) ? $_GET['page'] : 0;
$items_per_page = array_key_exists('ipp', $_GET) ? $_GET['ipp'] : 4;

$memcache = memcache_connect('localhost', 11211);
if(!($rows = $memcache->get($column))) {
	$mysqli = new mysqli("localhost", "root", "", "db");
	$res = $mysqli->query("select * from items order by $column asc");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	$memcache->set($column, $rows, 0, 30);
}
if($order == 'asc') {
	for($i = $page * $items_per_page; $i < $page * $items_per_page + $items_per_page && $i < count($rows) && $i >= 0; $i++) {
		print_row($rows[$i]);
	}
}
else {
	for($i = $page * $items_per_page + $items_per_page - 1; $i > $page * $items_per_page && $i >= 0 && $i < count($rows); $i--) {
		print_row($rows[$i]);
	}
}
?>
</table>
<?php
$next_page = $page + 1;
$prev_page = $page - 1;
if($prev_page >= 0) {
	echo "<a href='items.php?page=$prev_page&ipp=$items_per_page&order=$order&column=$column'>Previous page<br></a>";
}
if($next_page * $items_per_page < count($rows)) {
	echo "<a href='items.php?page=$next_page&ipp=$items_per_page&order=$order&column=$column'>Next page</a>";
}
?>