<?
///////////////////////////////////////////////
//   Разработчик: Виталий Медников           //
//   Поддержка: support@phpsolutions.ru      //
///////////////////////////////////////////////

IncludeModuleLangFile(__FILE__);
Class phpsolutions_feedback extends CModule{
	
	const MODULE_ID = 'phpsolutions.feedback';
	var $MODULE_ID = 'phpsolutions.feedback'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function __construct(){
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php")) ;
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
		
		$this->MODULE_NAME = GetMessage("PHPSOLUTIONS_FEEDBACK_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("PHPSOLUTIONS_FEEDBACK_INSTALL_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("PHPSOLUTIONS_FEEDBACK_INSTALL_PARTNER_NAME");
        $this->PARTNER_URI = "http://phpsolutions.ru/";
	}

	function InstallDB(){		
		RegisterModuleDependences( "main", "OnBeforeEndBufferContent", self::MODULE_ID, "CPHPSolutionsFeedback", "AddScriptFeedback" ) ;
	}

	function UnInstallDB(){		
        UnRegisterModuleDependences( "main", "OnBeforeEndBufferContent", self::MODULE_ID, "CPHPSolutionsFeedback", "AddScriptFeedback" ) ;		
	}

	function InstallFiles(){
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/phpsolutions.feedback/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/phpsolutions.feedback/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/phpsolutions.feedback/install/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/phpsolutions.feedback/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/phpsolutions.feedback/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/phpsolutions.feedback/", true, true);
        CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/phpsolutions.feedback/install/mail.php", $_SERVER["DOCUMENT_ROOT"]."/mail.php" );
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/phpsolutions.feedback/install/root", $_SERVER["DOCUMENT_ROOT"]."/", true, true);
		mkdir( $_SERVER["DOCUMENT_ROOT"]."/captcha/hash", 0755 ) ;
	}

	function UnInstallFiles(){
		DeleteDirFilesEx("/bitrix/js/phpsolutions.feedback");
        DeleteDirFilesEx("/bitrix/images/phpsolutions.feedback");		
        DeleteDirFilesEx("/mail.php");		
        DeleteDirFilesEx("/captcha");		
	}

	function InstallEvents(){
		$eventType = new CEventType ;
		$arEventTypeFields = array(
			0 => array(
				'LID'         => 'ru',
				'EVENT_NAME'  => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EVENT_NAME' ),
				'NAME'        => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME_RU' ),
				'DESCRIPTION' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_DESCRIPTION_RU' ),
			),
			1 => array(
				'LID'         => 'en',
				'EVENT_NAME'  => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EVENT_NAME' ),
				'NAME'        => GetMessage( 'PHPSOLUTIONS_FEEDBACK_NAME_EN' ),
				'DESCRIPTION' => GetMessage( 'PHPSOLUTIONS_FEEDBACK_DESCRIPTION_EN' ),
			),
		);
		foreach( $arEventTypeFields as $arField ){
			$rsET = $eventType->GetByID( $arField['EVENT_NAME'], $arField['LID'] ) ;
			$arET = $rsET->Fetch() ;
			if( !$arET ) $eventType->Add( $arField ) ;
		}
		
		$rsSites = CSite::GetList( $by = "def", $order = "desc", Array() ) ;
		while( $arSite = $rsSites->Fetch() ){
			$site_ids[] = $arSite["ID"] ;
		}

		$arrMess["ACTIVE"] = "Y";
		$arrMess["EVENT_NAME"] = GetMessage('PHPSOLUTIONS_FEEDBACK_EVENT_NAME');
		$arrMess["LID"] = $site_ids ;
		$arrMess["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
		$arrMess["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
		$arrMess["SUBJECT"] = GetMessage("PHPSOLUTIONS_FEEDBACK_MAIL_SUBJECT");
		$arrMess["BODY_TYPE"] = "text";
		$arrMess["MESSAGE"] = GetMessage("PHPSOLUTIONS_FEEDBACK_MAIL_BODY");

		$emess = new CEventMessage;
		$emess->Add($arrMess);
	}

	function UnInstallEvents(){
		$emessage = new CEventMessage ;
		$rsMess = CEventMessage::GetList( $by="site_id", $order="desc", array( "EVENT_NAME" => GetMessage( 'PHPSOLUTIONS_FEEDBACK_EVENT_NAME' ) ) ) ;
		while( $arMess = $rsMess->Fetch() ){
			$emessage->Delete( intval( $arMess[ 'ID' ] ) ) ;
		}
		
		$eventType = new CEventType;
		$eventType->Delete( GetMessage( 'PHPSOLUTIONS_FEEDBACK_EVENT_NAME' ) ) ;
	}

	function InstallIBlock(){
		if(CModule::IncludeModule("iblock")){ 
			$arFields = Array(
				'ID'=>'phpsolutions_feedback',
				'SECTIONS'=>'N',
				'IN_RSS'=>'N',
				'SORT'=>100,
				'LANG'=>Array(
					'en'=>Array(
						'NAME'=>'Feedback messages',
					),
					'ru'=>Array(
						'NAME'=>GetMessage( 'PHPSOLUTIONS_FEEDBACK_MESSAGES' ),
					)
				)
			);
			
			$obBlocktype = new CIBlockType;
			$obBlocktype->Add( $arFields );
			
			$rsSites = CSite::GetList( $by = "def", $order = "desc", Array() ) ;
			while( $arSite = $rsSites->Fetch() ){
				$sites[] = $arSite[ 'ID' ] ;
			}
			$ib = new CIBlock;
			$arFields = Array(
				"ACTIVE" => 'Y',
				"NAME" => GetMessage( 'PHPSOLUTIONS_FEEDBACK_MESSAGES_IB' ),
				"CODE" => 'phpsolutions_feedback_messages',
				"IBLOCK_TYPE_ID" => 'phpsolutions_feedback',
				"SITE_ID" => $sites,
				"SORT" => 500,
				"DESCRIPTION_TYPE" => 'text',
				"TIMESTAMP_X" => date( 'd.m.Y H:i:s' ),
				"RSS_ACTIVE" => 'N',
				"RSS_TTL" => 24,
				"RSS_FILE_ACTIVE" => 'N',
				"RSS_YANDEX_ACTIVE" => 'N',
				"INDEX_ELEMENT" => 'N',
				"INDEX_SECTION" => 'N',
				"WORKFLOW " => 'N',
				"VERSION" => 1,
				"GROUP_ID" => Array( "2" => "W" )
			);
			$ID = $ib->Add( $arFields ) ;
			if( $ID > 0 ){
				$ibp = new CIBlockProperty;
				$arFields = Array(
					"NAME" => GetMessage( 'PHPSOLUTIONS_FEEDBACK_PROPERTY_FROM' ),
					"ACTIVE" => "Y",
					"CODE" => "FROM",
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $ID,
					"ROW_COUNT" => 1,
					"COL_COUNT" => 60,
					"SORT" => 501,
				);
				$propId = $ibp->Add( $arFields ) ;
				$arFields = Array(
					"NAME" => GetMessage( 'PHPSOLUTIONS_FEEDBACK_PROPERTY_EMAIL' ),
					"ACTIVE" => "Y",
					"CODE" => "EMAIL",
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $ID,
					"ROW_COUNT" => 1,
					"COL_COUNT" => 60,
					"SORT" => 502,
				);
				$propId = $ibp->Add( $arFields ) ;
				$arFields = Array(
					"NAME" => GetMessage( 'PHPSOLUTIONS_FEEDBACK_PROPERTY_SUBJECT' ),
					"ACTIVE" => "Y",
					"CODE" => "SUBJECT",
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $ID,
					"ROW_COUNT" => 2,
					"COL_COUNT" => 60,
					"SORT" => 503,
				);
				$propId = $ibp->Add( $arFields ) ;
				$arFields = Array(
					"NAME" => GetMessage( 'PHPSOLUTIONS_FEEDBACK_PROPERTY_TEXT' ),
					"ACTIVE" => "Y",
					"CODE" => "TEXT",
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $ID,
					"ROW_COUNT" => 5,
					"COL_COUNT" => 60,
					"SORT" => 504,
				);
				$propId = $ibp->Add( $arFields ) ;
			}
		} 
	}

	function UnInstallIBlock(){
		if(CModule::IncludeModule("iblock")){ 
			CIBlockType::Delete('phpsolutions_feedback') ;
		}
	}

	function DoInstall(){
		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallIBlock() ;
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall(){
		UnRegisterModule( self::MODULE_ID ) ;
		$this->UnInstallIBlock() ;
		$this->UnInstallEvents();
		$this->UnInstallDB();
		$this->UnInstallFiles();
		COption::RemoveOption( self::MODULE_ID ) ;
	}
}
?>