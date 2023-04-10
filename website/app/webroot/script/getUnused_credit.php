<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );

//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
//echo 'Period 2019-07-01 to 2020-03-31'.'<br />';
$seconds = 0;
$price = 0;
$result = $mysqli->query("SELECT * FROM user_credit_prices where  date_add <= '2020-02-29 23:00:00' and seconds_left > 0 ORDER BY id ASC");//date_add >= '2019-06-30 22:00:00' and

//var_dump($seconds . ' seconds.  -> '.number_format($price,2,'.','').' €');


header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="all_credit_user_not_used.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
 
// send the column headers
fputcsv($file, array('date', 'reference', 'user_id', 'credit not used', 'estimate price'));

while($row = $result->fetch_array(MYSQLI_ASSOC)){
   $seconds = $row['seconds_left'];
   $price = $row['seconds_left'] * $row['price'];
   $result2 = $mysqli->query("SELECT * FROM user_credits where  id = '".$row['id_user_credit']."' ");
   $row2 = $result2->fetch_array(MYSQLI_ASSOC);
   $result3 = $mysqli->query("SELECT * FROM orders where  id = '".$row2['order_id']."' ");
   $row3 = $result3->fetch_array(MYSQLI_ASSOC);
    $line = array($row['date_add'], $row3['reference'], $row3['user_id'], $seconds, $price);
			fputcsv($file, $line);
}

?>