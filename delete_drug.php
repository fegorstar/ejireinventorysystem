<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['drug_id'])){
		$id = $_POST['drug_id'];		
	
			$deleteSql = "delete from medicine_msbs  where medicine_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>