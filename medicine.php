<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
} 

if(!$object->is_master_user())
{
    header('location:index.php');
}

$object->query = "
    SELECT * FROM medicine_msbs 
    INNER JOIN drug_type_list 
    ON drug_type_list.id = medicine_msbs.drug_type 
    INNER JOIN location_rack_msbs 
    ON location_rack_msbs.location_rack_id = medicine_msbs.medicine_location_rack 
    ORDER BY medicine_msbs.medicine_name ASC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_medicine"]))
{
    $formdata = array();

    if(empty($_POST["medicine_name"]))
    {
        $error .= '<li>Medicine Name is required</li>';
    }
    else
    {
       
            $formdata['medicine_name'] = $_POST["medicine_name"];
        
    }

    if(empty($_POST["pk_size"]))
    {
        $error .= '<li>PK Size is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["pk_size"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['pk_size'] = trim($_POST["pk_size"]);
        }
    }


    if(empty($_POST["brand"]))
    {
        $error .= '<li>Brand is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["brand"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['brand'] = trim($_POST["brand"]);
        }
    }

 
   
    if(empty($_POST["drug_type"]))
    {
        $error .= '<li>Drug Type is required</li>';
    }
    else
    {
        $formdata['drug_type'] = trim($_POST["drug_type"]);
    }

    if(empty($_POST["medicine_location_rack"]))
    {
        $error .= '<li>Medicine Location Rack is required</li>';
    }
    else
    {
        $formdata['medicine_location_rack'] = trim($_POST["medicine_location_rack"]);
    }    

    if($error == '')
    {
        $object->query = "
        SELECT * FROM medicine_msbs 
        WHERE medicine_name = '".$formdata['medicine_name']."' 
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Drug Already Exists</li>';
        }
        else
        {
            $data = array(
                ':medicine_name'                =>  $formdata['medicine_name'],
                ':pk_size'            =>  $formdata['pk_size'],
                ':brand'     =>  $formdata['brand'],
                ':drug_type'            =>  $formdata['drug_type'],
                ':medicine_available_quantity'  =>  0,
                ':medicine_location_rack'       =>  $formdata['medicine_location_rack'],
            
                ':medicine_add_datetime'        =>  $object->now,
                ':medicine_update_datetime'     =>  $object->now
            );

            $object->query = "
            INSERT INTO medicine_msbs 
            (medicine_name, pk_size, brand, drug_type, medicine_available_quantity, medicine_location_rack, medicine_add_datetime, medicine_update_datetime) 
            VALUES (:medicine_name, :pk_size, :brand, :drug_type, :medicine_available_quantity, :medicine_location_rack, :medicine_add_datetime, :medicine_update_datetime)
            ";

            $object->execute($data);

            header('location:medicine.php?msg=add');
        }
    }
}

if(isset($_POST["edit_medicine"]))
{
    $formdata = array();

    if(empty($_POST["medicine_name"]))
    {
        $error .= '<li>Medicine Name is required</li>';
    }
    else
    {
       
            $formdata['medicine_name'] = $_POST["medicine_name"];
        
    }

    if(empty($_POST["pk_size"]))
    {
        $error .= '<li>PK Size is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["pk_size"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['pk_size'] = trim($_POST["pk_size"]);
        }
    }


    if(empty($_POST["brand"]))
    {
        $error .= '<li>Brand is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["brand"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['brand'] = trim($_POST["brand"]);
        }
    }

 
   
    if(empty($_POST["drug_type"]))
    {
        $error .= '<li>Drug Type is required</li>';
    }
    else
    {
        $formdata['drug_type'] = trim($_POST["drug_type"]);
    }

    if(empty($_POST["medicine_location_rack"]))
    {
        $error .= '<li>Medicine Location Rack is required</li>';
    }
    else
    {
        $formdata['medicine_location_rack'] = trim($_POST["medicine_location_rack"]);
    }

    if($error == '')
    {
        $medicine_id = $object->convert_data(trim($_POST["medicine_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM medicine_msbs 
        WHERE medicine_name = '".$formdata['medicine_name']."' 
        AND medicine_manufactured_by = '".$formdata['medicine_name']."'
        AND medicine_id != '".$medicine_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Drug Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':medicine_name'                =>  $formdata['medicine_name'],
                ':pk_size'            =>  $formdata['pk_size'],
                ':brand'     =>  $formdata['brand'],
                ':drug_type'            =>  $formdata['drug_type'],
                ':medicine_location_rack'       =>  $formdata['medicine_location_rack'],
                ':medicine_update_datetime'     =>  $object->now,
                ':medicine_id'                  =>  $medicine_id
            );

            print_r($data);

            $object->query = "
            UPDATE medicine_msbs 
            SET medicine_name = :medicine_name, 
            pk_size = :pk_size,
            brand = :brand, 
            drug_type = :drug_type, 
            medicine_location_rack = :medicine_location_rack, 
            medicine_update_datetime = :medicine_update_datetime 
            WHERE medicine_id = :medicine_id
            ";

            $object->execute($data);

            header('location:medicine.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $medicine_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':medicine_status'      =>  $status,
        ':medicine_id'          =>  $medicine_id
    );

    $object->query = "
    UPDATE medicine_msbs 
    SET medicine_status = :medicine_status 
    WHERE medicine_id = :medicine_id
    ";

    $object->execute($data);

    header('location:medicine.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Drug Lists</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="medicine.php">Drug Lists</a></li>
                                <li class="breadcrumb-item active">Add Drug</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Add Drug
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_name" type="text" placeholder="Enter Medicine Name" name="medicine_name" value="<?php if(isset($_POST["medicine_name"])) echo $_POST["medicine_name"]; ?>" />
                                                    <label for="medicine_name">Drug Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="drug_type" class="form-control" id="drug_type">
                                                        <?php echo $object->fill_drug_type_list(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["drug_type"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("drug_type").value = "'.$_POST["drug_type"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="medicine_manufactured_by">Drug Type:</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="pk_size" type="text" placeholder="Enter PK Size" name="pk_size" value="<?php if(isset($_POST["pk_size"])) echo $_POST["pk_size"]; ?>" />
                                                    <label for="pk size">Packet (PK) Size</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   <input class="form-control" id="brand" type="text" placeholder="Enter Brand" name="brand" value="<?php if(isset($_POST["brand"])) echo $_POST["brand"]; ?>" />
                                                   
                                                    <label for="brand">Brand</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="medicine_location_rack" class="form-control" id="medicine_location_rack">
                                                        <?php echo $object->fill_location_rack(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["medicine_location_rack"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("medicine_location_rack").value = "'.$_POST["medicine_location_rack"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="medicine_location_rack">Location Rack</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="add_medicine" class="btn btn-success" value="Add" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $medicine_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($medicine_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM medicine_msbs 
                                    WHERE medicine_id = '$medicine_id'
                                    ";

                                    $medicine_result = $object->get_result();

                                    foreach($medicine_result as $medicine_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="medicine.php">Drug Lists</a></li>
                                    <li class="breadcrumb-item active">Edit Drug Data</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Edit Drug
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_name" type="text" placeholder="Enter Medicine Name" name="medicine_name" value="<?php echo $medicine_row["medicine_name"]; ?>" />
                                                        <label for="medicine_name">Drug Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="drug_type" class="form-control" id="drug_type">
                                                            <?php echo $object->fill_drug_type_list(); ?>
                                                        </select>
                                                        <label for="drug_type">Drug Type</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="pk_size" type="text" placeholder="Enter pk_size" name="pk_size" value="<?php echo $medicine_row["pk_size"]; ?>" />
                                                        <label for="medicine_pack_qty">Packet (PK) Size</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                       <input class="form-control" id="brand" type="text" placeholder="Enter Brand" name="brand" value="<?php echo $medicine_row["brand"]; ?>" />
                                                          <label for="medicine_category">Brand</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="medicine_location_rack" class="form-control" id="medicine_location_rack">
                                                            <?php echo $object->fill_location_rack(); ?>
                                                        </select>
                                                        <label for="medicine_location_rack">Location Rack</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="medicine_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_medicine" class="btn btn-primary" value="Update" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <script>
                                document.getElementById('drug_type').value = "<?php echo $medicine_row["drug_type"]; ?>";
                                document.getElementById('medicine_location_rack').value = "<?php echo $medicine_row["medicine_location_rack"]; ?>";

                                </script>
                                
                                <?php
                                    }
                                }
                                else
                                {
                                    echo '<div class="alert alert-info">Something Went Wrong</div>';
                                }                                
                            }
                            else
                            {
                                echo '<div class="alert alert-info">Something Went Wrong</div>';
                            }
                        ?>

                        <?php
                        }
                        else
                        {
                        ?>
                        
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Drug Lists</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Drug Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Drug Data Updated Successfully <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                              
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Medicine Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="medicine.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Drug Info</th>
                                                 <th>Drug Type</th>
                                                <th >Available Quantity</th>
                                              
                                              
                                                <th>Date Added</th>
                                                <th>Date Updated</th>
                                                <th width="200">Action</th>
                                            </tr>
                                        </thead>
                                       
                                       
                                        <tbody>
                                        <?php
                                        $cnt=1;
                                        foreach($result as $row)
                                        {
                                           ?>
                                                <tr  class="process_drug<?php echo $row['medicine_id']?>">
                                             <td><?php echo $cnt;?></td>

                                                <td>
 									
										<p><small>Name : <b><?php echo $row['medicine_name'] ?></b></small></p>
										<p><small> Location on Rack: <b><?php echo $row['location_rack_name'] ?></b></small></p>

										<p><small> Packet Size: <b><?php echo $row['pk_size'] ?></b></small></p>
										<p><small> Brand: <b><?php echo $row['brand'] ?></b></small></p>

									
                                                </td>
                                                 <td><?php echo $row["name"]; ?></td>
                                                 
                                                 <td><?php echo $row["medicine_available_quantity"]; ?></td>

                                               
                                              
                                                  <td><?php echo date("M d, Y",strtotime($row['medicine_add_datetime'])); ?></td>
                                                  <td><?php echo date("M d, Y",strtotime($row['medicine_update_datetime'])); ?></td>



                                                <td>
                                                <a title="Purchase Drug" href="medicine_purchase.php?action=add&code=<?php echo $object->convert_data("add")?> &medicine=<?php echo $object->convert_data($row["medicine_id"])?>" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Purchase</a>
                                                    <a title="Edit Drug" href="medicine.php?action=edit&code=?<?php echo $object->convert_data($row["medicine_id"])?>" class="btn btn-sm btn-primary">Edit</a>
                                                  
                                                         <button title="Delete Drug" class="btn  btn-sm btn-icon btn-danger btn-delete" id="<?php echo $row['medicine_id']?>" type="button"><i class="fa fa-trash"></i></button>
				
                                                </td>
                                            </tr>
                                                                                        
                                            
                                            </tbody>
                                         <?php  
                                           $cnt=$cnt+1;
 
                                        }
                                        ?>
                                      
                                    </table>
                                </div>
                            </div>
                           
                        <?php
                          
                        }
                        ?>

                        </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php

include('footer.php');

?>



                                                               
 <!-- modal to delete file--> 
     <div class="modal fade" id="modal_confirm3" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
							<div class="modal-body">
							
					<center><h5 class="text-danger">Are you sure you want to delete this Drug?</h5></center>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-success" id="btn_yesdel">Yes</button>
				
				</div>
			</div>
		</div>
	</div>
<!-- End Of modal to delete file--> 

   
 <script type="text/javascript">
$(document).ready(function(){
	$('.btn-delete').on('click', function(){
		var drug_id = $(this).attr('id');
		$("#modal_confirm3").modal('show');
		$('#btn_yesdel').attr('name', drug_id);
	});
	$('#btn_yesdel').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "delete_drug.php",
			data:{
				drug_id: id
			},
			success: function(){
				$("#modal_confirm3").modal('hide');
				$(".process_drug" + id).empty();
				$(".process_drug" + id).html("<td colspan='8'><center class='text-danger'>Deleting Drug...</center></td>");
			setTimeout(' window.location.href = "medicine.php"; ',1000);
			}
		});
	});
});
</script>