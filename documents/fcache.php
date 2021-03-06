<?php
	function reset_cache_ind($p = true, $i = true) {
		$memcache = memcache_connect('localhost', 11211);
		$mysqli = new mysqli("localhost", "root", "", "db");
		if($i){
			$res = $mysqli->query("select id from items order by id asc");
			while($id = $res->fetch_row()) {
				$ids[] = $id[0];
			}
			$sids = pack("i*", ...$ids);
			$memcache->set("id", $sids);
			
			unset($ids, $sids);
		}
		
		if($p){
			$res = $mysqli->query("select id from items order by price asc");
			while($id = $res->fetch_row()) {
				$ids[] = $id[0];
			}
			$sids = pack("i*", ...$ids);
			$memcache->set("price", $sids);
		}
	}
?>