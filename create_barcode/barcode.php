<html>
<head>
<style>
p.inline {display: inline-block;}
span { font-size: 13px;}
</style>
<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */

    }
</style>
</head>
<body onload="window.print();">
	<div style="margin-left: 5%">
		<?php
		include 'barcode128.php';
		$product = null;
		$product_id = rand();
		$rate = null;
		for($i=1;$i<=$_POST['print_qty'];$i++){
			$code = $product_id + $i;
			echo "<p class='inline'><span ><b>Name: $product</b></span>".bar128(stripcslashes($code))."<span ><b></b><span></p>&nbsp&nbsp&nbsp&nbsp";
		}

		?>
	</div>
</body>
</html>