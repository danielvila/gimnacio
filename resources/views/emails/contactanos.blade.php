<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Contactenos</title>
</head>
<body>	
	<?php
	if(isset($data['days'])){
	?>
	<h1>Aviso</h1>
	<p>Estimado {{$data['name']}} su membresia se vence en {{$data['days']}} días.</p>
	<?php
	}else{
	?>
	<h1>Mensaje desde la web</h1>
	<p><strong>Nombre:</strong> {{$data['name']}}</p>
	<p><strong>Correo:</strong> {{$data['email']}}</p>
	<p><strong>Telefono:</strong> {{$data['phone']}}</p>
	<p><strong>Mensaje:</strong> {{$data['description']}}</p>
	<?php
	}
	?>
</body>
</html>