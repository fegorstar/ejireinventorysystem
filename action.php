<?php

//action.php

include('class/db.php');

$object = new db();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch_medicine_data')
	{
		$data = array(
			':medicine_purchase_id'	=>	$_POST["med_id"]
		);

		$object->query = "
		SELECT * FROM medicine_purchase_msbs 
		INNER JOIN medicine_msbs 
		ON medicine_msbs.medicine_id =  medicine_purchase_msbs.medicine_id 
		WHERE medicine_purchase_msbs.medicine_purchase_id = :medicine_purchase_id
		";

		$object->execute($data);

		$result = $object->statement_result();

		$data = array();

		foreach($result as $row)
		{
			$data['medicine_id']					=	$row["medicine_id"];
			$data['medicine_name']					=	$row["medicine_name"];
			$data['pk_size']					=	$row["pk_size"];
			$data['date_manufactured']					=	$row["date_manufactured"];
			$data['medicine_batch_no']				=	$row["medicine_batch_no"];
			$data['available_quantity']				=	$row["available_quantity"];
			$data['expiry_date']			=	        $row["expiry_date"];
			$data['medicine_sale_price_per_unit']	=	$row["medicine_sale_price_per_unit"];
				$data['medicine_purchase_id']			=	$row["medicine_purchase_id"];
		}

		echo json_encode($data);

	}
}



?>