<?php
//topics
$GLOBALS['RESPONSE_VALIDATE_TOPIC'] = "PGL/response/valid";
$GLOBALS['RESPONSE_SEND_EVENTS_TOPIC'] = "PGL/response/send_events";
$GLOBALS['RESPONSE_EMERGENCY_TOPIC'] = 'PGL/response/emergency';
$GLOBALS['REQUEST_VALIDATE_USER_TOPIC'] = "PGL/request/valid_user";
$GLOBALS['REQUEST_STORE_USER_IN_DB_TOPIC'] = 'PGL/request/store_user';
$GLOBALS['REQUEST_GET_EMERGENCIES_TOPIC'] = 'PGL/request/get_emergencies';
$GLOBALS['REQUEST_GET_EVENTS_TOPIC'] = "PGL/request/get_events";
$GLOBALS['REQUEST_CREATE_PRODUCT_TOPIC'] = 'PGL/request/store_product';

//credentials for mqtt client
$GLOBALS['hostname'] = "test.mosquitto.org";
$GLOBALS['port'] = 1883;
$GLOBALS['cleanSession'] = false;

?>