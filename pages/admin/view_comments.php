<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Fetch all comments
$query = "SELECT id, name, comment, role, created_at FROM comments";
$comments_result = $mysqli->query($query);

// Check if the query was successful
if (!$comments_result) {
    die('Error: ' . $mysqli->error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_comment_id'])) {
    $comment_id = $_POST['delete_comment_id'];

    // Delete the comment from the database
    $delete_query = "DELETE FROM comments WHERE id = ?";
    $delete_stmt = $mysqli->prepare($delete_query);
    $delete_stmt->bind_param('i', $comment_id);
    $delete_stmt->execute();

    // Redirect back to the comments page
    header('Location: view_comments.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <style>
        /* Add this to your styles.css file */
body {
    font-family: Arial, sans-serif;
}

header {
    background-color: #f4f4f4;
    padding: 20px;
    text-align: center;
}

h1, h2 {
    color: #333;
}

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
<head>
    <meta charset="UTF-8">
    <title>View Comments</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        /* Your custom styles here */
    </style>
</head>
<body>
    <header>
        <h1>Admin - View Comments</h1>
    </header>
    <main>
        <h2>All Comments</h2>
        <?php if ($comments_result->num_rows > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Comment</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($comment['id']); ?></td>
                            <td><?php echo htmlspecialchars($comment['name']); ?></td>
                            <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                            <td><?php echo htmlspecialchars($comment['role']); ?></td>
                            <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                            <td>
                                <form action="view_comments.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No comments found.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </main>
</body>
</html>
