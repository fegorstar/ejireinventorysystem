<?php

//user.php

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
    SELECT * FROM drug_type_list
    ORDER BY id ASC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_drug_type"]))
{
    $formdata = array();

    if(empty($_POST["name"]))
    {
        $error .= '<li>Drug Type Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['name'] = trim($_POST["name"]);
        }
    }


    if($error == '')
    {
        $object->query = "
        SELECT * FROM drug_type_list 
        WHERE name = '".$formdata['name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Drug Type Already Exists</li>';
        }
        else
        {
            $data = array(
                ':name'             =>  $formdata['name'],
                  );

            $object->query = "
            INSERT INTO drug_type_list 
            (name) 
            VALUES (:name)
            ";

            $object->execute($data);

            header('location:drug_type_lists.php?msg=add');
        }
    }
}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Drug Type Lists</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item">
								<a href="drug_type_lists.php">Drug Type Lists</a></li>
                                <li class="breadcrumb-item active">Add Drug Type</li>
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
                                            <i class="fas fa-user-plus"></i> Add Drug Type
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="name" type="text" placeholder="Enter Company Name" name="name" value="<?php if(isset($_POST["name"])) echo $_POST["name"]; ?>" />
                                                    <label for="company_name">Name</label>
                                                </div>
                                                                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_drug_type" class="btn btn-success" value="Add" />
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
                        ?>
                        
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Drug Type Lists</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Drug Type Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                              
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Drug Type Lists
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="drug_type_lists.php?action=add&amp;code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i>Add New</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Name</th>
                                                
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                       
                                        <tbody>
                                        <?php
                                        $cnt=1;
                                        foreach($result as $row)
                                        {
                                                                                     
                                          ?>
                                            <tr  class="process_drugtype<?php echo $row['id']?>">
                                            <td><?php echo $cnt;?></td>
                                                <td><?php echo $row["name"] ?></td>
                                                
                                                <td>
                                                            <button title="delete drug type" class="btn  btn-sm btn-icon btn-danger btn-delete" id="<?php echo $row['id']?>" type="button"><i class="fa fa-trash"></i></button>
				
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


                                                               
 <!-- modal to delete file--> 
     <div class="modal fade" id="modal_confirm3" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
							<div class="modal-body">
							
					<center><h5 class="text-danger">Are you sure you want to delete this Drug Type?</h5></center>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-success" id="btn_yesdel">Yes</button>
				
				</div>
			</div>
		</div>
	</div>
<!-- End Of modal to delete file--> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   
 <script type="text/javascript">
$(document).ready(function(){
	$('.btn-delete').on('click', function(){
		var id = $(this).attr('id');
		$("#modal_confirm3").modal('show');
		$('#btn_yesdel').attr('name', id);
	});
	$('#btn_yesdel').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "delete_drug_type.php",
			data:{
				id: id
			},
			success: function(){
				$("#modal_confirm3").modal('hide');
				$(".process_drugtype" + id).empty();
				$(".process_drugtype" + id).html("<td colspan='8'><center class='text-danger'>Deleting Drug Type...</center></td>");
			setTimeout(' window.location.href = "drug_type_lists.php"; ',1000);
			}
		});
	});
});
</script>