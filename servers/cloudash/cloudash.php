<?php

require_once("RestRequest.inc.php");

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

function cloudash_MetaData() {
	return array(
    'DisplayName' => 'cloudash',
    'APIVersion' => '1.1', // Use API Version 1.1
    'RequiresServer' => false, // Set true if module requires a server to work
  );
}

function cloudash_ConfigOptions() {
	$configarray = array(
	  "username" => array (
	    "FriendlyName" => "UserName",
	    "Type" => "text", # Text Box
	    "Size" => "25", # Defines the Field Width
	    "Description" => "Textbox",
	    "Default" => "Example",
	  ),
	  "password" => array (
	    "FriendlyName" => "Password",
	    "Type" => "password", # Password Field
	    "Size" => "25", # Defines the Field Width
	    "Description" => "Password",
	    "Default" => "Example",
	  ),
	  "disk" => array (
	    "FriendlyName" => "Disk Space",
	    "Type" => "text", # Text Box
	    "Options" => "25",
	    "Description" => "Disk Space",
	    "Default" => "Disk Space",
	  ),
	  "cpu" => array (
	    "FriendlyName" => "Cpu Available",
	    "Type" => "text", # Text Box
	    "Options" => "25",
	    "Description" => "Cpu Available",
	    "Default" => "CPU Available",
	  ),
	  "memory" => array (
	    "FriendlyName" => "Memory Available",
	    "Type" => "text", # Text Box
	    "Options" => "25",
	    "Description" => "Memory Available",
	    "Default" => "Memory Available",
	  ),
	  "comments" => array (
	    "FriendlyName" => "Notes",
	    "Type" => "textarea", # Textarea
	    "Rows" => "3", # Number of Rows
	    "Cols" => "50", # Number of Columns
	    "Description" => "Description goes here",
	    "Default" => "Enter notes here",
	  ),
	);
	return $configarray;
}

function cloudash_CreateAccount($params) {
	//error_log(print_r($params, true));
	$username = $params["serverusername"];
	$password = $params["serverpassword"];

	$request = new RestRequest("http://".$params["serverip"]."/api/user", "POST");
	$request->setUsername($username);
	$request->setPassword($password);
	$auth = array(
		"username" => utf8ToUnicode($params["clientsdetails"]["email"]),
		"password" => utf8ToUnicode($params["password"]),
	);
	$about = array(
		"name" => $params["clientsdetails"]["firstname"],
		"phone" => $params["clientsdetails"]["phonenumberformatted"],
		"nif" => $params["clientsdetails"]["customfields1"],
	);
	$address = array(
		"street" => $params["clientsdetails"]["address1"],
		"city" => $params["clientsdetails"]["city"],
		"country" => $params["clientsdetails"]["countryname"],
		"zip" => $params["clientsdetails"]["postcode"],
	);
	$maxresources = array(
		"vms" => $params["configoption4"],
		"memory" => $params["configoption5"],
		"storage" => $params["configoption3"],
		"cpu" => $params["configoption4"],
	);
	$par = array(
		"auth" => $auth,
		"about" => $about,
		"address" => $address,
		"maxresources" => $maxresources,
		"type" => "user"
	);
	$request->execute($par);
	$result = json_decode($request->getResponseBody(), true);
	if ($result["username"] === $params["clientsdetails"]["email"]) {
		return "success";
	} else {
		return $result["error"];
	}
}

function cloudash_SuspendAccount($params) {
	$username = $params["serverusername"];
	$password = $params["serverpassword"];

	$request = new RestRequest("http://".$params["serverip"]."/api/user/".$params["clientsdetails"]["email"], "PUT");
	$request->setUsername($username);
	$request->setPassword($password);
	$about = array(
		"name" => $params["clientsdetails"]["firstname"],
		"phone" => $params["clientsdetails"]["phonenumberformatted"],
		"nif" => $params["clientsdetails"]["customfields1"],
	);
	$address = array(
		"street" => $params["clientsdetails"]["address1"],
		"city" => $params["clientsdetails"]["city"],
		"country" => $params["clientsdetails"]["countryname"],
		"zip" => $params["clientsdetails"]["postcode"],
	);
	$par = array(
		"about" => $about,
		"address" => $address,
		"status" => "suspended"
	);
	$request->execute($par);
	$result = json_decode($request->getResponseBody(), true);
}

function cloudash_UnsuspendAccount($params) {
	$username = $params["serverusername"];
	$password = $params["serverpassword"];

	$request = new RestRequest("http://".$params["serverip"]."/api/user/".$params["clientsdetails"]["email"], "PUT");
	$request->setUsername($username);
	$request->setPassword($password);
	$about = array(
		"name" => $params["clientsdetails"]["firstname"],
		"phone" => $params["clientsdetails"]["phonenumberformatted"],
		"nif" => $params["clientsdetails"]["customfields1"],
	);
	$address = array(
		"street" => $params["clientsdetails"]["address1"],
		"city" => $params["clientsdetails"]["city"],
		"country" => $params["clientsdetails"]["countryname"],
		"zip" => $params["clientsdetails"]["postcode"],
	);
	$par = array(
		"about" => $about,
		"address" => $address,
		"status" => "active"
	);
	$request->execute($par);
	$result = json_decode($request->getResponseBody(), true);
}

function utf8ToUnicode($str) {
  return preg_replace_callback("/./u", function ($m) {
    $ord = ord($m[0]);
    if ($ord <= 127) {
      return $m[0];
    } else {
      return trim(json_encode($m[0]), "\"");
    }
  }, $str);
}
?>
