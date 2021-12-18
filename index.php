<?php

use Aws\S3\Exception\S3Exception;

require 'app/start.php';

if(isset($_FILES['file'])){
	
	if($_FILES['file']['type'] != "application/pdf"){
		echo "Только pdf файлы";
		exit;
	}
	
	
	
	$file = $_FILES['file'];
	
	$name = $file['name'];
	$tmp_name = $file['tmp_name'];
	
	$extension = explode('.', $name);
	$extension = strtolower(end($extension));
	
	
	$key = md5(uniqid());
	$tmp_file_name = "{$key}.{$extension}";
	$tmp_file_path = "files/{$tmp_file_name}";
	
	$f = null;
	
	move_uploaded_file($tmp_name, $tmp_file_path);
	
	try{
		
		$f = $s3->putObject([
			'Bucket' => $config['s3']['bucket'],
			'Key' => "uploads/{$name}",
			'Body' => fopen($tmp_file_path, 'rb'),
			'ACL' => 'public-read'
		]);
		
		unlink($tmp_file_path);
			
	} catch(S3Exception $e){
		
		die("Ошибка загрузки");
	}
	
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Upload</title>
</head>
<body>
	<?php if($f): ?>
		<a href="<?php echo $f['ObjectURL']; ?>"><?php echo $f['ObjectURL']; ?></a>
	<?php else: ?>
		<form action="" method="post" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="submit" value="Upload">
		</form>
	<?php endif;?>
</body>
</html>