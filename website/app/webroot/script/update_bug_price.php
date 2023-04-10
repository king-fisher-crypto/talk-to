<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$list_cost = array();
			
			$result = $mysqli->query("SELECT * from costs order by id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$list_cost[$row['id']] = $row['cost'] / 60;
			}


$result = $mysqli->query("SELECT * FROM user_pay where date_pay >= '2020-06-29 02:00:18' and price < 0.1 and ca >= 1 ORDER BY id_user_pay ASC");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
  
  $result2 = $mysqli->query("SELECT C.agent_id,C.user_id, C.user_credit_history,C.media,C.is_factured, C.seconds,C.credits,C.ca,C.ca_currency, C.ca_ids, U.order_cat, U.mail_price, U.stripe_account from user_credit_history C, users U WHERE C.agent_id = U.id and C.user_credit_history = ".$row['id_user_credit_history']."");
  $row2 = $result2->fetch_array(MYSQLI_ASSOC);
  $agent_id = $row2['agent_id'];
  
  $seconds = $row2['seconds'];
				$order_cat = $row2['order_cat'];
				$mail_price = $row2['mail_price'];
				$media = $row2['media'];
				$is_factured = $row2['is_factured'];
				
				if($order_cat){
					$remuneration_time = $list_cost[$order_cat];


					$price = 0;
					if($is_factured){
						switch ($media) {
							case 'phone':
								$price = $seconds * $remuneration_time;
								break;
							case 'chat':
								$price = $seconds * $remuneration_time;
								break;
							case 'email':
								$price = $mail_price;
								break;
						}
					}
  $mysqli->query("UPDATE user_pay set price = '".$price ."' where id_user_credit_history = ".$row['id_user_credit_history']." ");
  $mysqli->query("UPDATE export_coms set price = '".$price ."' where user_credit_history_id = ".$row['id_user_credit_history']." ");
          var_dump("UPDATE user_pay set price = '".$price ."' where id_user_credit_history = ".$row['id_user_credit_history']." ");
var_dump('agent => '.$agent_id);
        }

}



echo 'end';
exit;

/*


$result = $mysqli->query("SELECT * from user_credit_history where ca = '' and is_factured = 1 and credits > 0 and date_start <= '2020-06-27 22:00:00' order by user_credit_history");

while($row = $result->fetch_array(MYSQLI_ASSOC)){
var_dump('user_credit_history =>' .$row['user_credit_history']);
  $result2 = $mysqli->query("SELECT * from user_credit_prices where user_id = '".$row['user_id']."'  order by id desc limit 1");
  $row2 = $result2->fetch_array(MYSQLI_ASSOC);
  
  var_dump($row2['price']);
  if($row2['price']){
     
    
    $ca = $row2['price'] * $row['credits']; 
    $ca_currency = $row2['devise'];
    $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = ".$row['user_credit_history']." ");
    var_dump("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = ".$row['user_credit_history']." ");
    if($ca_currency == '€' || !$ca_currency)
    $mysqli->query("UPDATE export_coms set ca_euro = '".$ca ."' where user_credit_history_id = ".$row['user_credit_history']." ");
    if($ca_currency == 'CHF')
    $mysqli->query("UPDATE export_coms set ca_chf = '".$ca ."' where user_credit_history_id = ".$row['user_credit_history']." ");
    if($ca_currency == '$')
    $mysqli->query("UPDATE export_coms set ca_dollar = '".$ca ."' where user_credit_history_id = ".$row['user_credit_history']." ");
  }else{
    $result3 = $mysqli->query("SELECT * from orders where user_id = '".$row['user_id']."'  and valid = 1 and product_price > 0 order by id desc limit 1");
    $row3 = $result3->fetch_array(MYSQLI_ASSOC);
    if($row3['product_credits']){
      $price = $row3['total'] / $row3['product_credits'];
      $ca = $price * $row['credits']; 
      $ca_currency = $row3['currency'];
    }
    if($ca){
    var_dump('price => '.$price);
    $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = ".$row['user_credit_history']." ");
    var_dump("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = ".$row['user_credit_history']." ");
    if($ca_currency == '€')
    $mysqli->query("UPDATE export_coms set ca_euro = '".$ca ."' where user_credit_history_id = ".$row['user_credit_history']." ");
    if($ca_currency == 'CHF')
    $mysqli->query("UPDATE export_coms set ca_chf = '".$ca ."' where user_credit_history_id = ".$row['user_credit_history']." ");
    if($ca_currency == '$')
    $mysqli->query("UPDATE export_coms set ca_dollar = '".$ca ."' where user_credit_history_id = ".$row['user_credit_history']." ");
    }
  }
}*/
exit;
?>