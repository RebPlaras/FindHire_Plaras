<?php 
    require_once '../core/dbConfig.php'; 
    require_once '../core/models.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Login</title>
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
            max-width: 450px;
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
        input {
            font-size: 1em;
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }
        input:focus {
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
    <h3>Login Page</h3>

    <div class="form-container">
        <!-- login form -->
        <div class="form-box">
            <h4>Login to Your Account</h4>
            <form action="../core/handleForms.php" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                
                <input type="submit" name="loginBtn" value="Login">
            </form>

            <div class="register-link">
                Don't have an account? <a href="registerpage.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
