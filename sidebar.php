<div class="wrapper">
    <nav id="sidebar" class="sidebar js-sidebar">
        <div class="sidebar-content js-simplebar">
            <a class="sidebar-brand" href="cart.php?DataE=<?php echo $_SESSION['DataE']; ?>">
                <span class="align-middle">HR Requipment</span>
            </a>

            <ul class="sidebar-nav">
            <?php 
                if($_SESSION['level'] == "1" || $_SESSION['level'] == "0"){ 
            ?>
                <li class="sidebar-header ms-1">
                    ผู้เบิก
                </li>
                <li class="sidebar-item <?php echo ($page == "index") ? "active":""?>">
                    <a class="sidebar-link" href="cart.php">
                        <i class="bi bi-house-fill"></i> <span class="align-middle">หน้าเบิก</span>
                    </a>
                </li>
                <!-- <li class="sidebar-item <?php echo ($page == "requisition")?"active":""?>">
                    <a class="sidebar-link" href="requisition.php">
                    <i class="bi bi-clipboard2-fill"></i> <span class="align-middle">รายการเบิก</span>
                    </a>
                </li> -->

                <li class="sidebar-item <?php echo ($page == "approve_page")?"active":""?>">
                    <a class="sidebar-link" href="approve_page.php">
                        <i class="bi bi-clipboard2-fill"></i> 
                        <span class="align-middle">รายการเบิก</span><span class="ms-2 badge rounded-pill bg-danger"><?php echo countOrders($_SESSION['employee_ID']); ?></span>
                    </a>
                </li>

                <!-- <li class="sidebar-item <?php echo ($page == "approve_page")?"active":""?>">
                    <a class="sidebar-link" href="approve_page.php">
                        <i class="bi bi-ui-checks-grid"></i>
                        <span class="align-middle">รายการอนุมัติ</span><span class="ms-2 badge rounded-pill bg-danger"><?php echo countOrders($_SESSION['employee_ID']); ?></span>
                    </a>
                </li> -->
            <?php
                } 
            ?>


            <?php if($_SESSION['level'] == "0"){ ?>
                <li class="sidebar-header ms-1">
                    การอนุมัติ
                </li>
                <li class="sidebar-item <?php echo ($page == "approve_page_noti")?"active":""?>">
                    <a class="sidebar-link" href="approve_page_noti.php">
                        <i class="fas fa-bell"></i>
                        <span class="align-middle">รอหัวหน้าอนุมัติ</span><span class="ms-2 badge rounded-pill bg-danger"><?php echo countOrders_all(); ?></span>
                    </a>
                </li>
                <li class="sidebar-item <?php echo ($page == "approve_page_admin")?"active":""?>">
                    <a class="sidebar-link" href="approve_page_admin.php">
                        <i class="bi bi-ui-checks-grid"></i>
                        <span class="align-middle">จ่ายของเบิก</span><span class="ms-2 badge rounded-pill bg-danger"><?php echo countOrders_Admin(); ?></span>
                    </a>
                </li>
                <li class="sidebar-header ms-1">
                    ตั้งค่า
                </li>
                <li class="sidebar-item <?php echo ($page == "catalog")?"active":""?>">
                    <a class="sidebar-link" href="catalog.php">
                        <i class="bi bi-archive-fill"></i> <span class="align-middle">ประเภท</span>
                    </a>
                </li>

                <!-- <li class="sidebar-item <?php //echo ($page == "category")?"active":""?>">
                    <a class="sidebar-link" href="category.php">
                        <i class="bi bi-tags-fill"></i> </i> <span class="align-middle">หมวดหมู่</span>
                    </a>
                </li> -->

                <li class="sidebar-item <?php echo ($page == "equipment")?"active":""?>">
                    <a class="sidebar-link" href="equipment.php">
                    <i class="bi bi-pen-fill"></i> <span class="align-middle">วัสดุอุปกรณ์</span>
                    </a>
                </li>

                <li class="sidebar-header ms-1">
                    รายงาน
                </li>
                <li class="sidebar-item <?php echo ($page == "report1")?"active":""?>">
                    <a class="sidebar-link" href="report.php">
                    <i class="bi bi-journal-bookmark"></i> <span class="align-middle">รายงาน</span>
                    </a>
                </li>

                <li class="sidebar-header ms-1">
                    กำหนดสิทธิ์
                </li>
                <li class="sidebar-item <?php echo ($page == "adminlist")?"active":""?>">
                    <a class="sidebar-link" href="admin_list.php">
                    <i class="fa-solid fa-unlock-keyhole"></i> <span class="align-middle">กำหนดสิทธิ์ Admin</span>
                    </a>
                </li>
            <?php } ?>
            </ul>
        </div>
    </nav>