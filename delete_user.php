<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['user_id'])){
		$id = $_POST['user_id'];		
	
			$deleteSql = "delete from user_msbs  where user_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>