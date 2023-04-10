<?php include_once '../auth/db-connect.php';
include('data-functions.php');
$media = filter_input(INPUT_POST, 'media', FILTER_SANITIZE_STRING);
if($media=='link'){
	//INSERT LINK INTO DB
	$customlink = filter_input(INPUT_POST, 'customlink', FILTER_SANITIZE_STRING);
	$linkdes = filter_input(INPUT_POST, 'linkdes', FILTER_SANITIZE_STRING);
	if($linkdes==''){$linkdes='';}
	$filetype = 'customlink';
	$stmt = $mysqli->prepare("INSERT INTO ap_banners (filename, filetype, url, description) VALUES (?, ?, ?, ?)");
	$stmt->bind_param('ssss', $customlink, $filetype, $customlink, $linkdes);
	$stmt->execute();
	$stmt->close();
	
}elseif($media=='video'){
	//INSERT VIDEO INTO DB
	$embedcode = $_POST['embedcode'];
	$filetype = 'video';
	$stmt = $mysqli->prepare("INSERT INTO ap_banners (filename, filetype) VALUES (?, ?)");
	$stmt->bind_param('ss', $embedcode, $filetype);
	$stmt->execute();
	$stmt->close();
		
}else{
	//FILE UPLOAD
	$customlink = filter_input(INPUT_POST, 'customlink', FILTER_SANITIZE_STRING);
	$target_dir = "banners/";
	$filename = $_FILES["file"]["name"];
	$size = $_FILES["file"]["size"];
	$upload_date = date('j M Y H:i:s');
	$name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
	$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	$db_filename = $name.'.'.$extension;
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	// Check if file already exists
	$counter = 0;
	while (file_exists($target_file)) {
		$db_filename = basename($name.'_'.$counter.'.'.$extension);
			$target_file = $target_dir . basename($name.'_'.$counter.'.'.$extension);
		$counter++;
	}
	// Check file size
	if ($_FILES["file"]["size"] > 500000) {

	}
	if ($uploadOk == 0) {
	} else {
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
			$stmt = $mysqli->prepare("INSERT INTO ap_banners (filename, filetype, url) VALUES (?, ?, ?)");
			$stmt->bind_param('sss', $db_filename, $extension, $customlink);
			$stmt->execute();
			$stmt->close();
			} else {

			}
	}
}
header('Location: ../banners-logos');
?>