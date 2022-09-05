<?php



function plugin_usedips_info ()
{
	return array
	(
		'name' => 'usedips',
		'longname' => 'Used IPs',
		'version' => '1.0',
		'home_url' => 'https://www.kiwirange.com/'
	);
}



function plugin_usedips_init ()
{
	//Define the variables (tabs, pages...) to be defined
	
	
	//Define the new page
	//global $page;
	//$page['usedips']['title'] = 'Used IPs';
	//$page['usedips']['parent'] = 'ipv4net'
	
	
	//Define a Tab inside network page
	global $tab;
	$tab['ipv4net']['usedips'] = 'Used IPs';
	registerTabHandler ('ipv4net', 'usedips', 'usedipsTabHandler');
	
	//global $trigger;
	//$trigger['object']['munin'] = 'triggerUsedipsList';
	
}







function plugin_usedips_install ()
{
	/* //TEST if is installed the PHP curl module to perform HTTP petitions against servers
	if (extension_loaded ('curl') === FALSE)
		throw new RackTablesError ('cURL PHP module is not installed', RackTablesError::MISCONFIGURED);
	*/
	
	//Create configuration values
	//addConfigVar ('USEDIPS_LISTSRC', 'false', 'string', 'yes', 'no', 'no', 'List of used IPs');
	
	return TRUE;
}




function plugin_usedips_uninstall ()
{
	//Delete all configuration values
	//deleteConfigVar ('USEDIPS_LISTSRC');

	//Delete and drop plugin tables
	//global $dbxlink;
	//$dbxlink->query	("DROP TABLE `CactiGraph`");
	//$dbxlink->query	("DROP TABLE `CactiServer`");

	return TRUE;
}


function plugin_usedips_upgrade ()
{
	//Perform actios to upgrade (eg: reorganize plugin tables or add new config values)
	return TRUE;
}




/*****************************
***** HANDLERS FUNCTIONS *****
******************************/

// Handler for plugin Tab inside networks
function usedipsTabHandler()
{
	//Get id of the actual network
	//$ipv4net = spotEntity ('ipv4net', $_REQUEST['id']);
	$netid = $_REQUEST['id'];
	//echo '<p>Variable netid: '.$netid.'</p>';
	
	//Fetch actual network
	$networkarray = getIPNetworkById ($netid);
	//$network = $network[0];
	//echo '<p>Variable networkarray: ';
	//print_r ($networkarray);
	//echo '</p>';
	
	$network = $networkarray[$netid];
	$networkip = long2ip ($network['ip']);
	$networkmask = $network['mask'];
	
	//echo '<p>Variable network: ';
	//print_r ($network);
	//echo '</p>';
	
	//echo '<p>Variable network=>ip: '.$network['ip'].'</p>';
	
	
	
	//Get the range of IPs of the network
	$iprange1 = $network['ip'];
	$iprange1clean = long2ip ($iprange1);
	//echo '<p>Variable iprange1: '.$iprange1.'</p>';
	$iprange2 = $iprange1 + 2 ** (32 - $network['mask']) - 1;
	$iprange2clean = long2ip ($iprange2);
	//echo '<p>Variable iprange2: '.$iprange2.'</p>';
	
	//Fetch all IP Addresses of actual network
	$iplist = getIPsByRange ($iprange1, $iprange2);
	//echo '<p>Variable iplist: ';
	//print_r ($iplist);
	//echo '</p>';
	//return TRUE;
	
	//Write the Page-Tab Header
	echo '<div class=portlet>'.
	       '<h1>'.$networkip.'/'.$networkmask.'</h1>'.
	       '<h2>'.$network['name'].'</h2>'.
	     '</div>'.
		 '<div class=portlet>'.
		   '<h2>Network Used IPs</h2>'.
		   '<h3>'.$iprange1clean.'~'.$iprange2clean.'</h3>'.
		 '</div>'.
		 '<div class=portlet>';
	
	
	//Write Table header
	echo '<table class="widetable" border="0" cellspacing="0" cellpadding="5" align="center" width="70%">'.
			'<tbody>'.
			'  <tr class="tdleft">'.
			  '  <th>Address</th>'.
			  '  <th>Name</th>'.
			  '  <th>Comment</th>'.
			  '  <th>Allocation</th>'.
			'  </tr>';
	
	//Other table format: <table class="cooltable" align="center" border="0" cellpadding="5" cellspacing="0">
	
	
	foreach ( $iplist as $ip )
	{
		$ipclean = long2ip ($ip['ip']);
		
		if ($ip['object_id'] == 0) {
			echo '<tr class="tdleft">'.
					 '<td><a class="underline" name="ip-'.$ipclean.'" href="index.php?page=ipaddress&amp;ip='.$ipclean.'">'.$ipclean.'</a></td>'.
					 '<td><div class="edit-container">'.
						'<span class="rsvtext editable id-'.$ipclean.' op-upd-ip-name initdone">'.$ip['name'].'</span>'.
						'<div class="empty" style="margin-left: 10px; width:12px; height:12px;">'.
							'<img class="edit-btn" src="?module=chrome&amp;uri=pix/pencil-icon.png" title="Edit text">'.
						'</div>'.
					 '</div></td>'.
					 '<td><div class="edit-container">'.
						'<span class="rsvtext editable id-'.$ipclean.' op-upd-ip-comment initdone">'.$ip['comment'].'</span>'.
						'<div class="empty" style="margin-left: 10px; width:12px; height:12px;">'.
							'<img class="edit-btn" src="?module=chrome&amp;uri=pix/pencil-icon.png" title="Edit text">'.
						'</div>'.
					 '</div></td>'.
					 '<td></td>'.
				 '</tr>';
			
		} else {
			
			$objectarray = getObjectById ($ip['object_id']);
			$object = $objectarray[0];
			
			echo '<tr class="tdleft trbusy">'.
					 '<td><a class="underline" name="ip-'.$ipclean.'" href="index.php?page=ipaddress&amp;ip='.$ipclean.'">'.$ipclean.'</a></td>'.
					 '<td><span class="rsvtext  id-'.$ipclean.' op-upd-ip-name"></span></td>'.
					 '<td><span class="rsvtext  id-'.$ipclean.' op-upd-ip-comment">'.$ip['comment'].'</span></td>'.
					 '<td><a href="index.php?page=object&amp;tab=default&amp;object_id='.$ip['object_id'].'&amp;hl_ip='.$ipclean.'" title="">'.$ip['ifname'].'@'.$object['name'].'</a></td>'.
				 '</tr>';
		}
		
		
	}
	
	//Write the Table Footer
	echo '  	</tbody>'.
		 '	</table>'.
		 '</div>';
	
	
	
	
}




/*function triggerUsedipsList()
{
	if (! count (getIPAddresses ()))
		return '';
	if
	(
		count (getMuninGraphsForNetwork (getBypassValue ())) or
		considerConfiguredConstraint (spotEntity ('object', getBypassValue ()), 'MUNIN_LISTSRC')
	)
		return 'std';
	return '';
}*/




/*****************************
***** DATABASE FUNCTIONS *****
******************************/


function getIPAddresses ()
{
	$result = usePreparedSelectBlade
	(
		'SELECT ip, name, comment, reserved ' .
		'FROM IPv4Address WHERE ip = \'176161571\''
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}


function getIPAllocsByRange ($rangeMin, $rangeMax)
{
	//echo '<p>Variable rangeMin: '.$rangeMin.'</p>';
	//echo '<p>Variable rangeMax: '.$rangeMax.'</p>';
	
	$result = usePreparedSelectBlade ('SELECT object_id, ip, name, type FROM IPv4Allocation WHERE ip >= ? and ip <= ? ORDER BY ip',  array ($rangeMin, $rangeMax));
	//$result = usePreparedSelectBlade ("SELECT object_id, ip, name, type FROM IPv4Allocation WHERE ip >= '{$rangeMin}' and ip <= '{$rangeMax}'");
	
	//echo '<p>Variable result: ';
	//print_r ($result);
	//echo '</p>';
	
	return $result->fetchAll (PDO::FETCH_ASSOC);
}


function getIPsByRange ($rangeMin, $rangeMax)
{
	//Get IPs from Allocation and Hard defined
	$result = usePreparedSelectBlade ('SELECT object_id, ip, \'\' as "name", name as "ifname", \'\' AS "comment", type FROM IPv4Allocation WHERE ip >= ? and ip <= ? UNION SELECT 0 AS "object_id", ip, name, \'\' as "ifname", comment, \'null\' AS "type" FROM IPv4Address WHERE ip >= ? and ip <= ? ORDER BY ip',  array ($rangeMin, $rangeMax, $rangeMin, $rangeMax));
	
	return $result->fetchAll (PDO::FETCH_ASSOC);
}



function getIPAdressesForNetwork ($network_id)
{
	$resnetwork = usePreparedSelectBlade ('SELECT', array ($network_id) );
	$result = usePreparedSelectBlade
	(
		'SELECT ip, name, comment, reserved FROM IPv4Address WHERE ip = ? ORDER BY ip',
		array ($network_id)
	);
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}


function getIPNetworkById ($network_id)
{
	$result = usePreparedSelectBlade ('SELECT id, ip, mask, name FROM IPv4Network WHERE id = ?', array ($network_id));
	//echo '<p>Variable result Network: ';
	//print_r ($result);
	//echo '</p>';
	return reindexById ($result->fetchAll (PDO::FETCH_ASSOC));
}


function getObjectById ($object_id)
{
	$result = usePreparedSelectBlade ('SELECT id, name, label, objtype_id, asset_no, has_problems, comment FROM Object WHERE id = ?', array ($object_id));
	return $result->fetchAll (PDO::FETCH_ASSOC);
}



/*
https://www.php.net/manual/es/function.long2ip.php
https://github.com/RackTables/racktables-contribs/blob/master/local_portgenerator.php
https://www.php.net/manual/es/functions.arguments.php
https://www.php.net/manual/es/function.array.php
https://www.techotopia.com/index.php/PHP_Arrays#Accessing_Elements_in_a_PHP_Array
https://stackoverflow.com/questions/7956060/how-do-i-get-the-value-from-objectstdclass
https://www.w3schools.com/php/php_operators.asp

https://www.mogilowski.net/projects/racktables/
https://www.freelists.org/post/racktables-users/writing-function-for-a-plugin
https://wiki.racktables.org/index.php/RackTablesDevelGuide


*/



/* DATABASES INFO


UNION IPv4Allocation + IPv4Adress





###########################
 TABLE IPv4Network

+---------+------------------+------+-----+---------+----------------+
| Field   | Type             | Null | Key | Default | Extra          |
+---------+------------------+------+-----+---------+----------------+
| id      | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| ip      | int(10) unsigned | NO   | MUL | 0       |                |
| mask    | int(10) unsigned | NO   |     | 0       |                |
| name    | char(255)        | YES  |     | NULL    |                |
| comment | text             | YES  |     | NULL    |                |
+---------+------------------+------+-----+---------+----------------+

Example

+----+------------+------+------------------+---------+
| id | ip         | mask | name             | comment |
+----+------------+------+------------------+---------+
|  1 | 3232261120 |   24 | LUSI2_IRMC       |         |
|  2 | 2886729984 |   24 | LUSI2_Gestion    | NULL    |
|  3 |  176095232 |   24 | DMZ_DNS          | NULL    |
|  4 |  176160768 |   16 | Virtualizacion   | NULL    |
|  5 |  176291840 |   16 | Nutanix_BMC      |         |
|  6 | 3232253440 |   24 | Gestion_firewall | NULL    |
|  7 |  169083136 |   24 | Ayuntamiento     | NULL    |
|  8 |  167903232 |   16 | MN3_Gestion      | NULL    |
|  9 |  167968768 |   16 | MN3_IRMC         | NULL    |
| 10 |  176947200 |   24 | Poseidon         | NULL    |
+----+------------+------+------------------+---------+






########################
 TABLE IPv4Allocation

+-----------+-----------------------------------------------------------+------+-----+---------+-------+
| Field     | Type                                                      | Null | Key | Default | Extra |
+-----------+-----------------------------------------------------------+------+-----+---------+-------+
| object_id | int(10) unsigned                                          | NO   | PRI | 0       |       |
| ip        | int(10) unsigned                                          | NO   | PRI | 0       |       |
| name      | char(255)                                                 | NO   |     |         |       |
| type      | enum('regular','shared','virtual','router','point2point') | NO   |     | regular |       |
+-----------+-----------------------------------------------------------+------+-----+---------+-------+

Example:

+-----------+-----------+----------+---------+
| object_id | ip        | name     | type    |
+-----------+-----------+----------+---------+
|        10 | 167772437 | ib0      | regular |
|        10 | 167903509 | eth0     | regular |
|        10 | 167969045 | eth_IRMC | regular |
|        11 | 167772438 | ib0      | regular |
|        11 | 167903510 | eth0     | regular |
|        11 | 167969046 | eth_IRMC | regular |
|        12 | 167772439 | ib0      | regular |
|        12 | 167903511 | eth0     | regular |
|        12 | 167969047 | eth_IRMC | regular |
|        13 | 167772440 | ib0      | regular |
+-----------+-----------+----------+---------+




###########################
 TABLE IPv4Adress

+----------+------------------+------+-----+---------+-------+
| Field    | Type             | Null | Key | Default | Extra |
+----------+------------------+------+-----+---------+-------+
| ip       | int(10) unsigned | NO   | PRI | 0       |       |
| name     | char(255)        | NO   |     |         |       |
| comment  | char(255)        | NO   |     |         |       |
| reserved | enum('yes','no') | YES  |     | NULL    |       |
+----------+------------------+------+-----+---------+-------+

Example

+-----------+---------------------------+--------------------------------------+----------+
| ip        | name                      | comment                              | reserved |
+-----------+---------------------------+--------------------------------------+----------+
| 168033811 | Secuenciador              |                                      | yes      |
| 176161544 | Gestion 3                 | Gestion 3                            | no       |
| 176161545 | CénitS-Webserver          | CénitS-Webserver (Collabtive, wiki)  | no       |
| 176161547 | CénitS-SysMonitor         | CénitS-SysMonitor                    | no       |
| 176161548 | CénitS-NewGrafana         | CénitS-NewGrafana                    | no       |
| 176161550 | CénitS-osTicket           | CénitS-osTicket                      | no       |
| 176161551 | External-BackupFundecyt   | External-BackupFundecyt              | no       |
| 176161556 | CénitS-Monitoring Zabbix  | CénitS-Monitoring Zabbix             | no       |
| 176161561 | CénitS-Webserver2         | Web principal                        | no       |
| 176161562 | CénitS-NuevaWeb           | CénitS-NuevaWeb                      | no       |
+-----------+---------------------------+--------------------------------------+----------+





############################
 TABLE Object

+--------------+------------------+------+-----+---------+----------------+
| Field        | Type             | Null | Key | Default | Extra          |
+--------------+------------------+------+-----+---------+----------------+
| id           | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name         | char(255)        | YES  |     | NULL    |                |
| label        | char(255)        | YES  |     | NULL    |                |
| objtype_id   | int(10) unsigned | NO   | MUL | 1       |                |
| asset_no     | char(64)         | YES  | UNI | NULL    |                |
| has_problems | enum('yes','no') | NO   |     | no      |                |
| comment      | text             | YES  |     | NULL    |                |
+--------------+------------------+------+-----+---------+----------------+

Example:

+----+-------------------------+----------------+------------+------------------+--------------+---------+
| id | name                    | label          | objtype_id | asset_no         | has_problems | comment |
+----+-------------------------+----------------+------------+------------------+--------------+---------+
|  1 | CCMI                    | NULL           |       1562 | NULL             | no           | NULL    |
|  2 | CPD - Antiguo           | NULL           |       1562 | NULL             | no           | NULL    |
|  3 | Infraestructura         | NULL           |       1561 | NULL             | no           | NULL    |
|  4 | Fujitsu PCR M1 742S r03 | NULL           |       1560 | s01r3            | no           | NULL    |
|  5 | Chasis-Fujitsu6         | Chasis Fujitsu |       1502 | Chassis-6        | no           | NULL    |
|  6 | Chasis-Fujitsu7         | Chasis Fujitsu |       1502 | Chassis-7        | no           | NULL    |
|  7 | Chasis-Fujitsu8         | Chasis Fujitsu |       1502 | Chassis-8        | no           | NULL    |
|  8 | Chasis-Fujitsu9         | Chasis Fujitsu |       1502 | Chassis-9        | no           | NULL    |
|  9 | Chasis-Fujitsu10        | Chasis Fujitsu |       1502 | Chassis-10       | no           | NULL    |
| 10 | s01r3b01                | s01r3b01       |          4 | compute-s01r3b01 | no           | NULL    |
+----+-------------------------+----------------+------------+------------------+--------------+---------+






*/

