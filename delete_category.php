<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['category_id'])){
		$id = $_POST['category_id'];		
	
			$deleteSql = "delete from category_msbs where category_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>