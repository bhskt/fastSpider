<?php
require "phpQuery/phpQuery.php";
$i=$argv[1];
$c=curl_init();
curl_setopt($c,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
if(!file_exists("flipkart")){
	mkdir("flipkart");
}

/*
* The required link can be found by opening a Flipkart product listing page like,
* http://www.flipkart.com/mobiles/pr?p[]=sort%3Dprice_asc&sid=tyy%2C4io&ref=d71c75ae-ddd8-4905-8868-7db52b6e5131
* which lists all mobile phones on Flipkart, assuming you're using Firefox browser, open the console & scroll down.
* You'll notice a GET request to page, http://www.flipkart.com/mobiles/pr
* when the browser sends an AJAX request to fetch new page data to show more items in that list.
* Click on that request to view its details. Copy the complete Request URL and use it below.
*/
curl_setopt($c,CURLOPT_URL,"http://www.flipkart.com/mobile-accessories/pr?p%5B%5D=sort%3Dprice_asc&sid=tyy%2C4mr&start={$i}&ajax=true");

echo "PAGE = ".$i."\n";
HERE:
phpQuery::newDocumentHTML(curl_exec($c));
$items=pq("div.gd-col");
echo "FOUND = ".$items->length;
if(count($items->length)==0){
	goto HERE;
}
foreach($items as $item){
	preg_match("/eVar22=([_a-z0-9]+)/i",pq($item)->find("div.product-unit")->attr("data-tracking-products"),$m);
	$itemData=[
		"category"=>trim($m[1]),
		"id"=>trim(pq($item)->find(".product-unit")->attr("data-pid")),
		"title"=>trim(pq($item)->find(".pu-title a")->text()),
		"link"=>trim(pq($item)->find(".pu-title a")->attr("href")),
		"image"=>trim(pq($item)->find("img")->attr("src")),
		"mrp"=>trim(pq($item)->find(".pu-old")->text()),
		"price"=>trim(pq($item)->find(".fk-font-17")->text()),
		"emi"=>trim(pq($item)->find(".pu-emi")->text()),
		"features"=>[],
		"rating"=>trim(pq($item)->find(".fk-stars-small")->attr("title"))
	];
	if(!file_exists("flipkart/".$itemData["category"]."/".$itemData["id"].".json")){
		if(pq($item)->find(".pu-usp li")->length!=0){
			$features=pq($item)->find(".pu-usp li");
			foreach($features as $feature){
				array_push($itemData["features"],trim(pq($feature)->text()));
			}
		}
		if(!file_exists("flipkart/".$itemData["category"])){
			mkdir("flipkart/".$itemData["category"]);
		}
		file_put_contents("flipkart/".$itemData["category"]."/".pq($item)->find(".product-unit")->attr("data-pid").".json",json_encode($itemData));
		echo "\nINSERTED = ".$itemData["title"];
	}
}
?>
