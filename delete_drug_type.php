<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['id'])){
		$id = $_POST['id'];		
	
			$deleteSql = "delete from drug_type_list where id='$id' ";
						$rsDelete = mysqli_query($conn, $deleteSql);			
	
	}
	
	
?>