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
		$product = $request->product;
		$product_id = $request->product_id;
		$rate = $request->rate;
		for($i=1;$i<=$request->print_qty;$i++){
			$code = $request->product_id + $i;
			echo "<p class='inline'><span ><b>Item: $product</b></span>".bar128(stripcslashes($code))."<span><b></b><span></p>&nbsp&nbsp&nbsp&nbsp";
		}

		?>
	</div>
</body>
</html>