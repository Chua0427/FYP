<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    background-color: #f0f2f5;
    background-image: url('img/');
    background-repeat: no-repeat;
    background-size: cover;
    height: 62vh;
    margin: 0;
  
        }
        .form-container {
            max-width: 450px;
            margin: 60px auto;
            padding: 25px;
            background-color: white;
            border-radius: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
            margin-top: 250px;
            margin-right: 250px;
        }
        .form-container:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        .form-container h5 {
    text-align: center;
    margin-bottom: 25px;
    color: #343a40;
    border: 2px solid gray;
    border-radius: 10px; 
    padding: 5px 10px; 
    display: inline-block; 
    background-color: #f8f9fa;
    margin-left: 120px;

}
        
       .form-control {
            padding-left: 45px;
        }
        .input-group-text {
            width: 40px;
            justify-content: center;
            background-color: #f8f9fa;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
    </style>
</head>
<body>

    <div class="form-container">
        <h5>Signup Form</h5>
        <form action="send.php" method="POST">

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required autocomplete="off">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required autocomplete="off">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="off">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="off">
            </div>

            <div class="mb-3 input-group">
                <input type="hidden" id="otp" name="otp" class="form-control">
            </div>

            <input type="hidden" id="subject" name="subject" value="Recieved OTP">
            <input type="hidden" id="ip" name="ip" value="">

            <button type="submit" name="send" class="btn btn-primary w-100">
             Sign Up <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

   


    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for generating a random 6-digit number without repetition -->
   <script>
    function generateRandomNumber() {
        let min = 100000;
        let max = 999999;
        let randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
        
        // Ensure the number is not repeated
        let lastGeneratedNumber = localStorage.getItem('lastGeneratedNumber');
        while (randomNumber === parseInt(lastGeneratedNumber)) {
            randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
        }
        
        // Save the new number to localStorage
        localStorage.setItem('lastGeneratedNumber', randomNumber);
        
        return randomNumber;
    }

    // Wait for the DOM to fully load before setting the OTP value
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('otp').value = generateRandomNumber();
    });
</script>

</body>
</html>
