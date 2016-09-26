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
<?php
	include 'fcache.php';

	function print_row($id) {
		global $memcache;
		global $mysqli;
		if(!($row = $memcache->get($id))) {
			if(!isset($mysqli)) {
				$mysqli = new mysqli("localhost", "root", "", "db");
			}
			$res = $mysqli->query("select * from items where id = $id");//we don't lose much time by getting rows separately, 
			//because the most time-consuming operation here is the connection
			$row = $res->fetch_assoc();
			$memcache->set($id, $row);
		}
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
	if(!($sids = $memcache->get($column))) {
		reset_cache_ind();
		$sids = $memcache->get($column);
	}
	$max_page = strlen($sids) / 4 / $items_per_page;
	echo "
	<form>
		Sorting
		<select name=\"column\">
			<option value=\"id\">ID</option>
			<option value=\"price\">Price</option>
		</select>
		<select name=\"order\">
			<option value=\"asc\">Asc</option>
			<option value=\"desc\">Desc</option>
		</select>
		<br>Page
		<input type=\"number\" value=$page min=0 max=$max_page name=\"page\">
		<br>Items per page
		<input type=\"number\" value=$items_per_page min=1 max=200 name=\"ipp\">
		<br>
		<input type=\"submit\" value=\"Apply\">
	</form>";
	$next_page = $page + 1;
	$prev_page = $page - 1;
	if($prev_page >= 0) {
		echo "<a href='items.php?page=$prev_page&ipp=$items_per_page&order=$order&column=$column'>Previous page<br></a>";
	}
	if($next_page * $items_per_page < strlen($sids) / 4) {
		echo "<a href='items.php?page=$next_page&ipp=$items_per_page&order=$order&column=$column'>Next page</a>";
	}
?>

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
for($i = $page * $items_per_page; $i < $page * $items_per_page + $items_per_page && $i < strlen($sids) / 4 && $i >= 0; $i++) {
	$pos = $order == 'asc' ? $i * 4 : strlen($sids) - $i * 4 - 4;
	$id = unpack("i", substr($sids, $pos, 4))[1];
	print_row($id);
}
?>
</table>
<?php
	if($prev_page >= 0) {
		echo "<a href='items.php?page=$prev_page&ipp=$items_per_page&order=$order&column=$column'>Previous page<br></a>";
	}
	if($next_page * $items_per_page < strlen($sids) / 4) {
		echo "<a href='items.php?page=$next_page&ipp=$items_per_page&order=$order&column=$column'>Next page</a>";
	}
?>