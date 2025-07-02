<?php
// Load user->email data from CSV file outside of the webroot directory.
$csv_file = '../../users.csv'; // adjust path as needed
$data = array();
if (($handle = fopen($csv_file, 'r')) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data[$row[0]] = $row[1];
    }
    fclose($handle);
}

// Get URL search parameter, should be the user id from the CSV, which is used to pull in the associated email address
$search_param = isset($_GET['u']) ? $_GET['u'] : null;

if (!isset($search_param) || !isset($data[$search_param])) {
    $error_flag = true;
}
else {
    $error_flag = false;
    // Get email address from data array
    $email_address = $data[$search_param];
}

// Render form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Release: Winnipeg Folk Festival</title>
    <link rel="stylesheet" href="css/main.min.css">
    <style>
        .spinner-border {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Winnipeg Folk Festival Photo Release Form</h1>
        <?php
        if ($error_flag === true) {
            echo '<h2>No data</h2></div></body></html>';
            die();
        }
        ?>
        <form id="myForm" onsubmit="return validateForm()">
            <input type="hidden" id="crew_email" name="crew_email" value="<?php echo $email_address;?>">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
            
            <label for="photo_number">Photo Number</label>
            <input type="text" id="photo_number" name="photo_number" required>

            <label for="disclaimer">
                <input type="checkbox" id="disclaimer" name="disclaimer" required>
                I hereby give the Winnipeg Folk Festival permission to use any photos/video in which I or 
                my child/ward appear without incurring debt or liabilities of any <kind class=""></kind>
            </label>
            <br />
            <button class="btn btn-large btn btn-block" type="submit" value="Submit">Submit</button>
        </form>

        <div id="thank_you" style="display:none;">
            Form submitted. You should have an email record; please check your Spam folder if you cannot find it. Thank you!
        </div>

        <div id="waiting-message" style="display:none;">
            Submitting your information, please wait. <br />
            <div class="spinner-border" role="status">
                <span class="sr-only">.</span>
            </div>
        </div>
        
        <div id="error-message" style="display:none; color: red;"></div>

    </div>
    <script type="text/javascript">
    function validateForm() {
        var name = document.getElementById("name").value;
        var email = document.getElementById("email").value;
        var date = document.getElementById("date").value;
        var photo_number = document.getElementById("photo_number").value;
        var disclaimer = document.getElementById("disclaimer").checked;
        var crew_email = document.getElementById("crew_email").value;

        if (name == "" || email == "" || date == "" || photo_number == "" || !disclaimer) {
            alert("Please fill out all fields and acknowledge the disclaimer");
            return false;
        }

        // Display waiting message
        document.getElementById("myForm").style.display = "none";
        document.getElementById("waiting-message").style.display = "block";

        // Submit form data to PHP script for email processing
        var formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("date", date);
        formData.append("photo_number", photo_number);
        formData.append("disclaimer", disclaimer);
        formData.append("crew_email", crew_email);

        fetch("process_form-phpmailer.php", {
            method: "POST",
            body: formData
        })
        .then(response => {
            if (response.ok === false) {                    
                throw new Error(response.statusText);
            }
            return response.text();
        })
        .then(data => {
            document.getElementById("waiting-message").style.display = "none";
            document.getElementById("thank_you").style.display = "block";
        })
        .catch((error) => {
            document.getElementById("waiting-message").style.display = "none";
            document.getElementById("error-message").style.display = "block";
            document.getElementById("error-message").innerHTML = "An error occurred while processing your form. Please try again later.";
        });     

        return false;
    }   
    </script>
</body>
</html>