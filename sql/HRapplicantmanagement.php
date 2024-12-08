<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: index.php");
    exit;
}

// Fetch job posts and applications
require_once '../core/dbConfig.php';
require_once '../core/models.php';

$jobPosts = getJobPosts($pdo); // Fetch the job posts for the HR role
$messages = getMessagesForHR($pdo); // Fetch messages for HR

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - FindHire</title>
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
            position: relative;
        }
        header h1 {
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
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .container {
            padding: 20px;
            max-width: 1000px;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }
        input, textarea, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background-color: #2d3e50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }
        button:hover {
            background-color: #1a2734;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2d3e50;
            color: white;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        table td:last-child, table th:last-child {
            width: 200px; 
        }
        .table-actions a {
            padding: 6px 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .table-actions a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<header>
    <h1>HR Dashboard - FindHire</h1>
    <form action="logout.php" method="POST">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
</header>

<div class="container">

    <!-- Job Post Form -->
    <section>
        <h2>Create Job Post</h2>
        <form method="POST" action="../core/handleForms.php">
            <input type="text" name="title" placeholder="Job Title" required>
            <textarea name="description" placeholder="Job Description" required></textarea>
            <textarea name="requirements" placeholder="Requirements" required></textarea>
            <button type="submit" name="createJob">Create Job</button>
        </form>
    </section>

    <!-- View Messages Section -->
    <section>
        <h2><a href="view_messages.php">View All Messages</a></h2>
    </section>

    <!-- Job Posts Table -->
    <section>
        <h2>Job Posts</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Requirements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobPosts as $jobPost) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jobPost['title']); ?></td>
                        <td><?php echo htmlspecialchars($jobPost['description']); ?></td>
                        <td><?php echo htmlspecialchars($jobPost['requirements']); ?></td>
                        <td class="table-actions">
                            <a href="manage_applications.php?job_id=<?php echo $jobPost['id']; ?>">Manage Applications</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>

</div>

</body>
</html>
