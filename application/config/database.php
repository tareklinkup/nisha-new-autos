<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	
  	'username' => 'nisaau5os_admin1',
  	'password' => 'du9cl?,4hL99',
  	'database' => 'nisaau5os_new',
	
	// 'username' => 'expressre2ailbd_admin11',
	// 'password' => 'e+Zg~uM$3I)~',
	// 'database' => 'expressre2ailbd_pos1',
		

	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
