<?php 

error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$username = "system";
$password = "poli12345";
$database = "localhost/XE";

$query = 'select * from products';
$connect = oci_connect($username, $password, $database);
if (!$connect) {
    $m = oci_error();
    trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
}
 
$s = oci_parse($connect, $query);
if (!$s) {
    $m = oci_error($connect);
    trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
}
$r = oci_execute($s);
if (!$r) {
    $m = oci_error($s);
    trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
}
if (!$connect) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}


if(isset($_POST["add_to_cart"]))
{
	if(isset($_SESSION["shopping_cart"]))
	{
		$item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
		if(!in_array($_GET["id"], $item_array_id))
		{
			$count = count($_SESSION["shopping_cart"]);
			$item_array = array(
				'item_id'			=>	$_GET["id"],
				'item_name'			=>	$_POST["hidden_name"],
				'item_price'		=>	$_POST["hidden_price"],
				'item_quantity'		=>	$_POST["quantity"]
			);
			$_SESSION["shopping_cart"][$count] = $item_array;
		}
		else
		{
			echo '<script>alert("Produs deja adaugat in cosul de cumparaturi.")</script>';
		}
	}
	else
	{
		$item_array = array(
			'item_id'			=>	$_GET["id"],
			'item_name'			=>	$_POST["hidden_name"],
			'item_price'		=>	$_POST["hidden_price"],
			'item_quantity'		=>	$_POST["quantity"]
		);
		$_SESSION["shopping_cart"][0] = $item_array;
	}
}

if(isset($_GET["action"]))
{
	if($_GET["action"] == "delete")
	{
		foreach($_SESSION["shopping_cart"] as $keys => $values)
		{
			if($values["item_id"] == $_GET["id"])
			{
				unset($_SESSION["shopping_cart"][$keys]);
				echo '<script>alert("Produs eliminat din cosul de cumparaturi!")</script>';
				echo '<script>window.location="index.php"</script>';
			}
		}
	}
}

?>

<html>
	<head>
	
		<img src="images/banner.jpg" alt="BANNER"> 
		<title>E-shopping.ro</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

	</head>
	<body>
		<br />
		<div class="container">
			<br />
			<h3 align="center">Bun venit la E-shopping.ro!</a></h3><br />
			<br /><br />

		
			<div class="container-fluid">
			<div class="row no-gutters products">
			<?php
            $query = "select id, name, image, price from products";

            $parse = oci_parse($connect, $query);
            $run = oci_execute($parse);

			while (($row = oci_fetch_assoc($parse)) != false) {
				echo '<div class="col-md-4">
						<form method="post" action="index.php?action=add&id= '.$row['ID'].'; ">
							<div style="border:1px solid #333; background-color:#f1f1f1; border-radius:5px; padding:16px;" align="center">
								<img src="images/'.$row['IMAGE'].'" class="img-fluid" /><br />

								<h4 class="text-info"> '.$row['NAME'].' </h4>

								<h4 class="text-danger">$  '.$row['PRICE'].' </h4>

								<input type="text" name="quantity" value="1" class="form-control" />

								<input type="hidden" name="hidden_name" value=" '. $row['NAME'] .' " />

								<input type="hidden" name="hidden_price" value=" '.$row['PRICE'].' " />

								<a href="product.php">
									<input type="button" name="redirect_to_product" style="margin-top:5px; background-color: black;" class="btn btn-success" value="Vezi detalii" />
								</a>


								<input type="submit" name="add_to_cart" style="margin-top:5px; background-color: black;" class="btn btn-success" value="Adauga in cos" />
							</div>
						</form>
					</div>';
			}
			?>
			</div>
			</div>
			
			<div style="clear:both"></div>
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			<h3>Detalii comanda</h3>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tr>
						<th width="40%">Nume produs</th>
						<th width="10%">Cantitate</th>
						<th width="20%">Pret</th>
						<th width="15%">Total</th>
						
					</tr>
					<?php
					if(!empty($_SESSION["shopping_cart"]))
					{
						$total = 0;
						foreach($_SESSION["shopping_cart"] as $keys => $values)
						{
					?>
					<tr>
						<td><?php echo $values["item_name"]; ?></td>
						<td><?php echo $values["item_quantity"]; ?></td>
						<td>$ <?php echo $values["item_price"]; ?></td>
						<td>$ <?php echo number_format((float)$values["item_quantity"] * (float)$values["item_price"], 2); ?></td>
						<td><a href="index.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span class="text-danger">Elimina produs</span></a></td>
					</tr>
					<?php
							$total = (float)$total + ((float)$values["item_quantity"] * (float)$values["item_price"]);
						}
					?>
					<tr>
						<td colspan="3" align="right">Total</td>
						<td align="right">$ <?php echo number_format($total, 2); ?></td>
						<td></td>
					</tr>
					<?php
					}
					?>
						
				</table>
			</div>
		</div>
	</div>
	<br />
	</body>
</html>