<?php


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$result = $mysqli->query("SELECT * from chat_messages where chat_id = 78026 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$content = addslashes($row['content']);
	$mysqli->query("INSERT INTO chat_histories (chat_id,user_id,content,date) values('".$row['chat_id']."','".$row['user_id']."','".$content."','".$row['date_add']."')");
	
}

?>