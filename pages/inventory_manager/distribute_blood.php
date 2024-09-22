<?php
include '../../includes/db.php';
include '../../includes/functions.php'; // Include common functions for sending email, SMS, etc.
session_start();

// Ensure the user is logged in and is an inventory manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    header('Location: ../../index.php');
    exit();
}

$inventory_manager_id = $_SESSION['user_id'];

// Fetch pending hospital requests
$query = "SELECT hr.id AS request_id, hr.hospital_id, hr.blood_type, hr.volume, hr.status, h.name AS hospital_name 
          FROM hospital_requests hr 
          JOIN users h ON hr.hospital_id = h.id 
          WHERE hr.status = 'pending'";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Hospital Requests</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #0056b3;
        }
        main {
            padding: 20px;
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        main h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 16px;
            text-align: left;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            color: #fff;
            background-color: #007bff;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        button:hover {
            background-color: #0056b3;
        }
        footer {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        a {
            text-decoration: none;
        }
        /* Google Translate Widget */
        .google-translate {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
     <!-- Google Translate Widget Script -->
     <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'am,tig,en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>
     <!-- Google Translate Widget -->
     <div id="google_translate_element" class="google-translate"></div>

    <header>
        <h1>Blood Distribution System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Go to Dashboard</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Pending Hospital Requests</h2>

        <?php if ($result->num_rows > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Hospital Name</th>
                        <th>Blood Type</th>
                        <th>Volume Needed (ml)</th>
                        <th>Status</th>
                        <th>Available Units</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $result->fetch_assoc()) {
                        $requestId = $request['request_id'];
                        $bloodType = $request['blood_type'];

                        // Count available units for the requested blood type
                        $countQuery = "SELECT COUNT(*) AS unit_count FROM blood_units WHERE blood_type = ?";
                        $countStmt = $mysqli->prepare($countQuery);
                        $countStmt->bind_param('s', $bloodType);
                        $countStmt->execute();
                        $countResult = $countStmt->get_result();
                        $countRow = $countResult->fetch_assoc();
                        $availableUnits = $countRow['unit_count'];
                        
                        $totalAvailableVolume = $availableUnits; 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                        <td><?php echo htmlspecialchars($request['hospital_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['blood_type']); ?></td>
                        <td><?php echo htmlspecialchars($request['volume']); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                        <td><?php echo $totalAvailableVolume; ?> ml</td>
                        <td>
                            <button onclick="distributeBlood('<?php echo $requestId; ?>', <?php echo $totalAvailableVolume; ?>, <?php echo $request['volume']; ?>, '<?php echo $bloodType; ?>')">Distribute</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No pending requests found.</p>
        <?php } ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Blood Distribution System | <a href="#">Privacy Policy</a></p>
    </footer>

    <script>
    function distributeBlood(requestId, totalAvailableVolume, requestedVolume, bloodType) {
        if (totalAvailableVolume >= requestedVolume) {
            // Sufficient blood volume available
            if (confirm('Sufficient blood units available. Do you want to distribute blood for request ID: ' + requestId + '?')) {
                // Send AJAX request to process distribution
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'process_distribution.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        alert(response.message);
                        if (response.success) {
                            // Optionally, reload the page to update the table
                            location.reload();
                        }
                    }
                };
                xhr.send(`request_id=${requestId}&blood_type=${encodeURIComponent(bloodType)}&volume=${requestedVolume}`);
            }
        } else {
            // Insufficient blood volume
            alert('Insufficient blood units available. Only ' + totalAvailableVolume + ' ml are available for request ID: ' + requestId);
        }
    }
    </script>

</body>
</html>
