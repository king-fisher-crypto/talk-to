<?php

//Spiriteo CMS

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$result = $mysqli->query("SELECT * from vouchers WHERE title like '%GROUPON%'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from voucher_histories WHERE code like '{$row['code']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
		if(!$row2['code'])
			$mysqli->query("delete from vouchers WHERE code = '{$row['code']}'");
}
exit;
?>