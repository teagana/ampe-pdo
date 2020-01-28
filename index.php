<?php

//CTRL + ~ to open terminal
//command to run on browser: php -S localhost:3000

$pdo = new PDO('sqlite:chinook.db');
$sql = 'SELECT InvoiceId, InvoiceDate, Total, customers.FirstName AS CustomerFirstName, 
customers.LastName AS CustomerLastName
    FROM invoices
    INNER JOIN customers
    ON customers.CustomerId = invoices.CustomerId';

//check if search parameter is set
if(isset($_GET['search'])) {
    $sql = $sql . ' WHERE customers.FirstName LIKE ?';
}

$statement = $pdo->prepare($sql);

//bind search parameter
if(isset($_GET['search'])) {
    $boundSearchParam = '%' . $_GET['search'] . '%';
    $statement->bindParam(1, $boundSearchParam); //number corresponds to the question mark
}

$statement->execute();

$invoices = $statement->fetchAll(PDO::FETCH_OBJ); //get results from statement
//the parameter makes it return as objects instead of associative arrays

// var_dump($invoices);

?>

<form action="index.php" method="GET">
    <input type="text" name="search" placeholder="Search..." 
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
    <button type="submit">Search</button>

    <a href="/">Clear</a>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Total</th> 
            <th colspan="2">Customer</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($invoices as $invoice) : ?>
            <tr>
                <td>
                    <?php echo $invoice->InvoiceId ?>
                </td>
                <td>
                    <?php echo $invoice->InvoiceDate ?>
                </td>
                <td>
                    <?php echo $invoice->Total ?>
                </td>
                <td>
                    <?php echo $invoice->CustomerFirstName . " " . $invoice->CustomerLastName ?>
                </td>
                <td>
                    <a href="invoice-details.php?invoice=<?php echo $invoice->InvoiceId ?>">
                        Details
                    </a>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if(count($invoices) === 0) : ?>
            <tr>
                <td colspan="4">
                    No results
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>