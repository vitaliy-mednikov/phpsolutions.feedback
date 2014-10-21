<?php

// отправка сообщения
$hash_path = 'captcha/hash';  		//Путь к файлам с хешем
$hash_lifetime = 120 ;  			//Время жизни хеша в секундах

//удаляем старые файлы хеша
$dir = opendir( $hash_path ) ;
while( $file = readdir( $dir ) ){
	if( $file != "." && $file != ".." ){
		$modified = filemtime( $hash_path.'/'.$file ) ;
		if( $modified <= time() - $hash_lifetime ) unlink( $hash_path.'/'.$file ) ;
	}
}
closedir( $dir ) ;

require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" ) ;

$phpsolutions_feedback_captcha = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_captcha'.'_'.SITE_ID, 'Y' ) ;
if( $phpsolutions_feedback_captcha == 'Y' ){
	if( strlen( $_POST[ 'captcha' ] ) > 0 && isset( $_POST[ 'hash' ] ) ){
		$hash = file_get_contents( $hash_path.'/'.$_POST[ 'hash' ].'.txt' ) ;
		$captcha = md5( htmlspecialchars ( strip_tags( stripcslashes( $_POST['captcha'] ) ) ) ) ;
		if( $captcha != $hash ) output( "wrongcaptcha" ) ;
	}
	else output( "failed" ) ;
}

CModule::IncludeModule( "main" ) ;

$name = htmlspecialchars ( strip_tags( stripcslashes( $_POST['name'] ) ) ) ;
$emailAddr = htmlspecialchars ( strip_tags( stripcslashes( $_POST['email'] ) ) ) ;
$issue = htmlspecialchars ( strip_tags( stripcslashes( $_POST['issue'] ) ) ) ;
$message = htmlspecialchars ( strip_tags( stripcslashes( $_POST['message'] ) ) ) ;

define( 'MODULE_ID', 'phpsolutions.feedback' ) ;
if( LANG_CHARSET != 'UTF-8' ){
	$name = iconv( 'utf-8', LANG_CHARSET, $name ) ;
	$emailAddr = iconv( 'utf-8', LANG_CHARSET, $emailAddr ) ;
	$issue = iconv( 'utf-8', LANG_CHARSET, $issue ) ;
	$message = iconv( 'utf-8', LANG_CHARSET, $message ) ;
}

$arEventFields = array(
	"AUTHOR"       => $name,
	"AUTHOR_EMAIL" => $emailAddr,
	"TEXT"         => $message,
	"SUBJECT"         => $issue,
);
CEvent::Send( "PHPSOLUTIONS_FEEDBACK_FORM", SITE_ID, $arEventFields ) ;

CModule::IncludeModule( "iblock" ) ;
$el = new CIBlockElement;

$PROP[ 'FROM' ] = $name ;
$PROP[ 'EMAIL' ] = $emailAddr ;
$PROP[ 'SUBJECT' ] = $issue ;
$PROP[ 'TEXT' ] = $message ;

$res = CIBlock::GetList(
    Array( "SORT" => "ASC" ), 
    Array( "CODE" => 'phpsolutions_feedback_messages' ),
	true
);
while( $ar_res = $res->Fetch() ){
	$iblock_id = $ar_res['ID'] ;
	$arLoadProductArray = Array(
		"IBLOCK_ID"       => $iblock_id,
		"PROPERTY_VALUES" => $PROP,
		"NAME"            => $name,
		"ACTIVE"          => "Y",
	);
	if( $el->Add( $arLoadProductArray ) ) output( "success" ) ;
	else output( "failed" ) ;
}
output( "failed" ) ;

function output( $res ){
	$output = json_encode( array( "response" => $res ) ) ;
	header( 'content-type: application/json; charset=utf-8' ) ;
	echo( $output ) ;
	die ;
}

?>