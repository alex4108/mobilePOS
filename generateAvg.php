<?php
	require('config.php');
	$mysqli = mysqlInit();
	$logQuery = $mysqli->query("SELECT * FROM log WHERE flag = 'transac'");
	$avg = array();
	
	// $avg[product_id][avg] = product_avg
	// $avg[product_id][amt] = amt_sold
	// $avg[product_id][totalPrice] = total_price
	$avg[0]['avg'] = 0;
	$avg[0]['amt'] = 0;
	$avg[0]['totalPrice'] = 0;

	$avg[1]['avg'] = 0;
	$avg[1]['amt'] = 0;
	$avg[1]['totalPrice'] = 0;


	$avg[2]['avg'] = 0;
	$avg[2]['amt'] = 0;
	$avg[2]['totalPrice'] = 0;


	$avg[3]['avg'] = 0;
	$avg[3]['amt'] = 0;
	$avg[3]['totalPrice'] = 0;


	$avg[4]['avg'] = 0;
	$avg[4]['amt'] = 0;
	$avg[4]['totalPrice'] = 0;


	$avg[5]['avg'] = 0;
	$avg[5]['amt'] = 0;
	$avg[5]['totalPrice'] = 0;


	while ($row = mysqli_fetch_assoc($logQuery)) {
		$data = unserialize($row['data']);
		if ( (!array_key_exists('void', $data)) || !$data['void']) {

			$avg[$data['inventory']['id']]['amt'] += $data['inventory']['amount'];
			$avg[$data['inventory']['id']]['totalPrice'] += $data['amount'];
				
			$prodAvg = $avg[$data['inventory']['id']]['totalPrice'] / $avg[$data['inventory']['id']]['amt'];

			$avg[$data['inventory']['id']]['avg'] = $prodAvg;
		}
	}

	$query = "UPDATE inventory SET ";
	$queryPart2 = array();
	$queryPart4 = array();
	foreach ($avg as $k => $v) {
		$queryPart2[$k] = "avgprice = '" . $v['avg'] . "', amt_sold = '" . $v['amt'] . "'";
		$queryPart4[$k] = $k;	
	}
	$queryPart3 = " WHERE id = ";
	
	// Build queries
	$finalQueries = array();
	for ($x = 0; $x <= 5; $x++) {
		print_r($query . $queryPart2[$x] . $queryPart3);
		print_r($queryPart4[$x] . '<br />');
	}
	


