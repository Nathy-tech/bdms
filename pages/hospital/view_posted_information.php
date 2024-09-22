<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is a hospital
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hospital') {
    header('Location: ../../index.php');
    exit();
}

// Fetch all information posts
$query = "SELECT title, content, posted_at FROM information";
$information_result = $mysqli->query($query);

// Check if the query was successful
if (!$information_result) {
    die('Error: ' . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Posted Information</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
     /* Example CSS for table and buttons */
table.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 18px;
    text-align: left;
}

table.styled-table th, table.styled-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

table.styled-table thead {
    background-color: #f4f4f4;
}

table.styled-table tbody tr:nth-of-type(even) {
    background-color: #f9f9f9;
}

.btn {
    padding: 8px 12px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    margin: 4px 2px;
    cursor: pointer;
    border: none;
    border-radius: 4px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

    </style>

</head>
<body>
    <header>
        <h1>Posted Information</h1>
    </header>
    <main>
        
        <?php if ($information_result->num_rows > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Posted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($info = $information_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($info['title']); ?></td>
                            <td><?php echo htmlspecialchars($info['content']); ?></td>
                            <td><?php echo htmlspecialchars($info['posted_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No information posts found.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </main>
</body>
</html>
