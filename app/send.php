<?php

/* Function definition is taken from
 * http://stackoverflow.com/a/5965940/3021745
 */
// function definition to convert array to xml
function array_to_xml($info, $xml) {
  foreach ($info as $key => $value) {
    if(is_array($value)) {
      if(!is_numeric($key)) {
        $subnode = $xml->addChild(ucfirst($key));
        array_to_xml($value, $subnode);
      } else {
        $subnode = $xml;
        array_to_xml($value, $subnode);
      }
    } else {
      $xml->addChild(ucfirst($key), "$value");
    }
  }
}

$url = 'http://glassfish.spagu.metropolia.fi/jonix/send?key=d5c54443-530d-40c0-89f9-0bcbc8cfb298';

// Get the JSON data
$data = file_get_contents("php://input");
$data = json_decode($data, true);

// ONIX message string
$onix = '';

// Transcode it to XML
$xml = new SimpleXMLElement('<ONIXMessage release="3.0" xmlns="http://ns.editeur.org/onix/3.0/reference"></ONIXMessage>');

// Function call to convert array to XML
array_to_xml($data, $xml);

// Passing on the generated XML
$onix = $xml->asXML();

// TODO: Send to server, get respond
// Create curl handle
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $onix);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Execute
$ch_result = curl_exec($ch);

// Check if any error occured
if(curl_errno($ch))
{
  $info = new StdClass();
  $info->result = curl_error($ch);
  $info->http_code = 404;
  $info->onix = $onix;
} else {
  $info = curl_getinfo($ch);
  $info['result'] = $ch_result;
  $info['onix'] = $onix;
  foreach ($data['product'] as $prod) {
    $info['view_product'][] = "http://glassfish.spagu.metropolia.fi/jonix/products/".
      $prod['recordReference'];
  }
}

// Close the connection
curl_close($ch);

// Return some status messages
echo json_encode($info);
