<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    proofreading
 * @subpackage proofreading/includes
 * @author     Scribit <wordpress@scribit.it>
 */
class Proofreading_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		global $wpdb;
		
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}proofreading_languages'") == ($wpdb->prefix . 'proofreading_languages');
		
		// If languages table don't exists drop all and recreate tables inserting their default data
		if (!$table_exists) :
		
			/* Languages */
			
			$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}proofreading_languages`;");
			$wpdb->query("CREATE TABLE `{$wpdb->prefix}proofreading_languages` (
			  `name` varchar(30) NOT NULL,
			  `code` char(2) NOT NULL,
			  `longCode` varchar(30) NOT NULL,
			  `active` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`code`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			$wpdb->query("INSERT INTO `{$wpdb->prefix}proofreading_languages`(`name`,`code`,`longCode`,`active`) VALUES ('Asturian','as','',0),('Belarusian','be','bel',0),('Breton','br','',0),('Catalan','ca','ca',0),('Chinese','zh','zh',0),('Danish','da','da',1),('Dutch','nl','nl',1),('English','en','en',1),('Esperanto','eo','eo',0),('French','fr','fr',1),('Galician','gl','gl',0),('German','de','de',1),('Greek','el','el',1),('Italian','it','it',1),('Japanese','ja','ja',1),('Khmer','km','km',0),('Persian','fa','fa',0),('Polish','pl','pl',1),('Portuguese','pt','pt',1),('Romanian','ro','ro',1),('Russian','ru','ru',1),('Serbian','sr','sr',1),('Slovak','sk','sk',0),('Slovenian','sl','sl',1),('Spanish','es','es',1),('Swedish','sv','sv',1),('Tagalog','tl','tl',1),('Tamil','ta','ta',1),('Ukrainian','uk','uk',0);");
			
			/* Language Rules */
			
			$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}proofreading_rules`;");
			$wpdb->query("CREATE TABLE `{$wpdb->prefix}proofreading_rules` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `key` varchar(128) NOT NULL,
			  `lang_code` char(2) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `lang` (`lang_code`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			$wpdb->query("INSERT INTO `{$wpdb->prefix}proofreading_rules`(`id`,`name`,`key`,`lang_code`) VALUES (2,'Altre','MISC','it'),(3,'Grammatica - Aggettivi','CAT1','it'),(4,'Grammatica - Articoli','CAT2','it'),(5,'Grammatica - Elisioni e troncamenti','CAT3','it'),(6,'Grammatica - Frase','CAT4','it'),(7,'Grammatica - Preposizioni','CAT5','it'),(8,'Grammatica - Punteggiatura','CAT6','it'),(9,'Grammatica - Verbi','CAT7','it'),(10,'Possibile errore di battitura','TYPOS','it'),(11,'Punteggiatura','PUNCTUATION','it'),(12,'Stile - Espressioni','CAT8','it'),(13,'Stile - Frase','CAT9','it'),(14,'Stile - Leggibilità','CAT10','it'),(15,'Stile - Numeri','CAT11','it'),(16,'Tipografia','TYPOGRAPHY','it'),(17,'Ulteriori errori comuni - ortografia','CAT13','it'),(18,'Ulteriori errori comuni - voci del verbo \'avere\'','CAT12','it'),(19,'Uso delle maiuscole','CASING','it'),(20,'Capitalization','CASING','en'),(21,'Collocations','COLLOCATIONS','en'),(22,'Commonly Confused Words','CONFUSED_WORDS','en'),(23,'Grammar','GRAMMAR','en'),(24,'Miscellaneous','MISC','en'),(25,'Misused terms in EU publications (Gardner)','MISUSED_TERMS_EU_PUBLICATIONS','en'),(26,'Nonstandard Phrases','NONSTANDARD_PHRASES','en'),(27,'Plain English','PLAIN_ENGLISH','en'),(28,'Possible Typo','TYPOS','en'),(29,'Punctuation','PUNCTUATION','en'),(31,'Redundant Phrases','REDUNDANCY','en'),(32,'Semantic','SEMANTICS','en'),(33,'Style','STYLE','en'),(34,'Typography','TYPOGRAPHY','en'),(35,'Wikipedia','WIKIPEDIA','en'),(36,'Anglicismes (calques, emprunts directs, etc.)','TYPOGRAPHY','fr'),(37,'Archaïsmes (tours vieillis, anciens et vieux)','CAT_ARCHAISMES','fr'),(38,'Confusion d’homonymes et paronymes','CAT_HOMONYMES_PARONYMES','fr'),(39,'Faute de frappe possible','TYPOS','fr'),(40,'Grammaire','CAT_GRAMMAIRE','fr'),(41,'Majuscules','CASING','fr'),(42,'Marques de commerce','CAT_MARQUES_DE_COMMERCE','fr'),(43,'Pléonasmes','CAT_PLEONASMES','fr'),(44,'Ponctuation','PUNCTUATION','fr'),(45,'Règles de base','MISC','fr'),(46,'Régionalismes','CAT_REGIONALISMES','fr'),(47,'Tours critiqués divers (barbarismes, impropriétés, solécismes, etc.)','CAT_TOURS_CRITIQUES','fr'),(48,'Typographie','TYPOGRAPHY','fr'),(49,'Élision','CAT_ELISION','fr'),(50,'Concordancia','CAT2','es'),(51,'Concordancia predicativa','CAT3','es'),(52,'Diversas','MISC','es'),(53,'Estilo','CAT4','es'),(54,'Gramática','GRAMMAR','es'),(55,'Mayúsculas y minúsculas','CASING','es'),(56,'Mecanografía','TYPOGRAPHY','es'),(57,'Ortografía (concepto)','CAT1','es'),(58,'Ortografía (tipográficos)','TYPOS','es'),(59,'Posible error tipográfico','TYPOS','es'),(60,'Puntuación','PUNCTUATION','es'),(61,'Reglas específicas para Wikipedia','WIKIPEDIA','es'),(62,'Cambios en las normas lingüísticas','CAMBIOS_NORMA','es'),(63,'Aandachtspunten in de betekenis en consistentie.','SEMANTICS','nl'),(64,'Aandachtspunten rond o.a. punten en aanhalingstekens.','TYPOGRAPHY','nl'),(65,'Aaneen of los','COMPOUNDING','nl'),(66,'Afkortingen','AFKORTINGEN','nl'),(67,'Beledigend','BELEDIGEND','nl'),(68,'Bungelend','BUNGELEND','nl'),(69,'Congruentie','CONGRUENTIE','nl'),(70,'Constructiefout','CONSTRUCTIEFOUT','nl'),(71,'Diverse regels','OVERIG','nl'),(72,'Diversen','MISC','nl'),(73,'Eigennamen beroemdheden etc.','EIGENNAMEN','nl'),(74,'Gemakkelijk te verwarren woorden','CONFUSED_WORDS','nl'),(75,'Geografie','GEOGRAFIE','nl'),(76,'Getallen','GETALLEN','nl'),(77,'Herhaling van woorden of woordgroepen.','REPETITIONS','nl'),(78,'Hoofdlettergebruik','CASING','nl'),(79,'Hoofdletters','CASING','nl'),(80,'Informeel','COLLOQUIALISMS','nl'),(81,'Interpunctie','PUNCTUATION','nl'),(82,'Leenwoorden met een gangbaar alternatief','LEENWOORDEN','nl'),(83,'Leesbaarheid','LEESBAARHEID','nl'),(84,'Mogelijke typefouten','TYPOS','nl'),(85,'Notaties','NOTATIES','nl'),(86,'Ouderwets','OUDERWETS','nl'),(87,'Spellingcontrole','TYPOS','nl'),(88,'Stijlkwesties','STYLE','nl'),(89,'Streektaal','STREEKTAAL','nl'),(90,'Typografie','TYPOGRAPHY','nl'),(91,'Uit: Aarsrivalen, scheldkarbonades en terminale baden','AARSRIVALEN','nl'),(92,'Uit: Hoe bereidt je een paard? : clichés','CLICHES','nl'),(93,'Vaste uitdrukkingen','VASTE_UITDRUKKINGEN','nl'),(94,'Vergissingen','VERGISSINGEN','nl'),(95,'Verschil België en Nederland','BE_NL','nl'),(96,'Voluit schrijven','VOLUIT_SCHRIJVEN','nl'),(97,'Vormfouten','VORMFOUTEN','nl'),(98,'Woordgroepen','WOORDGROEPEN','nl'),(99,'Woordverwarring','CONFUSED_WORDS','nl'),(100,'Briefe und E-Mails','CORRESPONDENCE','de'),(101,'Empfohlene Rechtschreibung laut Duden','EMPFOHLENE_RECHTSCHREIBUNG','de'),(102,'Geschlechtergerechte Sprache','GENDER_NEUTRALITY','de'),(103,'Getrennt- und Zusammenschreibung','COMPOUNDING','de'),(104,'Grammatik','GRAMMAR','de'),(105,'Groß-/Kleinschreibung','CASING','de'),(106,'Hilfestellung für Kommasetzung','HILFESTELLUNG_KOMMASETZUNG','de'),(107,'Leicht zu verwechselnde Wörter','CONFUSED_WORDS','de'),(108,'Mögliche Tippfehler','TYPOS','de'),(109,'Prominente/geographische Eigennamen','PROPER_NOUNS','de'),(110,'Redewendungen','IDIOMS','de'),(111,'Redundanz','REDUNDANCY','de'),(112,'Semantische Unstimmigkeiten','SEMANTICS','de'),(113,'Sonstiges','MISC','de'),(114,'Stil','STYLE','de'),(115,'Typographie','TYPOGRAPHY','de'),(116,'Umgangssprache','COLLOQUIALISMS','de'),(117,'Wikipedia','WIKIPEDIA','de'),(118,'Zeichensetzung','PUNCTUATION','de'),(119,'Zusammen-/Getrenntschreibung','COMPOUNDING','de'),(120,'Calinadas','EGGCORNS','pt'),(121,'Capitalização','CASING','pt'),(122,'Confusão de Palavras','CONFUSED_WORDS','pt'),(123,'Contrações','CONTRACTIONS','pt'),(124,'Desenvolvimento','DEVELOPMENT','pt'),(125,'Erros Ortográficos','MISSPELLING','pt'),(126,'Erros de Tradução','TRANSLATION_ERRORS','pt'),(127,'Estilo','STYLE','pt'),(128,'Frases-feitas e expressões idiomáticas','CLICHES','pt'),(129,'Gramática Geral','GRAMMAR','pt'),(130,'Linguagem Formal','FORMAL_SPEECH','pt'),(131,'Palavras Compostas','COMPOUNDING','pt'),(132,'Pontuação','PUNCTUATION','pt'),(133,'Redundância','REDUNDANCY','pt'),(134,'Regras de Marcas e Termos Registados','REGISTERED_BRANDS','pt'),(135,'Repetições','REPETITIONS','pt'),(136,'Sem Categoria Definida','MISC','pt'),(137,'Semântica','SEMANTICS','pt'),(138,'Sintaxe','SYNTAX','pt'),(139,'Tipografia','TYPOGRAPHY','pt'),(140,'Wikipédia','WIKIPEDIA','pt'),(141,'Грамматика','GRAMMAR','ru'),(142,'Дополнительные правила','EXTEND','ru'),(143,'Заглавные буквы','CASING','ru'),(144,'Логические ошибки','LOGIC','ru'),(145,'Общие правила','MISC','ru'),(146,'Проверка орфографии','TYPOS','ru'),(147,'Пунктуация','PUNCTUATION','ru'),(148,'Стиль','STYLE','ru'),(149,'Типографика','TYPOGRAPHY','ru'),(150,'Agreement','AGREEMENT','el'),(151,'Homonymy','HOMONYMY','el'),(152,'Orthography','ORTHOGRAPHY','el'),(153,'Redundant Phrases','REDUNDANCY','el'),(154,'Syntax','SYNTAX','el'),(155,'Διάφορα','MISC','el'),(156,'Πιθανό λάθος','TYPOS','el'),(157,'Στίξη','PUNCTUATION','el'),(158,'Τυπογραφικά','TYPOGRAPHY','el'),(159,'Υφολογικά λάθη','STYLE','el'),(160,'Χρήση κεφαλαίων','CASING','el'),(161,'Punctuation','PUNCTUATION','ja'),(162,'Typography','TYPOGRAPHY','ja'),(163,'さ入れ言葉','CAT8','ja'),(164,'ら入れ言葉','CAT9','ja'),(165,'ら抜き言葉','CAT6','ja'),(166,'レタス言葉','CAT7','ja'),(167,'文法','CAT1','ja'),(168,'誤変換','CAT3','ja'),(169,'誤字','CAT2','ja'),(170,'連語関係','CAT4','ja'),(171,'重複','CAT5','ja'),(172,'Compound problems','COMPOUNDING','sv'),(173,'Eventuellt ett stavfel','TYPOS','sv'),(174,'Grammatik','GRAMMAR','sv'),(175,'Kontamination','CAT7','sv'),(176,'Ord som hor ihop','CAT5','sv'),(177,'Ord som ofta förväxlas','CAT4','sv'),(178,'Punctuation','PUNCTUATION','sv'),(179,'Sammanskrivning rekomenderas','CAT2','sv'),(180,'Stor eller liten bokstav','CASING','sv'),(181,'Syntax','CAT6','sv'),(182,'Särskrivning rekomenderas','CAT1','sv'),(183,'Typography','TYPOGRAPHY','sv'),(184,'punkter efter förkortningar','CAT3','sv'),(185,'Övrigt','MISC','sv'),(186,'Acordul încrucișat','CAT11','ro'),(187,'Cacofonie','CAT10','ro'),(188,'Capitalizare','CASING','ro'),(189,'Diverse','MISC','ro'),(190,'Ghilimele','CAT2','ro'),(191,'Greșeli de exprimare','CAT3','ro'),(192,'Greșeli gramaticale - acord adjectiv','CAT5','ro'),(193,'Greșeli gramaticale - acord predicat','CAT6','ro'),(194,'Greșeli gramaticale - acord pronume','CAT8','ro'),(195,'Greșeli gramaticale - acord substantiv','CAT4','ro'),(196,'Greșeli gramaticale - acordul după caz','CAT7','ro'),(197,'Litere inversate','CAT1','ro'),(198,'Numeral','CAT13','ro'),(199,'Pleonasme','CAT9','ro'),(200,'Posibilă greșeală de tastare','TYPOS','ro'),(201,'Punctuation','PUNCTUATION','ro'),(202,'Punctuație','PUNCTUATION','ro'),(203,'Repetiții','CAT12','ro'),(204,'Style','STYLE','ro'),(205,'Typography','TYPOGRAPHY','ro'),(206,'Błędy fonetyczne','PHONETICS','pl'),(207,'Błędy frazeologiczne','CONFUSED_WORDS','pl'),(208,'Błędy interpunkcyjne','PUNCTUATION','pl'),(209,'Błędy leksykalne','STYLE','pl'),(210,'Błędy odmiany','GRAMMAR','pl'),(211,'Błędy ortograficzne','SPELLING','pl'),(212,'Błędy rodzaju gramatycznego','GENDER','pl'),(213,'Błędy różne','MISC','pl'),(214,'Błędy składniowe','SYNTAX','pl'),(215,'Błędy typograficzne','TYPOGRAPHY','pl'),(216,'Błędy w szyku wyrazów','WORD_ORDER','pl'),(217,'Formatowanie liczb','NUMBERS','pl'),(218,'Miscellaneous','MISC','pl'),(219,'Pisownia małą i wielką literą','CASING','pl'),(220,'Pleonazmy','REDUNDANCY','pl'),(221,'Prawdopodobna literówka','TYPOS','pl'),(222,'Prawdopodobne literówki','TYPOS','pl'),(223,'Wyrazy modne i nadużywane','SEMANTICS','pl'),(224,'Велико/мало почетно слово','CASING','sr'),(225,'Интерпункција','PUNCTUATION','sr'),(226,'Логичке грешке','SR_LOGIC','sr'),(227,'Могућа грешка','TYPOS','sr'),(228,'Могуће грешке приликом писања','SR_TYPOS','sr'),(229,'Разно','MISC','sr'),(230,'Стил писања','SR_CAT_STYLE','sr'),(231,'Типографија','TYPOGRAPHY','sr'),(232,'Diverse','MISC','da'),(233,'Forkortelser med eller uden punktum','CAT4','da'),(234,'Grammatik','GRAMMAR','da'),(235,'Grammatik, komma','GRAMMAR_COMMA','da'),(236,'Mulig slåfejl','TYPOS','da'),(237,'Mulige ordforveksling','CAT2','da'),(238,'Mulige ordforveksling med sjældent ord','CAT3','da'),(239,'Mulige slåfejl','CAT1','da'),(240,'STORE/små bogstaver','CASING','da'),(241,'Tegnsætning','PUNCTUATION','da'),(242,'Typografi','TYPOGRAPHY','da'),(243,'Možna tipkarska napaka','TYPOS','sl'),(244,'Napake pri kraticah','CAT2','sl'),(245,'Napake pri postavljanju ločil','CAT3','sl'),(246,'Napake uporabe predlogov','CAT4','sl'),(247,'Pogoste napake','CAT6','sl'),(248,'Polvikanje','CAT5','sl'),(249,'Postavitev ločil','PUNCTUATION','sl'),(250,'Razno','MISC','sl'),(251,'Slog - številke','CAT1','sl'),(252,'Tipografija','TYPOGRAPHY','sl'),(253,'Velike začetnice','CASING','sl'),(254,'அச்சுக்கலை','TYPOGRAPHY','ta'),(255,'இலக்கண அமைப்பில் சொற்கள்','CAT1','ta'),(256,'சந்தி','CAT3','ta'),(257,'தனிச் சொற்களை எழுதும் முறை','CAT2','ta'),(258,'நிறுத்தக்குறியீடு','PUNCTUATION','ta'),(259,'பாணி','STYLE','ta'),(260,'Adjective Plurality','ADJ_PLURAL','tl'),(261,'Affix Usage','AFFIX_USAGE','tl'),(262,'Alternation of D and R','D_AND_R','tl'),(263,'Code-switching','CODE_SWITCHING','tl'),(264,'Exchange Word Positions','WORD_POSITIONS','tl'),(265,'False Friend','FALSE_FRIENDS','tl'),(266,'Kapitalisasiyon','CASING','tl'),(267,'Ligature Usage','LIGATURE_USAGE','tl'),(268,'Loan Words','LOAN_WORDS','tl'),(269,'Missing Determiner','MISSING_DETERMINERS','tl'),(270,'Missing Last Word','MISSING_LAST_WORD','tl'),(271,'Missing Lexical Marker','MISSING_LEXICAL_MARKERS','tl'),(272,'Ng and Nang','NG_NANG','tl'),(273,'Posibleng Typo','TYPOS','tl'),(274,'Punctuation','PUNCTUATION','tl'),(275,'Typography','TYPOGRAPHY','tl'),(276,'Word Repetition','REPETITION','tl'),(277,'Wrong Determiner','DETERMINERS','tl');");

			/* Rules settings */
			
			$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}proofreading_rules_settings`;");
			$wpdb->query("CREATE TABLE `{$wpdb->prefix}proofreading_rules_settings` (
			  `lang_code` char(2) NOT NULL,
			  `included_rules` varchar(1024) DEFAULT NULL,
			  PRIMARY KEY (`lang_code`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		endif;
		
		// If not already inserted, insert default correction language as wordpress language.
		$language_default = get_option('proofreading-language-default');
		if ( strlen( $language_default ) == 0 ){
			
			$wp_lang = get_bloginfo("language");
			if ( $i = strrpos( $wp_lang, '-' ) ) $wp_lang = substr( $wp_lang, 0, $i );
			
			$sql = $wpdb->prepare("SELECT code
				FROM `{$wpdb->prefix}proofreading_languages`
				WHERE longCode = %s", $wp_lang);
			$res = $wpdb->get_col($sql);

			//$res = $wpdb->get_col("SELECT code FROM `{$wpdb->prefix}proofreading_languages` WHERE longCode = '$wp_lang';");
			if ( count($res) > 0 ) update_option( 'proofreading-language-default', $res[0]);
			
		}
		
		update_option( PROOFREADING_VERSION_SETTINGNAME , PROOFREADING_VERSION );
	}

}