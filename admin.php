<?php include('header.php');
if (empty($_SESSION['employee_ID'])) {
    header('location:index.php');
    // exit();
}
$page = "index";
?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    
</div>
</div>
<?php include('footer.php'); ?>
