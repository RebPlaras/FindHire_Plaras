<?php 
    require_once '../core/dbConfig.php'; 
    require_once '../core/models.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Hire - Register</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        h3 {
            text-align: center;
            font-size: 32px;
            margin-top: 50px;
            color: #2d3e50;
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 40px;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-box {
            margin-bottom: 30px;
        }
        .form-box h4 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        input, select {
            font-size: 1.1em;
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #2d3e50;
            background-color: #fff;
        }
        input[type="submit"] {
            background-color: #2d3e50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            padding: 12px;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #1a2734;
        }
        .register-link {
            text-align: center;
            font-size: 1.1em;
            margin-top: 20px;
        }
        .register-link a {
            color: #2d3e50;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h3>Register an Account</h3>

    <div class="form-container">
        <!-- Register Form -->
        <div class="form-box">
            <h4>Create Your Account</h4>
            <form action="../core/handleForms.php" method="POST">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
                
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                
                <label for="role">Register as</label>
                <select name="role" id="role" required>
                    <option value="Applicant">Applicant</option>
                    <option value="HR">HR</option>
                </select>
                
                <input type="submit" name="registerBtn" value="Register">
            </form>

            <div class="register-link">
                Already have an account? <a href="index.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
