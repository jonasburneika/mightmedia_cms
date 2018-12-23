<?php
//medzio darymo f-ja
function build_tree( $data, $id = 0, $active_class = 'active' ) {

	global $lang;
	if ( !empty( $data ) ) {
		$re = "";
		foreach ( $data[$id] as $row ) {
			if ( isset( $data[$row['id']] ) ) {
				$re .= "<li><a href=\"" . url( '?id,' . $row['id'] ) . "\" >" . $row['pavadinimas'] . "</a><span style=\"display: inline; width: 100px;margin:0; padding:0; height: 16px;\"><a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';d,' . $row['id'] ) . "\"  onClick=\"return confirm(\'" . $lang['admin']['delete'] . "?\')\"><img src=\"" . ROOT . "core/assets/images/icons/cross.png\" title=\"" . $lang['admin']['delete'] . "\"  /></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';r,' . $row['id'] ) . "\"><img src=\"" . ROOT . "core/assets/images/icons/wrench.png\" title=\"" . $lang['admin']['edit'] . "\"/></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';e,' . $row['id'] ) . "\"><img src=\"" . ROOT . "core/assets/images/icons/pencil.png\" title=\"" . $lang['admin']['page_text'] . "\" /></a></span><ul>";
				$re .= build_tree( $data, $row['id'], $active_class );
				$re .= "</ul></li>";
			} else {
				$re .= "<li><a href=\"" . url( '?id,' . $row['id'] ) . "\" >" . $row['pavadinimas'] . "</a><span style=\"display: inline; width: 100px; margin:0; padding:0; height: 16px;\">
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';d,' . $row['id'] ) . "\" onClick=\"return confirm(\'" . $lang['admin']['delete'] . "?\')\"><img src=\"" . ROOT . "core/assets/images/icons/cross.png\" title=\"" . $lang['admin']['delete'] . "\"/></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';r,' . $row['id'] ) . "\"><img src=\"" . ROOT . "core/assets/images/icons/wrench.png\" title=\"" . $lang['admin']['edit'] . "\" /></a>
<a href=\"" . url( '?id,999;a,' . getAdminPagesbyId('meniu') . ';e,' . $row['id'] ) . "\" ><img src=\"" . ROOT . "core/assets/images/icons/pencil.png\" title=\"" . $lang['admin']['page_text'] . "\" /></a></span>
</li>";
			}
		}
		return $re;
	}
}

function editor( $tipas = 'jquery', $dydis = 'standartinis', $id = FALSE, $value = '' ) {

	global $conf, $lang;

	if (! $id) {
		$id = md5(uniqid());
	}

	if ( is_array( $id ) ) {
		foreach ( $id as $key => $val ) {
			$arr[$val] = "'$key'";
		}
		$areos = implode( $arr, "," );
	} else {
		$areos = "'$id'";
	}

	$root = ROOT;
	$return = '';

	if ( $conf['Editor'] == 'textarea' ) {
		
		if ( is_array( $id ) ) {
			foreach ( $id as $key => $val ) {
				$return .= <<<HTML

	<textarea id="{$key}" name="{$key}" rows="1" class="form-control no-resize auto-growth">{$value[$key]}</textarea>
HTML;
			}
		} else {
			$return .= <<<HTML

<textarea id="{$id}" name="{$id}" rows="1" class="form-control no-resize auto-growth">{$value}</textarea>
HTML;

		}
	} elseif ($conf['Editor'] == 'tinymce') {
		$dir    = adresas();
		$return .= <<<HTML
      <!-- Load TinyMCE -->
<script src="{$dir}htmlarea/tinymce/tinymce.js" type="text/javascript"></script>
<script type="text/javascript">
	//TinyMCE
    tinymce.init({
        selector: "textarea.tinymce",
        theme: "modern",
        height: 300,
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
		image_advtab: true,
		// images_upload_url: 'postAcceptor.php', - images local upload
    });

</script>
<!-- /TinyMCE -->
HTML;
		if ( is_array( $id ) ) {
			foreach ( $id as $key => $val ) {

				$return .= <<< HTML
<textarea id="{$key}" name="{$key}" class="tinymce">{$value[$key]}</textarea>
HTML;
			}
		} else {
			$return .= <<< HTML
<textarea id="{$id}" name="{$id}" class="tinymce">{$value}</textarea>
HTML;

		}
		
	} elseif ( $conf['Editor'] == 'ckeditor' ) {
		$dir = adresas();

		$return .= <<<HTML
	<script type="text/javascript" src="{$dir}htmlarea/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		//CKEditor
		CKEDITOR.replaceClass = 'ckeditor';
		CKEDITOR.config.height = 300;
		CKEDITOR.config.extraPlugins = 'uploadimage';
	</script>
HTML;

		if ( is_array( $id ) ) {
			foreach ( $id as $key => $val ) {

				$return .= <<< HTML
<textarea id="{$key}" name="{$key}" class="ckeditor">{$value[$key]}</textarea>
HTML;
			}
		} else {
			$return .= <<< HTML
<textarea id="{$id}" name="{$id}" class="ckeditor">{$value}</textarea>
HTML;

		}
	}
	
	return $return;
}

function defaultHead() 
{
	global $conf;

	?>
	<base href="<?php echo adresas(); ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo input(strip_tags($conf['Pavadinimas']) . ' - Admin'); ?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="index,follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="/core/assets/images/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/core/assets/images/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/core/assets/images/favicon/favicon-16x16.png">
	<link rel="manifest" href="/core/assets/images/favicon/site.webmanifest">
	<link rel="mask-icon" href="/core/assets/images/favicon/safari-pinned-tab.svg" color="#db7300">
	<meta name="msapplication-TileColor" content="#ff440e">
	<meta name="theme-color" content="#ffffff">
	<?php
		if  (getSettingsValue('translation_status') == 1){
			if (isset($_SESSION['Translation'])){ echo $_SESSION['Translation'];}
			?>
			<style>
			 .notifyTranslation{
				border: 2px dotted red;
			 }
			</style>
			<script>
				function addListener(obj, eventName, listener) { //function to add event
					if (obj.addEventListener) {
						obj.addEventListener(eventName, listener, false);
					} else {
						obj.attachEvent("on" + eventName, listener);
					}
				}
				addListener(document, "DOMContentLoaded", finishedDCL); //add event DOMContentLoaded
				function finishedDCL() {
					var theParent = document.body;
					var theKid = document.createElement("div");
					theKid.id = 'translationDiv';
					var style = document.createElement('style');
					style.type = 'text/css';
					style.innerHTML = '.translationDivCss {height: 20px;z-index: 10; background: green;color: white;text-align: center;font-size: 20px;padding: 10px; }';
					document.getElementsByTagName('head')[0].appendChild(style);
					theKid.innerHTML = 'Translation is ON';
					theKid.className = 'translationDivCss';
					// append theKid to the end of theParent
					theParent.appendChild(theKid);
					// prepend theKid to the beginning of theParent
					theParent.insertBefore(theKid, theParent.firstChild);
				}
				function editLanguageText(frase) {
					var group = frase.getAttribute("data-group");
					var key = frase.getAttribute("data-key");
					var element = document.getElementById(group + '_' + key);
					var person = prompt('OLD text: # ' + element.innerHTML + ' # Enter new text below: ', element.innerHTML);
					updateTranslationInDB(group, key, person,function(event){event.preventDefault()});
				}

				function addTranslateClass(frase){
					frase.classList.add("notifyTranslation");
				}

				function removeTranslateClass(frase){
					frase.classList.remove("notifyTranslation");
				}

				function updateTranslationInDB(group, key, newValue) {
					console.log(group+' '+key+' '+newValue);
					var element = document.getElementById(group + '_' + key);
					var xhttp = new XMLHttpRequest();
					var url = "../extensions/translation/updateTranslation.php?group," + group + ";key," + key +";newValue," + newValue;
					//Send the proper header information along with the request
					xhttp.open('GET', url, true);
					xhttp.send();
					
				}
			</script>
	<?php }
}

function adminPages() 
{
	global $url, $lang, $conf, $buttons, $timeout, $prisijungimas_prie_mysql;

	if($versionData = checkVersion()) {
		notifyUpdate($versionData);
	}	

	$fileName = (isset($url['a']) && ! empty(getAllAdminPages($url['a'])) ? getAllAdminPages($url['a']) : null);

	if (! empty($fileName) && file_exists(ROOT . $fileName) && isset($_SESSION[SLAPTAS]['username']) && $_SESSION[SLAPTAS]['level'] == 1 && defined( "OK" ) ) {
		if (count($_POST) > 0 && $conf['keshas'] == 1) {
			notifyMsg(
				[
					'type'		=> 'warning',
					'message' 	=> $lang['system']['cache_info']
				]
			);
		}
		
		include_once ROOT . $fileName;

	} elseif (isset($url['m'])) {

		switch ($url['m']) {
			case 1:
				$page = 'uncache.php';
				break;
			case 2:
				$page = 'pokalbiai.php';
				break;
			case 3:
				$page = 'antivirus.php';
				break;
			case 4:
				$page = 'search.php';
				break;
			case 'upgrade':
				$page = 'upgrade.php';
				break;
		}

		include_once 'pages/' . $page;
	} else {
		include_once 'pages/dashboard.php';
	}
}

function getAdminExtensionsMenu($page = null) 
{
	global $adminExtensionsMenu;

	$menu = applyFilters('adminExtensionsMenu', $adminExtensionsMenu);

	return ! empty($page) ? $menu[$page] : $menu;
}

function getAllAdminPages($page = null)
{
	$adminMenu 				= getAdminPages();
	$adminExtensionsMenu 	= getAdminExtensionsMenu();

	$allPages = array_merge($adminMenu, $adminExtensionsMenu);

	if(! empty($page)) {
		return ! empty($allPages[$page]) ? $allPages[$page] : null;
	}
	
	return $allPages;
}

function getAdminPages($page = null) 
{
	global $adminMenu;

	$menu = $adminMenu; //todo: add hooks

	return ! empty($page) ? $menu[$page] : $menu;
}
//todo: optimise it
function getAdminPagesbyId($id = null, $key = null) 
{

	$menu = getAllAdminPages();
	$idArray = [];

	foreach ($menu as $name => $file) {
		$newKey = basename($file, '.php');

		$idArray[$newKey] = [
			'file'	=> $file,
			'name'	=> $name,
		];
	}

	$key = ! empty($key) ? $key : 'name';

	return ! empty($id) ? $idArray[$id][$key] : $menu;
}

function getFeedArray($feedUrl) 
{
     
    $content = file_get_contents($feedUrl);
	$x = simplexml_load_string($content, null, LIBXML_NOCDATA);
	
    return $x->channel;
}

//atvaizduojam blokus
function dragItem($id, $content, $subMenu = null)
{
	return '<li class="dd-item dd3-item" data-id="' . $id . '">
	<div class="dd-handle dd3-handle"></div>
	<div class="dd3-content">
		' . $content . '
	</div>
	' . (! empty($subMenu) ? $subMenu : '') . '
	</li>';
}

//filtering
function tableFilter($formData, $data, $formId = '')
{
	global $lang;

	$newFormData['#'] = '<input type="checkbox" id="visi" name="visi" onclick="checkedAll(\'' . $formId . '\');" class="filled-in"><label for="visi"></label>';

	foreach($formData as $key => $value) {
		$input = '<div class="form-group">';
		$input .= '<div class="form-line">';
		$input .= '<input type="text" name="' . $key . '" value="' . (isset($data[$key]) ? $data[$key] : '') . '" class="form-control">';
		$input .= '</div>';
		$input .= '</div>';

		$newFormData[$value] = $input;
	}

	$newFormData[$lang['admin']['action']] = '<button type="submit" class="btn btn-primary waves-effect">' . $lang['admin']['filtering'] . '</button>';

	return $newFormData;
}

function deleteRedirectSession()
{
	unset($_SESSION[SLAPTAS]['redirect']);

}

function buttons($id = null)
{
	global $buttons;

	$buttons = applyFilters('adminButtons', $buttons);

	if(! empty($id)) {
		return isset($buttons[$id]) && ! empty($buttons[$id]) ? $buttons[$id] : null;
	} 
	
	return $buttons;
}

function icons($group, $icon)
{
	global $icons;

	$icons = applyFilters('adminMenuIcons', $icons);
	
	return ! empty($icons[$group][$icon]) ? $icons[$group][$icon] : null;
}

function iconsMenu($icon)
{
	global $icons;

	$iconsMenu = $icons['menu'];
	$iconsMenu = applyFilters('adminMenuIcons', $iconsMenu);
	
	return ! empty($iconsMenu[$icon]) ? $iconsMenu[$icon] : null;
}


if(! function_exists('getSettingsValue')) {
	function getSettingsValue($key, $options = null)
	{
		global $conf;
		if (isset($conf[$key])){
			return $conf[$key];
		}
		
		$request = "SELECT `val` FROM `" . LENTELES_PRIESAGA . "nustatymai` WHERE `key` = " . escape($key);
		//Adding additional info to the querry i.e. LIKE, LIMIT, ORDER BY and etc.
		if (is_array($options)){
			$mysqliOptions = ['LIKE', 'LIMIT', 'ORDER BY', 'OFFSET'];
			foreach ($options as $optionKey => $optionValue) {
				if (in_array($optionKey,$mysqliOptions)){
					$sqlStatement =  str_replace("'", '', escape($optionKey)). " " . escape($optionValue);
					$updateRequest .= " " . $sqlStatement;
				}
			}
		}
		$result =  mysql_query1($request);
		if (count($result) > 0) {
			return $result[0]['val'];
		} else {
			return null;
		}
	}
}
if(! function_exists('setSettingsValue')) {
	function setSettingsValue($val, $key, $options = null)
	{
		$request = "SELECT * FROM `" . LENTELES_PRIESAGA . "nustatymai` WHERE `key` = " . escape($key);
		if (sizeof(mysql_query1($request)) > 0) {
			
			//DataSet for given key is found. We can update the value
			$updateRequest = "UPDATE `" . LENTELES_PRIESAGA . "nustatymai` SET `val`= " . escape($val) . " WHERE `key` = " . escape($key);
			//Adding additional info to the querry i.e. LIKE, LIMIT, ORDER BY and etc.
			if (is_array($options)){
				$mysqliOptions = ['LIKE', 'LIMIT', 'ORDER BY', 'OFFSET'];
				foreach ($options as $optionKey => $optionValue) {
					if (in_array($optionKey,$mysqliOptions)){
						$sqlStatement =  str_replace("'", '', escape($optionKey)). " " . escape($optionValue);
						$updateRequest .= " " . $sqlStatement;
					}
				}
			}
			if ($result = mysql_query1($updateRequest)){
				return $result;
			}
		} else {
			//DataSet for given key is NOT found. Inserting new key with a given value
			$insertRequest = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`key`,`val`) VALUES (" . escape($key) . "," . escape($val) . ")";
			if ($result = mysql_query1($insertRequest)){
				return $result;
			}
		}
		
		return $result;
	}
}

if(! function_exists('getLangText')) {
	function getLangText($group, $key, $new = false, $value = ''){
		global $lang;
		$sqlCheckTranslation = "SELECT `value` FROM `" . LENTELES_PRIESAGA . "translations` WHERE `group`= " . escape($group) . " and `key`= " . escape($key) . " ORDER BY `last_update` DESC LIMIT 1";
		if ($textFromDb = mysql_query1($sqlCheckTranslation)){
			$langTextFromDataBase =  $textFromDb['value'];
		}

		if (array_key_exists($group, $lang) && (array_key_exists($key, $lang[$group]))){
			$langText = $lang[$group][$key];
		} else {
			$language = lang();
			langTextError($group, $key, $language);
			$langText = null;
		}

		if (lang() == 'lt'){ $needTranslation = '--- nenurodyta ---'; } else if (lang() == 'en') { $needTranslation = '--- undefined ---';}
		if  (getSettingsValue('translation_status') == 1){
			$result = '<p id ="' . $group . '_' . $key . '"  class= "col-10" onclick="editLanguageText(this,function(event){event.preventDefault()})" ';
			$result .= 'onmouseover="addTranslateClass(this)" onmouseout="removeTranslateClass(this)" style="width: 100%;"';
			$result .= ' data-group="' . $group . '" data-key="' . $key . '">';
			if (isset($langTextFromDataBase)){ 
				$result .= $langTextFromDataBase . '</p>'; 
			} else { 
				!is_null($langText) ? $result .= $langText . '</p>' :  $result .= $needTranslation . '</p>';
			} 
			return $result;

		} else if (isset($langTextFromDataBase)) {
			return $langTextFromDataBase;
		} else {
			return $langText;	
		}
			
	}
}

if (! function_exists('langTextError')){
	function langTextError($group, $key, $language) {
		/**
		 *  Aprasyti funkcija, kai nera kalbinio teksto
		 *  padaryti LOG failą/DB kurį rodys prie vertimo nustatymų.
		 *  Jeigu bus daugiau negu x eilučių pridėti puslapiavimą.
		 */
	}
}
