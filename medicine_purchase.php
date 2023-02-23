
<head>
<link href="datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">
</head>

<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}

$where = '';

if(!$object->is_master_user())
{
    $where = "WHERE medicine_purchase_msbs.medicine_purchase_enter_by = '".$_SESSION["user_id"]."' ";
}

$object->query = "
    SELECT * FROM medicine_purchase_msbs 
    INNER JOIN medicine_msbs 
    ON medicine_msbs.medicine_id = medicine_purchase_msbs.medicine_id 
    INNER JOIN  supplier_msbs 
    ON  supplier_msbs.supplier_id = medicine_purchase_msbs.supplier_id 
    ".$where."
    ORDER BY medicine_purchase_msbs.medicine_purchase_id DESC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_medicine_purchase"]))
{
    $formdata = array();

    if(empty($_POST["medicine_id"]))
    {
        $error .= '<li>Drug Name is required</li>';
    }
    else
    {
        $formdata['medicine_id'] = trim($_POST["medicine_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Supplier is required</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["medicine_batch_no"]))
    {
        $error .= '<li>Drug Batch No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["medicine_batch_no"]))
        {
            $error .= '<li>Only letters and Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_batch_no'] = trim($_POST["medicine_batch_no"]);
        }
    }

    if(empty($_POST["medicine_purchase_qty"]))
    {
        $error .= '<li>Purchase Quantity is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9']*$/", $_POST["medicine_purchase_qty"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_purchase_qty'] = trim($_POST["medicine_purchase_qty"]);
        }
    }

    if(empty($_POST["medicine_purchase_price_per_unit"]))
    {
        $error .= '<li>Purchase Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_purchase_price_per_unit'] = trim($_POST["medicine_purchase_price_per_unit"]);
    }    


    if(empty($_POST["manufacturing_date"]))
    {
        $error .= '<li>Date Manufactured is required</li>';
    }
    else
    {
        $formdata['manufacturing_date'] =  strtotime(trim($_POST["manufacturing_date"]));
        //changing the format
       $newformat_manufacturingdate = date('Y-m-d',$formdata['manufacturing_date']);

    }

    if(empty($_POST["expiry_date"]))
    {
        $error .= '<li>Expiry Date is required</li>';
    }
    else
    {
        $formdata['expiry_date'] = strtotime(trim($_POST["expiry_date"]));
        //changing the format
        
       $newformat_expirydate = date('Y-m-d',$formdata['expiry_date']);
    }

  
    if(empty($_POST["medicine_sale_price_per_unit"]))
    {
        $error .= '<li>Sale Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_sale_price_per_unit'] = trim($_POST["medicine_sale_price_per_unit"]);
    }

    if($error == '')
    {
        $total_cost = floatval($formdata['medicine_purchase_qty']) * floatval($formdata['medicine_purchase_price_per_unit']);
        $data = array(
            ':medicine_id'                      =>  $formdata['medicine_id'],
            ':supplier_id'                      =>  $formdata['supplier_id'],
            ':medicine_batch_no'                =>  $formdata['medicine_batch_no'],
            ':medicine_purchase_qty'            =>  $formdata['medicine_purchase_qty'], 
            ':available_quantity'               =>  $formdata['medicine_purchase_qty'], 
            ':medicine_purchase_price_per_unit' =>  $formdata['medicine_purchase_price_per_unit'],
            ':medicine_purchase_total_cost'     =>  $total_cost,
            ':date_manufactured'       =>   $newformat_manufacturingdate,
            ':expiry_date'        =>  $newformat_expirydate,
            ':medicine_sale_price_per_unit'     =>  $formdata['medicine_sale_price_per_unit'],
            ':medicine_purchase_enter_by'       =>  $_SESSION["user_id"],
            ':medicine_purchase_datetime'       =>  $object->now,
           
        );

        $object->query = "
        INSERT INTO medicine_purchase_msbs 
        (medicine_id, supplier_id, medicine_batch_no, medicine_purchase_qty, available_quantity, medicine_purchase_price_per_unit, medicine_purchase_total_cost, date_manufactured, expiry_date, medicine_sale_price_per_unit, medicine_purchase_enter_by, medicine_purchase_datetime) 
        VALUES (:medicine_id, :supplier_id, :medicine_batch_no, :medicine_purchase_qty, :available_quantity, :medicine_purchase_price_per_unit, :medicine_purchase_total_cost, :date_manufactured, :expiry_date, :medicine_sale_price_per_unit, :medicine_purchase_enter_by, :medicine_purchase_datetime)
            ";

        $object->execute($data);

        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity + ".$formdata['medicine_purchase_qty']." 
        WHERE medicine_id = '".$formdata['medicine_id']."'
        ";

        $object->execute();

        header('location:medicine_purchase.php?msg=add');
    }
}




//for Editing
if(isset($_POST["edit_medicine_purchase"]))
{
    $formdata = array();

    if(empty($_POST["medicine_id"]))
    {
        $error .= '<li>Drug Name is required</li>';
    }
    else
    {
        $formdata['medicine_id'] = trim($_POST["medicine_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Supplier is required</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["medicine_batch_no"]))
    {
        $error .= '<li>Drug Batch No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9']*$/", $_POST["medicine_batch_no"]))
        {
            $error .= '<li>Only letters and Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_batch_no'] = trim($_POST["medicine_batch_no"]);
        }
    }

    if(empty($_POST["medicine_purchase_qty"]))
    {
        $error .= '<li>Purchase Quantity is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9']*$/", $_POST["medicine_purchase_qty"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['medicine_purchase_qty'] = trim($_POST["medicine_purchase_qty"]);
        }
    }

    if(empty($_POST["medicine_purchase_price_per_unit"]))
    {
        $error .= '<li>Purchase Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_purchase_price_per_unit'] = trim($_POST["medicine_purchase_price_per_unit"]);
    }    


    if(empty($_POST["manufacturing_date"]))
    {
        $error .= '<li>Manufacturing Date is required</li>';
    }
    else
    {
        $formdata['manufacturing_date'] = strtotime(trim($_POST["manufacturing_date"]));
         //changing the format
        
       $newformat_manufacturingdate = date('Y-m-d',$formdata['manufacturing_date']);

    }

    if(empty($_POST["expiry_date"]))
    {
        $error .= '<li>Expiry Date is required</li>';
    }
    else
    {
         $formdata['expiry_date'] = strtotime(trim($_POST["expiry_date"]));
        //changing the format
        
       $newformat_expirydate = date('Y-m-d',$formdata['expiry_date']);
    }

   
    if(empty($_POST["medicine_sale_price_per_unit"]))
    {
        $error .= '<li>Sale Price per unit is required</li>';
    }
    else
    {
        $formdata['medicine_sale_price_per_unit'] = trim($_POST["medicine_sale_price_per_unit"]);
    }

    if($error == '')
    {
        $medicine_purchase_id = $object->convert_data(trim($_POST["medicine_purchase_id"]), 'decrypt');

        $object->query = "
        SELECT medicine_purchase_qty FROM medicine_purchase_msbs 
        WHERE medicine_purchase_id = '".$medicine_purchase_id."'
        ";

        $temp_result = $object->get_result();

        $medicine_purchase_qty = 0;

        foreach($temp_result as $temp_row)
        {
            $medicine_purchase_qty = $temp_row["medicine_purchase_qty"];
        }

        $total_cost = floatval($formdata['medicine_purchase_qty']) * floatval($formdata['medicine_purchase_price_per_unit']);

        $data = array(
            ':medicine_id'                      =>  $formdata['medicine_id'],
            ':supplier_id'                      =>  $formdata['supplier_id'],
            ':medicine_batch_no'                =>  $formdata['medicine_batch_no'],
            ':medicine_purchase_qty'            =>  $formdata['medicine_purchase_qty'],
            ':available_quantity'               =>  $formdata['medicine_purchase_qty'], 
            ':medicine_purchase_price_per_unit' =>  $formdata['medicine_purchase_price_per_unit'],
            ':medicine_purchase_total_cost'     =>  $total_cost,
            ':date_manufactured'       =>  $newformat_manufacturingdate,
            ':expiry_date'        =>  $newformat_expirydate,
            ':medicine_sale_price_per_unit'     =>  $formdata['medicine_sale_price_per_unit'],
            ':medicine_purchase_id'             =>  $medicine_purchase_id
        );

        $object->query = "
            UPDATE medicine_purchase_msbs 
            SET medicine_id = :medicine_id, 
            supplier_id = :supplier_id,
            medicine_batch_no = :medicine_batch_no, 
            medicine_purchase_qty = :medicine_purchase_qty, 
            available_quantity = :available_quantity, 
            medicine_purchase_price_per_unit = :medicine_purchase_price_per_unit, 
            medicine_purchase_total_cost = :medicine_purchase_total_cost, 
            date_manufactured = :date_manufactured, 
            expiry_date = :expiry_date, 
           medicine_sale_price_per_unit = :medicine_sale_price_per_unit  
            WHERE medicine_purchase_id = :medicine_purchase_id
            ";

        $object->execute($data);

        if($medicine_purchase_qty != $formdata['medicine_purchase_qty'])
        {
            $final_update_qty = 0;
            if($medicine_purchase_qty > $formdata['medicine_purchase_qty'])
            {
                $final_update_qty = $medicine_purchase_qty - $formdata['medicine_purchase_qty'];

                $object->query = "
                UPDATE medicine_msbs 
                SET medicine_available_quantity = medicine_available_quantity - ".$final_update_qty." 
                WHERE medicine_id = '".$formdata['medicine_id']."'
                ";
            }
            else
            {
                $final_update_qty = $formdata['medicine_purchase_qty'] - $medicine_purchase_qty;

                $object->query = "
                UPDATE medicine_msbs 
                SET medicine_available_quantity = medicine_available_quantity + ".$final_update_qty." 
                WHERE medicine_id = '".$formdata['medicine_id']."'
                ";
            }

            $object->execute();
        }

        header('location:medicine_purchase.php?msg=edit');
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $medicine_purchase_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

    $medicine_id = $object->convert_data(trim($_GET["id"]), 'decrypt');

    $object->query = "
    SELECT medicine_purchase_qty FROM medicine_purchase_msbs 
    WHERE medicine_purchase_id = '".$medicine_purchase_id."'
    ";

    $temp_result = $object->get_result();

    $medicine_purchase_qty = 0;

    foreach($temp_result as $temp_row)
    {
        $medicine_purchase_qty = $temp_row["medicine_purchase_qty"];
    }

    $status = trim($_GET["status"]);
    $data = array(
        ':medicine_purchase_status'      =>  $status,
        ':medicine_purchase_id'          =>  $medicine_purchase_id
    );

    $object->query = "
    UPDATE medicine_purchase_msbs 
    SET medicine_purchase_status = :medicine_purchase_status 
    WHERE medicine_purchase_id = :medicine_purchase_id
    ";

    $object->execute($data);

    if($status == 'Disable')
    {
        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity - ".$medicine_purchase_qty." 
        WHERE medicine_id = '".$medicine_id."'
        ";
    }
    else
    {
        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity + ".$medicine_purchase_qty." 
        WHERE medicine_id = '".$medicine_id."'
        ";
    }

    $object->execute();

    header('location:medicine_purchase.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Drug Purchase Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="medicine_purchase.php">Drug Purchase Management</a></li>
                                <li class="breadcrumb-item active">Add Drug Purchase</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Add Drug Purchase
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                        <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="supplier_id" class="form-control" id="supplier_id">
                                                        <?php echo $object->fill_supplier(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["supplier_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("supplier_id").value = "'.$_POST["supplier_id"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="supplier_id">Supplier Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="medicine_id" class="form-control" id="medicine_id">
                                                        <?php echo $object->fill_medicine(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["medicine_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("medicine_id").value = "'.$_POST["medicine_id"].'"
                                                        </script>
                                                        ';
                                                    }

                                                    if(isset($_GET["medicine"]))
                                                    {
                                                        $medicine_id = $object->convert_data(trim($_GET["medicine"]), 'decrypt');
                                                        echo '
                                                        <script>
                                                        document.getElementById("medicine_id").value = "'.$medicine_id.'"
                                                        </script>
                                                        ';
                                                    }

                                                    ?>
                                                    <label for="medicine_id">Drug Name</label>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_purchase_qty" type="number" placeholder="Enter Quantity" name="medicine_purchase_qty" value="<?php if(isset($_POST["medicine_purchase_qty"])) echo $_POST["medicine_purchase_qty"]; ?>" />
                                                    <label for="medicine_purchase_qty">Quantity</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_purchase_price_per_unit" type="number" placeholder="Enter Purchase Price per Unit" name="medicine_purchase_price_per_unit" step=".01" value="<?php if(isset($_POST["medicine_purchase_price_per_unit"])) echo $_POST["medicine_purchase_price_per_unit"]; ?>" />
                                                    <label for="medicine_purchase_price_per_unit">Purchase Price per Unit</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" >
                                                
                                                <input class="form-control datepicker-here " data-language="en"  type="text" placeholder="Enter Manufacturing Date"   id="manufacturing_date" name="manufacturing_date" value="<?php if(isset($_POST["manufacturing_date"])) echo $_POST["manufacturing_date"]; ?>">
                                              
                                                         <label for="manufacturing_date">Manufacturing Date (Mfg).</label>
                                                   
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control datepicker-here"  data-language="en"  type="text" id="expiry_date" name="expiry_date" placeholder="Enter Expiry date"   value="<?php if(isset($_POST["expiry_date"])) echo $_POST["expiry_date"]; ?>" />
                                                    <label for="expiry_date">Expiry Date:</label>
                                              

                                                </div>
                                            </div>
                                        </div>



                                    
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_batch_no" type="text" placeholder="Enter Batch Number" name="medicine_batch_no" value="<?php if(isset($_POST["medicine_batch_no"])) echo $_POST["medicine_batch_no"]; ?>" />
                                                    <label for="medicine_batch_no">Drug Batch No.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="medicine_sale_price_per_unit" type="number" placeholder="Enter Sale Price per Unit" name="medicine_sale_price_per_unit" step=".01" value="<?php if(isset($_POST["medicine_sale_price_per_unit"])) echo $_POST["medicine_sale_price_per_unit"]; ?>" />
                                                    <label for="medicine_sale_price_per_unit">Sale Price per Unit</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="add_medicine_purchase" class="btn btn-success" value="Add" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $medicine_purchase_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($medicine_purchase_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM medicine_purchase_msbs 
                                    WHERE medicine_purchase_id = '$medicine_purchase_id'
                                    ";

                                    $medicine_purchase_result = $object->get_result();

                                    foreach($medicine_purchase_result as $medicine_purchase_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="medicine_purchase.php">Medicine Purchase Management</a></li>
                                    <li class="breadcrumb-item active">Edit Medicine Purchase Data</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Edit Drug Purchase Data
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="medicine_id" class="form-control" id="medicine_id">
                                                        <?php echo $object->fill_medicine(); ?>
                                                        </select>
                                                        <label for="medicine_id">Drug Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="supplier_id" class="form-control" id="supplier_id">
                                                        <?php echo $object->fill_supplier(); ?>
                                                        </select>
                                                        <label for="supplier_id">Supplier Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <div class="form-floating mb-3">
                                                            <input class="form-control" id="medicine_purchase_qty" type="number" placeholder="Enter Quantity" name="medicine_purchase_qty" value="<?php echo $medicine_purchase_row["medicine_purchase_qty"]; ?>" />
                                                            <label for="medicine_purchase_qty">Drug Quantity</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_purchase_price_per_unit" type="number" placeholder="Enter Purchase Price per Unit" name="medicine_purchase_price_per_unit" step=".01" value="<?php echo $medicine_purchase_row["medicine_purchase_price_per_unit"]; ?>" />
                                                        <label for="medicine_purchase_price_per_unit">Purchase Price per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-6">
                                                 <div class="form-floating mb-3" >
                                                    <input class="form-control datepicker-here"   data-language="en"  type="text" placeholder="Enter Manufacturing Date"   id="manufacturing_date" name="manufacturing_date" value="<?php echo $medicine_purchase_row["date_manufactured"]; ?>" />
                                               
                                                   <label for="manufacturing_date">Manufacturing Date (Mfg).</label>
                                                   

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" id="dp3">
                                                    <input class="form-control datepicker-here"  data-language="en"  type="text" id="expiry_date" name="expiry_date" placeholder="Enter Expiry date"   value="<?php echo $medicine_purchase_row["expiry_date"]; ?>" />
                                                    <label for="expiry_date">Expiry Date:</label>
                                               

                                                </div>
                                            </div>
                                        </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_batch_no" type="text" placeholder="Enter Batch Number" name="medicine_batch_no" value="<?php echo $medicine_purchase_row["medicine_batch_no"]; ?>" />
                                                        <label for="medicine_batch_no">Medicine Batch No.</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="medicine_sale_price_per_unit" type="number" placeholder="Enter Sale Price per Unit" name="medicine_sale_price_per_unit" step=".01" value="<?php echo $medicine_purchase_row["medicine_sale_price_per_unit"]; ?>" />
                                                        <label for="medicine_sale_price_per_unit">Sale Price per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="medicine_purchase_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_medicine_purchase" class="btn btn-primary" value="Update" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <script>
                                document.getElementById('medicine_id').value = "<?php echo $medicine_purchase_row["medicine_id"]; ?>";
                                document.getElementById('supplier_id').value = "<?php echo $medicine_purchase_row["supplier_id"]; ?>";
                                document.getElementById('manufacturing_date').value = "<?php echo $medicine_purchase_row["date_manufactured"]; ?>";
                                document.getElementById('expiry_date').value = "<?php echo $medicine_purchase_row["expiry_date"]; ?>";
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
                                <li class="breadcrumb-item active">Drug Purchase Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Drug Purchase Detail Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Drug Purchase Data was Updated Successfully! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                               
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Drug Purchase Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="medicine_purchase.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> New Purchase</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                            <th>S/N</th>

                                                <th>Drug Info</th>
                                              
                                                <th>Quantity</th>
                                                <th>Available Qty.</th>
                                                <th>Price per Unit</th>
                                                <th>Total Cost</th>
                                                <th>Date Mfg.</th>
                                                <th>Expiry Date</th>
                                                <th>Sale Price</th>
                                                <th>Purchase Date</th>
                                           
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                       
                                        <tbody>
                                        <?php
                                         $cnt=1;
                                        foreach($result as $row)
                                        {
                                         

 ?>
                                            <tr  class="process_purchase<?php echo $row['medicine_purchase_id']?>">
                                                <td><?php echo $cnt;?></td>
                                                <td>
                                                <p><small>Name : <b><?php echo $row['medicine_name'] ?></b></small></p>
										<p><small>Supplier: <b><?php echo $row['supplier_name'] ?></b></small></p>

										<p><small>Batch No: <b><?php echo $row['medicine_batch_no'] ?></b></small></p>
									

                                              </td>
                                                     <td><?php echo $row["medicine_purchase_qty"];?></td>
                                                   <td> <?php echo $row["available_quantity"]?></td>
                                                   <td><?php echo $object->cur_sym . $row["medicine_purchase_price_per_unit"] ?></td>
                                                <td><?php echo $object->cur_sym . $row["medicine_purchase_total_cost"] ?></td>
                                            <td><?php echo date("M d, Y",strtotime($row['date_manufactured'])); ?></td>
                                                  <td><?php echo date("M d, Y",strtotime($row['expiry_date'])); ?></td>


                                                <td><?php echo $object->cur_sym . $row["medicine_sale_price_per_unit"] ?></td>
                                                <td><?php echo $row["medicine_purchase_datetime"] ?></td>


                                              
                                             
                                                <td width="100">
                                                   <a title="Edit drug purchased" href="medicine_purchase.php?action=edit&code=<?php echo $object->convert_data($row["medicine_purchase_id"]) ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                                    <button title="delete drug purchased" class="btn  btn-sm btn-icon btn-danger btn-delete" id="<?php echo $row['medicine_purchase_id']?>" type="button"><i class="fa fa-trash"></i></button>
				
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
							
					<center><h5 class="text-danger">Are you sure you want to delete this Purchased Drug Details?</h5></center>
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
		var medicine_purchase_id = $(this).attr('id');
		$("#modal_confirm3").modal('show');
		$('#btn_yesdel').attr('name', medicine_purchase_id);
	});
	$('#btn_yesdel').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "delete_drug_purchased.php",
			data:{
				medicine_purchase_id: id
			},
			success: function(){
				$("#modal_confirm3").modal('hide');
				$(".process_purchase" + id).empty();
				$(".process_purchase" + id).html("<td colspan='12'><center class='text-danger'>Deleting Drug Purchased...</center></td>");
			setTimeout(' window.location.href = "medicine_purchase.php"; ',1000);
			}
		});
	});
});
</script>

