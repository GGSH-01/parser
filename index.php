<?php 
// used functions from https://github.com/dastanaron/grabber

    spl_autoload_register(function ($class) {
        $class = str_replace('\\', '/', $class) . '.php';
        require_once($class);
        
    });

    use classes\grabber\Grabber;

    $url = 'file:///C:/Georgiy/OpenServer/domains/parser/wo_for_parse.html'; //use your url    
    $parse = new Grabber($url, ['include'=>true]);

    $data['trackingNumber'] = $parse->PathQuery('h3', 'id', 'wo_number')    //PathQuery($tag, $selector, $value)
        ->PathExec()                                                       //This method composes a search query for DOM content. An html tag must be passed as $tag (for example: p);
        ->getValueOne();                                                     //$selector is a tag attribute designation (eg: class); $value - the value of the selector, (for example: phpsulogo).

    $data['poNumber'] = $parse->PathQuery('h3', 'id', 'po_number')  
        ->PathExec()                                                        // PathExec() method executes a search query formed by the PathQuery methods
        ->getValueOne();                                                    // getValueOne() - returns the first match of the selection

    $data['scheduled'] = $parse->PathQuery('h3', 'id', 'scheduled_date')
        ->PathExec()
        ->getValueOne();

    $data['scheduled'] = dellSpace($data['scheduled']);
    $data['scheduled'] = date("Y-m-d H:i", strtotime($data['scheduled']));
        
    $data['customer'] = $parse->PathQuery('h3', 'id', 'location_customer')
        ->PathExec()
        ->getValueOne();

    $data['trade'] = $parse->PathQuery('h3', 'id', 'trade')
        ->PathExec()
        ->getValueOne();
    
    $data['nte'] = $parse->PathQuery('h3', 'id', 'nte')
        ->PathExec()
        ->getValueOne();

    $data['nte'] = preg_replace("/[^.0-9]/", '', $data['nte']);

    $data['storeID'] = $parse->PathQuery('h3', 'id', 'location_name')
        ->PathExec()
        ->getValueOne();

    $fullAdress = $parse->PathQuery('a', 'id', 'location_address')
        ->PathExec()
        ->getValueOne();

    $fullAdress = dellSpace($fullAdress);
    $adress = explode(" ", $fullAdress);
    $data['street'] = $adress[0] . " " . $adress[1] . " " . $adress[2];
    $data['city'] = $adress[3];
    $data['state'] = $adress[4];
    $data['zipcode'] = $adress[5];
    

    $data['phone'] = $parse->PathQuery('a', 'id', 'location_phone')
        ->PathExec()
        ->getValueOne();
    
    $data['phone'] = preg_replace("/[^0-9]/", '', $data['phone']);


    $headTable = array('Tracking Number', 'PO Number', 'Scheduled', 'Customer', 'Trade', 'NTE', 'Store ID', 'Street', 'City', 'State', 'Zip-code', 'Phone number');
 
    $table = array($headTable, $data);
    if($fp = fopen('result/result.csv', 'w')){
        echo "complite";
    }
    foreach ($table as $fields) {
        fputcsv($fp, $fields, ';', '"');
    }
    fclose($fp);
    

    function dellSpace($str)
    {
        $str = trim($str);
        $str = str_replace(PHP_EOL, ' ', $str);
        $str = str_replace("	", " ", $str);
        while( strpos($str,"  ")!==false){
           $str = str_replace("  ", " ", $str);
        }
        return $str;
    }
?>