<?php
/*
* Author: Michele Salvini
* info@slv.dj
*
* GET Call to index.php with parameter 'id' corresponding to URL to scrape
*
* ex: http://www.example.com/index.php?id=www.domaintoscrape.com
*
*/

/* Comment the next 2 lines if you don't require to be logged in */
session_start();
if (!isset($_SESSION['user_id'])) die('Not logged in');
/* End session check  */


function httpGet($Url, $headers = array()){
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	return array('result' => $result, 'info' => $info);
}

$curl = httpGet('http://' . str_replace('http://' , '', $_GET['id']));

$content = $curl['result'];
$info = $curl['info'];

if(!preg_match("#^(?P<value>https?:\/\/[^\/]*)$#", $info['url'], $page_path))
	preg_match("#^(?P<value>https?:\/\/.*)\/[^\/]*$#", $info['url'], $page_path);

preg_match("#^(?P<value>https?:\/\/[^\/]*).*$#", $info['url'], $page_root);

if(!preg_match('#\<meta.*property="og:image.*content="(?P<value>.+)".*\>#i', $content, $scraped_img))
	if(!preg_match('#\<meta.*content="(?P<value>.+)".*property="og:image.*\>#i', $content, $scraped_img))
		preg_match_all('#\<img.*src="(?P<value>.*\.jpe?g)".*\>#i', $content, $scraped_img);


if(!preg_match('#\<meta.*property="og:title.*content="(?P<value>.+)".*\>#i', $content, $title))
	if(!preg_match('#\<meta.*content="(?P<value>.+)".*property="og:title.*\>#i', $content, $title))
		if(!preg_match('#\<title\>(?P<value>.+)\<\/title\>#i', $content, $title))
			$title['value'] = '';

if(!preg_match('#\<meta.*property="og:description.*content="(?P<value>.+)".*\>#i', $content, $description))
 	if(!preg_match('#\<meta.*content="(?P<value>.+)".*property="og:description.*\>#i', $content, $description))
	 	if(!preg_match('#\<meta.*content="(?P<value>.+)".*name="description.*\>#i', $content, $description))
		 	if(!preg_match('#\<meta.*name="description.*content="(?P<value>.+)".*\>#i', $content, $description)) {
				$description['value'] = '';
		 	}

if(!preg_match('#\<meta.*property="og:url.*content="(?P<value>.+)".*\>#i', $content, $url))
	if(!preg_match('#\<meta.*content="(?P<value>.+)".*property="og:url.*\>#i', $content, $url))
		$url['value'] = current(explode("#", $_GET['id']));

if (!is_array($scraped_img['value'])) $scraped_img['value'] = array($scraped_img['value']);

$img['value'] = array();

foreach ($scraped_img['value'] as $i) {

	if (preg_match("#^\/.*#", $i)) $img['value'][] = $page_root['value'] . $i;
	else if (preg_match("#^https?:\/\/.*#", $i)) $img['value'][] = $i;
	else $img['value'][] = $page_path['value'] . '/' . $i;
} 

$out = array(
	'description' => (string)trim($description['value']),
	'title' => (string)$title['value'],
	'url' => (string)$url['value'],
	'img' => $img['value']
);

die(json_encode($out));