<?php
// Security Login
session_start();

if ( empty( $_SESSION[ 'privilage' ] ) || ( $_SESSION[ 'privilage' ] != "admin" ) ) {
	header( "location: index.html" );
} else {
	$userlogin = $_SESSION[ 'loginname' ];
	$userid = $_SESSION[ 'loginid' ];
	$privilage = $_SESSION[ 'privilage' ];

	// Database Connection
	require_once "src/sql/config.php";
}
?>

<!doctype html>
<!--======================================== Java (Hostel Status) =======================================-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load( 'current', {
		'packages': [ 'corechart' ]
	} );
	google.charts.setOnLoadCallback( drawChart );

	function drawChart() {

		var data = google.visualization.arrayToDataTable( [
			[ 'Room Status', 'Total' ],
			<?php
			// Query
			$query = "SELECT h_status, count(*) as number FROM hostel_details GROUP BY h_status";
			$result = mysqli_query($connection, $query);
			
			while ($row = mysqli_fetch_array($result)){
				echo "['".$row["h_status"]."',",$row["number"]."],";
			}
			?>
		] );

		var options = {
			titlePosition: 'none',
			legend: 'none',
			backgroundColor: {
				fill: 'transparent'
			},
			chartArea: {
				height: 190
			},
			colors: [ '#5cb85c', '#d9534f', '#0275d8', '#f0ad4e' ],
			height: 200
		};

		var chart = new google.visualization.PieChart( document.getElementById( 'hostelStatusChart' ) );

		chart.draw( data, options );
	}

	// =========================================== Java (Visitor) =======================================
	<?php
	$months = array('Jan', 'Feb', 'Mar', 'April', 'May', 'june', 'july', 'ogos', 'Sept', 'Oct', 'Nov' , 'Dis');
	$year = date('Y');

	$str = "";
	$data =  array();
	array_push($data, array('Month', 'Male', 'Female'));
	$month = 1;
	do{

		$total_male =  mysqli_query($connection, "SELECT v_gender, COUNT(*) as total
		 FROM visitor_details WHERE v_gender ='Male' 
		 AND MONTH(v_date_in) = '$month' AND YEAR(v_date_in) = '$year' GROUP BY v_gender");


		$total_female=  mysqli_query($connection, "SELECT v_gender, COUNT(*) as total
		 FROM visitor_details WHERE v_gender ='Female' 
		 AND MONTH(v_date_in) = '$month' AND YEAR(v_date_in) = '$year' GROUP BY v_gender");


		$male = mysqli_fetch_assoc($total_male);

		$female = mysqli_fetch_assoc($total_female);

		$item = [$months[$month-1], (is_null($male))? 0 : (int)$male['total'], (is_null($female))? 0 : (int)$female['total']];

		array_push($data, $item);

		$month++;
	} while($month <= 12);
?>

	google.charts.load( 'current', {
		packages: [ 'corechart', 'bar' ]
	} );
	google.charts.setOnLoadCallback( drawChart2 );

	function drawChart2() {
		var data = google.visualization.arrayToDataTable( <?=json_encode($data)?> );

		var options = {
			height: 302,
			backgroundColor: {
				fill: 'transparent'
			},

			chart: {
				subtitle: 'Record track on <?php echo date("Y"); ?>
				',
			}
		};

		var chart = new google.charts.Bar( document.getElementById( 'visitorTrackingChart' ) );
		chart.draw( data, google.charts.Bar.convertOptions( options ) );
	}

	// =========================================== Java (Maintanance Cost) =======================================
	google.charts.load( 'current', {
		'packages': [ 'line' ]
	} );
	google.charts.setOnLoadCallback( drawChart3 );

	function drawChart3() {

		var data = google.visualization.arrayToDataTable( [
			[ 'Date', 'Maintanance Cost' ],

			<?php
			$query = "SELECT * FROM `maintanance_details` GROUP BY `m_cdate`";
			$result = mysqli_query($connection, $query);
			$resultcheck = mysqli_num_rows( $result );
			
			if ( $resultcheck > 0 ) {
				
				while ($row = mysqli_fetch_array($result)){
					$thedate = date("F Y",strtotime($row["m_cdate"]));
					echo "['".$thedate."',",$row["m_cost"]."],";
				}
			}
			
			else{
				echo "['No Maintanance', 1],";
			}
		  	?>

		] );

		var options = {
			backgroundColor: 'transparent',
			legend: {
				position: 'none'
			},
			chart: {
				title: 'Maintanance Cost History',
				subtitle: 'in Ringgit Malaysia (RM)'
			},
			height: 400
		};

		var chart = new google.charts.Line( document.getElementById( 'maintananceCostChart' ) );

		chart.draw( data, google.charts.Line.convertOptions( options ) );
	}
</script>
<html>
<head>
	<meta charset="utf-8">
	<link rel="icon" sizes="76x76" href="../img/Utem_logo.png">
	<link rel="icon" type="image/png" href="../img/Utem_logo.png">
	<title>Dashboard</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport"/>

	<!--======================================== CSS =======================================-->
	<link href="src/css/bootstrap.min.css" rel="stylesheet">
	<link href="src/css/navbar.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" type="text/css" rel="stylesheet">

	<!--======================================== CSS (DataTable) =======================================-->
	<link href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">

	<!--=================================== Core JS Files ==================================-->
	<script src="src/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
	<script src="src/js/core/bootstrap.min.js" type="text/javascript"></script>
	<script src="src/js/core/popper.min.js" type="text/javascript"></script>

</head>

<body>
	<!--==================================== Topbar ==================================-->
	<header>
		<nav class="navbar navbar-dark bg-dark"><a class="navbar-brand">Smart Dormitory Management</a>
			<span class="navbar-text ml-auto">
				<?= ucwords($userlogin); ?>
			</span>
			<a href="src/sql/logout.php" onclick="return confirm('Are you sure to logout?')" class="btn btn-outline-danger ml-3">Logout</a>
		</nav>
	</header>

	<!--===================================== Sidebar ================================-->
	<div class="sidebar">
		<center>
			<img class="logo" src="src/img/Utem_logo.png" alt="#">
			<a class="active" href="dashboard-admin.php"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
			<a href="Hostel/HostelSetup_admin.php"><i class="fas fa-bed"></i><span>Hostel Setup</span></a>
			<a href="Registration/registration-admin.php"><i class="fas fa-user"></i><span>Student Registration</span></a>
			<a href="Student/student-admin.php"><i class="fas fa-user-circle"></i><span>Student Account</span></a>
			<a href="Maintanance/maintanance-admin.php"><i class="fas fa-tools"></i><span>Maintanance</span></a>
			<a href="Visitor/visitor-admin.php"><i class="fas fa-eye"></i><span>Visitor Tracking</span></a>
		</center>
	</div>
	<!--===================================== Content ================================-->
	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4">
					<div class="card">
						<div class="card-header bg-secondary">
							<h4 class="card-title pt-1">Hostel status</h4>
						</div>
						<div class="card-body bg-light">
							<div id="hostelStatusChart"></div>
						</div>
						<div class="card-footer">
							<h5>Legend</h5>
							<i class="fa fa-circle text-success"></i> Empty
							<i class="fa fa-circle text-primary ml-2"></i> Student
							<br>
							<i class="fa fa-circle text-warning"></i> Visitor
							<i class="fa fa-circle text-danger ml-2"></i> Maintanance
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card">
						<div class="card-header bg-secondary">
							<h4 class="card-title pt-1">Visitor Tracking</h4>
						</div>
						<div class="card-body bg-light">
							<div id="visitorTrackingChart"></div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-8">
					<div class="card">
						<div class="card-header bg-secondary">
							<h4 class="card-title pt-1">Maintanance</h4>
						</div>
						<div class="card-body bg-light">
							<div id="maintananceCostChart"></div>
							<hr>
							<p class="card-text"><small class="text-muted"><i class="fas fa-database"></i> Based on the latest database updated</small>
							</p>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card">
						<div class="card-header bg-secondary">
							<h4 class="card-title pt-1">Task</h4>
						</div>
						<div class="card-body bg-light">
							<h6 class="card-subtitle mb-2"><i class="far fa-check-square"></i> Task to do</h6>
							<hr>
							<?php
							$query = "SELECT * FROM `maintanance_details` WHERE m_status = 'Ongoing'";
							$result = mysqli_query( $connection, $query );

							while ( $row = mysqli_fetch_array( $result ) ) {
								?>
							<div class="row">
								<div class="col-1">
									<?= $row['m_id']?>
								</div>
								<div class="col-11">
									<?= $row['m_task']?>
								</div>
							</div>
							<hr>
							<?php } ?>
							<a href="Maintanance/maintanance-admin.php" class="btn btn-outline-primary">Go to Maintanance</a>
						</div>
						<div class="card-footer">
							<p class="card-text"><small class="text-muted">Latest update</small>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>