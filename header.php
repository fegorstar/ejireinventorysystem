<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard -Ejire Pharmacy Sales and Inventory System</title>
        <link href="<?php echo $object->base_url; ?>css/simple-datatables-style.css" rel="stylesheet" />
        <link href="<?php echo $object->base_url; ?>css/styles.css" rel="stylesheet" />
        <script src="<?php echo $object->base_url; ?>js/font-awesome-5-all.min.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="<?php echo $object->base_url; ?>css/vanillaSelectBox.css" />
        <script src="<?php echo $object->base_url; ?>js/vanillaSelectBox.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">Ejire Inventory System</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <!--<div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>!-->
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php
                      if($object->is_master_user())
                      
                            {
?>
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="setting.php">Setting</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        <?php
                        }
                        else {
                        
                        ?>
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                          <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        <?php

                        }
                        ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <?php
                            if($object->is_master_user())
                            {
                            ?>
                            
                             <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Dashboard
                            </a>
                                <a class="nav-link" href="order.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div>
                                Sales
                            </a>
                            
                            
                            <a class="nav-link" href="drug_type_lists.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                                Drug Type Lists
                            </a>
                             <a class="nav-link" href="supplier.php">
                                <div class="sb-nav-link-icon"><i class="far fa-building"></i></div>
                                Drug Suppliers
                            </a>
                              
                            
                              <a class="nav-link" href="medicine.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                                Drug Lists
                            </a>
                             <a class="nav-link" href="medicine_purchase.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-clipboard"></i></div>
                               Procure Drugs
                            </a>
                            
                              <a class="nav-link" href="Expired_medicine.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                               Expired Drug
                            </a>

                              <a class="nav-link" href="patients.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                              Patient Records
                            </a>
                             <a class="nav-link" href="location_rack.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-search-location"></i></div>
                                Location Rack
                            </a>
                            
                           

                            <a class="nav-link" href="user.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Users
                            </a>
                            <a class="nav-link" href="logout.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                                Logout
                            </a>
                          
                           
                            
                          
                           
                            
                           
                          
                            <?php
                            }
                            
                             
                            else {
                            
                            ?>
                             <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Dashboard
                            </a>
                            
                             <a class="nav-link" href="order.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div>
                                Sales
                            </a>
                             <a class="nav-link" href="patients.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                              Patient Records
                            </a>

                            
                         
                            <a class="nav-link" href="logout.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                                Logout
                            </a>
 
                            
                            <?php

                            }
                            ?>
                          
                           
                           
                          
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?php echo $object->Get_user_name(); ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>