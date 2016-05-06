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
	  "disk" => array (
	    "FriendlyName" => "HDD",
	    "Type" => "text", # Text Box
	    "Options" => "25",
	    "Description" => "HD",
	    "Default" => "10",
	  ),
	  "cpu" => array (
	    "FriendlyName" => "CPU Cores",
	    "Type" => "text", # Text Box
	    "Options" => "25",
	    "Description" => "CPU Cores",
	    "Default" => "1",
	  ),
	  "memory" => array (
	    "FriendlyName" => "Memory",
	    "Type" => "text", # Text Box
	    "Options" => "25",
	    "Description" => "Memory",
	    "Default" => "1024",
	  )
	);
	return $configarray;
}

function cloudash_CreateAccount($params) {
	error_log(print_r($params, true));
	$username = $params["serverusername"];
	$password = $params["serverpassword"];

	$request = new RestRequest($params["serverip"]."/api/user", "POST");
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
		"vms" => $params["configoption2"],
		"memory" => $params["configoption3"],
		"storage" => $params["configoption1"],
		"cpu" => $params["configoption2"],
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

	$request = new RestRequest($params["serverip"]."/api/user/".$params["clientsdetails"]["email"], "PUT");
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

	$request = new RestRequest($params["serverip"]."/api/user/".$params["clientsdetails"]["email"], "PUT");
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
