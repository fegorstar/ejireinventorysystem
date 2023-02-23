<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['supplier_id'])){
		$id = $_POST['supplier_id'];		
	
			$deleteSql = "delete from supplier_msbs  where supplier_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>