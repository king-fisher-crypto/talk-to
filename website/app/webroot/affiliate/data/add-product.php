<?php include_once '../auth/db-connect.php';
include('data-functions.php');
$title = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
$commission = filter_input(INPUT_POST, 'commission', FILTER_SANITIZE_STRING);
$recurring = filter_input(INPUT_POST, 'recurring', FILTER_SANITIZE_STRING);
$recurring_fee = filter_input(INPUT_POST, 'recurring_fee', FILTER_SANITIZE_STRING);

//FILE SETTINGS
if(isset($_FILES['file']["name"])){
$target_dir = "products/";
$filename = $_FILES["file"]["name"];
$size = $_FILES["file"]["size"];
$upload_date = date('j M Y H:i:s');
$name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$db_filename = $name.'.'.$extension;
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
if($url==''){$url=$_SERVER['SERVER_NAME'];}
// Check if file already exists
$counter = 0;
while (file_exists($target_file)) {
	$db_filename = basename($name.'_'.$counter.'.'.$extension);
    $target_file = $target_dir . basename($name.'_'.$counter.'.'.$extension);
	$counter++;
}
move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
}
//if(!isset($_POST['file'])){$db_filename = '';}
	
		$stmt = $mysqli->prepare("INSERT INTO ap_products (product_img, name, price, commission, recurring, recurring_fee) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('ssssss', $db_filename, $title, $price, $commission, $recurring, $recurring_fee);
		$stmt->execute();
		$stmt->close();
    

header('Location: ../products');
?>