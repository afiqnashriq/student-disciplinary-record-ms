<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Error - UPTM System</title>
  <link rel="icon" type="image/png" href="relate/uptm_logo2.png">
  <style>
    body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #fff3f3;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.error-container {
  background-color: white;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(255,0,0,0.2);
  text-align: center;
  max-width: 400px;
}

h1 {
  color: #d8000c;
  margin-bottom: 20px;
}

p {
  color: #333;
  margin-bottom: 10px;
}

.back-button {
  display: inline-block;
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #d8000c;
  color: white;
  text-decoration: none;
  border-radius: 5px;
}

.back-button:hover {
  background-color: #a00000;
}

  </style>
</head>
<body>
  <div class="error-container">
    <h1>⚠️ Something Went Wrong</h1>
    <p>We're sorry, but an error occurred while processing your request.</p>
    <p>Please check your input or try again later.</p>
    <a href="index.php" class="back-button">Return to Login</a>
  </div>
</body>
</html>
