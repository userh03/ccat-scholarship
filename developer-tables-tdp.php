<?php
    session_start();

    include("php/connection.php");

    // check if session is not set
    if(!isset($_SESSION['dev_id']))
    {
      header("location: index.php");
      exit();
    }
    
    $dev_id = $_SESSION['dev_id'];
    $query = "SELECT * FROM devs_tbl WHERE dev_id = '$dev_id'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $d_fname = $row['d_fname'];
    $d_lname = $row['d_lname'];
    $d_position = $row['d_position'];
    $profile_picture = $row['profile_picture'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TDP Applicants List</title>
    <link rel="shortcut icon" href="images/logo.ico" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css"> 
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  </head>
  <body class="app sidebar-mini">
    <!-- Navbar-->
    <header id="hNav" style="background: rgb(255, 255, 255); z-index: 9999;" class="app-header"><a id="bLogo" class="app-header__logo" href="developer-dashboard.php" style="cursor: pointer;"><img class="iMg" src="images/logo2.png"></a>
      <!-- Sidebar toggle button--><a id="sSide" style="background: white;" class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
                <!--Notification Menu-->
                <style>
          .red-dot {
            position: absolute;
            background-color: red;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            top: 15px;
            right: 15px;
          }
        </style>
          <?php
              include("php/connection.php");
              // Execute the SQL query to count new notifications with view_status = 1
              $query = "SELECT COUNT(*) AS status_count FROM notify_tbl WHERE view_status = 1";
              $result = mysqli_query($con, $query);

              // Check if the query was successful
              if ($result) {
                  // Fetch the result as an associative array
                  $row = mysqli_fetch_assoc($result);
                  $notification_count = $row['status_count'];
                  
              } else {
                  // Handle the case where the query fails
                  $notification_count = 0;
              }
            ?>
          <li class="dropdown">
            <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Show notifications">
              <i class="fa fa-bell-o fa-lg">
                <?php if ($notification_count > 0): ?>
                  <span class="red-dot"></span>
                <?php endif; ?>
              </i>
            </a>
            <ul class="app-notification dropdown-menu dropdown-menu-right">
              <li class="app-notification__title">You have <?php echo $notification_count ?> new notification(s).</li>
              <div class="app-notification__content" style="width: 260px;">
                <?php
                  include("php/connection.php");
                  $query = "SELECT * FROM notify_tbl";
                  $result = mysqli_query($con, $query);

                  if (mysqli_num_rows($result) > 0) {
                      while ($row = mysqli_fetch_assoc($result)) {
                          $id = $row['id'];
                          $app_fname = $row['app_fname'];
                          $app_lname = $row['app_lname'];
                          $view_status = $row['view_status'];

                          // Skip notifications with view_status 0
                          if ($view_status == 0) {
                              continue;
                          }

                          // Set the timezone to Asia/Manila for both sent_time and current_time
                          $sent_time = new DateTime($row['sent_time'], new DateTimeZone('Asia/Manila'));
                          $current_time = new DateTime('now', new DateTimeZone('Asia/Manila'));

                          // Calculate the time difference
                          $interval = $current_time->getTimestamp() - $sent_time->getTimestamp();

                          // Calculate time ago
                          if ($interval < 60) {
                              $time_ago = $interval . ' seconds ago';
                          } elseif ($interval < 3600) {
                              $minutes_ago = round($interval / 60);
                              $time_ago = $minutes_ago . ' minutes ago';
                          } else {
                              $hours_ago = round($interval / 3600);
                              $time_ago = $hours_ago . ' hour(s) ago';
                          }

                          // Rest of your code remains the same

                          // Set the font-weight based on the view_status
                          $fontWeight = ($view_status == 1) ? 'font-weight: 700;' : '';
                ?>
                          <li>
                              <a class="app-notification__item" onclick="handleNotificationClick(event)" href="#" data-id="<?php echo $id ?>">
                                <div data-id="<?php echo $id ?>">
                                    <div style="display: flex; align-items: center;" data-id="<?php echo $id ?>">
                                    <span class="app-notification__icon" data-id="<?php echo $id ?>">
                                      <span class="fa-stack fa-lg" data-id="<?php echo $id ?>">
                                        <i class="fa fa-circle fa-stack-2x text-primary" data-id="<?php echo $id ?>"></i>
                                        <i class="fa fa-envelope fa-stack-1x fa-inverse" data-id="<?php echo $id ?>"></i>
                                      </span>
                                    </span>
                                    <p class="app-notification__message" data-id="<?php echo $id ?>" style="<?php echo $fontWeight ?>"><?php echo $app_fname .' '. $app_lname ?> sent an Application</p>
                                    </div>
                                    <p class="app-notification__meta" data-id="<?php echo $id ?>" style="margin-left: 50px;"><?php echo $time_ago ?></p>
                            
                                </div>
                              </a>
                          </li>
                  <?php
                      }
                  } else {
                  }
                ?>
              </div>
            </ul>
          </li>
          <script>
            function handleNotificationClick(event) {
              event.preventDefault(); // Prevent the default navigation behavior
              
              // Get the notification ID from the data-id attribute
              var notificationId = $(event.target).data('id'); // Use $(event.target) to access the clicked element
              
              // Send an AJAX request to update the view_status
              $.ajax({
                type: 'POST',
                url: 'php/notifyStatusUpdate.php', // Replace with the actual URL to your PHP script
                data: { id: notificationId },
                success: function(response) {
                // Check if the response is equal to "success"
                if (response === 'Success') {                  
                  // Optionally, you can redirect the user to a new page after the update
                  window.location.href = 'developer-tables.php';
                }
              },
              });
            }
          </script>
            <!-- User Menu-->
          <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
              <li><a class="dropdown-item" href="developer-page-user.php"><i class="fa fa-user fa-lg"></i> Profile</a></li>
              <li><a class="dropdown-item" href="developer-user-settings.php"><i class="fa fa-cog fa-lg"></i> Account Settings</a></li>
              <li><a class="dropdown-item" onclick="logOut();" href="#"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
            </ul>
          </li>
      </ul>
    </header>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside id="sNav" style="background: rgb(255, 255, 255); z-index: 9998;" class="app-sidebar">
      <div class="app-sidebar__user"><img id="uProfile" class="app-sidebar__user-avatar" src="<?php echo $profile_picture ?>">
        <div style="color:black;">
        <?php
            $truncated_name = $d_fname;

            // Split the string into an array of words
            $words = explode(' ', $truncated_name);

            // Check if the array has at least four words
            if (count($words) >= 2) {
                // Combine the first three words and join the rest with a new line
                $designation_name = implode(' ', array_slice($words, 0, 2)) . '<br>' . implode(' ', array_slice($words, 2));
            } else {
                // If there are fewer than four words, keep the original string
                $designation_name = $truncated_name;
            }
          ?>
        <p class="app-sidebar__user-name"><?php echo $designation_name." ". $d_lname ?></p>
        <?php
            $building = $d_position;

            // Split the string into an array of words
            $words = explode(' ', $building);

            // Check if the array has at least four words
            if (count($words) >= 4) {
                // Combine the first three words and join the rest with a new line
                $designation = implode(' ', array_slice($words, 0, 3)) . '<br>' . implode(' ', array_slice($words, 3));
            } else {
                // If there are fewer than four words, keep the original string
                $designation = $building;
            }
          ?>
          <p class="app-sidebar__user-designation"><?php echo $designation ?></p>
        </div>
      </div>
      <?php include 'php/navbar.php' ?>
    </aside>
    <main class="app-content">
      <div class="app-title">
      <div id="divProfile">
          <style>
            /* Media query for mobile view */
            @media screen and (max-width: 767px) {
                #divProfile {
                  display: none;
                }
              }
          </style>          <h1><i class="fa fa-th-list"></i> TDP Applicants List</h1>
          <p>List of TDP Applicants</p>
        </div>
        <ul class="app-breadcrumb breadcrumb side">
          <li class="breadcrumb-item"><a href="developer-dashboard.php"><i class="fa fa-home fa-lg"></i></a></li>
          <li class="breadcrumb-item active"><a href="#">TDP Applicants List</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">           
            <?php
              $phpVersion = phpversion();

              if (version_compare($phpVersion, '7.4.8', '<=')) {
                  echo '<button onclick="generateCSV()" id="apprv" style="float:right; margin-bottom: 5px;" class="btn btn-primary">Generate CSV</button>';
              } elseif (version_compare($phpVersion, '8.2.0', '>=')) {
                  echo '<button onclick="generateExcel()" id="apprv" style="float:right; margin-bottom: 5px;" class="btn btn-primary">Generate Excel</button>';
              } else {
                  echo "Unsupported PHP version";
              }
            ?>

            <h5 style="display: inline-block; margin: 0 5px;">Filter</h5>
            <style>
              #demoSelect {
                width: 100%;
                padding: 5px; /* Adjust the padding as needed */
                box-sizing: border-box; /* Ensure padding is included in the width */
              }

              @media (min-width: 768px) {
                #demoSelect {
                  width: 700px;
                }
              }
            </style>
              <select id="demoSelect" multiple="">
                <optgroup label="Select Categories">
                  <option>Department օf Computer Studies</option>
                  <option>Department օf Engineering</option>
                  <option>Department օf Industrial Technology</option>
                  <option>Department օf Management Studies</option>
                  <option>Department օf Teacher Education</option>
                  <option>OTHERS</option>
                </optgroup>
              </select>   
              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="sampleTables">
                  <thead>
                    <tr style="font-size: 16px; text-align: center;">
                      <th scope="col">5-Digit Control Number</th>
                      <th scope="col">Student ID Number</th>
                      <th scope="col">Full name</th>
                      <th scope="col">Gender</th>
                      <th scope="col">Birthdate</th>
                      <th scope="col">Degree Program</th>
                      <th scope="col">Year Level</th>
                      <th scope="col">Academic Units Enrolled</th>
                      <th scope="col">ZIP Code</th>
                      <th scope="col">Email Address</th>
                      <th scope="col">Phone Number</th>
                      <th scope="col">Actual Tuition and Other School Fees</th>
                      <th scope="col">Billed Amount</th>
                      <th scope="col">Stipend</th>
                      <th scope="col">Person with Disability</th>
                      <th scope="col">Total Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!--PHP Database Display-->
                    <?php
                    
                      $sql = "SELECT
                      5_digit_Control_Number,
                      Student_Number,
                      Last_Name,
                      Given_Name,
                      Middle_Initial,
                      Sex_at_Birth,
                      Birthdate,
                      Degree_Program,
                      Year_Level,
                      Academic_Units_Enrolled,
                      ZIP_Code,
                      E_mail_address,
                      Phone_Number,
                      Actual_Tuition_and_Other_School_Fees,
                      Billed_Amount,
                      Stipend,
                      Person_with_Disability,
                      TOTAL_AMOUNT
                  FROM
                      `tes_applicants_tbl`";
                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();
                      
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $fivedigit_Control_Number = $row['5_digit_Control_Number'];
                          $Student_Number = $row['Student_Number'];
                          $Last_Name = $row['Last_Name'];
                          $Given_Name = $row['Given_Name'];
                          $Middle_Initial = $row['Middle_Initial'];
                          $Sex_at_Birth = $row['Sex_at_Birth'];
                          $Birthdate = $row['Birthdate'];
                          $Degree_Program = $row['Degree_Program'];
                          $Year_Level = $row['Year_Level'];
                          $Academic_Units_Enrolled = $row['Academic_Units_Enrolled'];
                          $ZIP_Code = $row['ZIP_Code'];
                          $E_mail_address = $row['E_mail_address'];
                          $Phone_Number = $row['Phone_Number'];
                          $Actual_Tuition_and_Other_School_Fees = $row['Actual_Tuition_and_Other_School_Fees'];
                          $Billed_Amount = $row['Billed_Amount'];
                          $Stipend = $row['Stipend'];
                          $Person_with_Disability = $row['Person_with_Disability'];
                          $TOTAL_AMOUNT = $row['TOTAL_AMOUNT'];
                              
                          echo '<tr style="font-size: 15px;">
                              <td scope="row"><strong>Student ID #:</strong>'.$fivedigit_Control_Number.'</td>
                              <td><strong>Gender:</strong>'.$Student_Number.'</td>
                              <td><strong>Full Name:</strong>'.$Last_Name.', '.$Given_Name. $Middle_Initial.'</td>
                              <td><strong>Gender:</strong>'.$Sex_at_Birth.'</td>
                              <td><strong>Birthday:</strong>'.$Birthdate.'</td>
                              <td><strong>Degree Program:</strong>'.$Degree_Program.'</td>
                              <td><strong>Year Level:</strong>'.$Year_Level.'</td>
                              <td><strong>Academic Units Enrolled:</strong>'.$Academic_Units_Enrolled.'</td>
                              <td><strong>ZIP Code:</strong>'.$ZIP_Code.'</td>
                              <td><strong>Email Address:</strong>'.$E_mail_address.'</td>
                              <td><strong>Phone Number:</strong>'.$Phone_Number.'</td>
                              <td><strong>Actual Tuition and Other School Fees:</strong>'.$Actual_Tuition_and_Other_School_Fees.'</td>
                              <td><strong>Billed Amount:</strong>'.$Billed_Amount.'</td>
                              <td><strong>Stipend:</strong>'.$Stipend.'</td>
                              <td><strong>Person with Disability:</strong>'.$Person_with_Disability.'</td>
                              <td><strong>Total Amount:</strong>'.$TOTAL_AMOUNT.'</td>
                              
                          </tr>';  
                      }
                    ?> 
                  </tbody>
                </table>    
              </div> 
              <style>
                strong{
                  display: none;
                }
                /* CSS media query for mobile devices */
                @media (max-width: 767px) {
                  #apprv, #dnd{
                    font-size: smaller;
                  }
                  strong{
                    display: inline-block;
                    margin: 0 5px;
                  }
                  #sampleTables tr {
                    display: block;
                    margin-bottom: 15px;
                    border: none;
                  }
                  thead, tr{
                    display: none;
                    width: 100%;
                  }
                  #sampleTables td {
                    display: block;
                    text-align: left;
                  }

                  #sampleTables td:before {
                    content: attr(data-label);
                    float: left;
                    font-weight: bold;
                  }

                  #sampleTables th,
                  #sampleTables td:before {
                    width: 100%;
                  }

                  #sampleTables .modal-dialog {
                    max-width: 100%;
                    margin: 1rem;
                  }
                  .table{
                    margin: auto;
                  }
                }
              </style>                       
            </div>
          </div>
        </div>
      </div>
      <footer>
        <div class="container" style="margin: auto;">
          <div class="row">
              <div class="col-md-12">
                  <hr>
                  <p style="text-align: center;">&copy; 2023 CCAT - CAMPUS. All rights reserved.</p>
              </div>
          </div>
        </div>
      </footer>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <script type="text/javascript">$('#sampleTables').DataTable();</script>
    <script>
      $(document).ready(function() {
        var table = $('#sampleTables').DataTable();
        table.destroy();

        // Now you can reinitialize the DataTable
        $('#sampleTables').DataTable({
          searching: false
        });
      });
    </script>
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="js/plugins/select2.min.js"></script>
    <script>
      $(document).ready(function() {
        // Initialize select2 plugin
        $('#demoSelect').select2({
          tags: true, // Allow custom tags
        });

        // Function to filter the table rows
        function filterTable() {
          var selectedCategories = $('#demoSelect').val(); // Get the selected categories

          // Show/hide rows based on selected categories
          $('#sampleTables tbody tr').each(function() {
            var row = $(this);
            var fields = row.find('td'); // Get all the fields in the row

            var found = false;
            fields.each(function() {
              var fieldText = $(this).text().trim().toLowerCase(); // Convert field text to lowercase

              // Check if any field matches the selected categories (case-insensitive)
              if (selectedCategories.length === 0 || selectedCategories.some(category => fieldText.includes(category.toLowerCase()))) {
                found = true;
                return false; // Exit the loop if a match is found
              }
            });

            if (found) {
              row.show(); // Show the row
            } else {
              row.hide(); // Hide the row
            }
          });
        }

        // Filter the table when the select2 value changes
        $('#demoSelect').on('change', filterTable);

        // Call the filterTable function initially to show the appropriate rows
        filterTable();
      });
    </script>
    <!-- Data table plugin-->
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
    <!-- Google analytics script-->
    <script type="text/javascript">
      if(document.location.hostname == 'pratikborsadiya.in') {
      	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      	ga('create', 'UA-72504830-1', 'auto');
      	ga('send', 'pageview');
      }
    </script>
    <!-- Logout Prompt -->
    <script type="text/javascript" src="js/plugins/sweetalert.min.js"></script>
    <script type="text/javascript">
        function logOut(sa_id) {
            swal({
                title: "Are you sure?",
                text: "You will logout in this session",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, logout now!",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                  window.location.href = 'php/logout.php';
                }
            });
        };
    </script>
    <script>
        function generateCSV() {
            window.location.href = 'php/csv_generate_approved.php';
        }

        function generateExcel() {
            window.location.href = 'php/generate_report_approved.php';
        }
    </script>
  </body>
</html>