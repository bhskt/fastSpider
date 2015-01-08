<?php
$i=$argv[1];
$c=curl_init();
curl_setopt($c,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
curl_setopt($c,CURLOPT_USERAGENT,"Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)");
if(!file_exists("snapdeal")){
	mkdir("snapdeal");
}

/*
* The required link can be found by opening a Snapdeal product listing page like,
* http://www.snapdeal.com/products/mobiles-mobile-phones?sort=plrty&
* which lists all mobile phones on Snapdeal, assuming you're using Firefox browser, open the console & scroll down.
* You'll notice a GET request to page, http://www.snapdeal.com/acors/json/product/get/search/175/20/20
* when the browser sends an AJAX request to fetch new page data to show more items in that list.
* Copy this URL and use it below.
*/
curl_setopt($c,CURLOPT_URL,"http://www.snapdeal.com/acors/json/product/get/search/175/{$i}/50");

echo "PAGE = ".$i."\n";
HERE:
$items=json_decode(curl_exec($c),true);
echo "FOUND = ".count($items["productDtos"]);
if(count($items["productDtos"])==0){
	goto HERE;
}
foreach($items["productDtos"] as $item){
	if(!file_exists("snapdeal/".$item["labelUrl"])){
		mkdir("snapdeal/".$item["labelUrl"]);
	}
	if(!file_exists("snapdeal/".$item["labelUrl"]."/".$item["id"].".json")){
		file_put_contents("snapdeal/".$item["labelUrl"]."/".$item["id"].".json",json_encode($item));
		echo "\nINSERTED = ".$item["name"];
	}
}
?>
