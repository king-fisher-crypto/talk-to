<?php include_once '../auth/db-connect.php';
$product_id = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_STRING);
//DELETE FILE FROM SERVER
$get_path = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT name FROM ap_products WHERE id=$product_id"));
$file_path = $get_path['name'];
unlink('products/'.$file_path); 
if ($stmt = $mysqli->prepare("DELETE FROM ap_products WHERE id = ? LIMIT 1"))
	{ 
	$stmt->bind_param("i", $product_id);	
	$stmt->execute();
	$stmt->close();
	} else { echo "ERROR: could not prepare SQL statement."; }
	
$mysqli->close();

header('Location: ../products');