<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginBtn'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../sql/index.php");
        exit;
    }

    // Authenticate user
    $user = authenticateUser($pdo, $email, $password);
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        $redirectPage = ($user['role'] === 'HR') ? '../sql/HRapplicantmanagement.php' : '../sql/jobApplicantpage.php';
        header("Location: $redirectPage");
        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: ../sql/index.php");
        exit;
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registerBtn'])) {
    $name = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../sql/registerpage.php");
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Register user
    if (registerUser($pdo, $name, $email, $hashedPassword, $role)) {
        $_SESSION['message'] = "Registration successful. You can now log in.";
        header("Location: ../sql/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Error registering user.";
        header("Location: ../sql/registerpage.php");
        exit;
    }
}

// Handle job application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyToJobBtn'])) {
    $jobID = $_POST['jobID'] ?? '';
    $applicantID = $_POST['applicantID'] ?? $_SESSION['user_id']; // Default to current user if not set
    $message = $_POST['message'] ?? ''; // Message entered by the applicant
    $resume = $_FILES['resume'] ?? null; // Resume file

    // Validate input
    if (empty($jobID) || empty($message) || !$resume || $resume['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "All fields are required and a valid resume must be uploaded.";
        header("Location: ../sql/jobApplicantpage.php");
        exit;
    }

    // Process file upload
    $resumePath = '../uploads/' . basename($resume['name']);
    if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
        // Save application to database
        $isApplicationSaved = applyForJob($pdo, $applicantID, $jobID, $message, $resumePath);
        
        if ($isApplicationSaved) {
            $_SESSION['message'] = "Application submitted successfully!";
            header("Location: ../sql/jobApplicantpage.php");
            exit;
        } else {
            $_SESSION['error'] = "Error submitting application.";
            header("Location: ../sql/jobApplicantpage.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error uploading resume.";
        header("Location: ../sql/jobApplicantpage.php");
        exit;
    }
}



// Handle sending a follow-up message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendFollowUpBtn'])) {
    $applicationID = $_POST['applicationID'] ?? '';
    $followUpMessage = $_POST['followUpMessage'] ?? '';
    $senderID = $_SESSION['user_id']; // The current user (applicant

    // Validate input
    if (empty($applicationID) || empty($followUpMessage)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../sql/jobApplicantpage.php");
        exit;
    }

    // Save follow-up message
    if (sendFollowUpMessage($pdo, $senderID, $followUpMessage)) {
        $_SESSION['message'] = "Follow-up sent successfully.";
        header("Location: ../sql/jobApplicantpage.php");
        exit;
    } else {
        $_SESSION['error'] = "Error sending follow-up message.";
        header("Location: ../sql/jobApplicantpage.php");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createJob'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];

    if (createJobPost($pdo, $title, $description, $requirements)) {
        $_SESSION['message'] = "Job post created successfully.";
        header("Location: ../sql/HRapplicantmanagement.php");
        exit;
    } else {
        $_SESSION['error'] = "Error creating job post.";
        header("Location: ../sql/HRapplicantmanagement.php");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acceptApplication'])) {
    $applicationID = $_POST['applicationID'];

    if (acceptApplication($pdo, $applicationID)) {
        $_SESSION['message'] = "Application accepted successfully.";
        header("Location: ../sql/HRapplicantmanagement.php");
        exit;
    } else {
        $_SESSION['error'] = "Error accepting application.";
        header("Location: ../sql/HRapplicantmanagement.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateStatusBtn'])) {
    $applicationID = $_POST['applicationID'];
    $status = $_POST['status']; // Get the status (Accepted/Rejected)
    $hrID = $_SESSION['user_id']; // HR's ID (sender)

    // Fetch the applicant's user_id from the application
    $sql = "SELECT user_id FROM job_applications WHERE id = :applicationID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($application) {
        $applicantID = $application['user_id'];

        // Call function to update the application status
        if (updateApplicationStatus($pdo, $applicationID, $status)) {
            // Prepare the message
            $messageContent = "Your application for the job has been $status.";
            
            // Call function to send a message to the applicant
            if (sendMessage($pdo, $hrID, $applicantID, $messageContent)) {
                $_SESSION['message'] = "Application status updated and notification sent!";
            } else {
                $_SESSION['error'] = "Error sending notification to the applicant.";
            }
        } else {
            $_SESSION['error'] = "Error updating the application status.";
        }
    } else {
        $_SESSION['error'] = "Application not found.";
    }

    header("Location: manage_applications.php?job_id=" . $_GET['job_id']);
    exit;
}


// Handle updating the message status to 'Read'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['markAsReadBtn'])) {
    $messageID = $_POST['message_id'] ?? '';

    if (!empty($messageID)) {
        // Update the message status to 'Read'
        $sql = "UPDATE messages SET status = 'Read' WHERE id = :message_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':message_id', $messageID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Message marked as read.";
        } else {
            $_SESSION['error'] = "Error marking message as read.";
        }
    } else {
        $_SESSION['error'] = "Invalid message ID.";
    }

    // Redirect back to the HR dashboard
    header("Location: ../sql/view_messages.php");
    exit;
}

?>
