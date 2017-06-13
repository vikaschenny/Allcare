<?php
$html = new simple_html_dom();
 
// Load from a string
$html->load('<html><body><p>Hello World!</p><p>We're here</p></body></html>');
 
// Load a file
$html->load_file('http://net.tutsplus.com/');
?>