<?php
 
$services_json = getenv('VCAP_SERVICES');
$services = json_decode($services_json, true);
$config = null;
foreach ($services as $name => $service) {
    if (0 === stripos($name, 'mysql')) {
        $config = $service[0]['credentials'];
        break;
    }
}
is_null($config) && die('MySQL service information not found.');
 
$db_hostname = $config["hostname"];
$db_hostport = $config["port"];
$db_username = $config["user"];
$db_password = $config["password"];

//echo $db_hostname;
//echo "<br >";
//echo $db_hostport;
//echo "<br >";
//echo $db_username;
//echo "<br >";
//var_dump($db_hostname);
//var_dump($db_hostport);
//var_dump($db_username);
//var_dump($db_password);

?>