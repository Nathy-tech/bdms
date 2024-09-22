<?php
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

// Handle deletion of a post
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM information WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $message = "Information deleted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle editing of a post
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $mysqli->prepare("UPDATE information SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param('ssi', $title, $content, $id);

    if ($stmt->execute()) {
        $message = "Information updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle posting new information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $mysqli->prepare("INSERT INTO information (title, content) VALUES (?, ?)");
    $stmt->bind_param('ss', $title, $content);

    if ($stmt->execute()) {
        $message = "Information posted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch existing information
$information = $mysqli->query("SELECT * FROM information");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Information</title>
    <link rel="stylesheet" href="../../css/styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 900px;
            width: 100%;
            margin-top: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #d32f2f;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        button {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #b71c1c;
        }

        .nav-link {
            display: block;
            margin: 20px 0;
            text-align: center;
        }

        .nav-link a {
            color: #d32f2f;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        .nav-link a:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #4caf50; /* Green color for success */
            font-weight: bold;
        }

        .info-list {
            margin-top: 20px;
        }

        .info-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-list th, .info-list td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .info-list th {
            background-color: #f4f4f4;
            color: #555;
        }

        .info-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .info-list tr:hover {
            background-color: #f1f1f1;
        }

        .info-list a {
            color: #d32f2f;
            text-decoration: none;
            font-weight: bold;
        }

        .info-list a:hover {
            text-decoration: underline;
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


<div class="container">
    <h1>Post Information</h1>

    <?php if (isset($message)): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit">Post</button>
    </form>

    <div class="nav-link">
        <a href="index.php">Back to Dashboard</a>
    </div>

    <div class="info-list">
        <h2>Existing Information</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $information->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . (strlen($row['content']) > 100 ? '...' : ''); ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($_GET['edit'])): ?>
        <?php
        $id = intval($_GET['edit']);
        $stmt = $mysqli->prepare("SELECT * FROM information WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $edit_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        ?>
        <div class="container">
            <h2>Edit Information</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_info['id']); ?>">
                <label for="edit_title">Title:</label>
                <input type="text" id="edit_title" name="title" value="<?php echo htmlspecialchars($edit_info['title']); ?>" required>

                <label for="edit_content">Content:</label>
                <textarea id="edit_content" name="content" required><?php echo htmlspecialchars($edit_info['content']); ?></textarea>

                <button type="submit" name="edit">Update</button>
            </form>
            <div class="nav-link">
                <a href="?">Back to List</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
