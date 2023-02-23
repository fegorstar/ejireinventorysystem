<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['location_rack_id'])){
		$id = $_POST['location_rack_id'];		
	
			$deleteSql = "delete from location_rack_msbs where location_rack_id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>