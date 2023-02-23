<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['order_id'])){
		$id = $_POST['order_id'];		
	
			$deleteSql = "delete from order_msbs where order_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>