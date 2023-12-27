<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to your MySQL database (replace these with your actual credentials)
    $host = "localhost";
    $username = "root";
    $password = "root@1";
    $database = "bill";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $customerName = $_POST["customer_name"];
    $customerAddress = $_POST["address"];
    $customerPhone = $_POST["phone"];
    $customerEmail = $_POST["email"];

    // Use prepared statements to prevent SQL injection
    $customerInsertQuery = $conn->prepare("INSERT INTO cust_master 
                                            (cust_name, 
                                            phone, email,
                                            address) 
                                        VALUES (?, ?, ?, ?)");
    if (!$customerInsertQuery) {
        die("Error preparing customer query: " . $conn->error);
    }

    $customerInsertQuery->bind_param("ssss", $customerName, $customerAddress, $customerPhone, $customerEmail);

    // Check if the customer data was inserted successfully
    if ($customerInsertQuery->execute()) {
        // Get the customer ID
        $customerId = $conn->insert_id;

        // Save invoice details
        $invoiceNumber = $_POST["invoice"];
        function isInvoiceNoExists($conn, $invoiceNo) {
            $invoiceNo = $conn->real_escape_string($invoiceNo); // Sanitize input to prevent SQL injection
            $sql = "SELECT invoice_no FROM bill_master WHERE invoice_no = '$invoiceNo'";
            $result = $conn->query($sql);
            return $result->num_rows > 0;
        }
        
        // Example usage - retrieve invoice number from POST request
       
        $invoiceNoToCheck = $invoiceNumber;

        if (isInvoiceNoExists($conn, $invoiceNoToCheck)) {
            echo '<script>
                if (confirm("Invoice number already exists. Do you want to proceed anyway?")) {
                    window.location.href = "trail.php";
                } else {
                    window.location.href = "trail.php";
                }
            </script>';
            $conn->close();
            exit; // Ensure that the script stops executing after redirection
        }
        
         $subtotal = $_POST["subtotal"];
        $discountType = $_POST["disc_type"];
        $discountValue = $_POST["disc_value"];
        $discountAmount =$_post["disc_amt"];
        $netAmount = $_POST["net_amt"];
        $gstPercent = $_POST["gst"];
        $gstAmount = $_POST["gst_amt"];
        $totalAmount = $_POST["t_amt"];
        $roundingBy = $_POST["round_by"];
        $roundingOff = $_POST["round_off"];

        // Use prepared statements
        $invoiceInsertQuery = $conn->prepare("INSERT INTO bill_master,subtot
                                             (invoice_no, discount_type, discount,discount_amount
                                              net_amount, gst, gst_amount, total_amount,
                                               rounding_by, rounding_off) 
                                                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$invoiceInsertQuery) {
            die("Error preparing invoice query: " . $conn->error);
        }

        $invoiceInsertQuery->bind_param("ssddddddd", $invoiceNumber, $discountType, $discountValue,  $discountAmount ,
                                        $netAmount, $gst, $gstAmount, $totalAmount, $roundingBy, $roundingOff);

                                       
        // Check if the invoice data was inserted successfully
        if ($invoiceInsertQuery->execute()) {
            // Get the invoice ID
            $invoiceId = $conn->insert_id;

            $itemCount = count($_POST['items']); // Adjust based on the actual form structure

            for ($i = 0; $i < $itemCount; $i++) {
                $itemName = $_POST['items']["item_$i"];
                $rate = $_POST['rates']["rate_$i"];
                $quantity = $_POST['quantities']["quantity_$i"];
                $amount = $_POST['amounts']["amount_$i"];
                $image = $_POST['img']["img_$i"];
            
                // Use prepared statements
                $itemInsertQuery = $conn->prepare("INSERT INTO bill_details (bill_id, item_name, rate, quantity, amount,img) VALUES (?, ?, ?, ?, ?,?)");
            
                if (!$itemInsertQuery) {
                    die("Error preparing item query: " . $conn->error);
                }
            
                $itemInsertQuery->bind_param("ssssss", $invoiceId, $itemName, $rate, $quantity, $amount,$image);
            
                // Check if the item data was inserted successfully
                if (!$itemInsertQuery->execute()) {
                    die("Error inserting item data: " . $itemInsertQuery->error);
                }
            
                // Close the item prepared statement
                $itemInsertQuery->close();
            }
            


            // Close the invoice prepared statement
            $invoiceInsertQuery->close();
        } else {
            die("Error inserting invoice data: " . $invoiceInsertQuery->error);
        }

        // Close the customer prepared statement
        $customerInsertQuery->close();
    } else {
        die("Error inserting customer data: " . $customerInsertQuery->error);
    }

 
    $conn->close();

 
     header("Location: trail.php");
     exit; 
 } else {
     //echo "Invalid request method";
 }
 ?>