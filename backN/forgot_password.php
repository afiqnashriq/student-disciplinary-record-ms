<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - UPTM</title>
  <link rel="icon" type="image/png" href="../relate/uptm_logo2.png">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e3f2fd, #f0f4f8);
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .logo-container {
      position: absolute;
      top: 20px;
      right: 20px;
    }

.logo-container img {
  width: 200px; 
  height: auto;
  filter: drop-shadow(0 0 3px rgba(0,0,0,0.2));
}


    .reset-container {
      background-color: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      margin-bottom: 25px;
      color: #0078D7;
      font-size: 24px;
      font-weight: 600;
    }

    label {
      display: block;
      margin-top: 20px;
      font-weight: 600;
      color: #555;
      text-align: left;
    }

    input[type="email"] {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.3s ease;
    }

    input[type="email"]:focus {
      border-color: #0078D7;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 30px;
      background-color: #0078D7;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #005fa3;
    }

    @media (max-width: 480px) {
      .reset-container {
        padding: 30px 20px;
        margin: 0 15px;
      }

      h2 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="logo-container">
    <img src="../relate/uptm logo.png" alt="UPTM Logo">
  </div>

  <div class="reset-container">
    <h2>Reset Your Password</h2>
    <form action="send_verification.php" method="POST">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="Enter your registered email" required>

      <button type="submit">Send Verification</button>
    </form>
  </div>
</body>
</html>
