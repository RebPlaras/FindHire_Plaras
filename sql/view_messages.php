<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: index.php");
    exit;
}

// Fetch all messages
require_once '../core/dbConfig.php';
require_once '../core/models.php';

$messages = getMessagesForHR($pdo); // Fetch all messages sent to HR

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages - HR Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }
        header {
            background-color: #2d3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 32px;
        }
        a {
            color: #2d3e50;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .container {
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
        }
        .back-link:hover {
            background-color: #2980b9;
        }
        .message {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .message-header {
            font-weight: bold;
            color: #2d3e50;
        }
        .message-content {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .message-footer {
            font-size: 14px;
            color: #777;
        }
        button {
            background-color: #2d3e50;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #1a2734;
        }
    </style>
</head>
<body>

<header>
    <h1>View Messages - HR Management</h1>
</header>

<div class="container">
    <a href="HRapplicantmanagement.php" class="back-link">Back to Dashboard</a>

    <?php foreach ($messages as $message) { ?>
        <div class="message">
            <p class="message-header">From: <?php echo htmlspecialchars($message['applicant_name']); ?></p>
            <p class="message-content"><strong>Message:</strong> <?php echo htmlspecialchars($message['content']); ?></p>
            <p class="message-footer"><strong>Sent on:</strong> <?php echo htmlspecialchars($message['created_at']); ?></p>
            <p class="message-footer"><strong>Status:</strong> <?php echo htmlspecialchars($message['status']); ?></p>

            <!-- Mark message as 'Read' -->
            <?php if ($message['status'] === 'Sent') { ?>
                <form method="POST" action="../core/handleForms.php">
                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                    <button type="submit" name="markAsReadBtn">Mark as Read</button>
                </form>
            <?php } ?>
        </div>
    <?php } ?>
</div>

</body>
</html>
