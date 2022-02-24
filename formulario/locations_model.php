<?php
require("../php/db.php");

// Gets data from URL parameters.
if(isset($_GET['add_location'])) {
    add_location();
}

// agrele lo que es direcciones, nombre y capas una imagen
function add_location(){
    $con=mysqli_connect ("localhost", 'root', '','cum');
    if (!$con) {
        die('Not connected : ' . mysqli_connect_error());
    }
    $lat = $_GET['lat'];
    $lng = $_GET['lng'];
    $storesName = $_GET['adress'];
    $phoneFormatted = $_GET['phoneFormatted'];
    $city = $_GET['city'];
    // Inserts new row with place data.
    $query = sprintf("INSERT INTO stores " .
        " (id, lat, lng, adress, phoneFormatted, city) " .
        " VALUES (NULL, '%s', '%s');",
        mysqli_real_escape_string($con,$lat),
        mysqli_real_escape_string($con,$storesName),
        mysqli_real_escape_string($con,$phoneFormatted),
        mysqli_real_escape_string($con,$city));

    $result = mysqli_query($con,$query);
    echo json_encode("Inserted Successfully");
    if (!$result) {
        die('Invalid query: ' . mysqli_error($con));
    }
}
function get_saved_locations(){
    $con=mysqli_connect ("localhost", 'root', '','test');
    if (!$con) {
        die('Not connected : ' . mysqli_connect_error());
    }
    // update location with location_status if admin location_status.
    $sqldata = mysqli_query($con,"select lng,lat from locations ");

    $rows = array();
    while($r = mysqli_fetch_assoc($sqldata)) {
        $rows[] = $r;

    }
    $indexed = array_map('array_values', $rows);

    //  $array = array_filter($indexed);

    echo json_encode($indexed);
    if (!$rows) {
        return null;
    }
}

?>