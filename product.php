<?php

session_start(); 


$username = "system";
$password = "poli12345";
$database = "localhost/XE";

$query = 'select * from products';
$connect = oci_connect($username, $password, $database);

if (!$connect) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
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
		<br/>
		<br/>
		<div class="container-fluid">
			<div class="row no-gutters products">
			<?php
            $query = "select id, name, image, price, description, id_ray from products";

            $parse = oci_parse($connect, $query);
            $run = oci_execute($parse);
			

			while (($row = oci_fetch_assoc($parse)) != false) {
				echo '
						<form method="post" action="index.php?action=add&id= '.$row['ID'].'; ">
							<div align="center">
								<img src="images/'.$row['IMAGE'].'" class="img-fluid" /><br />

								<h1 class="text-info"> '.$row['NAME'].' </h1>
								
								<h3 class="text-info"> '.$row['DESCRIPTION'].' </h3>

								<h4 class="text-danger">$  '.$row['PRICE'].' </h4>

								<input type="text" name="quantity" value="1" class="form-control" />

								<input type="hidden" name="hidden_name" value=" '. $row['NAME'] .' " />

								<input type="hidden" name="hidden_price" value=" '.$row['PRICE'].' " />

								<a href="index.php">
									<input type="button" name="redirect_to_product" style="margin-top:5px; background-color: black;" class="btn btn-success" value="Inapoi la produse" />
								</a>


								<input type="submit" name="add_to_cart" style="margin-top:5px; background-color: black;" class="btn btn-success" value="Adauga in cos" />
							</div>
						</form>';
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