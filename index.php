<?php
function parser($t, $v) {
    $result;
    switch ($t) {
        case 'number':
            if(preg_match('/^-?\d+$/', $v)) {
                return intval($v);
            }
            if(preg_match('/^-?\d+\.\d+$/', $v)) {
                return floatval($v);
            }
            $result = intval($v);
            break;
        
        case 'boolean':
            $result = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            break;
            
        default:
            $result = $v;
            break;
    }
    return $result;
}

function getBackendConfig($conn){
    $sql = "SELECT * FROM backend_config";
    $result = $conn->query($sql);
    $a = [];
    while($row = $result->fetch_assoc()) {
        $a[$row['config_key']] = parser($row['config_type'], $row['config_value']);
    }
    return $a;
}

function getFrontendConfig($conn){
    $sql = "SELECT * FROM frontend_config";
    $result = $conn->query($sql);
    $a = [];
    while($row = $result->fetch_assoc()) {
        $a[$row['config_key']] = parser($row['config_type'], $row['config_value']);
    }
    return $a;
}


$options = json_decode(file_get_contents("./credentials.json"),true);



$conn = new mysqli($options['host'], $options['username'], $options['password'], $options["database"], $options["port"]);


if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
$config = [];
if(isset($_GET['backend'])){
    $config["backend"] = getBackendConfig($conn);
}

if(isset($_GET['frontend'])){
    $config["frontend"] = getFrontendConfig($conn);
}
header("Content-Type: application/json");

print(json_encode($config));

?>