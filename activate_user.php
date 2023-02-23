<?php
 require('db_connect.php'); // Connect to the database
	
	if(ISSET($_POST['user_id'])){
		$user_id = $_POST['user_id'];		
			
// UPDATE THE USER LOCATION
			$sql = "UPDATE user_msbs SET user_status='Enable' WHERE user_id='$user_id' LIMIT 1";
            $query = mysqli_query($conn, $sql);

	}
	
	
?>