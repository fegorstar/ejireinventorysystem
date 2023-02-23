<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}

$where_condition = '';

if(!$object->is_master_user())
{
    $where_condition = "
    WHERE order_msbs.order_created_by = '".$_SESSION["user_id"]."' 
    ";
}

$object->query = "
    SELECT * FROM order_msbs 
    INNER JOIN user_msbs 
    ON user_msbs.user_id = order_msbs.order_created_by 
    ".$where_condition."
    ORDER BY order_id DESC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_order"]))
{
    $formdata = array();

    if(empty($_POST["patient_name"]))
    {
        $error .= '<li>Patient Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["patient_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['patient_name'] = trim($_POST["patient_name"]);
        }
    }

    if(empty($_POST["category"]))
    {
        $error .= '<li>category is required</li>';
    }
    else
    {
                   $formdata['category'] = trim($_POST["category"]);
        
    }

    if($error == '')
    {
        $data = array(
            ':patient_name'                =>  $formdata['patient_name'],
            ':category'                 =>  $formdata['category'],
            ':order_total_amount'          =>  $_POST['order_total_amount'],
            ':order_created_by'            =>  $_SESSION['user_id'],
              ':order_added_on'              =>  $object->now,
            ':order_updated_on'            =>  $object->now
        );

        $object->query = "
        INSERT INTO order_msbs 
        (patient_name, category, order_total_amount, order_created_by, order_added_on, order_updated_on) 
        VALUES (:patient_name, :category, :order_total_amount, :order_created_by, :order_added_on, :order_updated_on)
        ";

        $object->execute($data);

        $order_id = $object->connect->lastInsertId();

        $medicine_id = $_POST["medicine_id"];

        $medicine_purchase_id = $_POST["medicine_purchase_id"];

        $medicine_quantity = $_POST["medicine_quantity"];

        $medicine_price = $_POST["medicine_price"];

        if(count($medicine_id) > 0)
        {
            for($i = 0; $i < count($medicine_id); $i++)
            {
                $sub_data = array(
                    ':order_id'             =>  $order_id,
                    ':medicine_id'          =>  $medicine_id[$i],
                    ':medicine_purchase_id' =>  $medicine_purchase_id[$i],
                    ':medicine_quantity'    =>  $medicine_quantity[$i],
                    ':medicine_price'       =>  $medicine_price[$i]
                );

                $object->query = "
                INSERT INTO order_item_msbs 
                (order_id, medicine_id, medicine_purchase_id, medicine_quantity, medicine_price) 
                VALUES(:order_id, :medicine_id, :medicine_purchase_id, :medicine_quantity, :medicine_price)
                ";

                $object->execute($sub_data);

                $object->query = "
                UPDATE medicine_purchase_msbs 
                SET available_quantity = available_quantity - ".$medicine_quantity[$i]." 
                WHERE medicine_purchase_id = '".$medicine_purchase_id[$i]."'
                ";

                $object->get_result();

                $object->query = "
                UPDATE medicine_msbs 
                SET medicine_available_quantity = medicine_available_quantity - ".$medicine_quantity[$i]." 
                WHERE medicine_id = '".$medicine_id[$i]."'
                ";

                $object->get_result();

            }
        }

        header('location:order.php?msg=add');
    }
}

if(isset($_GET["action"], $_GET["item_code"], $_GET["order_code"]) && $_GET["action"] == 'remove_item')
{
    $order_item_id = $object->convert_data(trim($_GET["item_code"]), 'decrypt');

    $object->query = "
    SELECT * FROM order_item_msbs 
    WHERE order_item_id = '".$order_item_id."'
    ";

    $item_result = $object->get_result();

    foreach($item_result as $item_row)
    {
        $medicine_id = $item_row["medicine_id"];
        $medicine_purchase_id = $item_row["medicine_purchase_id"];
        $medicine_quantity = $item_row["medicine_quantity"];
        $medicine_price = $item_row["medicine_price"];
        $object->query = "
        DELETE FROM order_item_msbs 
        WHERE order_item_id = '".$order_item_id."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE order_msbs 
        SET order_total_amount = order_total_amount - ".$medicine_quantity * $medicine_price ." 
        WHERE order_id = '".$item_row["order_id"]."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE medicine_purchase_msbs 
        SET available_quantity = available_quantity + ".$medicine_quantity." 
        WHERE medicine_purchase_id = '".$medicine_purchase_id."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity + ".$medicine_quantity." 
        WHERE medicine_id = '".$medicine_id."'
        ";

        $object->get_result();
    }

    header('location:order.php?action=edit&code='.$_GET["order_code"].'');

}

if(isset($_POST["edit_order"]))
{
    $formdata = array();

    if(empty($_POST["patient_name"]))
    {
        $error .= '<li>Patient Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["patient_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['patient_name'] = trim($_POST["patient_name"]);
        }
    }

    if(empty($_POST["category"]))
    {
        $error .= '<li>category is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["category"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['category'] = trim($_POST["category"]);
        }
    }

    if($error == '')
    {
        $order_id = $object->convert_data(trim($_POST["order_id"]), 'decrypt');

        $data = array(
            ':patient_name'         =>  $formdata['patient_name'],
            ':category'          =>  $formdata['category'],
            ':order_total_amount'   =>  $_POST["order_total_amount"],
            ':order_updated_on'     =>  $object->now,
            ':order_id'             =>  $order_id
        );

        $object->query = "
        UPDATE order_msbs 
        SET patient_name = :patient_name, 
        category = :category, 
        order_total_amount = :order_total_amount, 
        order_updated_on = :order_updated_on 
        WHERE order_id = :order_id
        ";

        $object->execute($data);

        $medicine_id = $_POST["medicine_id"];
        $medicine_purchase_id = $_POST["medicine_purchase_id"];
        $medicine_quantity = $_POST["medicine_quantity"];
        $medicine_price = $_POST["medicine_price"];

        for($i = 0; $i < count($medicine_id); $i++)
        {
            $object->query = "
            SELECT * FROM order_item_msbs 
            WHERE order_id = '".$order_id."' 
            AND medicine_id = '".$medicine_id[$i]."' 
            AND medicine_purchase_id = '".$medicine_purchase_id[$i]."'
            ";

            $order_item_result = $object->get_result();

            foreach($order_item_result as $order_item_row)
            {
                $medicineid = $order_item_row["medicine_id"];
                $medicinepurchaseid = $order_item_row["medicine_purchase_id"];
                $medicinequantity = $order_item_row["medicine_quantity"];
                $medicineprice = $order_item_row["medicine_price"];

                if($medicinequantity != $medicine_quantity[$i])
                {
                    $data = array(
                        ':medicine_quantity'    =>  $medicine_quantity[$i],
                        ':order_item_id'        =>  $order_item_row['order_item_id']
                    );
                    $object->query = "
                    UPDATE order_item_msbs 
                    SET medicine_quantity = :medicine_quantity 
                    WHERE order_item_id = :order_item_id
                    ";

                    $object->execute($data);

                    $final_update_qty = 0;
                    if($medicinequantity > $medicine_quantity[$i])
                    {
                        $final_update_qty = $medicinequantity - $medicine_quantity[$i];

                        $object->query = "
                        UPDATE medicine_purchase_msbs 
                        SET available_quantity = available_quantity + ".$final_update_qty." 
                        WHERE medicine_purchase_id = '".$medicine_purchase_id[$i]."'
                        ";

                        $object->execute();

                        $object->query = "
                        UPDATE medicine_msbs 
                        SET medicine_available_quantity = medicine_available_quantity + ".$final_update_qty." 
                        WHERE medicine_id = '".$medicine_id[$i]."'
                        ";

                        $object->execute();
                        
                    }
                    else
                    {
                        $final_update_qty = $medicine_quantity[$i] - $medicinequantity;

                        $object->query = "
                        UPDATE medicine_purchase_msbs 
                        SET available_quantity = available_quantity - ".$final_update_qty." 
                        WHERE medicine_purchase_id = '".$medicine_purchase_id[$i]."'
                        ";

                        $object->execute();

                        $object->query = "
                        UPDATE medicine_msbs 
                        SET medicine_available_quantity = medicine_available_quantity - ".$final_update_qty." 
                        WHERE medicine_id = '".$medicine_id[$i]."'
                        ";

                        $object->execute();
                    }
                }
            }
        }

        header('location:order.php?msg=edit');

    }
}




if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $order_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

    $object->query = "
    SELECT * FROM order_item_msbs 
    WHERE order_id = '".$order_id."'
    ";

    $item_result = $object->get_result();

    foreach($item_result as $item_row)
    {
        $object->query = "
        UPDATE medicine_purchase_msbs 
        SET available_quantity = available_quantity + ".$item_row['medicine_quantity']." 
        WHERE medicine_purchase_id = '".$item_row['medicine_purchase_id']."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE medicine_msbs 
        SET medicine_available_quantity = medicine_available_quantity + ".$item_row['medicine_quantity']." 
        WHERE medicine_id = '".$item_row['medicine_id']."'
        ";

        $object->get_result();
    }

    $object->query = "
    DELETE FROM order_item_msbs 
    WHERE order_id = '".$order_id."'
    ";

    $object->execute();

    $object->query = "
    DELETE FROM order_msbs 
    WHERE order_id = '".$order_id."'
    ";

    $object->execute();

    header('location:order.php?msg=delete');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Sales Lists</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="order.php">Sales Management</a></li>
                                <li class="breadcrumb-item active">Add Sales</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>
                            <span id="msg_area"></span>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> New Sales
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                        <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   <select name="category" id="" class="form-control" >
                                                  
 									<option value="" ></option>
									<option value="Guest" >Guest</option>
									<option value="Patient" >Patient</option>
								</select>
								    <label for="doctor_name">Select Category</label>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="patient_name" type="text" placeholder="Enter Customer Name" name="patient_name" value="<?php if(isset($_POST["patient_name"])) echo $_POST["patient_name"]; ?>" />
                                                    <label for="patient_name">Customer Name</label>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <select class="form-control" id="add_medicine_id">
                                                    <?php echo $object->get_medicine_array(); ?>
                                                </select>
                                                &nbsp;
                                                <button type="button" name="add_medicine" id="add_medicine" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add to List</button>
                                            </div>
                                            <div class="col-md-6">
                                                
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="30%">Drug</th>
                                                        <th width="10%">Packet Size</th>
                                                        <th width="5%">Mfg</th>
                                                        <th width="10%">Batch No.</th>
                                                        <th width="10%">Expiry Date</th>
                                                        <th width="10%">Quantity</th>
                                                        <th width="10%">Unit Price</th>
                                                        <th width="10%">Total Price</th>
                                                        <th width="5%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="order_item_area">
                                                <?php
                                                if(isset($_POST["medicine_purchase_id"]))
                                                {
                                                    if(count($_POST["medicine_purchase_id"]) > 0)
                                                    {
                                                        $medicine_purchase_id = $_POST["medicine_purchase_id"];
                                                        for($i = 0; $i < count($medicine_purchase_id); $i++)
                                                        {
                                                            $data = array(
                                                                ':medicine_purchase_id' =>  $medicine_purchase_id[$i]
                                                            );

                                                            $object->query = "
                                                            SELECT * FROM medicine_purchase_msbs 
                                                            INNER JOIN medicine_msbs 
                                                            ON medicine_msbs.medicine_id =  medicine_purchase_msbs.medicine_id 
                                                            WHERE medicine_purchase_msbs.medicine_purchase_id = :medicine_purchase_id";

                                                            $object->execute($data);

                                                            $order_result = $object->statement_result();

                                                            $data = array();

                                                            foreach($order_result as $order_row)
                                                            {
                                                                echo '
                                                                <tr>
                                                                    <td>'.$order_row['medicine_name'].'<input type="hidden" name="medicine_id[]" value="'.$order_row['medicine_id'].'" /><input type="hidden" name="medicine_purchase_id[]" value="'.$order_row['medicine_purchase_id'].'" /></td>
                                                                    <td>'.$order_row['pk_size'].'</td>
                                                                    <td>'.$order_row['date_manufactured'].'</td>
                                                                    <td>'.$order_row['medicine_batch_no'].'</td>
                                                                    <td>'.$order_row['expiry_date'].'</td>
                                                                    <td><input type="number" name="medicine_quantity[]" class="form-control medicine_quantity" placeholder="Quantity" value="'.$_POST["medicine_quantity"][$i].'" min="1" onblur="calculate_total()" /></td>
                                                                    <td><span class="item_unit_price">'.$order_row['medicine_sale_price_per_unit'].'</span><input type="hidden" name="medicine_price[]" value="'.$order_row['medicine_sale_price_per_unit'].'" /></td>
                                                                    <td><span class="item_total_price">'.$order_row['medicine_sale_price_per_unit'] * $_POST["medicine_quantity"][$i] .'</span></td>
                                                                    <td><button type="button" name="remove_item" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fas fa-minus"></i></button></td>
                                                                </tr>
                                                                ';
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="7" align="right"><b>Total</b></td>
                                                        <td colspan="2" id="order_total_amount"><?php if(isset($_POST["order_total_amount"])) echo $_POST["order_total_amount"]; ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="hidden" name="order_total_amount" id="hidden_order_total_amount" value="<?php if(isset($_POST["order_total_amount"])) echo $_POST["order_total_amount"]; ?>" />
                                            <input type="submit" name="add_order" class="btn btn-success" value="Pay" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <script>

                            function _(element)
                            {
                                return document.getElementById(element);
                            }

                            let mySelect = new vanillaSelectBox('#add_medicine_id', {
                                maxWidth: 600,
                                maxHeight: 400,
                                minWidth: 500,
                                search: true,
                                placeHolder: "Filter Drug",
                                minOptionWidth: 500,
                                maxOptionWidth: 600
                            });

                            _('add_medicine').onclick = function()
                            {
                                var med_id = _('add_medicine_id').value;
                                console.log(med_id);
                                if(med_id == '')
                                {
                                    _('msg_area').innerHTML = '<div class="alert alert-danger">Please Select Drug</div>';
                                    setTimeout(function(){
                                        _('msg_area').innerHTML = '';
                                    }, 5000);
                                }
                                else
                                {
                                    var form_data = new FormData();
                                    form_data.append('med_id', med_id);
                                    form_data.append('action', 'fetch_medicine_data');
                                    fetch('action.php', {

                                        method:"POST",

                                        body:form_data

                                    }).then(function(response){

                                        return response.json();

                                    }).then(function(responseData){
                                        console.log(responseData);
                                        mySelect.empty();
                                        if(responseData.available_quantity == 0)
                                        {
                                            _('msg_area').innerHTML = '<div class="alert alert-danger">This Medicine Quantity Not Available</div>';
                                            setTimeout(function(){
                                                _('msg_area').innerHTML = '';
                                            }, 5000);
                                        }
                                        else
                                        {
                                            mySelect.disableItems(med_id);
                                            var no = random_number(100, 999);
                                            var html = '<tr id="'+no+'">';
                                            html += '<td>'+responseData.medicine_name+'<input type="hidden" name="medicine_id[]" value="'+responseData.medicine_id+'" /><input type="hidden" name="medicine_purchase_id[]" value="'+responseData.medicine_purchase_id+'" /></td>';
                                            html += '<td>'+responseData.pk_size+'</td>';
                                            html += '<td>'+responseData.date_manufactured+'</td>';
                                            html += '<td>'+responseData.medicine_batch_no+'</td>';
                                            html += '<td>'+responseData.expiry_date+'</td>';
                                            html += '<td><input type="number" name="medicine_quantity[]" class="form-control medicine_quantity" placeholder="Quantity" value="1" min="1" onblur="check_qty(this); calculate_total();" data-number="'+no+'" /></td>';
                                            html += '<td><span class="item_unit_price">'+responseData.medicine_sale_price_per_unit+'</span><input type="hidden" name="medicine_price[]" value="'+responseData.medicine_sale_price_per_unit+'" /></td>';
                                            html += '<td><span class="item_total_price" id="item_total_price_'+no+'">'+responseData.medicine_sale_price_per_unit+'</span></td>';
                                            html += '<td><button type="button" name="remove_item" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fas fa-minus"></i></button></td>';
                                            html += '</tr>';

                                            var data = document.getElementById('order_item_area');

                                            data.insertRow().innerHTML = html;

                                            calculate_total();
                                        }

                                    }); 
                                }                                

                            }

                            function deleteRow(btn) {
                                var row = btn.parentNode.parentNode;
                                row.parentNode.removeChild(row);
                                calculate_total();
                            }

                            function random_number(min, max) {
                                min = Math.ceil(min);
                                max = Math.floor(max);
                                return Math.floor(Math.random() * (max - min + 1)) + min;
                            }

                            function calculate_total()
                            {
                                var qty = document.getElementsByClassName('medicine_quantity');
                                var unit_price = document.getElementsByClassName('item_unit_price');
                                var total_price = document.getElementsByClassName('item_total_price');

                                var total = 0;

                                if(qty.length > 0)
                                {
                                    for(var i = 0; i < qty.length; i++)
                                    {
                                        console.log('Qty - ' + qty[i].value);
                                        console.log('Unit Price - ' + unit_price[i].innerHTML);
                                        var temp_total_price = parseFloat(qty[i].value) * parseFloat(unit_price[i].innerHTML);
                                        total_price[i].innerHTML = temp_total_price.toFixed(2);
                                        total = parseFloat(total) + parseFloat(temp_total_price);
                                    }
                                }
                                _('hidden_order_total_amount').value = total;
                                _('order_total_amount').innerHTML = total.toFixed(2);
                            }

                            function check_qty(element)
                            {
                                var min = element.min;
                                if(parseInt(element.value) < min)
                                {
                                    element.value = min;
                                }
                                if(element.value == '')
                                {
                                    element.value = min;
                                }
                            }

                            

                            </script>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $order_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($order_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM order_msbs 
                                    WHERE order_id = '$order_id'
                                    ";

                                    $order_result = $object->get_result();

                                    foreach($order_result as $order_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="order.php">Order Management</a></li>
                                    <li class="breadcrumb-item active">Edit Order Data</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Edit Order Data
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="patient_name" type="text" placeholder="Enter Customer Name" name="patient_name" value="<?php echo $order_row["patient_name"]; ?>" />
                                                        <label for="patient_name">Customer Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                   <select name="category" id="" class="form-control" >
                                                  
 									<option value="" ></option>
									<option value="Guest" >Guest</option>
									<option value="Patient" >Patient</option>
								</select>
								    <label for="doctor_name">Select Category</label>

                                                </div>
                                            </div>

                                            </div>
                                           
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                           <th width="30%">Drug</th>
                                                        <th width="10%">Packet Size</th>
                                                        <th width="5%">Mfg</th>
                                                        <th width="10%">Batch No.</th>
                                                        <th width="10%">Expiry Date</th>
                                                        <th width="10%">Quantity</th>
                                                        <th width="10%">Unit Price</th>
                                                        <th width="10%">Total Price</th>
                                                        <th width="5%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="order_item_area">
                                                    <?php

                                                    $object->query = "
                                                    SELECT * FROM order_item_msbs 
                                                    WHERE order_id = '".$order_row["order_id"]."'
                                                    ";

                                                    $order_item_result = $object->get_result();

                                                    foreach($order_item_result as $order_item_row)
                                                    {
                                                        $object->query = "
                                                            SELECT * FROM medicine_purchase_msbs 
                                                            INNER JOIN medicine_msbs 
                                                            ON medicine_msbs.medicine_id =  medicine_purchase_msbs.medicine_id 
                                                            WHERE medicine_purchase_msbs.medicine_purchase_id = '".$order_item_row['medicine_purchase_id']."'
                                                        ";

                                                        $medicine_pur_result = $object->get_result();

                                                        foreach($medicine_pur_result as $medicine_pur_row)
                                                        {
                                                            echo '
                                                            <tr>
                                                                <td>'.$medicine_pur_row['medicine_name'].'<input type="hidden" name="medicine_id[]" value="'.$order_item_row['medicine_id'].'" /><input type="hidden" name="medicine_purchase_id[]" value="'.$order_item_row['medicine_purchase_id'].'" /></td>
                                                                  <td>'.$medicine_pur_row['pk_size'].'</td>
                                                                    <td>'.$medicine_pur_row['date_manufactured'].'</td>
                                                                    <td>'.$medicine_pur_row['medicine_batch_no'].'</td>
                                                                  <td>'.$medicine_pur_row['expiry_date'].'</td>
                                                                 <td><input type="number" name="medicine_quantity[]" class="form-control medicine_quantity" placeholder="Quantity" value="'.$order_item_row["medicine_quantity"].'" min="1" onblur="check_qty(this); calculate_total();" /></td>
                                                                <td><span class="item_unit_price">'.$medicine_pur_row['medicine_sale_price_per_unit'].'</span><input type="hidden" name="medicine_price[]" value="'.$order_item_row['medicine_price'].'" /></td>
                                                                <td><span class="item_total_price">'.number_format($medicine_pur_row['medicine_sale_price_per_unit'] * $order_item_row["medicine_quantity"], 2).'</span></td>
                                                                <td><button type="button" name="remove_item" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($order_item_row["order_item_id"]).'`, `'.$_GET["code"].'`);"><i class="fas fa-minus"></i></button></td>
                                                            </tr>                                                            ';
                                                        }
                                                    }
                                                    ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="7" align="right"><b>Total</b></td>
                                                            <td colspan="2" id="order_total_amount"><?php echo $order_row["order_total_amount"]; ?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="order_total_amount" id="hidden_order_total_amount" value="<?php echo $order_row["order_total_amount"]; ?>" />
                                                <input type="hidden" name="order_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_order" class="btn btn-primary" value="Update" />
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <script>
                                    function _(element)
                                    {
                                        return document.getElementById(element);
                                    }

                                    function calculate_total()
                                    {
                                        var qty = document.getElementsByClassName('medicine_quantity');
                                        var unit_price = document.getElementsByClassName('item_unit_price');
                                        var total_price = document.getElementsByClassName('item_total_price');

                                        var total = 0;

                                        if(qty.length > 0)
                                        {
                                            for(var i = 0; i < qty.length; i++)
                                            {
                                                console.log('Qty - ' + qty[i].value);
                                                console.log('Unit Price - ' + unit_price[i].innerHTML);
                                                var temp_total_price = parseFloat(qty[i].value) * parseFloat(unit_price[i].innerHTML);
                                                total_price[i].innerHTML = temp_total_price.toFixed(2);
                                                total = parseFloat(total) + parseFloat(temp_total_price);
                                            }
                                        }
                                        _('hidden_order_total_amount').value = total;
                                        _('order_total_amount').innerHTML = total.toFixed(2);
                                    }

                                    function check_qty(element)
                                    {
                                        var min = element.min;
                                        if(parseInt(element.value) < min)
                                        {
                                            element.value = min;
                                        }
                                        if(element.value == '')
                                        {
                                            element.value = min;
                                        }
                                    }

                                    function delete_data(item_code, order_code)
                                    {
                                        if(confirm("Are you sure you want this medicine from Order?"))
                                        {
                                            window.location.href="order.php?action=remove_item&item_code="+item_code+"&order_code="+order_code+"";
                                        }
                                    }
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
                                <li class="breadcrumb-item active">Order Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Order Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Order Data Updated <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                                           }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Order Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="order.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> New Sales</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>Ref No.</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Amount Paid</th>
                                                <th>Created By</th>
                                               
                                                <th>Date Added</th>
                                                <th>Date Updated</th>
                                                <th>Action</th>
                                            </tr>
                                       
                                        <tbody>
                                        <?php
                                        foreach($result as $row)
                                        {
                                  
                                           $date_purchased=  date("M d, Y",strtotime($row['order_added_on']));
                                           $date_updated=  date("M d, Y",strtotime($row['order_updated_on']));  
                                           
 ?>
                                            <tr  class="process_order<?php echo $row['order_id']?>">
                                                <td><?php echo $row["order_id"] ?></td>
                                                <td><?php echo $row["patient_name"]; ?></td>
                                                <td><?php echo $row["category"]; ?></td>
                                                <td> <?php echo $object->cur_sym . $row["order_total_amount"] ?></td>
                                                <td><?php echo $row["user_name"];?></td>
                                              
                                                <td><?php echo $date_purchased; ?></td>
                                                <td><?php echo $date_updated; ?></td>
                                                <td width="200">
                                                
                                               
                                                    <a href="print_order.php?action=pdf&code=<?php echo $object->convert_data($row["order_id"]) ?>" class="btn-warning btn btn-sm" target="_blank">Print</a>
                                                    <a title="Edit order" href="order.php?action=edit&code=<?php echo $object->convert_data($row["order_id"]) ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                               <button title="delete order" class="btn  btn-sm btn-icon btn-danger btn-delete" id="<?php echo $row['order_id']?>" type="button"><i class="fa fa-trash"></i></button>
				
                                                </td>
                                            </tr>
                                            
                                       
                                        </tbody>
                                         <?php 
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

 
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
                                                               
 <!-- modal to delete file--> 
     <div class="modal fade" id="modal_confirm3" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
							<div class="modal-body">
							
					<center><h5 class="text-danger">Are you sure you want to delete this Order?</h5></center>
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
		var order_id = $(this).attr('id');
		$("#modal_confirm3").modal('show');
		$('#btn_yesdel').attr('name', order_id);
	});
	$('#btn_yesdel').on('click', function(){
		var id = $(this).attr('name');
		$.ajax({
			type: "POST",
			url: "delete_order.php",
			data:{
				order_id: id
			},
			success: function(){
				$("#modal_confirm3").modal('hide');
				$(".process_order" + id).empty();
				$(".process_order" + id).html("<td colspan='8'><center class='text-danger'>Deleting Order...</center></td>");
			setTimeout(' window.location.href = "order.php"; ',1000);
			}
		});
	});
});
</script>	 