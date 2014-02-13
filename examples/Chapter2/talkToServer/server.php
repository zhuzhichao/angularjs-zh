<?php

header('Cntent-type: text/json');

$items = array(
	array('id' => 0, "title" => "Paint pots", "description" => "Pots full of paint", "price" => 3.95),
	array("id" => 1, "title" => "Polka dots", "description" => "Dots with that polka groove", "price" => 12.95),
	array("id" => 2, "title" => "Pebbles", "description" => "Just little rocks, really", "price" => 6.95)
);
$return = json_encode($items);

print_r($return) ;

?>