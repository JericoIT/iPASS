<?php
session_start();
include '../connection.php';

function generateAutoId($connection) {
    $year = date('y'); // Last two digits of the year
    $month = date('m'); // Current month
    $result = $connection->query("SELECT COUNT(*) as count FROM tbl_user");
    if ($result) {
        $row = $result->fetch_assoc();
        $count = $row['count'] + 1; // Increment count by 1
    } else {
        $count = 1; // If no users exist yet, start from 1
    }
    $autoId = $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
    return $autoId;
}

// Handle form submission to add, edit, or disable a user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    
    if ($action == "add_user") {
        $autoId = generateAutoId($conn);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $lname = $_POST['lname'];
        $userlevel = $_POST['userlevel'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $barangay = $_POST['barangay'];
        $street = $_POST['street'];
        $postal_code = $_POST['postal_code'];
        
        $sql = "INSERT INTO tbl_user (id_number, password, fname, mname, lname, userlevel, email, city, barangay, street, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $autoId, $password, $fname, $mname, $lname, $userlevel, $email, $city, $barangay, $street, $postal_code);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add user.";
        }
        
    } elseif ($action == "edit_user") {
        $original_email = $_POST['original_email'];
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $lname = $_POST['lname'];
        $userlevel = $_POST['userlevel'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $barangay = $_POST['barangay'];
        $street = $_POST['street'];
        $postal_code = $_POST['postal_code'];
        
        $sql = "UPDATE tbl_user SET fname=?, mname=?, lname=?, userlevel=?, email=?, city=?, barangay=?, street=?, postal_code=? WHERE id_number=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $fname, $mname, $lname, $userlevel, $email, $city, $barangay, $street, $postal_code, $original_email);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update user.";
        }
        
    } elseif ($action == "disable_user") {
        $email = $_POST['disable_email'];
        $sql = "UPDATE tbl_user SET status='disabled' WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "User disabled successfully!";
        } else {
            $_SESSION['message'] = "Failed to disable user.";
        }
    }
    
    $stmt->close();
}

// Fetch user data from the database
$sql = "SELECT id_number, fname, mname, lname, userlevel, email FROM tbl_user";
$result = $conn->query($sql);

$user_data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $user_data[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Fontfaces CSS-->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="../vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="../vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="../vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="../vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="../vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <style>
    .modal {
  z-index: 1031!important; /* or a higher value than the modal background's z-index */
}
.modal-backdrop {
  z-index: -1030!important; /* or a lower value than the modal's z-index */
}
    
  </style>
</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="index.html">
                            <img src="images/icon/logo.png" alt="CoolAdmin" />
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li class="has-sub">
                      
                                <a href="dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                            </li>
                            <li>
                            <a class="js-arrow" href="#">
                            <i class="fas fa-chart-bar"></i>Programs & Services</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="program_management.html">Program Management</a>
                                </li>
                                <li>
                                    <a href="service_management.html">Service Management</a>
                                </li>
                                <li>
                                    <a href="training.html">Training Management</a>
                                </li>
                            </ul>
                            </li>
                            <li>
                            <a class="js-arrow" href="#">
                                <i class="fas fa-chart-bar"></i>Business & Loans</a>
                                <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                    <li>
                                        <a href="program_management.html">Business Management</a>
                                    </li>
                                    <li>
                                        <a href="service_management.html">Loan Management</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="active has-sub">
                                <a href="user_management.php">
                                    <i class="fas fa-tachometer-alt"></i>User Management</a>
                            </li>
                            <li class="active has-sub">
                                <a href="utilities.html">
                                    <i class="fas fa-tachometer-alt"></i>Utilities</a>
                            </li>
                        </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
                    <img src="#" alt="Department of Trade and Industry" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li>
                        <a class="js-arrow" href="#">
                        <i class="fas fa-chart-bar"></i>Programs & Services</a>
                        <ul class="list-unstyled navbar__sub-list js-sub-list">
                            <li>
                                <a href="program_management.html">Program Management</a>
                            </li>
                            <li>
                                <a href="service_management.html">Service Management</a>
                            </li>
                            <li>
                                <a href="training.html">Training Management</a>
                            </li>
                        </ul>
                        </li>
                        <li>
                        <a class="js-arrow" href="#">
                            <i class="fas fa-chart-bar"></i>Business & Loans</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="program_management.html">Business Management</a>
                                </li>
                                <li>
                                    <a href="service_management.html">Loan Management</a>
                                </li>
                            </ul>
                        </li>
                        <li class="active has-sub">
                            <a href="user_management.php">
                                <i class="fas fa-tachometer-alt"></i>User Management</a>
                        </li>
                        <li class="active has-sub">
                            <a href="utilities.html">
                                <i class="fas fa-tachometer-alt"></i>Utilities</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <?php
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
?>

<div class="page-container">
    <!-- HEADER DESKTOP-->
    <header class="header-desktop">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="header-wrap">
                    <form class="form-header" action="" method="POST">
                        <input class="au-input au-input--xl" type="text" name="search" placeholder="" />
                        <button class="au-btn--submit" type="submit">
                            <i class="zmdi zmdi-search"></i>
                        </button>
                    </form>
                    <div class="header-button">
                        <div class="noti-wrap">
                            <div class="noti__item js-item-menu">
                                <i class="zmdi zmdi-notifications"></i>
                                <div class="notifi-dropdown js-dropdown"></div>
                            </div>
                        </div>
                        <div class="account-wrap">
                            <div class="account-item clearfix js-item-menu">
                                <div class="content">
                                    <a class="js-acc-btn" href="#"><?php echo $_SESSION['fname'] . ' ' . $_SESSION['lname']; ?></a>
                                </div>
                                <div class="account-dropdown js-dropdown">
                                    <div class="info clearfix">
                                        <div class="content">
                                            <h5 class="name">
                                                <a href="#"><?php echo $_SESSION['fname'] . ' ' . $_SESSION['lname']; ?></a>
                                            </h5>
                                            <span class="email"><?php echo $_SESSION['email']; ?></span>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                            <a href="#">
                                                <i class="zmdi zmdi-account"></i>Account</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="#">
                                                <i class="zmdi zmdi-settings"></i>Setting</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="#">
                                                <i class="zmdi zmdi-money-box"></i>Billing</a>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__footer">
                                        <a href="../logout.php">
                                            <i class="zmdi zmdi-power"></i>Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- END HEADER DESKTOP-->


            <div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="mr-2 fa fa-align-justify"></i>
                            <strong class="card-title" v-if="headertext">User Management</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-data">
                                <table id="userTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>User Level</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userTableBody">
                                        <?php foreach ($user_data as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['id_number']); ?></td>
                                                <td><?php echo htmlspecialchars($user['fname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['mname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['lname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['userlevel']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editUserModal" onclick="populateEditForm(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#disableUserModal" onclick="setDisableUserId('<?php echo $user['email']; ?>')">Disable</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="user-data__footer">
                                <div class="add-user">
                                    <button id="button" class="au-btn au-btn-icon au-btn--blue" data-toggle="modal" data-target="#addUserModal">
                                        <i class="zmdi zmdi-plus"></i>Add User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                                <!-- Add User Modal -->
                                <div class="modal fade" id="addUserModal" tabindex="0" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Form for adding user -->
                                                <form id="addUserForm" method="POST" action="">
                                                    <input type="hidden" name="action" value="add_user">
                                                    <div class="form-group">
                                                        <label for="fname">First Name</label>
                                                        <input type="text" class="form-control" id="fname" name="fname" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mname">Middle Name</label>
                                                        <input type="text" class="form-control" id="mname" name="mname" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="lname">Last Name</label>
                                                        <input type="text" class="form-control" id="lname" name="lname" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="userlevel">User Level</label>
                                                        <input type="text" class="form-control" id="userlevel" name="userlevel" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="email" class="form-control" id="email" name="email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password">Password</label>
                                                        <input type="password" class="form-control" id="password" name="password" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="city">City</label>
                                                        <input type="city" class="form-control" id="city" name="city" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="barangay">Barangay</label>
                                                        <input type="barangay" class="form-control" id="barangay" name="barangay" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="street">Street</label>
                                                        <input type="street" class="form-control" id="street" name="street" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="postal_code">Postal Code</label>
                                                        <input type="postal_code" class="form-control" id="postal_code" name="postal_code" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Add User</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <!-- End Add User Modal -->
                                 <!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="0" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="POST" action="">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="original_email" id="editOriginalEmail">
                    <div class="form-group">
                        <label for="editFname">First Name</label>
                        <input type="text" class="form-control" id="editFname" name="fname" required>
                    </div>
                    <div class="form-group">
                        <label for="editMname">Middle Name</label>
                        <input type="text" class="form-control" id="editMname" name="mname" required>
                    </div>
                    <div class="form-group">
                        <label for="editLname">Last Name</label>
                        <input type="text" class="form-control" id="editLname" name="lname" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserlevel">User Level</label>
                        <input type="text" class="form-control" id="editUserlevel" name="userlevel" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                                                        <label for="city">City</label>
                                                        <input type="city" class="form-control" id="city" name="city" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="barangay">Barangay</label>
                                                        <input type="barangay" class="form-control" id="barangay" name="barangay" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="street">Street</label>
                                                        <input type="street" class="form-control" id="street" name="street" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="postal_code">Postal Code</label>
                                                        <input type="postal_code" class="form-control" id="postal_code" name="postal_code" required>
                                                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Disable User Modal -->
<div class="modal fade" id="disableUserModal" tabindex="0" role="dialog" aria-labelledby="disableUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disableUserModalLabel">Disable User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="disableUserForm" method="POST" action="">
                    <input type="hidden" name="action" value="disable_user">
                    <input type="hidden" name="disable_email" id="disableEmail">
                    <p>Are you sure you want to disable this user?</p>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Disable</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
    </div>
    <!-- Jquery JS-->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="../vendor/slick/slick.min.js">
    </script>
    <script src="../vendor/wow/wow.min.js"></script>
    <script src="../vendor/animsition/animsition.min.js"></script>
    <script src="../vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="../vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="../vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="../vendor/circle-progress/circle-progress.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="../vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="../js/main.js"></script>

    <script>
    function populateEditForm(user) {
        document.getElementById('editOriginalEmail').value = user.email;
        document.getElementById('editFname').value = user.fname;
        document.getElementById('editMname').value = user.mname;
        document.getElementById('editLname').value = user.lname;
        document.getElementById('editUserlevel').value = user.userlevel;
        document.getElementById('editEmail').value = user.email;
    }

    function setDisableUserId(email) {
        document.getElementById('disableEmail').value = email;
    }

    // Check if a session message exists
    <?php if(isset($_SESSION['message'])): ?>
        document.getElementById('notification').innerText = '<?php echo $_SESSION['message']; ?>';
        document.getElementById('notification').style.display = 'block';
        // Clear the session message after displaying
        <?php unset($_SESSION['message']); ?>
        // Hide the notification after a few seconds
        setTimeout(() => {
            document.getElementById('notification').style.display = 'none';
        }, 3000);
    <?php endif; ?>
</script>
</body>
</html>