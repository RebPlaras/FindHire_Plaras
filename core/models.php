<?php 

require_once 'dbConfig.php';

function authenticateUser($pdo, $email, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Authentication successful
        }
    } catch (PDOException $e) {
        error_log("Error in authenticateUser: " . $e->getMessage());
    }
    return false; // Authentication failed
}

function registerUser($pdo, $name, $email, $password, $role) {
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        return $stmt->execute(); // Registration successful
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate email error
            error_log("Duplicate email error in registerUser: " . $e->getMessage());
        } else {
            error_log("Error in registerUser: " . $e->getMessage());
        }
    }
    return false; // Registration failed
}

// Function to fetch all job posts for applicants
function getAllJobPosts($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM job_posts");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all job posts
    } catch (PDOException $e) {
        error_log("Error in getAllJobPosts: " . $e->getMessage());
    }
    return []; // Return an empty array if there's an error
}

function applyForJob($pdo, $applicantID, $jobID, $message, $resumePath) {
    try {
        // Insert job application into the database with message and resume
        $stmt = $pdo->prepare("INSERT INTO job_applications (user_id, job_post_id, application_status, applied_at, message, resume) 
                               VALUES (:user_id, :job_post_id, :application_status, CURRENT_TIMESTAMP, :message, :resume)");

        // Bind the parameters
        $stmt->bindParam(':user_id', $applicantID);
        $stmt->bindParam(':job_post_id', $jobID);
        $stmt->bindParam(':application_status', $applicationStatus);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':resume', $resumePath);

        // Default status is "Pending"
        $applicationStatus = 'Pending';

        // Execute the query
        return $stmt->execute();  // Return true if the query executed successfully
    } catch (PDOException $e) {
        // Log the error and return false
        error_log("Error in applyForJob: " . $e->getMessage());
        return false;
    }
}


function sendFollowUpMessage($pdo, $senderID, $messageContent) {
    // Insert message into the database, no need for receiver_id anymore
    $sql = "INSERT INTO messages (sender_id, content) VALUES (:sender_id, :content)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sender_id', $senderID, PDO::PARAM_INT);
    $stmt->bindParam(':content', $messageContent, PDO::PARAM_STR);
    
    return $stmt->execute(); // Execute the query and return true if successful
}

function getMessagesForHR($pdo) {
    // Fetch all messages where sender is an applicant
    $sql = "SELECT m.id, m.sender_id, m.content, m.created_at, m.status, u.name AS applicant_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            ORDER BY m.created_at DESC";  // Order by message creation time
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserApplications($pdo, $userID) {
    $sql = "SELECT ja.id AS applicationID, jp.title AS job_title, ja.application_status AS status
            FROM job_applications ja
            JOIN job_posts jp ON ja.job_post_id = jp.id
            WHERE ja.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all applications for the user
}


function createJobPost($pdo, $title, $description, $requirements) {
    try {
        $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, requirements) VALUES (:title, :description, :requirements)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':requirements', $requirements);
        return $stmt->execute(); // Return true if insert is successful
    } catch (PDOException $e) {
        error_log("Error creating job post: " . $e->getMessage());
    }
    return false; // Return false if insert fails
}

function acceptApplication($pdo, $applicationID) {
    try {
        $stmt = $pdo->prepare("UPDATE job_applications SET application_status = 'Accepted' WHERE id = :id");
        $stmt->bindParam(':id', $applicationID);
        return $stmt->execute(); // Return true if update is successful
    } catch (PDOException $e) {
        error_log("Error accepting application: " . $e->getMessage());
    }
    return false; // Return false if update fails
}


function getMessages($pdo, $hrUserId) {
    // Get all messages where the HR is the receiver
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.content AS message, m.status, m.created_at
            FROM messages m
            WHERE m.receiver_id = :hrUserId
            ORDER BY m.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':hrUserId', $hrUserId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all messages for HR
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Function to fetch all job posts
function getJobPosts($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM job_posts ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all posts as an associative array
    } catch (PDOException $e) {
        error_log("Error fetching job posts: " . $e->getMessage());
        return [];  // Return an empty array in case of an error
    }
}

// Fetch job post by ID
function getJobPostById($pdo, $jobID) {
    // Prepare SQL query to fetch the job post with the given ID
    $sql = "SELECT * FROM job_posts WHERE id = :jobID";
    $stmt = $pdo->prepare($sql);

    // Bind the job ID to the prepared statement
    $stmt->bindParam(':jobID', $jobID, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the job post details (returns an associative array)
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getApplicationsByJobId($pdo, $jobId) {
    $sql = "SELECT ja.id AS application_id, ja.user_id, ja.application_status, 
                   ja.message, ja.resume, u.name AS applicant_name 
            FROM job_applications ja
            JOIN users u ON ja.user_id = u.id
            WHERE ja.job_post_id = :jobId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById($pdo, $userId) {
    $sql = "SELECT id, name FROM users WHERE id = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Return the user data, or an empty array if no user is found
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

function updateApplicationStatus($pdo, $applicationID, $newStatus) {
    $sql = "UPDATE job_applications 
            SET application_status = :newStatus 
            WHERE id = :applicationID";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
    $stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);

    return $stmt->execute();
}


function getApplicantMessages($pdo, $applicantId) {
    // Query to fetch messages sent by the applicant
    $stmt = $pdo->prepare("SELECT m.id, m.content, m.status, m.created_at
                           FROM messages m
                           WHERE m.sender_id = :applicant_id");
    $stmt->bindParam(':applicant_id', $applicantId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch and return the results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getApplicationDetails($pdo, $userId, $jobId) {
    $sql = "SELECT resume, message 
            FROM job_applications 
            WHERE user_id = :userId AND job_post_id = :jobId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
