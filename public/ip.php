<!DOCTYPE html>
<html>
<head>
	<title>Mon IP</title>
	<style type="text/css">
		#ip {
			font-size: 42px;
			text-align: center;
			color: #000000;
		}
	</style>
</head>
<body>
<div id="ip">IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></div>
</body>
</html>