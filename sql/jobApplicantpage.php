<?php 
session_start();
require_once '../core/dbConfig.php'; 
require_once '../core/models.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Applicant') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Job Applications</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #2d3e50;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        header h3 {
            font-size: 24px;
            color: white;  
            margin-bottom: 20px;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2em;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
        }
        input, select, textarea {
            font-size: 1.2em;
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="file"] {
            padding: 5px;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2d3e50;
            color: white;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        td form {
            display: flex;
            flex-direction: column;
        }
        button, input[type="submit"] {
            background-color: #2d3e50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2em;
        }
        button:hover, input[type="submit"]:hover {
            background-color: #1a2734;
        }
        .message-textarea {
            min-height: 80px;
        }
    </style>
</head>
<body>

<header>
    <h3>FindHire - Job Application System</h3>
    <!-- Logout Button inside header -->
    <form action="logout.php" method="POST">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
</header>

<div class="container">

    <!-- Available Job Listings -->
    <h3>Available Job Listings</h3>
    <table>
        <thead>
            <tr>
                <th>Job ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Requirements</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $jobPosts = getAllJobPosts($pdo); 
            foreach ($jobPosts as $job) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['id']); ?></td>
                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                    <td><?php echo htmlspecialchars($job['description']); ?></td>
                    <td><?php echo htmlspecialchars($job['requirements']); ?></td>
                    <td>
                        <form action="../core/handleForms.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="jobID" value="<?php echo htmlspecialchars($job['id']); ?>">
                            <input type="hidden" name="applicantID" value="<?php echo $_SESSION['user_id']; ?>">
                            <label for="message">Why are you the best fit?</label>
                            <textarea name="message" id="message" class="message-textarea" required></textarea>
                            <label for="resume">Upload Resume (PDF):</label>
                            <input type="file" name="resume" id="resume" accept="application/pdf" required>
                            <input type="submit" name="applyToJobBtn" value="Apply">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Your Applications -->
    <h3>Your Applications</h3>
    <table>
        <thead>
            <tr>
                <th>Application ID</th>
                <th>Job Title</th>
                <th>Status</th>
                <th>Follow Up</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $applications = getUserApplications($pdo, $_SESSION['user_id']); 
            foreach ($applications as $application) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['applicationID']); ?></td>
                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                    <td>
                        <form action="../core/handleForms.php" method="POST">
                            <input type="hidden" name="applicationID" value="<?php echo htmlspecialchars($application['applicationID']); ?>">
                            <textarea name="followUpMessage" placeholder="Write your message" class="message-textarea" required></textarea>
                            <input type="submit" name="sendFollowUpBtn" value="Send Follow-Up">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Sent Messages -->
    <h3>Your Sent Messages</h3>
    <table>
        <thead>
            <tr>
                <th>Message ID</th>
                <th>Message Content</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Fetch all messages sent by the current applicant
            $messages = getApplicantMessages($pdo, $_SESSION['user_id']); 
            foreach ($messages as $message) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['id']); ?></td>
                    <td><?php echo htmlspecialchars($message['content']); ?></td>
                    <td><?php echo htmlspecialchars($message['status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>
