<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['medicine_purchase_id'])){
		$id = $_POST['medicine_purchase_id'];		
	
			$deleteSql = "delete from medicine_purchase_msbs  where medicine_purchase_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>