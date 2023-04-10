<?php include('../auth/db-connect.php');
	
$product_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

//DATA REQUIRED
$get_data = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_products WHERE id=$product_id"));
$name = $get_data['name'];
$price = $get_data['price'];
$commission = $get_data['commission'];
$recurring = $get_data['recurring'];
$recurring_fee = $get_data['recurring_fee'];

echo 'Add this code to the conversion page, such as a thank you page.  Remember if your using a shopping cart, please reference the <a href="documentation">documentation</a>
for specific dynamic integration instructions that apply to your shopping cart (such as woocommerce, opencart) rather than using the code below <br><br>
			$sale_amount = "'.$price.'";<br>
			$product =  "'.$name.'";<br>';
			if($commission!='0'){ echo '$commission =  "'.$commission.'";<br>';}
			if($recurring!=''){ echo '$recurring =  "'.$recurring.'";<br>
			$recurring_fee =  "'.$recurring_fee.'";<br>';}
			echo 'include(\'affiliate-pro/controller/record-sale.php\');';
