<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['patient_id'])){
		$id = $_POST['patient_id'];		
	
			$deleteSql = "delete from patients  where id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>