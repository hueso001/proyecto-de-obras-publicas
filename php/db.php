<?php 

// coneccion con mySQL server

$coneccion = mysqli_connect('localhost', 'root', '', 'test');
if (!$coneccion) {
    die('NO Conectado: ' . mysqli_connect_error());
}

?>