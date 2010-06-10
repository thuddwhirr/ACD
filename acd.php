<?php

if(array_key_exists('json',$_REQUEST)){
	include("_json.php");
} else {
	include("_html.php");
}
