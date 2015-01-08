<?php
$i=$argv[1];
$c=curl_init();
curl_setopt($c,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
curl_setopt($c,CURLOPT_SSL_VERIFYPEER,false);
if(!file_exists("paytm")){
	mkdir("paytm");
}

/*
 * Use the http://www.paytm.com Main Menu Navigation links to form the below three variables.
 * Like, the link for mobiles phones is https://paytm.com/shop/g/electronics/mobile-accessories/mobiles
 * Therefore, $shop="electronics"; $category="mobile-accessories"; $subCategory="mobiles";
*/
$shop="electronics";
$category="mobile-accessories";
$subCategory="mobiles";

if(!file_exists("paytm/".$shop.">".$category.">".$subCategory)){
	mkdir("paytm/".$shop.">".$category.">".$subCategory);
}
echo "PAGE = ".$i."\n";
curl_setopt($c,CURLOPT_URL,"https://catalog.paytm.com/v1//g/".$shop."/".$category."/".$subCategory."?page_count=".$i."&items_per_page=30");
HERE:
$items=json_decode(curl_exec($c),true);
echo "FOUND = ".count($items["grid_layout"]);
if(count($items["grid_layout"])==0){
	goto HERE;
}
foreach($items["grid_layout"] as $item){
	curl_setopt($c,CURLOPT_URL,$item["url"]);
	if(!file_exists("paytm/".$shop.">".$category.">".$subCategory."/".$item["product_id"].".json")){
		RETRY:
		$data=curl_exec($c);
		if(empty($data)){
			goto RETRY;
		}
		file_put_contents("paytm/".$shop.">".$category.">".$subCategory."/".$item["product_id"].".json",$data);
		echo "\nADDED = ".$item["name"];
	}
}
?>
