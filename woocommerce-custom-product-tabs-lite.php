<?php
/**
 * Plugin Name: WooCommerce Custom Product Tabs Lite For WPS, Trucker, Drag Api
 * Plugin URI: https://www.duraymedia.com/
 * Description: Extends WooCommerce to add a custom product tab and add to cart to WPS, Trucker, Drag API handler
 * Author: Stephanie Cella
 * Author URI: https://www.duraymedia.com/
 * Version: 2.8.1
 * Text Domain: woocommerce-custom-product-tabs-lite
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2020, Duraymedia Millitary Pack, Inc. (stephaniecellap00@outlook.com)
 *
 * @author      Stephanie Cella
 * @copyright   Copyright (c) 2012-2020, Duray Media, Inc. (stephaniecellap00@outlook.com)
 */
// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
//order inventory meta box

// order information box

add_action( 'add_meta_boxes', 'trusted_list_order_meta_boxes' );

function trusted_list_order_meta_boxes() {

    add_meta_box(
        'woocommerce-order-verifyemail',
        __( 'WPS Order Information List' ),
        'trusted_list_order_meta_box_content',
        'shop_order',
        'side',
        'default'
    );
    add_meta_box(
        'woocommerce-order-inventory-verify',
        __( 'Product inventory of Warehouse' ),
        'product_inventory_per_warehouse',
        'shop_order',
        'side',
        'default'
    );	
}
function product_inventory_per_warehouse($post)
{
$order = wc_get_order( $post->ID );
    $items = $order->get_items();
    foreach ( $items as $item ) {
		
        $product_name = $item->get_name();
        $product_id = $item->get_product_id();
        $product_variation_id = $item->get_variation_id();

        $product = wc_get_product( $product_id );
		$product_sku = $product->get_attribute('Hardrive / WPS #');
		$product_Tucker =  $product->get_attribute('Tucker V-Twin #');
		echo "<h4>".$product->get_title()."</h4>";
		$order_button = "<div class='wrapper'><div class='submit-form'><input type='hidden' name= 'po_number' value='".$post->ID."'><input type='hidden' name= 'sku' value='".$product_sku."'/><input type='hidden' name= 'drag' value='".$product_Tucker."'/><button type='button' class='button button-primary calculate-warehouse' >Product Invetory</button>
		</div>
		<div class='inventory-information'></div></div>
		";
		echo $order_button;		
	}		
}
add_action('wp_ajax_my_action_show_inventory_warehouse', 'my_action_show_inventory_warehouse_function');
function my_action_show_inventory_warehouse_function()
{

    if ( $_POST ) 
	{
		$wps_apifilter =$_POST['wps_sku'];
		if($wps_apifilter!= null)
		{
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.wps-inc.com/items?filter[sku]=".$wps_apifilter,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
				"authorization: Bearer ",
				"cache-control: no-cache",
				"postman-token: 99010635-3cab-c996-7759-3f76ceae580c"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);
			$json_data_price  = json_decode($response,true);
			echo "<br>Starndard Dealer price :" . $json_data_price['data'][0]['standard_dealer_price']."<br>";
			echo "List price :" . $json_data_price['data'][0]['list_price']."<br>";
			
			$curl = curl_init();
			//echo $wps_apifilter;
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "http://api.wps-inc.com/items/crutch/".$wps_apifilter."?include=quantities",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
				"authorization: Bearer ",
				"cache-control: no-cache",
				"postman-token: 88f447f1-c743-da98-04dc-4005a9aa90ae"
			  ),
			));
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			
			curl_close($curl);
			
			if ($err) {
			  echo "cURL Error #:" . $err;
			} else {
			   
			  $json_data = json_decode($response,true);
			}        
			$json_array = $json_data["data"]["quantities"]["data"];
			//print_r($json_array );
			if (sizeof($json_array)>0)
			{
			//    echo "<br><center><b>HardDrive/WPS Inventory:</b></center><br>";
			
			echo "<center><table BORDER=1 CELLSPACING=0 CELLPADDING=0 style='margin-right: auto;margin-left: auto;text-align: center;' width=450 align='center' bordercolor='red'>";
			$city_array= array();
			$county_array = array();
			$inventory_array= array();
			$jayParsedAry = [
			   [
					 "id" => 1, 
					 "db2_key" => "ID", 
					 "name" => "Boise", 
					 "created_at" => "2017-03-01 17:49:23", 
					 "updated_at" => "2017-03-01 17:49:23" 
				  ], 
			   [
						"id" => 2, 
						"db2_key" => "CA", 
						"name" => "Fresno", 
						"created_at" => "2017-03-01 17:49:23", 
						"updated_at" => "2017-03-01 17:49:23" 
					 ], 
			   [
						   "id" => 4, 
						   "db2_key" => "PA", 
						   "name" => "Elizabethtown", 
						   "created_at" => "2017-03-01 17:49:23", 
						   "updated_at" => "2017-03-01 17:49:23" 
						], 
			   [
							  "id" => 5, 
							  "db2_key" => "IN", 
							  "name" => "Ashley", 
							  "created_at" => "2017-03-01 17:49:23", 
							  "updated_at" => "2017-03-01 17:49:23" 
						   ], 
			   [
								 "id" => 6, 
								 "db2_key" => "TX", 
								 "name" => "Midlothian", 
								 "created_at" => "2017-03-01 17:49:23", 
								 "updated_at" => "2017-03-01 17:49:23" 
							  ], 
			   [
									"id" => 7, 
									"db2_key" => "GA", 
									"name" => "Midway", 
									"created_at" => "2019-12-27 20:59:57", 
									"updated_at" => "2019-12-27 20:59:57" 
								 ], 
			   [
									   "id" => 8, 
									   "db2_key" => "PA2", 
									   "name" => "Jessup", 
									   "created_at" => "2019-10-28 17:06:34", 
									   "updated_at" => "2019-10-28 17:06:34" 
									] 
			]; 			
			foreach($json_array as $item) {
				/*$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "http://api.wps-inc.com/warehouses/".$item['warehouse_id'],
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET",
				  CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ",
					"cache-control: no-cache",
					"postman-token: 6ffbd7e2-e9bf-5c12-3c87-5dcff6ec97cd"
				  ),
				));
				
				$response = curl_exec($curl);
				$err = curl_error($curl);
				
				curl_close($curl);
				
				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  $warehouse_data = json_decode($response,true);
				}
				*/
				$county = $jayParsedAry[$item['warehouse_id']-1]['db2_key'];
				//$city = $warehouse_data['data']['name'];
				//array_push($city_array,$county);
				array_push($county_array,$county);
				array_push($inventory_array,$item['obtainable']);
				

			}
			//print_r($city_array);
			
			echo "<tr class='table-header'><td>HardDrive/WPS</td>";
			for($j=0;$j<sizeof($county_array);$j++)
			{
				echo "<td>".$county_array[$j]."</td>";
			}
			echo "</tr>";
			echo "<tr><td>Available</td>";
			for($j=0;$j<sizeof($county_array);$j++)
			{
				echo "<td>".$inventory_array[$j]."</td>";
			}
				echo "</tr>";
				echo "</table></center>";
			}
		}
	
		// making truck api table ------  php curl----------------------------
		$trucker_apifilter = $_POST['drag_sku'];
		if($trucker_apifilter != null)
		{
		
			$curl = curl_init();
			$trucker_apifilter = str_replace('-', '', $trucker_apifilter);
			//echo $trucker_apifilter;
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.tucker.com/bin/trws?apikey=YRB8X3J1KFOM9Q2ZTVX81RFNSICY&cust=712920&output=JSON&type=INV&item=".$trucker_apifilter,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"postman-token: 1cbd3861-409d-8818-6691-f000b3271f74"
			  ),
			));
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			
			curl_close($curl);
			  $county_truck_array = array();
			  $inventory_truck_array = array();                
			if ($err) 
			{
			  echo "cURL Error #:" . $err;
			} 
			else {
			    $truck_json = json_decode($response,true);
			  
				 // print_r($truck_json["INV"]["item"][0]["dc"]);
				  //echo sizeof($truck_json["INV"]["item"][0]["dc"]);
				 if(sizeof($truck_json["INV"]["item"][0]["dc"])>0)
				{
			  //    echo "<br><center><b>Tucker Inventory:</b></center><br>";
				  echo "<br><center><table BORDER=1 CELLSPACING=0 CELLPADDING=0 style='margin-right: auto;margin-left: auto;text-align: center;' width=450 align='center' bordercolor='red'>";
				  for ($i=0;$i<sizeof($truck_json["INV"]["item"][0]["dc"]);$i++)
				  {
					  $county_truck = $truck_json["INV"]["item"][0]["dc"][$i]["dcname"];
					  $inventory_truck = $truck_json["INV"]["item"][0]["dc"][$i]["dcinv"];
					  array_push($county_truck_array,$county_truck);
					  array_push($inventory_truck_array,$inventory_truck);
				  }

					echo "<tr class='table-header'><td>Tucker :</td>";
					for($j=0;$j<sizeof($county_truck_array);$j++)
					{
						echo "<td>".$county_truck_array[$j]."</td>";
					}
					echo "</tr>";
					echo "<tr><td>Available</td>";
					for($j=0;$j<sizeof($inventory_truck_array);$j++)
					{
						echo "<td>".$inventory_truck_array[$j]."</td>";
					}
					echo "</tr>";
					echo "</table></center>";  
				}
			}
		}	
    }            		
}

// Custom metabox content
add_action('wp_ajax_my_action', 'my_ajax_action_function');

function my_ajax_action_function(){

    $reponse = array();
    if(!empty($_POST)){
         $response['response'] = $_POST; 
		if($_POST['get_shipping_address_2']=="")
		{
			$_POST['get_shipping_address_2']="ST";
		}
		$curl = curl_init();
		$post = array(
			'po_number' => $_POST['po_number'],
			'pay_type' => 'OO',
			'default_warehouse' => 'TX',
			'ship_via' => 'FDXG',
			'ship_name' => $_POST['first_name']." ".$_POST['last_name'],
			'ship_address1' => $_POST['get_shipping_address_1'],
			//'ship_address2' => $_POST['get_shipping_address_2'],
			'ship_city' => $_POST['get_shipping_city'],
			'ship_state' => $_POST['get_shipping_state'],
			'ship_zip' => $_POST['get_shipping_postcode'],
			'email' => $_POST['get_billing_email']
		);
		$data = json_encode($post);
		//print_r($data);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wps-inc.com/carts",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
			"authorization: Bearer ",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: 98028d54-b0ba-3357-5cc1-d649606d2946"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		$curl = curl_init();
		$cart = array(
			'item_sku' => $_POST['sku'],
			'quantity' => $_POST['quantity'],
		);
		$po =$_POST['po_number'];
		$cart_data = json_encode($cart);
		curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.wps-inc.com/carts/".$po."/items",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $cart_data,
		  CURLOPT_HTTPHEADER => array(
			"authorization: Bearer ",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: b9041e74-54ae-de68-3d4f-71d1c9a93846"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
/*
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wps-inc.com/carts/".$_POST['po_number'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"authorization: Bearer ",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: 11b00b5c-4626-d04b-09d7-7f9b467e0a5d"
		  ),
		));

		$responses = curl_exec($curl);
		$err = curl_error($curl);
		//$responses = json_encode($responses,true);
		curl_close($curl);
*/
		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  print_r($response);
		}	
    } else {
         $reponse = array();
    }


    //Don't forget to always exit in the ajax function.
    exit();

}
add_action('wp_ajax_my_action_order_wps', 'my_action_order_wps_function');

function my_action_order_wps_function(){
	if(!empty($_POST)){	
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wps-inc.com/orders/",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\"po_number\":\"{$_POST['po_number']}\"}",
		  CURLOPT_HTTPHEADER => array(
			"authorization: Bearer ",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: 372fa407-3a90-845f-af65-2d0e2bac6cdf"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$response = json_encode($response,true);
		print_r($response);
		curl_close($curl);

	}
}
add_action('wp_ajax_my_action_drag_order', 'wp_ajax_my_action_drag_order_function');

function wp_ajax_my_action_drag_order_function()
{
	if(isset($_POST))
	{
		$curl = curl_init();
		$full_name = $_POST['first_name']." ".$_POST['last_name'];
		$address_line_1 = $_POST['get_shipping_address_1'];
		$address_line_2 = $_POST['get_shipping_address_2'];
		$city = $_POST['get_shipping_city'];
		$state = $_POST['get_shipping_state'];
		$postal_code = $_POST['get_shipping_postcode'];
		$country = $_POST['get_shipping_country'];
		$lineArray = $_POST['lineArray'];
		$lineArray=json_encode($lineArray);
		$data = '{ "dealer_number": "20851","order_type": "DS","purchase_order_number": '.$_POST["po_number"].',"shipping_method": "ground", "validate_price": false,"cancellation_policy": "0", "ship_to_address": { "name": "'.$full_name.'", "company": " ","address_line_1": "'.$address_line_1.'", "address_line_2": "'.$address_line_2.'", "city": "'.$city.'", "state": "'.$state.'", "postal_code": "'.$postal_code.'", "country": "'.$country.'" }, "line_items": '.$lineArray.' }';  
		//print_r($lineArray);
		//$data =json_decode($data, true);
		print_r($data);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://stage-api.lemansplatform.com/api/orders/dropship",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
			"api-key: D9MM62Q-CBF4X69-PRS3HN1-6WJCGE7",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: e11f3672-0947-92cb-b52e-21eb1c45536f"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}	
	}
}
add_action('wp_ajax_my_delete_action_wps_order', 'my_delete_action_wps_order_function');

function my_delete_action_wps_order_function()
{
	if(isset($_POST))
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wps-inc.com/carts/".$_POST["po_number"],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_HTTPHEADER => array(
			"authorization: Bearer ",
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: 501b8cd7-a3de-18da-26e8-9bdddb78b0aa"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}		
	}
}	
function trusted_list_order_meta_box_content( $post ){
    $order = wc_get_order( $post->ID );
?>


	
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.hidden-element {
    display: inline-block;
    margin-right: 10px;
}
.hidden-element-order {
    display: inline-block;
}
</style>
<script>
jQuery(document).ready(function($){
	/// add to cart on WPS
	let appendElement =$('.order-information');
	$('.hidden-element > button.wps').click(function(){
		appendElement = $('.order-information');
		let parentNode = $(this).parent().parent().children();
		let po_number = parentNode.eq(0).children().eq(0).val();
		let quantity = parentNode.eq(1).children().eq(0).val();
		let sku = parentNode.eq(2).children().eq(0).val()	;	
		console.log(sku);
		$(this).attr("disabled", true);	
		if(sku=="None")
		{
			alert("please correct Real sku of WPS number for this product, to correct sku number go to product page and you can update hardrive/wps attribute item Thank you for understanding");
			return;
		}
			
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'my_action' , 'po_number': po_number, 'quantity': quantity , 'sku': sku,'first_name':'<?php echo $order->get_shipping_first_name();?>','last_name':'<?php echo $order->get_shipping_last_name();?>','get_shipping_address_1':'<?php echo $order->get_shipping_address_1();?>','get_shipping_address_2':'<?php echo $order->get_shipping_address_2();?>','get_shipping_city':'<?php echo $order->get_shipping_city();?>','get_shipping_state':'<?php echo $order->get_shipping_state();?>' ,'get_shipping_postcode':'<?php echo $order->get_shipping_postcode();?>','get_shipping_country':'<?php echo $order->get_shipping_country();?>','get_billing_email':'<?php echo $order->get_billing_email();?>' }
		  }).done(function( msg ) {
				 console.log( "Data Saved: " + msg );
				 if($('.cart-infomation').length>0){
					 $('.cart-infomation').remove();
				 }
				 
				 appendElement.append("<div class='cart-infomation'>"+msg+"</div>");
		 });
		 
	})
	///wps order 
	$('#order-wps').click(function(){


		let po_number = <?php echo $post->ID;?>;
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'my_action_order_wps' , 'po_number': po_number }
		  }).done(function( msg ) {
				 alert( "Data Saved: " + msg );  
		 });		
	})	
	/// wps delete order per po number
	$('.hidden-element > button.delete-cart-wps').click(function(){


		let po_number = <?php echo $post->ID;?>;
		$(this).parent().prev().children().eq(0).attr("disabled", false);	
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'my_delete_action_wps_order' , 'po_number': po_number }
		  }).done(function( msg ) {
				//alert( "Data Saved: " + msg ); 
				if($('.cart-infomation').length>0){
					 $('.cart-infomation').remove();
				}
				appendElement.append("<div class='cart-infomation'>Whole Carts were removed by the po number </div>");
				
		 });
			 
	})	
	let lineArray = new Array();
	let i = 0
	$('.hidden-element > button.add-to-cart-drag').click(function(){
		i= i + 1;
		let parentNode = $(this).parent().parent().children();
		let po_number = parentNode.eq(0).children().eq(0).val();
		let quantity = parentNode.eq(1).children().eq(0).val();
		let sku = parentNode.eq(3).children().eq(0).val()	;			
		let line_number = po_number + i.toString();
		$(this).parent().attr('item-line',line_number);
		let newObject={'line_number':line_number,'part_number':sku,'quantity':quantity};
		lineArray.push(newObject);
		console.log(lineArray);
		$(this).attr("disabled", true);
		if(sku=="None")
		{
			alert("please correct Real sku of TUCKER number for this product, to correct sku number go to product page and you can update hardrive/wps attribute item Thank you for understanding");
			return;
		}	
	
	})
	$('.hidden-element > button.delete-to-cart-drag').click(function(){
		let parentNode = $(this).parent().prev();
		let removeValue = parentNode.attr('item-line');
		let tempArray =  new Array();
		for (var i=0; i< lineArray.length;i++)
		{
			if(lineArray[i]['line_number']==removeValue)
			{
				continue;
			}
			tempArray.push(lineArray[i]);
		}
		lineArray = tempArray;
		$(this).parent().prev().children().eq(0).attr("disabled", false);

	
	})	

	$('.hidden-element-order > button#order-Drag').click(function(){
		let parentNode = $(this).parent().parent().children();
		let po_number = <?php echo $post->ID;?>;
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'my_action_drag_order' , 'po_number': po_number, 'lineArray': lineArray,'first_name':'<?php echo $order->get_shipping_first_name();?>','last_name':'<?php echo $order->get_shipping_last_name();?>','get_shipping_address_1':'<?php echo $order->get_shipping_address_1();?>','get_shipping_address_2':'<?php echo $order->get_shipping_address_2();?>','get_shipping_city':'<?php echo $order->get_shipping_city();?>','get_shipping_state':'<?php echo $order->get_shipping_state();?>' ,'get_shipping_postcode':'<?php echo $order->get_shipping_postcode();?>','get_shipping_country':'<?php echo $order->get_shipping_country();?>','get_billing_email':'<?php echo $order->get_billing_email();?>' }
		  }).done(function( msg ) {
				 console.log( msg );
				 alert("drag ordered");
		 });	
	})	
let ajaxinformation = new Object();	
	$('div.wrapper > div.submit-form > button.calculate-warehouse').click(function(){
		ajaxinformation = $(this).parent().next();
		let wps_sku = $(this).prev().prev().val();
		let drag_sku =$(this).prev().val(); 
		if (wps_sku=="" || drag_sku =="")
		{
			alert("please make sure Drag property and WPS property is correct. Ajax can't show product inventory for wps and drag as well ");
			return;
		}
		$(this).attr("disabled", true);
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: { action: 'my_action_show_inventory_warehouse' , 'drag_sku': drag_sku,'wps_sku':wps_sku }
		  }).done(function( msg ) {
				 
				 if($('.ajax-resonse').length >0)
				 {
					 $('.ajax-resonse').remove();
				 }
				  
				 ajaxinformation.append("<div class='ajax-response'"+msg+"</div>");
				 
		 });	
	})	
	
})
</script>	
<?php
    $items = $order->get_items();
    foreach ( $items as $item ) {
        $product_name = $item->get_name();
        $product_id = $item->get_product_id();
        $product_variation_id = $item->get_variation_id();
		$quantity = $item->get_quantity();
        $product = wc_get_product( $product_id );

        $product_sku = $product->get_attribute('Hardrive / WPS #');
		$product_drag =  $product->get_attribute('Drag Specialties #');
		if($product_sku==null)
		{
			$product_sku = "None";
		}
        global $wpdb;
		$first_name = $order->get_shipping_first_name();
		$last_name = $order->get_shipping_last_name();
		$fullName = $first_name."+".$last_name;
        $result = $wpdb->get_results( "SELECT * FROM `HOY8M9_wps_orders` WHERE `sku` = '$product_sku' AND `ship_name` = '$fullName' AND `po_number` = '$post->ID' ");

	

		echo "<h4><a href='https://directcycleparts.org/wp-admin/post.php?post=$product_id&action=edit'>".$product->get_title()."</a></h4>";
        
		$html="";
        $html .= "<div class='submit-form'><form><div class='hidden-element'><input type='hidden' name= 'po_number' value='".$post->ID."'></div><div class='hidden-element'><input type='hidden' name= 'quantity' value='".$quantity."'/></div><div class='hidden-element'><input type='hidden' name= 'sku' value='".$product_sku."'/></div><div class='hidden-element'><input type='hidden' name= 'drag' value='".$product_drag."'/></div><div class='hidden-element'><button type='button' id ='".$product_sku."' class='button button-primary calculate-action wps' >Add To Cart On WPS</button></div><div class='hidden-element'><button type='button' id ='delete-cart-wps' class='button button-primary calculate-action delete-cart-wps' >Delete Cart From WPS</button></div><div class='hidden-element'><button type='button' id ='".$product_sku."' class='button button-primary add-to-cart-drag' >Add to Cart By Drag</button></div><div class='hidden-element'><button type='button' id ='".$product_sku."' class='button button-primary delete-to-cart-drag' >Delete to Cart By Drag</button></div></form></div>";		
        foreach($result as $row) {
			
            $row = json_decode(json_encode($row), true);
 
			$i=0;
			$html .= "<table>";
            foreach ($row as $cell) {
				$i++;
				
				if($i==2)
				{
					$html .= "<tr><td>Po Number</td><td>" . $cell . "</td></tr>";
				}
				if($i==3)
				{
					$html .= "<tr><td>Ship Name</td><td>" . $cell . "</td></tr>";
				}	
				if($i==18)
				{
					$html .= "<tr><td>QTY </td><td>" . $cell . "</td></tr>";
				}
				if($i==4)
				{
					$html .= "<tr><td>WPS# </td><td>" . $cell . "</td></tr>";
				}	
				if($i==16)
				{
					$html .= "<tr><td>Tracking </td><td>" . $cell . "</td></tr>";
				}				
				if($i==5)
				{
					$html .= "<tr><td>Send To  </td><td>" . $cell . "</td></tr>";
				}	
				if($i==21)
				{
					$html .= "<tr><td>Price  </td><td>" . $cell . "</td></tr>";
				}				
                ;
            }
			
			$html .= "</table><br>";
		
        }

		echo $html;
    }
	$order_button = "<br><div class='submit-form'><form><div class='hidden-element-order'><button type='button' id ='order-wps' class='button button-primary calculate-action' >Order WPS</button></div>
	<div class='hidden-element-order'><button type='button' id ='order-Drag' class='button button-primary calculate-action' >Order Drag</button></div></form></div>
	<div class='order-information'></div>
	";
	echo $order_button;

	
}

// Saving or doing an action when submitting
add_action( 'save_post', 'trusted_list_save_meta_box_data' );
function trusted_list_save_meta_box_data( $post_id ){

    // Only for shop order
    if ( 'shop_order' != $_POST[ 'post_type' ] )
        return $post_id;

    // Check if our nonce is set (and our cutom field)
    if ( ! isset( $_POST[ 'trusted_list_nonce' ] ) && isset( $_POST['submit_trusted_list'] ) )
        return $post_id;

    $nonce = $_POST[ 'trusted_list_nonce' ];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce ) )
        return $post_id;

    // Checking that is not an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user’s permissions (for 'shop_manager' and 'administrator' user roles)
    if ( ! current_user_can( 'edit_shop_order', $post_id ) && ! current_user_can( 'edit_shop_orders', $post_id ) )
        return $post_id;

    // Action to make or (saving data)
    if( isset( $_POST['submit_trusted_list'] ) ) {
        $order = wc_get_order( $post_id );
        // $customeremail = $order->get_billing_email();
        $order->add_order_note(sprintf("test2"));
    }
}


add_action('admin_menu', 'search_plugin_create_menu');
function search_plugin_create_menu() {
    //create new top-level menu
    add_menu_page('API TOKEN SETTING', 'API TOKEN SETTING', 'administrator', __FILE__, 'search_plugin_settings_page' /*, plugins_url('/images/icon.png', __FILE__)*/ );

    //call register settings function
    add_action( 'admin_init', 'register_search_plugin_settings_page' );
}
function register_search_plugin_settings_page(){
    register_setting( 'my-cool-plugin-settings-group', 'wps_api_token' );
    register_setting( 'my-cool-plugin-settings-group', 'trucker_api_token' );
    register_setting( 'my-cool-plugin-settings-group', 'drag_api_token' );
}
function search_plugin_settings_page() {
    ?>	<h2>TO use Drag, WPS, Trucker, you should take api token or token key
        for development, I setup WPS api token </h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
        <?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>
        <div>
            <p>WPS API TOKEN :&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="wps_api_token" value="<?php echo esc_attr( get_option('wps_api_token') ); ?>" style="width: 50%;" /></p>
            <p>TRUCKER api TOKEN: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="trucker_api_token" value="<?php echo esc_attr( get_option('trucker_api_token') ); ?>" style="width: 50%;" /></p>
            <p>DRAGE API TOCKEN:&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="drag_api_token" value="<?php echo esc_attr( get_option('drag_api_token') ); ?>" style="width: 50%;" /></p>

        </div>
        <?php submit_button(); ?>
    </form>
    <?php
	echo do_shortcode('[wpdataaccess pub_id="1"]');
}
