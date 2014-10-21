<?
///////////////////////////////////////////////
//   Разработчик: Виталий Медников           //
//   Поддержка: support@phpsolutions.ru      //
///////////////////////////////////////////////

if(!$USER->IsAdmin()) return;

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
$APPLICATION->SetAdditionalCSS("/bitrix/js/phpsolutions.feedback/feedback.css");

define( 'MODULE_ID', 'phpsolutions.feedback' ) ;

$rsSites = CSite::GetList( $by="sort", $order="desc", Array( "ACTIVE" => "Y" ) ) ;
while( $t = $rsSites->GetNext()){
	$sites["REFERENCE"][] = $t[NAME];
	$sites["REFERENCE_ID"][] = $t[LID];
}	
if( count( $sites ) == 0 ){
	$message = GetMessage( "PHPSOLUTIONS_FEEDBACK_ERROR_NO_SITE" ) ;
	$mess_type = "ERROR" ;
	CAdminMessage::ShowMessage(array(
	   "MESSAGE"=> $message,
	   "TYPE"=> $mess_type,
	));
	return ;
}

if( strlen( $_REQUEST["phpsolutions_feedback_site"]) > 0 ){	
	$_SESSION['phpsolutions_feedback_site'] = htmlspecialcharsbx($_REQUEST["phpsolutions_feedback_site"]);	
}
if(strlen($_SESSION['phpsolutions_feedback_site'])>0){
	$site_id = $_SESSION['phpsolutions_feedback_site'];
}
else{
	$site_id = $sites[ "REFERENCE_ID" ][ 0 ] ;
}

$message = '' ;
$mess_type = "OK" ;

$options_names = array(
	"phpsolutions_feedback_jquery",
	"phpsolutions_feedback_exclude_url",
	"phpsolutions_feedback_name_maxlength",
	"phpsolutions_feedback_text_maxlength",
	"phpsolutions_feedback_subjects",
	"phpsolutions_feedback_position",
	"phpsolutions_feedback_topmargin",
	"phpsolutions_feedback_header",
	"phpsolutions_feedback_name",
	"phpsolutions_feedback_email",
	"phpsolutions_feedback_dropdown_title",
	"phpsolutions_feedback_message",
	"phpsolutions_feedback_submit",
	"phpsolutions_feedback_recieved_msg",
	"phpsolutions_feedback_not_recieved_msg",
	"phpsolutions_feedback_footer",
	"phpsolutions_feedback_hide_on_submit",
	"phpsolutions_feedback_text_color",
	"phpsolutions_feedback_bg_color",
	"phpsolutions_feedback_submit_color",
	"phpsolutions_feedback_captcha",
) ;

// ---------------------  сброс настроек -------------------------

if( $reset && strlen( $site_id ) > 0 && $site_id != 'ru' && check_bitrix_sessid() ){
	COption::RemoveOption( MODULE_ID ) ;
	$message = GetMessage( "PHPSOLUTIONS_FEEDBACK_SETTINGS_RESTORED" ) ;
}

// ---------------------  сохранение настроек -------------------------

if( strlen( $apply ) > 0 && strlen( $site_id ) > 0 && $site_id != 'ru' && check_bitrix_sessid() ){
	$message = GetMessage( "PHPSOLUTIONS_FEEDBACK_SETTINGS_UPDATED" ) ;
	$mess_type = "OK" ;
	
	foreach( $options_names as $name ){		
		
		if( strlen( $_REQUEST[$name] ) > 0 ){
			if( $name == 'phpsolutions_feedback_name_maxlength' || $name == 'phpsolutions_feedback_text_maxlength' || $name == 'phpsolutions_feedback_topmargin' ){
				if( (int)$_REQUEST[ $name ] > 0 ){
					COption::SetOptionString( MODULE_ID, $name.'_'.$site_id, (int)$_REQUEST[ $name ] ) ;
				}
				else{ 					
					$message = GetMessage( "PHPSOLUTIONS_FEEDBACK_INT_ERROR" ) ;	
					$mess_type = "ERROR" ;	
				}
			}
			else{
				COption::SetOptionString(MODULE_ID, $name.'_'.$site_id, htmlspecialcharsbx($_REQUEST[$name]));			
			}
		}	
	}
}

// ---------------------  получение настроек -------------------------
{
$options = array(		
	"phpsolutions_feedback_site" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SITE_LABEL' ),
		'field-type' => 'select',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_site'.'_'.$site_id, $site_id ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SITE_HINT' ),
		'options' => $sites,
		'code' => "onchange='change_site( this.value );'"
	),
	"phpsolutions_feedback_jquery" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_JQUERY_LABEL' ),
		'field-type' => 'select',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_jquery'.'_'.$site_id, "Y" ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_JQUERY_HINT' ),
		'options' => array(
			"REFERENCE" => array( GetMessage( 'PHPSOLUTIONS_FEEDBACK_YES' ), GetMessage( 'PHPSOLUTIONS_FEEDBACK_NO' ) ),
			"REFERENCE_ID" => array('Y','N')
		)
	),
	"phpsolutions_feedback_captcha" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_CAPTCHA_LABEL' ),
		'field-type' => 'select',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_captcha'.'_'.$site_id, "Y" ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_CAPTCHA_HINT' ),
		'options' => array(
			"REFERENCE" => array( GetMessage( 'PHPSOLUTIONS_FEEDBACK_YES' ), GetMessage( 'PHPSOLUTIONS_FEEDBACK_NO' ) ),
			"REFERENCE_ID" => array('Y','N')
		)
	),
	"phpsolutions_feedback_exclude_url" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EXCLUDE_URL_LABEL' ),
		'field-type' => 'textarea',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_exclude_url'.'_'.$site_id, '' ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EXCLUDE_URL_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_name_maxlength" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME_MAXLENGTH_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_name_maxlength'.'_'.$site_id, 30 ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME_MAXLENGTH_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_text_maxlength" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TEXT_MAXLENGTH_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_text_maxlength'.'_'.$site_id, 1000 ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TEXT_MAXLENGTH_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_subjects" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SUBJECTS_LABEL' ),
		'field-type' => 'textarea',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_subjects'.'_'.$site_id, GetMessage('PHPSOLUTIONS_FEEDBACK_SUBJECTS') ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SUBJECTS_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_position" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_POSITION_LABEL' ),
		'field-type' => 'select',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_position'.'_'.$site_id, "left" ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_POSITION_HINT' ),
		'options' => array(
			"REFERENCE" => array( GetMessage( 'PHPSOLUTIONS_FEEDBACK_POSITION_LEFT' ), GetMessage( 'PHPSOLUTIONS_FEEDBACK_POSITION_RIGHT' ) ),
			"REFERENCE_ID" => array( 'left', 'right' )
		)
	),
	"phpsolutions_feedback_topmargin" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TOPMARGIN_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_topmargin'.'_'.$site_id, 60 ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TOPMARGIN_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_header" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_HEADER_LABEL' ),
		'field-type' => 'textarea',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_header'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_HEADER' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_HEADER_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_name" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_name'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_email" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EMAIL_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_email'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_EMAIL' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EMAIL_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_dropdown_title" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_DROPDOWN_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_dropdown_title'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_DROPDOWN' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_DROPDOWN_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_captcha_title" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_CAPTCHA_TITLE_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_captcha_title'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_CAPTCHA_TITLE' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_CAPTCHA_TITLE_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_message" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_MESSAGE_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_message'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_MESSAGE' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_MESSAGE_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_submit" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SUBMIT_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_submit'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_SUBMIT' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TOPMARGIN_SUBMIT' ),
		'options' => ''
	),
	"phpsolutions_feedback_recieved_msg" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_RECIEVED_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_recieved_msg'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_RECIEVED' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_RECIEVED_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_not_recieved_msg" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NOTRECIEVED_LABEL' ),
		'field-type' => 'text',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_not_recieved_msg'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_NOTRECIEVED' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NOTRECIEVED_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_footer" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_FOOTER_LABEL' ),
		'field-type' => 'textarea',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_footer'.'_'.$site_id, GetMessage( 'PHPSOLUTIONS_FEEDBACK_FOOTER' ) ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_FOOTER_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_hide_on_submit" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_HIDEONSUBMIT_LABEL' ),
		'field-type' => 'select',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_hide_on_submit'.'_'.$site_id, "Y" ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_HIDEONSUBMIT_HINT' ),
		'options' => array(
			"REFERENCE" => array( GetMessage( 'PHPSOLUTIONS_FEEDBACK_YES' ), GetMessage( 'PHPSOLUTIONS_FEEDBACK_NO' ) ),
			"REFERENCE_ID" => array('true','false')
		)
	),
	"phpsolutions_feedback_text_color" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TEXT_COLOR_LABEL' ),
		'field-type' => 'color',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_text_color'.'_'.$site_id, '#FFFFFF' ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_TEXT_COLOR_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_bg_color" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_BG_LABEL' ),
		'field-type' => 'color',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_bg_color'.'_'.$site_id, '#008040' ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_BG_HINT' ),
		'options' => ''
	),
	"phpsolutions_feedback_submit_color" => Array(
		'label' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SUBMIT_LABEL' ),
		'field-type' => 'color',
		'value' => COption::GetOptionString( MODULE_ID, 'phpsolutions_feedback_submit_color'.'_'.$site_id, '#FFFFFF' ),
		'hint' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_SUBMIT_HINT' ),
		'options' => ''
	),
);
}
// ---------------------  вывод формы -------------------------

if( $message != '' ){
	CAdminMessage::ShowMessage(array(
	   "MESSAGE"=> $message,
	   "TYPE"=> $mess_type,
	));
}

$tabControl = new CAdminTabControl(
	'tabControl',
	array(
		array(
		'DIV' => 'edit1',
		'TAB' => GetMessage('MAIN_TAB_SET'),
		'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')
		),
	)
);
$tabControl->Begin();

?>

<form id="edit1" enctype="multipart/form-data" name="phpsolutions_feedback" method='POST' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>'>
<?=bitrix_sessid_post();?>
<?$tabControl->BeginNextTab();?>

<? foreach( $options as $k => $v ){ ?>
	<tr class="field-str">
		<td width='50%' class='field-name adm-detail-content-cell-l'> <?= $v[ 'hint' ] ? ShowJSHint( $v[ 'hint' ] ) : '' ?> <label><?= $v[ 'label' ] ?>: </label></td>
		<td width='50%'>
			<?
			if( $v[ 'field-type' ] == 'select' ){
				echo SelectBoxFromArray(
				$k,
				$v[ 'options' ],
				$v[ 'value' ],
				"",
				$v[ 'code' ]
				) ;
			}
			elseif( $v[ 'field-type' ] == 'textarea' ){
				echo '<textarea cols="80" rows="5" name="'.$k.'">'.$v[ 'value' ].'</textarea>' ;
			}
			elseif( $v[ 'field-type' ] == 'text' ){
				echo '<input size="50" type="text" name="'.$k.'" value="'.$v[ 'value' ].'">' ;
			}
			elseif( $v[ 'field-type' ] == 'color' ){
				echo '<input type="color" name="'.$k.'" value="'.$v[ 'value' ].'">' ;
			}
			?>
		</td>
	</tr>	
<?
}
?>

<?$tabControl->Buttons();?>

<script language='JavaScript'>
function confirm_reset(){
    if(confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>')){
        document.getElementById( "resetparam" ).value = '1' ;
		document.forms[ "edit1" ].submit() ;
	}
}
</script>

<input id="resetparam" type="hidden" name="reset" value="0">
<input type="submit" name="apply" value="<?echo GetMessage('MAIN_APPLY')?>">
<input type="button" onclick="confirm_reset();" title='<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>' value='<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>'>
<?
$tabControl->End();
CUtil::InitJSCore(Array("jquery"));
?>		
</form>
<script>
function change_site( sid ){
	window.location.href = '<?= $APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam() ?>' + '&phpsolutions_feedback_site=' + sid ;
}
</script>