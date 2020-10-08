<?php

// Include wp-load.php
require_once('../wp-load.php');
error_reporting(1);
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export.csv"');
global $wpdb;
$file 			= fopen('php://output', 'wb');
$data_array = array(
			"Type"								=> "Type",
			"Parent SKU"						=> "Parent SKU",
			"SKU"								=> "SKU",
			"Product title"						=> "Product title",
			"Published"							=> "Published",
			"Visibility in catalogue"			=> "Visibility in catalogue",
			"Short description"					=> "Short description",
			"Description"						=> "Description",
			"Shop selection"					=> "Shop selection",
			"Stock"								=> "Stock",
			"Low stock amount"					=> "Low stock amount",
			"Product Image"						=> "Product Image",
			"Second image"						=> "Second image",
			"Gallery Image 1"					=> "Gallery Image 1",
			"Gallery Image 2"					=> "Gallery Image 2",
			"Gallery Image 3"					=> "Gallery Image 3",
			"Gallery Image 4"					=> "Gallery Image 4",
			"Custom product type"				=> "Custom product type",
			"Max Qty limit"						=> "Max Qty limit",
			"Care Tips"							=> "Care Tips",
			"Champion image credit"				=> "Champion image credit",
			"Regular price"						=> "Regular price",
			"Sale price"						=> "Sale price",
			"Categories"						=> "Categories",
			"Attribute 1 name"					=> "Attribute 1 name",
			"Attribute 1 value(s)"				=> "Attribute 1 value(s)",
			"Attribute 2 name"					=> "Attribute 2 name",
			"Attribute 2 value(s)"				=> "Attribute 2 value(s)",
			"Linked products- Upsells)"			=> "Linked products- Upsells)",
			"Linked products- Cross sells"		=> "Linked products- Cross sells",
			"Product tag"						=> "Product tag",
			"Product types"						=> "Product types",
			"SEO KeyWord"						=> "SEO KeyWord",
			"SEO Title"							=> "SEO Title",
			"SEO Description"					=> "SEO Description"
);

fputcsv($file, $data_array);
$all_ids = get_posts( array(
		'post_type' 	=> 'product',
    	'numberposts' 	=> 1,
    	'post_status' 	=> 'publish',
    	'fields' 		=> 'ids',
    	'order'			=> "desc"	
 	)
);
foreach ( $all_ids as $id ) {
 	$product 				= wc_get_product($id);
 	$post_meta 				= get_post_meta($id);
 	$pro_s_desc				= $product ->get_short_description();
 	$pro_desc				= $product ->get_description();
 	$pro_type 				= $product->get_type();
    $pro_title 				= $product->get_name();
    $pro_sku 				= $product->get_sku();
    $pro_cat_detail 		= wp_get_post_terms( $id, 'product_cat' );
    $pro_cat 				= $pro_cat_detail[0]->name;
    $pro_tag_detail 		= wp_get_post_terms( $id, 'product_tag' );
    $pro_tag 				= array();
    foreach ($pro_tag_detail as $pro_tags) {
    	$pro_tag[]			=  $pro_tags->name;
    }
    $pro_shop_detail 		= wp_get_post_terms($id,'woo_shop');
    $pro_shop 				= array();
    foreach ($pro_shop_detail as $pro_shops) {
    	$pro_shop[]			=  $pro_shops->name;
    }
    $plant_type_detail 		= wp_get_post_terms( $id, 'plant_type' );
    $plant_type 			= array();
    foreach ($plant_type_detail as $plant_types ) {
    	$plant_type[]		= $plant_types->name;
    }

    $pro_status 			= $product->get_status();
    $pro_reg_price 			= $product->get_price();
    $pro_sale_price			= $product->get_sale_price();
    $pro_stock				= $product->get_stock_quantity();
    $max_qty_limit 			= $post_meta['max_qty_limit'][0];
    $care_tips 				= $post_meta['care_tips'][0];
    $custom_product_type 	= $post_meta['custom_product_type'][0];
    $champion_image 		= $post_meta['champion_image'][0];
    $thumbnail_id 			= $post_meta['_thumbnail_id'][0];
    $thumb_url				= wp_get_attachment_url($thumbnail_id);
    $thumb_title			= end(explode("/", $thumb_url));
    $second_image_id 		= $post_meta['second_image'][0];
    $second_image_url		= wp_get_attachment_url($second_image_id);
    $second_image_title		= end(explode("/", $second_image_url));
    $pa_size				= array();
    $pa_color				= array();
    foreach( wc_get_product_terms( $id, 'pa_size' ) as $size ){
	   $pa_size[]			= $size->name;
	   $size_name			= implode(",", $pa_size);
	}
	foreach( wc_get_product_terms( $id, 'pa_color' ) as $color ){
	   	$pro_color			= $color->name;
	   	$color_name			= implode(",", $pro_color);
	}
	$galleries 				= $wpdb->get_results("SELECT meta_value  FROM $wpdb->postmeta WHERE (meta_key = '_product_image_gallery' AND post_id = '".$id."')");
	
	$gal_images_ids 		= $galleries[0]->meta_value;
	$gal_image_title		= array();
	$gal_img_1				= '';
	$gal_img_2				= '';
	$gal_img_3				= '';
	$gal_img_4				= '';
	foreach (explode(",", $gal_images_ids) as $gal_img_id) {
		$gal_images_url 	= wp_get_attachment_url($gal_img_id);
		$gal_image_title[]	= end(explode("/", $gal_images_url));
		$gal_img_1			= $gal_image_title[0];
		if (isset($gal_image_title[1]) && !empty($gal_image_title[1])) {
			$gal_img_2		= $gal_image_title[1];
		}
		if (isset($gal_image_title[2]) && !empty($gal_image_title[2])) {
			$gal_img_3		= $gal_image_title[2];
		}
		if (isset($gal_image_title[3]) && !empty($gal_image_title[3])) {
			$gal_img_4		= $gal_image_title[3];
		}
	}	
	$soe_keyword			= "";
	$soe_title				= "";	
	$soe_metadesc			= "";
	if(isset($post_meta['_yoast_wpseo_focuskw']) && !empty($post_meta['_yoast_wpseo_focuskw'])){
		$soe_keyword 		= $post_meta['_yoast_wpseo_focuskw'][0];
	}
	if(isset($post_meta['_yoast_wpseo_title']) && !empty($post_meta['_yoast_wpseo_title'])){
		$soe_title 		= $post_meta['_yoast_wpseo_title'][0];
	}
	if(isset($post_meta['_yoast_wpseo_metadesc']) && !empty($post_meta['_yoast_wpseo_metadesc'])){
		$soe_metadesc 		= $post_meta['_yoast_wpseo_metadesc'][0];
	}
	$data_array = array(
		"Type"								=> $pro_type,
		"Parent SKU"						=> $pro_sku,
		"SKU"								=> $pro_sku,
		"Product title"						=> $pro_title,
		"Published"							=> 1,
		"Visibility in catalogue"			=> "visible",
		"Short description"					=> $pro_s_desc,
		"Description"						=> $pro_desc,
		"Shop selection"					=> implode(",", $pro_shop),
		"Stock"								=> $pro_stock,
		"Low stock amount"					=> 2,
		"Product Image"						=> $thumb_title,
		"Second image"						=> $second_image_title,
		"Gallery Image 1"					=> $gal_img_1,
		"Gallery Image 2"					=> $gal_img_2,
		"Gallery Image 3"					=> $gal_img_3,
		"Gallery Image 4"					=> $gal_img_4,
		"Custom product type"				=> $custom_product_type,
		"Max Qty limit"						=> $max_qty_limit,
		"Care Tips"							=> $care_tips,
		"Champion image credit"				=> $champion_image,
		"Regular price"						=> $pro_reg_price,
		"Sale price"						=> $pro_sale_price,
		"Categories"						=> $pro_cat,
		"Attribute 1 name"					=> "Size",
		"Attribute 1 value(s)"				=> " ",
		"Attribute 2 name"					=> "Color",
		"Attribute 2 value(s)"				=> " ",
		"Linked products- Upsells)"			=> " ",
		"Linked products- Cross sells"		=> " ",
		"Product tag"						=> implode(",", $pro_tag),
		"Product types"						=> implode(",", $plant_type),
		"SEO KeyWord"						=> $soe_keyword,
		"SEO Title"							=> $soe_title,
		"SEO Description"					=> $soe_metadesc
	);
	fputcsv($file , $data_array);
        
}