<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to your MySQL database (replace these with your actual credentials)
    $host = "localhost";
    $username = "root";
    $password = "root@1";
    $database = "bill";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

if(isset($_POST['search'])){
    $invoice = $_POST['search'];
    $sql = "SELECT * FROM bill_master b
            INNER JOIN cust_master c ON c.cust_id = b.cust_id
            INNER JOIN bill_details a ON b.bill_id = a.bill_id
            WHERE b.invoice_no = $invoice";

    $result = $conn->query($sql);
}

    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Bill Format with Search</title>
    <link rel="stylesheet" href="bill.css">
</head>
<body>
    <header>
        <div class="search-bar">
            <form method="post" action="">
                <input type="text" class="search-input" name="search" placeholder="Search...">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>
    
    <?php 
    if (isset($result) && $result->num_rows > 0) {
        // Fetching the common details (customer information, discount, etc.) only once
        $row = $result->fetch_assoc();
    ?>

    <div class="bill-format">
        <div class="customer-info">
            <p>Name: <?php echo $row['cust_name']; ?></p>
            <p>Phone No: <?php echo $row['phone']; ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Image</th>
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through items from the database
                // Note: You need to adjust this part based on your actual database structure
                // For now, it's just a placeholder
                do {
                ?>
                    <tr>
                        <td><?php echo $row['item_name']; ?></td>
                        <td> <img src=".<?php echo $row['img']; ?>" width="30px" height="50px"></td> 
                        <td><?php echo $row['rate']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                    </tr>
                <?php 
                } while ($row = $result->fetch_assoc());
                ?>
                
            </tbody>
         
        </table>

        <table>

        <?php  
            $result->num_rows>0;
            if($row = $result->fetch_assoc()){
         ?>

        <tr>
                        <td>discount type <br><?php echo $row['discount_type']; ?></td>
                        <td><?php echo $row['discount']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>Discount Amount</td>
                        <td><?php echo $row['discount_amount']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>Net amount</td>
                        <td><?php echo $row['total_amount']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>GST</td>
                        <td><?php echo $row['gst']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>GST Amount</td>
                        <td><?php echo $row['gst_amount']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>Current Amount</td>
                        <td><?php echo $row['net_amount']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>Roundoff </td>
                        <td><?php echo $row['rounding_by']; ?></td>
                       
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td><?php echo $row['rounding_off']; ?></td>
                       
                    </tr>

                    </table>
                    <?php } ?>
                    
    </div>
    <?php 
    }
    ?>
</body>
</html>