<?php include_once '../auth/db-connect.php';
include('data-functions.php');

	//FILE UPLOAD

	$target_dir = "logos/";
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
			$update_one = $mysqli->prepare("UPDATE ap_settings SET site_logo = ? WHERE id=1"); 
			$update_one->bind_param('s', $db_filename);
			$update_one->execute();
			$update_one->close();
			} else {

			}
	}

header('Location: ../settings');
?>