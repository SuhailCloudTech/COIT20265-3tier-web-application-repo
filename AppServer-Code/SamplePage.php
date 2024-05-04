<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virgin Australia Application Database Connectivity Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #005086; /* Dark Blue */
            color: white;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin-top: 0;
            color: white; /* White color for the title */
        }
        footer {
            background-color: #005086;
            color: white;
            padding: 10px 20px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #005086; /* Dark Blue */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        input[type="text"], input[type="submit"], .back-button, .delete-button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        input[type="text"] {
            width: 250px;
        }
        input[type="submit"], .delete-button {
            background-color: #4CAF50; /* Green */
            color: white;
        }
        input[type="submit"]:hover, .back-button:hover, .delete-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>Database Test Page</h1>
    </header>

    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="NAME" maxlength="45" required />
        <label for="address">Address:</label>
        <input type="text" id="address" name="ADDRESS" maxlength="90" required />
        <input type="submit" value="Add Data" />
    </form>

    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
        <input type="submit" name="clear" value="Clear Data" />
    </form>

    <button class="back-button" onclick="goBack()">Go Back</button>

    <table>
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>ADDRESS</th>
        </tr>

        <?php
        include "/var/www/inc/dbinfo.inc";

        function AddEmployee($connection, $name, $address)
        {
            $n = mysqli_real_escape_string($connection, $name);
            $a = mysqli_real_escape_string($connection, $address);
            $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";
            if (!mysqli_query($connection, $query)) {
                echo ("<p>Error adding employee data.</p>");
            }
        }

        function VerifyEmployeesTable($connection, $dbName)
        {
            if (!TableExists("EMPLOYEES", $connection, $dbName)) {
                $query = "CREATE TABLE EMPLOYEES (
                    ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    NAME VARCHAR(45),
                    ADDRESS VARCHAR(90)
                )";
                if (!mysqli_query($connection, $query)) {
                    echo ("<p>Error creating table.</p>");
                }
            }
        }

        function TableExists($tableName, $connection, $dbName)
        {
            $t = mysqli_real_escape_string($connection, $tableName);
            $d = mysqli_real_escape_string($connection, $dbName);
            $checktable = mysqli_query($connection,
                "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");
            if (mysqli_num_rows($checktable) > 0) {
                return true;
            }
            return false;
        }

        // Function to clear all data from the table
        function clearData($connection)
        {
            $query = "TRUNCATE TABLE EMPLOYEES";
            if (!mysqli_query($connection, $query)) {
                echo ("<p>Error clearing employee data.</p>");
            }
        }

        // Establish database connection
        $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // Select the database
        $database = mysqli_select_db($connection, DB_DATABASE);

        // Verify if employees table exists, if not create it
        VerifyEmployeesTable($connection, DB_DATABASE);

        // Process form submission and add employee data to database
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $employee_name = htmlentities($_POST['NAME']);
            $employee_address = htmlentities($_POST['ADDRESS']);
            if (strlen($employee_name) || strlen($employee_address)) {
                AddEmployee($connection, $employee_name, $employee_address);
            }
            // Clear data if clear button is clicked
            if (isset($_POST['clear'])) {
                clearData($connection);
            }
        }

        // Retrieve and display employee data from database
        $result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");
        while ($query_data = mysqli_fetch_row($result)) {
            echo "<tr>";
            echo "<td>", $query_data[0], "</td>",
            "<td>", $query_data[1], "</td>",
            "<td>", $query_data[2], "</td>";
            echo "</tr>";
        }
        mysqli_free_result($result);
        mysqli_close($connection);
        ?>

    </table>

    <footer>
        <p>Virgin Australia. All rights reserved.</p>
    </footer>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
