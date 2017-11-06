<?php
require_once 'classes/router.class.php';
require_once 'Config/SqlDef.php';
require_once 'ZConnect/PDO.php';

$router = new router();

$router->add('/get_items', function() {
    $con = new \ZFrame_Service\ZConnect();
    $result = $con->getItems();
    echo json_encode($result);
});

$router->add('/set_actived', function() {
	$postData = json_decode(file_get_contents("php://input"));

    $con = new \ZFrame_Service\ZConnect();
    $oldList = $con->getItems();

    $cnt = count($oldList);
    $avgTmp = 0;
    $minChooseTimes = 0;
    foreach ($oldList as $key => $value) {
    	$tmp = intval($oldList[$key]['weight']);
    	$thisChooseTimes = intval($oldList[$key]['choose_times']);
    	if ($minChooseTimes === 0 || $thisChooseTimes < $minChooseTimes) {
    		$minChooseTimes = $thisChooseTimes;
    	}
    	$avgTmp += $tmp;
    	if (intval($oldList[$key]['id']) !== intval($postData->id)) {
    		$oldList[$key]['weight'] = $cnt * $tmp;
    	}
    	$cnt--;
    }
    $avgTmp = intval($avgTmp / count($oldList));
    $minBaseNewWeight = 0;
    foreach ($oldList as $key => $value) {
    	$thisWeight = intval($oldList[$key]['weight']);
    	$thisChooseTimes = intval($oldList[$key]['choose_times']) - $minChooseTimes;
    	if (intval($oldList[$key]['id']) !== intval($postData->id)) {
    		$baseNewWeight = ($thisWeight + $avgTmp) / (($thisChooseTimes > 0) ? $thisChooseTimes : 1);
    		$oldList[$key]['weight'] = $baseNewWeight;
	    	if ($minBaseNewWeight === 0 || $baseNewWeight < $minBaseNewWeight) {
	    		$minBaseNewWeight = $baseNewWeight;
	    	}
    	} else {
    		$oldList[$key]['weight'] = 1;
    	}
    }
    if(intval($minBaseNewWeight / 10) > 0) {
        foreach ($oldList as $key => $value) {
    		$oldList[$key]['weight'] = intval($oldList[$key]['weight'] / 10);
        }
    }
    $con->updateItems($oldList, $postData);
    echo json_encode($con->getItems());
    //echo json_encode($oldList);

});

$router->add('default', function() {
    $result = new \stdClass();
    $result->Date = date('Y-m-d H:i:s');
    $result->ReturnCode = '10001';
    $result->ErrorMessage = 'Illegal request';
    echo json_encode($result);
    exit();
});

$router->run();
?>