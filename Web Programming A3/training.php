<!DOCTYPE html>
<html lang='en-GB'>
<head>
    <title>PHP16 A</title>
    <style>
		body {
			font-family: Arial, sans-serif;
			margin: 0;
			padding: 0;
		}
		h1 {
			text-align: center;
			margin-top: 50px;
		}
		form {
			margin: 0 auto;
			width: 400px;
			border: 1px solid #ccc;
			padding: 20px;
		}
		label {
			display: block;
			margin-bottom: 10px;
		}
		select,
		input[type="text"],
		input[type="email"] {
			display: block;
			margin-bottom: 20px;
			padding: 10px;
			border: 1px solid #ccc;
			border-radius: 4px;
			width: 100%;
			box-sizing: border-box;
		}
		input[type="submit"] {
			background-color: #4CAF50;
			color: white;
			border: none;
			border-radius: 4px;
			padding: 10px 20px;
			cursor: pointer;
		}
		input[type="submit"]:hover {
			background-color: #3e8e41;
		}
	</style>
</head>
<body>
    <h1>Book Training Session</h1>
    <?php
    // Turn on error reporting and display all errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // The following PHP code is used to set up an error array to store validation errors
    $errors = [];
    
    // The following PHP code is used to define database connection parameters
    $hostname = 'studdb.csc.liv.ac.uk'; 
    $dbname = 'sgpgezah'; 
    $username = 'sgpgezah'; 
    $password = 'sgpgezah1234'; 

    // The following PHP code is used to define the PDO data source name and options
    $dsn = "mysql:host=$hostname;dbname=$dbname";
    $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    );

    // Begin PHP code block
    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, $username, $password);

        // Query the database for all distinct training topics with capacity greater than zero, ordered alphabetically
        $topics_query = $pdo->query("SELECT DISTINCT topic FROM training_sessions WHERE capacity > 0 ORDER BY topic ASC");
        // Fetch all results from the query into an associative array
        $topics = $topics_query->fetchAll(PDO::FETCH_ASSOC);
    ?>  <!-- Display a form to select a training topic -->
        <form action="training.php" method="post">
            <label for="topic">Select a topic:</label>
            <select name="topic" id="topic">
            <option selected disabled>Select an option</option>

            <!-- Loop through each topic and add an option to the select dropdown for each one -->
                <?php foreach ($topics as $topic) { ?>
                    <option value="<?= $topic['topic'] ?>"><?= $topic['topic'] ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Submit">
        </form>

        <?php
        // If a topic has been selected and submitted, show available times
        if (isset($_POST['topic'])) {
            $topic_id = $_POST['topic'];

            // Prepare a statement to query the database for all training times for the selected topic, with capacity greater than zero
            $times_query = $pdo->prepare("SELECT datetime FROM training_sessions WHERE topic = :topic AND capacity > 0 ORDER BY datetime ASC");
            // Bind the value of the topic ID to the prepared statement
            $times_query->bindParam(':topic', $topic_id, PDO::PARAM_STR);
            // Execute the prepared statement and fetch all results into an associative array
            $times_query->execute();
            $times = $times_query->fetchAll(PDO::FETCH_ASSOC);
        ?>  <!-- Display a form to select a training time for the selected topic -->
            <form action="training.php" method="post">
                <label for="datetime">Select a time:</label>
                <select name="datetime" id="datetime">
                    <option selected disabled>Select an option</option>
                     <!-- Loop through each available time and add an option to the select dropdown for each one -->
                    <?php foreach ($times as $time) { ?>
                        <option value="<?= $time['datetime'] ?>"><?= $time['datetime'] ?></option>
                    <?php } ?>
                </select>

                 <!-- Include a hidden input field to pass the selected topic ID to the next form submission -->
                <input type="hidden" name="topic" value="<?= $topic_id ?>">
                <!-- Display a submit button to submit the form and book the selected training time -->
                <input type="submit" value="Submit">
            </form>
        <?php } ?>

        <?php
        // If time has been submitted, show name form
        if (isset($_POST['datetime'])) {
            $datetime = $_POST['datetime'];
        ?>  <!-- Display a form to get user's name -->
            <form action="training.php" method="post">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name">
                <input type="hidden" name="datetime" value="<?= $datetime ?>">
                <input type="hidden" name="topic" value="<?= $_POST['topic'] ?>">
                <input type="submit" name="submit_name" value="Submit Name">
            </form>
        <?php } ?>
        <?php
        // Validate name
        if (isset($_POST['submit_name'])) {
             // Get the submitted name and trim it
            $name = trim($_POST['name']);
            if (!empty($name)) {
                  // Check if the name is in the correct format
                $regex = '/^[a-zA-Z\s]+$/i';
                if (!preg_match($regex, $name)) {
                    $errors['name'] = 'Invalid name format';
                }
            } else {
                $errors['name'] = 'Name is required';
            }
             // If there are errors, display them, otherwise display email form
            if (isset($errors['name'])) {
                echo "<p style='color: red'>" . $errors['name'] . "</p>";
            } else {
    ?>           <!-- Display a form to get user's email -->
                <form action="training.php" method="post">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
                    <input type="hidden" name="datetime" value="<?= $_POST['datetime'] ?>">
                    <input type="hidden" name="topic" value="<?= $_POST['topic'] ?>">
                    <input type="submit" name="submit_email" value="Confirm Booking">
                </form>
    <?php
            }
        }
    ?>
<?php 
// Initialize $errors array to store any validation errors
$errors = array();

    // Validate email
    if (isset($_POST['submit_email'])) {
        // Clear $errors array
        $errors = array();
        $email = trim($_POST['email']);

        // Check if email is empty
        if (empty($email)) {
            $errors['email'] = 'Please enter a valid email address.';
        } else {
            // Validate email format using regular expression
            $regex = '/^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,}$/i';
            if (!preg_match($regex, $email)) {
                $errors['email'] = 'Invalid email format';
            }
        }
        // Retrieve topic name and time if no errors
        if (empty($errors)) {
             // Query for distinct topics
            $topic_query = $pdo->prepare("SELECT DISTINCT topic FROM training_sessions WHERE capacity > 0 ORDER BY topic ASC");
            $topic_query->execute();
            $topic = $topic_query->fetch();
            $topic_id = $topic['topic'];

            // Query for available times for selected topic
            $time_query = $pdo->prepare("SELECT datetime FROM training_sessions WHERE topic = :topic AND capacity > 0 ORDER BY datetime ASC");
            $time_query->bindParam(':topic', $topic_id, PDO::PARAM_STR);
            $time_query->execute();
            $time = $time_query->fetchColumn();

            // Create DateTime object from $time and format it for insertion into database
            $datetime = new DateTime($time);
            $formatted_datetime = $datetime->format('Y-m-d H:i:s');
        }
        // Validate name
        $name = trim($_POST['name']);
        
        if (empty($name)) {
            $errors['name'] = 'Please enter a name.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p style='color: red'>" . $error . "</p>";
            }
        } else {
            $pdo->beginTransaction();
            // Check if the selected topic and date-time has available capacity
            $topic_id = $_POST['topic'];
            $datetime = $_POST['datetime'];
            $capacity_query = $pdo->prepare("SELECT capacity FROM training_sessions WHERE topic = :topic AND datetime = :datetime");
            $capacity_query->bindParam(':topic', $topic_id, PDO::PARAM_STR);
            $capacity_query->bindParam(':datetime', $datetime, PDO::PARAM_STR);
            $capacity_query->execute();
            $capacity = $capacity_query->fetchColumn();

            // Retrieve the total capacity of all sessions
            $total_capacity_query = $pdo->prepare("SELECT SUM(capacity) FROM training_sessions");
            $total_capacity_query->execute();
            $total_capacity = $total_capacity_query->fetchColumn();

            if ($capacity <= 0) {
                // The session is fully booked, rollback the transaction and return an error message
                $pdo->rollBack();
                $errors['capacity'] = 'No more sessions available';
            } else if ($capacity_query->rowCount() > 0 && $capacity_query->rowCount() < $total_capacity) {
                // There are still some places available in other sessions, proceed with booking
            
                // Insert booking into database
                $insert_booking_query = $pdo->prepare("INSERT INTO bookings (topic, datetime, name, email) VALUES (:topic, :datetime, :name, :email)");
                $insert_booking_query->bindParam(':topic', $topic_id, PDO::PARAM_STR);
                $insert_booking_query->bindParam(':datetime', $formatted_datetime, PDO::PARAM_STR);
                $insert_booking_query->bindParam(':name', $name, PDO::PARAM_STR);
                $insert_booking_query->bindParam(':email', $email, PDO::PARAM_STR);
                $insert_booking_query->execute();

                // Update the capacity of the selected session
                $update_capacity_query = $pdo->prepare("UPDATE training_sessions SET capacity = capacity - 1 WHERE topic = :topic AND datetime = :datetime");
                $update_capacity_query->bindParam(':topic', $topic_id, PDO::PARAM_STR);
                $update_capacity_query->bindParam(':datetime', $datetime, PDO::PARAM_STR);
                $update_capacity_query->execute();

                // Commits a transaction and return a success message 
                $pdo->commit();
                // Display booking summary
                echo "<h2>Booking summary:</h2>";
                echo "<p><strong>Topic:</strong> " . $topic_id . "</p>";
                echo "<p><strong>Time:</strong> " . $time . "</p>";
                echo "<p><strong>Name:</strong> " . $name . "</p>";
                echo "<p><strong>Email:</strong> " . $email . "</p>";
            }
              else  {// All sessions are fully booked, return an error message
                $errors['capacity'] = 'All sessions are full';

            }     
        }
    }
?>
<?php } catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} ?>
    </body>
    </html>

                

                
