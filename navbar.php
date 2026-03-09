    <nav class="navbar navbar-expand navbar-light navbar-bg">
        <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>
        
        <div class="navbar-collapse collapse ">
            <ul class="navbar-nav navbar-align">
                <li class="nav-item dropdown">
                    <?php 
                        if($page_shop == 'index'){
                    ?>
                    <div class="nav-icon dropdown-toggle d-inline-block d-sm-none" data-bs-toggle="modal" data-bs-target="#ShopModal">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <?php
                        }
                    ?>
                    <!-- <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                        <i class="align-middle" data-feather="settings"></i>
                    </a> -->
                    <span data-bs-toggle="dropdown"><?php echo $_SESSION['employee_Name'] ?></span>
                    <a class="nav-link dropdown-toggle d-none d-sm-inline-block " href="#">
                        <img src="image/user.png" class="avatar img-fluid rounded me-" alt="Charles Hall" /><span class=" top-0 start-100 translate-middle badge border border-light rounded-circle bg-success p-2"><span class="visually-hidden">unread messages</span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <button class="dropdown-item" onclick="logout()">ออกจากระบบ</button>
                    </div>
                </li>
            </ul>
        </div>

    </nav>