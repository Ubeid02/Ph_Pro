<?php
	include "koneksi.php";
	
	if (!empty($_POST['valuess'])) {

		date_default_timezone_set('Asia/Jakarta');
		
		$valuess = $_POST['valuess'];
		$dates = date('Y-m-d H:i:s');
		$timeses = date('H:i:s');
		

		$sql = "INSERT INTO ph_pro (dates, valuess, timeses) VALUES (?,?,?)";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, "sss", $dates, $valuess, $timeses);
		mysqli_stmt_execute($stmt);

		if (mysqli_stmt_affected_rows($stmt) > 0) {
			echo "Sensor value inserted successfully.";
		} else {
			echo "Error inserting sensor value.";
		}

		mysqli_stmt_close($stmt);
		mysqli_close($conn);

	}
?>
