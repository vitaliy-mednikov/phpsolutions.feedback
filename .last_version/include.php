<?
///////////////////////////////////////////////
//   Разработчик: Виталий Медников           //
//   Поддержка: support@phpsolutions.ru      //
///////////////////////////////////////////////

IncludeModuleLangFile(__FILE__);

Class CPHPSolutionsFeedback{
	function AddScriptFeedback(){
		global $APPLICATION; 
		if(CModule::IncludeModule('phpsolutions.feedback')){		
            if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true){		
				$phpsolutions_feedback_exclude_url = COption::GetOptionString('phpsolutions.feedback', 'phpsolutions_feedback_exclude_url'.'_'.SITE_ID, "" );
				if( $phpsolutions_feedback_exclude_url != '' ){
					$tmp_list = explode( "\n", $phpsolutions_feedback_exclude_url ) ;
					foreach( $tmp_list as $v ){
						if( $path = parse_url( trim( $v ), PHP_URL_PATH ) ) $exclusion_list[] = $path ;
					}
					if( in_array( $_SERVER[ 'REQUEST_URI' ], $exclusion_list ) ) return ;
				}
				if( COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_jquery'.'_'.SITE_ID, 'Y' ) == 'Y' ) CUtil::InitJSCore( Array( "jquery" ) ) ;
				$phpsolutions_feedback_name_maxlength = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_name_maxlength'.'_'.SITE_ID, 30 ) ;
				$phpsolutions_feedback_text_maxlength = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_text_maxlength'.'_'.SITE_ID, 1000 ) ;
				$phpsolutions_feedback_subjects = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_subjects'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_SUBJECTS" ) ) ;
				$phpsolutions_feedback_subjects_tmp = explode( "\n", $phpsolutions_feedback_subjects ) ;
				$phpsolutions_feedback_subjects = array() ;
				foreach( $phpsolutions_feedback_subjects_tmp as $v ){
					$v = trim( $v ) ;
					$phpsolutions_feedback_subjects[] = "'$v'" ;
				}
				$phpsolutions_feedback_subjects = implode( ', ', $phpsolutions_feedback_subjects ) ;
				$phpsolutions_feedback_position = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_position'.'_'.SITE_ID, 'left' ) ;
				$phpsolutions_feedback_topmargin = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_topmargin'.'_'.SITE_ID, 60 ) ;
				$phpsolutions_feedback_header = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_header'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_HEADER" ) ) ;
				$phpsolutions_feedback_name = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_name'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_NAME" ) ) ;
				$phpsolutions_feedback_email = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_email'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_EMAIL" ) ) ;
				$phpsolutions_feedback_dropdown_title = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_dropdown_title'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_DROPDOWN" ) ) ;
				$phpsolutions_feedback_message = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_message'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_MESSAGE" ) ) ;
				$phpsolutions_feedback_submit = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_submit'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_SUBMIT" ) ) ;
				$phpsolutions_feedback_recieved_msg = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_recieved_msg'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_RECIEVED" ) ) ;
				$phpsolutions_feedback_not_recieved_msg = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_not_recieved_msg'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_NOTRECIEVED" ) ) ;
				$phpsolutions_feedback_footer = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_footer'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_FOOTER" ) ) ;
				$phpsolutions_feedback_hide_on_submit = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_hide_on_submit'.'_'.SITE_ID, 'true' ) ;
				$phpsolutions_feedback_text_color = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_text_color'.'_'.SITE_ID, '#FFFFFF' ) ;
				$phpsolutions_feedback_bg_color = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_bg_color'.'_'.SITE_ID, '#008040' ) ;
				$phpsolutions_feedback_submit_color = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_submit_color'.'_'.SITE_ID, '#FFFFFF' ) ;
				$phpsolutions_feedback_captcha = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_captcha'.'_'.SITE_ID, 'Y' ) ;
				$phpsolutions_feedback_captcha_title = COption::GetOptionString( 'phpsolutions.feedback', 'phpsolutions_feedback_captcha_title'.'_'.SITE_ID, GetMessage( "PHPSOLUTIONS_FEEDBACK_CAPTCHA_TITLE" ) ) ;
				$APPLICATION->AddHeadString( "<script>
				$(document).ready(function(){ 
					$( \"body\" ).append( \"<div id='my-contact-div'><!-- contactable html placeholder --></div>\" ) ;
				});
				jQuery(function(){
					jQuery('#my-contact-div').contactable(
					{
						header: '".$phpsolutions_feedback_header."',
						url: '/mail.php',
						name: '".$phpsolutions_feedback_name."',
						email: '".$phpsolutions_feedback_email."',
						dropdownTitle: '".$phpsolutions_feedback_dropdown_title."',
						dropdownOptions: [".$phpsolutions_feedback_subjects."],
						message : '".$phpsolutions_feedback_message."',
						submit : '".$phpsolutions_feedback_submit."',
						recievedMsg : '".$phpsolutions_feedback_recieved_msg."',
						notRecievedMsg : '".$phpsolutions_feedback_not_recieved_msg."',
						footer: '".$phpsolutions_feedback_footer."',
						showCaptcha: '".$phpsolutions_feedback_captcha."',
						captchaTitle: '".$phpsolutions_feedback_captcha_title."',
						wrongCaptcha: '".GetMessage( "PHPSOLUTIONS_FEEDBACK_WRONG_CAPTCHA" )."',
						refreshCaptcha: '".GetMessage( "PHPSOLUTIONS_FEEDBACK_REFRESH_CAPTCHA" )."',
						hash: '".uniqid()."',
						hideOnSubmit: ".$phpsolutions_feedback_hide_on_submit."
					});
				});
				var form_position = '".$phpsolutions_feedback_position."' ;
				</script>", true ) ;			
				$APPLICATION->AddHeadScript( "/bitrix/js/phpsolutions.feedback/feedback.js" ) ;
				$APPLICATION->AddHeadString( "<link href='/bitrix/js/phpsolutions.feedback/feedback-".$phpsolutions_feedback_position.".css' type='text/css' rel='stylesheet' />", true ) ;
				$APPLICATION->AddHeadString( "<style>
#contactable-inner {
	background-color:".$phpsolutions_feedback_bg_color.";
	color:".$phpsolutions_feedback_text_color.";
	top:".( $phpsolutions_feedback_topmargin + 8 )."px;
}
#contactable-contactForm {
	background-color:".$phpsolutions_feedback_bg_color.";
	color:".$phpsolutions_feedback_text_color.";
	top:".( $phpsolutions_feedback_topmargin + 160 )."px;
}
form#contactable-contactForm .contactable-submit {
	color:".$phpsolutions_feedback_submit_color.";
}
				</style>", true ) ;			
			}		
		}
	}	
}
?>