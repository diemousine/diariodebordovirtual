<?php
/*
 * Esse arquivo contém todas as rotinas necessárias para:
 * - Estabelecer uma conexão com o banco de dados;
 */

// Esta função estabelece uma conexão com o bando de dados;
function bdcon() {

	// Create connection
	$conn = mysqli_connect('localhost', 'root', '', 'mousinec_dbv');
	// Check connection
	if (!$conn) {
    	die("Ihhhhhh bugou: " . mysqli_connect_error());
	}

	mysqli_set_charset($conn, 'utf8');
	return $conn;
}
?>