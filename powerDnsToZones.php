#!/bin/env php
<?php

$pdns_db = ['host' => 'localhost', 'user' => 'pdns', 'pass' => 'pdns', 'name' => 'pdns'];
$zone_ns = ['ns1.domain.tld', 'ns2.domain.tld'];
$zone_adm = 'hostmaster.domain.tld';

$zones = [];
$db = new mysqli($pdns_db['host'], $pdns_db['user'], $pdns_db['pass'], $pdns_db['name']);
if ($q = $db->query('SELECT * FROM domains')) {
	while ($r = $q->fetch_assoc()) {
		$d = strtolower($r['name']);
		if ($q1 = $db->query('SELECT * FROM records WHERE domain_id=' . $r['id'])) {
			$x = ['PTR' => [], 'NS' => []];
			while ($r1 = $q1->fetch_assoc()) {
				$r1['name'] = strtolower($r1['name']);
				switch ($r1['type']) {
					case 'PTR':
						$x[ $r1['type'] ][] = [$r1['name'], $r1['content']];
						break;
					case 'NS':
						$x[ $r1['type'] ][] = strtolower($r1['content']);
						break;
				}
			}
			$q1->free();
		}
		$zones[ $d ] = $x;
	}
	$q->free();
}
$db->close();

if (is_array($zones) && count($zones) > 0) {
	$p = './tmp/';
	if (!is_dir($p)) {
		if (!mkdir($p)) {
			die('Unable to make tmp directory' . PHP_EOL);
		}
	}
	if (count($zones) > 0) {
		foreach ($zones as $d => $r) {
			$x = '                           ';
			$t = ['$TTL    604800', '$ORIGIN ' . $d . '. ', '@    3600      SOA         ' . $zone_ns[0] . '.  (', $x . $zone_adm . '. ', $x . date("Ymd") . '01', $x . '1800', $x . '600', $x . '604800', $x . '600 )'];
			foreach ($zone_ns as $x) {
				$t[] = '                           86400      IN      NS      ' . $x . '.';
			}
			$t[] = NULL;
			foreach ($r['PTR'] as $x) {
				$name = explode('.', $x[0]);
				$t[] = str_pad($name[0], 4, ' ', STR_PAD_RIGHT) . '      IN      PTR      ' . $x[1] . '.';
			}
			file_put_contents($p . $d . '.zone', implode(PHP_EOL, $t) . PHP_EOL);
		}
	}
}

?>
