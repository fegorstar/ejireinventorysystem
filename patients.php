
<head>
<link href="datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">
</head>
<?php

//user.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}


$object->query = "
    SELECT * FROM patients 
    ORDER BY id DESC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_user"]))
{
    $formdata = array();

    if(empty($_POST["patient_name"]))
    {
        $error .= '<li>Patient Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["patient_name"]))
        {
            $error .= '<li>Only letters and white space allowed</li>';
        }
        else
        {
            $formdata['patient_name'] = trim($_POST["patient_name"]);
        }
    }

  
    if(empty($_POST["sex"]))
    {
        $error .= '<li>Sex is required</li>';
    }
    else
    {
        $formdata['sex'] = trim($_POST["sex"]);
    }
    
     if(empty($_POST["age"]))
    {
        $error .= '<li>Age is required</li>';
    }
    else
    {
        $formdata['age'] = trim($_POST["age"]);
    }
    
     if(empty($_POST["doa"]))
    {
        $error .= '<li>Date of Admission is required</li>';
    }
    else
    {
         $formdata['doa'] = strtotime(trim($_POST["doa"]));
         //changing the format
        
       $newformat_doa = date('Y-m-d',$formdata['doa']);

    }
    
    if(empty($_POST["ward_number"]))
    {
        $error .= '<li>Ward Number is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["ward_number"]))
        {
            $error .= '<li>Only Numbers, letters and white space allowed</li>';
        }
        else
        {
            $formdata['ward_number'] = trim($_POST["ward_number"]);
        }
    }


 if(empty($_POST["phoneno"]))
    {
        $error .= '<li>Phone Number is required</li>';
    }
    else
    {
        if(!preg_match('/^[0-9]{11}+$/', $_POST["phoneno"])){
            $error .= '<li>Only Numbers is allowed</li>';
        }
        else
        {
            $formdata['phoneno'] = trim($_POST["phoneno"]);
        }
    }





    if($error == '')
    {
        $object->query = "
        SELECT * FROM patients 
        WHERE phoneno = '".$formdata['phoneno']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Phone Number Already Exists</li>';
        }
        else
        {
            $data = array(
                ':patient_name'        =>  $formdata['patient_name'],
                ':sex'       =>  $formdata['sex'],
                ':age'    =>  $formdata['age'],
                ':doa'        =>  $newformat_doa,
                ':phoneno'      => $formdata['phoneno'],
                ':ward_number'      => $formdata['ward_number'],
               
            );

            $object->query = "
            INSERT INTO patients 
            (patient_name,sex,phoneno, age, date_of_admission, ward_number) 
            VALUES (:patient_name, :sex,:phoneno, :age, :doa, :ward_number)
            ";

            $object->execute($data);

            header('location:patients.php?msg=add');
        }
    }
}

if(isset($_POST["edit_user"]))
{
    $formdata = array();

      if(empty($_POST["patient_name"]))
    {
        $error .= '<li>Patient Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["patient_name"]))
        {
            $error .= '<li>Only letters and white space allowed</li>';
        }
        else
        {
            $formdata['patient_name'] = trim($_POST["patient_name"]);
        }
    }

  
    if(empty($_POST["sex"]))
    {
        $error .= '<li>Sex is required</li>';
    }
    else
    {
        $formdata['sex'] = trim($_POST["sex"]);
    }
    
     if(empty($_POST["age"]))
    {
        $error .= '<li>Age is required</li>';
    }
    else
    {
        $formdata['age'] = trim($_POST["age"]);
    }
    
     if(empty($_POST["doa"]))
    {
        $error .= '<li>Date of Admission is required</li>';
    }
    else
    {
         $formdata['doa'] = strtotime(trim($_POST["doa"]));
         //changing the format
        
       $newformat_doa = date('Y-m-d',$formdata['doa']);

    }
    
    if(empty($_POST["ward_number"]))
    {
        $error .= '<li>Ward Number is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["ward_number"]))
        {
            $error .= '<li>Only Numbers, letters and white space allowed</li>';
        }
        else
        {
            $formdata['ward_number'] = trim($_POST["ward_number"]);
        }
    }


 if(empty($_POST["phoneno"]))
    {
        $error .= '<li>Phone Number is required</li>';
    }
    else
    {
        if(!preg_match('/^[0-9]{11}+$/', $_POST["phoneno"])){
            $error .= '<li>Only Numbers is allowed</li>';
        }
        else
        {
            $formdata['phoneno'] = trim($_POST["phoneno"]);
        }
    }
    
    
    if($error == '')
    {
        $user_id = $object->convert_data(trim($_POST["user_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM patients 
        WHERE phoneno = '".$formdata['phoneno']."' 
        AND id != '".$user_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Patient Already Exists</li>';
        }
        else
        {
            $data = array(
                ':patient_name'        =>  $formdata['patient_name'],
                ':sex'       =>  $formdata['sex'],
                ':age'    =>  $formdata['age'],
                ':doa'        =>  $newformat_doa,
                ':phoneno'      => $formdata['phoneno'],
                ':ward_number'      => $formdata['ward_number'],
                ':user_id'          =>  $user_id
            );

            $object->query = "
            UPDATE patients 
            SET patient_name = :patient_name, 
             sex = :sex,
             age = :age,
             date_of_admission= :doa,
             phoneno = :phoneno,
            ward_number = :ward_number 
            WHERE id = :user_id
            ";

            $object->execute($data);

            header('location:patients.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $user_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':user_status'      =>  $status,
        ':user_id'          =>  $user_id
    );

    $object->query = "
    UPDATE user_msbs 
    SET user_status = :user_status 
    WHERE user_id = :user_id
    ";

    $object->execute($data);

    header('location:user.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Patient Records Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="patients.php">Patients Management</a></li>
                                <li class="breadcrumb-item active">Add Patient</li>
                            </ol>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    if(isset($error) && $error != '')
                                    {
                                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                    }
                                    ?>
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user-plus"></i> Add New Patient
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                            
                                            <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="patient_name" type="text" placeholder="Enter Patient Name" name="patient_name" value="<?php if(isset($_POST["patient_name"])) echo $_POST["patient_name"]; ?>" />
                                                    <label for="user_name">Patient Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                  <select name="sex" class="form-control" id="sex">
                                                  <option value=""></option>
                                                       <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                         


                                                    </select>
                                                    <label for="brand">Sex</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="age" type="number" placeholder="Enter Patient Age" name="age" value="<?php if(isset($_POST["age"])) echo $_POST["age"]; ?>" />
                                                    <label for="age">Age</label>  </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   
                                                <input class="form-control datepicker-here " data-language="en"  type="text" placeholder="Enter Date of Admission"   id="doa" name="doa" value="<?php if(isset($_POST["doa"])) echo $_POST["doa"]; ?>">
                                              
                                                           <label for="dob">Date of Admission</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="ward_number" type="text" placeholder="Enter Ward Number" name="ward_number" value="<?php if(isset($_POST["ward_number"])) echo $_POST["ward_number"]; ?>" />
                                                    <label for="age">Ward Number</label>  </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   
                                                <input class="form-control" type="text" placeholder="Enter Phone Number"   id="phoneno" name="phoneno" value="<?php if(isset($_POST["phoneno"])) echo $_POST["phoneno"]; ?>">
                                              
                                                           <label for="dob">Phone No:</label>
                                                </div>
                                            </div>
                                        </div>



                                              
                                               
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_user" class="btn btn-success" value="Add" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $user_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($user_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM patients 
                                    WHERE id = '$user_id'
                                    ";

                                    $user_result = $object->get_result();

                                    foreach($user_result as $user_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="patients.php">Patient Management</a></li>
                                    <li class="breadcrumb-item active">Edit Patient</li>
                                </ol>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        if(isset($error) && $error != '')
                                        {
                                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                        }
                                        ?>
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-user-edit"></i> Edit Patients Details
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                     <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="patient_name" type="text" placeholder="Enter Patient Name" name="patient_name" value="<?php echo $user_row["patient_name"]; ?>" />
                                                    <label for="user_name">Patient Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                  <select name="sex" class="form-control" id="sex">
                                                  <option value=""></option>
                                                       <option value="Male">Male</option>
                                                        <option value="Female">Female</option>

                                                    </select>
                                                    <label for="brand">Sex</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="age" type="number" placeholder="Enter Patient Age" name="age" value="<?php echo $user_row["age"]; ?>" />
                                                    <label for="age">Age</label>  </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   
                                                <input class="form-control datepicker-here " data-language="en"  type="text" placeholder="Enter Date of Admission"   id="doa" name="doa" value="<?php echo $user_row["date_of_admission"]; ?>">
                                              
                                                           <label for="dob">Date of Admission</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="ward_number" type="text" placeholder="Enter Ward Number" name="ward_number" value="<?php echo $user_row["ward_number"]; ?>" />
                                                    <label for="age">Ward Number</label>  </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   
                                                <input class="form-control" type="text" placeholder="Enter Phone Number"   id="phoneno" name="phoneno" value="<?php echo $user_row["phoneno"]; ?>">
                                              
                                                           <label for="dob">Phone No:</label>
                                                </div>
                                            </div>
                                        </div>



                                        
                                        
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="user_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_user" class="btn btn-primary" value="Update" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                <li class="breadcrumb-item active">Patient Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Patient Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Patient Data Successfully Updated <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                               
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Patient Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="patients.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                            <th>S/N</th>

                                                <th>Patient Name</th>
                                                <th>Phone No</th>
                                                <th>Sex</th>
                                                <th>Age</th>
                                                <th>Ward Number</th>
                                                <th>Date of Admission</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                       
                                        <tbody>
                                        
                                        
                                        <?php
                                        
                                          $cnt=1;

                                        foreach($result as $row)
                                        {
                                        
                                      
                                          
                                           ?>
                                            <tr  class="process_patient<?php echo $row['id']?>">
                                             <td style="height: 30px"><?php echo $cnt;?></td>

                                                <td style="height: 30px"><?php echo $row["patient_name"] ?></td>
                                                <td style="height: 30px"><?php echo $row["phoneno"]; ?></td>
                                                <td style="height: 30px"><?php echo $row["sex"]; ?></td>
                                                 <td style="height: 30px"><?php echo $row["age"]; ?></td>
                                                  <td style="height: 30px"><?php echo $row["ward_number"]; ?></td>
                                                   <td><?php echo date("M d, Y",strtotime($row['date_of_admission'])); ?></td>
                                              
                                                
                                                <td style="height: 30px">
                                                      					   <a title="Edit Patient" href="patients.php?action=edit&code=<?php echo $object->convert_data($row["id"]) ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                               <button title="delete Patient" class="btn  btn-sm btn-icon btn-danger btn-delete" id="<?php echo $row['id']?>" type="button"><i class="fa fa-trash"></i></button>
				                          	
						
                                                </td>
                                                
                                            </tr>
                                            
                                       
                                        </tbody>
                                         <?php 
                                           $cnt=$cnt+1;
 

}?>
                                      

                                      
                                    </table>
                                </div>
                            </div>
                            
                        <?php
                        }
                        ?>

                        </div>

<?php

include('footer.php');

?>



 <script src="datepicker/js/jquery-3.4.1.min.js"></script>

<script src="datepicker/js/datepicker.min.js"></script>
<script src="datepicker/js/i18n/datepicker.en.js"></script>



                                                               
 <!-- modal to delete file--> 
     <div class="modal fade" id="modal_confirm3" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
							<div class="modal-body">
							
					<center><h5 class="text-danger">Are you sure you want to delete this Patient?</h5></center>
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
		var patient_id = $(this).attr('id');
		$("#modal_confirm3").modal('show');
		$('#btn_yesdel').attr('name', patient_id);
	});
	$('#btn_yesdel').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "delete_patient.php",
			data:{
				patient_id: id
			},
			success: function(){
				$("#modal_confirm3").modal('hide');
				$(".process_patient" + id).empty();
				$(".process_patient" + id).html("<td colspan='8'><center class='text-danger'>Deleting Patient...</center></td>");
			setTimeout(' window.location.href = "patients.php"; ',1000);
			}
		});
	});
});
</script>