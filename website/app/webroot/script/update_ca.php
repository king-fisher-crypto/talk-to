<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$result = $mysqli->query("SELECT * from user_credit_history WHERE type_pay = 'aud' and domain_id = 29 and ca_currency != '$'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $id_com = $row['user_credit_history'];
  $ca = 0;
					    $ca_currency = '€';
              $the_country_id = 1;
              switch ($row['domain_id']) {
                  case '19':
                    $the_country_id = 1;
                    $ca_currency = '€';
                    break;
                  case '13':
                    $the_country_id = 3;
                    $ca_currency = 'CHF';
                    break;
                  case '11':
                    $the_country_id = 4;
                    $ca_currency = '€';
                    break;
                  case '22':
                    $the_country_id = 5;
                    $ca_currency = '€';
                    break;
                  case '29':
                    $the_country_id = 13;
                    $ca_currency = '$';
                    break;
                }
  
          $result2 = $mysqli->query("SELECT * from  country_lang_phone where country_id = '".$the_country_id."' and lang_id = 1");
        $row2 = $result2->fetch_array(MYSQLI_ASSOC);
              if($row2['surtaxed_minute_cost']){
                $cost_second = $row2['surtaxed_minute_cost'] / 60;
                $ca = $row['seconds'] * $cost_second;
              }

  if($ca){
    if($ca_currency == '€' || !$ca_currency)
      $mysqli->query("UPDATE export_coms set ca_euro = '".$ca ."' where user_credit_history_id = ".$id_com." ");
    if($ca_currency == 'CHF')
      $mysqli->query("UPDATE export_coms set ca_chf = '".$ca ."' where user_credit_history_id = ".$id_com." ");
    if($ca_currency == '$')
      $mysqli->query("UPDATE export_coms set ca_dollar = '".$ca ."' where user_credit_history_id = ".$id_com." ");
  
      //$mysqli->query("UPDATE user_pay set ca = '".$ca ."' where id_user_credit_history = ".$id_com." ");
    
     $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."', ca_currency = '".$ca_currency ."'  where user_credit_history = ".$id_com."");
    var_dump("UPDATE user_credit_history set ca = '".$ca ."', ca_currency = '".$ca_currency ."'  where user_credit_history = ".$id_com." ");
  }
}
echo 'end aud';
exit;


$result = $mysqli->query("SELECT * FROM invoice_accounts ORDER BY id ASC");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
  
  $ca_currency = $row['currency'];
  $ca = $row['total_amount'];
  $id_com = $row['user_credit_last_history'];
  
  if($ca){
   /* if($ca_currency == '€' || !$ca_currency)
      $mysqli->query("UPDATE export_coms set ca_euro = '".$ca ."' where user_credit_history_id = ".$id_com." ");
    if($ca_currency == 'CHF')
      $mysqli->query("UPDATE export_coms set ca_chf = '".$ca ."' where user_credit_history_id = ".$id_com." ");
    if($ca_currency == '$')
      $mysqli->query("UPDATE export_coms set ca_dollar = '".$ca ."' where user_credit_history_id = ".$id_com." ");
  
      $mysqli->query("UPDATE user_pay set ca = '".$ca ."' where id_user_credit_history = ".$id_com." and ca = 0");*/
    
     $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."', ca_currency = '".$ca_currency ."'  where user_credit_history = ".$id_com." and ca = 0");
    //var_dump("UPDATE user_credit_history set ca = '".$ca ."', ca_currency = '".$ca_currency ."'  where user_credit_history = ".$id_com." and ca = 0");exit;
  }

}



echo 'end';
exit;




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
    }/*else{
      $price = 19.90 / 600;
      $ca = $price * $row['credits']; 
      $ca_currency = '€';
    }*/
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
}
exit;
?>