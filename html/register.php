<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Basic styling for the form */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            background-image:url(https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/Picture4.png);
            background-size:cover;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: rgb(139, 172, 124);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgb(105, 155, 108);
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    
<div class="form-container">
    <h2>Register</h2>
    <form name="registerForm" action="../php/register.php" method="POST" onsubmit="return validateForm()">
        <script>
            function validateForm() {
                const username = document.forms["registerForm"]["username"].value;
                const password = document.forms["registerForm"]["password"].value;
                
                // Check if the username or password contains spaces
                const invalidChars = /[\s]/;  // Regex to detect spaces
                
                if (invalidChars.test(username)) {
                    alert("Username cannot contain spaces.");
                    return false;
                }
                
                if (invalidChars.test(password)) {
                    alert("Password cannot contain spaces.");
                    return false;
                }
                
                if (username.length < 6 || username.length > 12) {
                    alert("Username must be between 6 and 12 characters.");
                    return false;
                }

                // Check the length of the password
                if (password.length < 8 || password.length > 20) {
                    alert("Password must be between 8 and 20 characters.");
                    return false;
                }
        
                // You can also check for other invalid characters here if needed
                const specialChars = /[^a-zA-Z0-9]/;  // Allow only letters and numbers
                
                if (specialChars.test(username)) {
                    alert("Username can only contain letters and numbers.");
                    return false;
                }
        
                if (specialChars.test(password)) {
                    alert("Password can only contain letters and numbers.");
                    return false;
                }
        
                return true;  // Proceed with form submission
            }
            let response = await fetch('../php/register.php')
        </script>
        
        <!-- User inputs -->
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        
        <!-- Show error message if any -->
        
        <?php if (isset($error_message) && !empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="index_loged.php">Sign In</a></p>
</div>

</body>
</html>
