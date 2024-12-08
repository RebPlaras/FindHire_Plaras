<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: index.php");
    exit;
}

// Fetch job applications for a specific job post
require_once '../core/dbConfig.php';
require_once '../core/models.php';

$jobID = $_GET['job_id'] ?? ''; // Get the job ID from the URL
if (empty($jobID)) {
    // Redirect if no job ID is provided
    header("Location: HRapplicantmanagement.php");
    exit;
}

// Fetch job details
$jobPost = getJobPostById($pdo, $jobID);
if (!$jobPost) {
    // Redirect if the job doesn't exist
    header("Location: HRapplicantmanagement.php");
    exit;
}

// Handle application status update (accept or reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateStatus'])) {
    $applicationID = $_POST['applicationID'];
    $newStatus = $_POST['status'];

    if (updateApplicationStatus($pdo, $applicationID, $newStatus)) {
        $_SESSION['message'] = "Application status updated successfully!";
        header("Location: manage_applications.php?job_id=$jobID");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update application status.";
    }
}

$applications = getApplicationsByJobId($pdo, $jobID);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Applications - FindHire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #2d3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        header h1 {
            margin: 0;
        }
        .container {
            padding: 20px;
            max-width: 1100px;
            margin: 30px auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
        }
        .back-link:hover {
            background-color: #2980b9;
        }
        h2 {
            color: #2d3e50;
            margin-bottom: 15px;
        }
        .message, .error {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
            background-color: #dfe6e9;
            color: #2d3e50;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2d3e50;
            color: white;
        }
        td a {
            color: #3498db;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
        select, input[type="submit"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<header>
    <h1>Manage Applications for Job: <?php echo htmlspecialchars($jobPost['title']); ?></h1>
</header>

<div class="container">
    <a href="HRapplicantmanagement.php" class="back-link">Back to Dashboard</a>

    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='message'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <h2>Applications</h2>
    <table>
        <thead>
            <tr>
                <th>Applicant ID</th>
                <th>Applicant Name</th>
                <th>Application Status</th>
                <th>Resume</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application) { ?>
    <tr>
        <td><?php echo htmlspecialchars($application['user_id']); ?></td>
        <td><?php echo htmlspecialchars($application['applicant_name']); ?></td>
        <td><?php echo htmlspecialchars($application['application_status']); ?></td>
        <td>
            <?php if (!empty($application['resume'])) { ?>
                <a href="<?php echo htmlspecialchars($application['resume']); ?>" target="_blank">View Resume</a>
            <?php } else { ?>
                No resume uploaded
            <?php } ?>
        </td>
        <td><?php echo nl2br(htmlspecialchars($application['message'])); ?></td>
        <td>
            <form action="manage_applications.php?job_id=<?php echo $jobID; ?>" method="POST">
                <input type="hidden" name="applicationID" value="<?php echo htmlspecialchars($application['application_id']); ?>">
                <select name="status" required>
                    <option value="Pending" <?php echo ($application['application_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Accepted" <?php echo ($application['application_status'] == 'Accepted') ? 'selected' : ''; ?>>Accepted</option>
                    <option value="Rejected" <?php echo ($application['application_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <input type="submit" name="updateStatus" value="Update Status">
            </form>
        </td>
    </tr>
<?php } ?>

        </tbody>
    </table>
</div>

</body>
</html>
