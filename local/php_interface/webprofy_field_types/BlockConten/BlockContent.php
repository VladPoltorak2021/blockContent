<?
// расширим свой класс от числового типа
class UserDataBlockConten extends CUserTypeInteger
{

    // инициализация пользовательского свойства для главного модуля
    static function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "block_content",
            "CLASS_NAME" => "UserDataBlockConten",
            "DESCRIPTION" => "Блочное описание",
            "BASE_TYPE" => "int",
        );
    }

    // инициализация пользовательского свойства для инфоблока
    static function GetIBlockPropertyDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "block_content",
            "DESCRIPTION" => "Блочное описание",
            'GetPropertyFieldHtml' => array('UserDataBlockConten', 'GetPropertyFieldHtml'),
            'GetAdminListViewHTML' => array('UserDataBlockConten', 'GetAdminListViewHTML'),
            'ConvertToDB' => array('UserDataBlockConten', 'ConvertToDB'),
            'ConvertFromDB' => array('UserDataBlockConten', 'ConvertFromDB'),
            //'GetPublicViewHTML' => array('UserDataBlockConten', 'GetPublicViewHTML'),
        );
    }

    // представление свойства
    public static function getViewHTML($name, $value)
    {
        return $value;
    }

    // редактирование свойства
    public static  function getEditHTML($name, $value, $is_ajax = false)
    {
        $uid = uniqid();
        $json_data = base64_encode(json_encode($value));

        return <<<SSS
	<input type="hidden" class="block_content_field" value="{$json_data}" id="field_{$uid}" data-uid="{$uid}" data-name="{$name}">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="/local/php_interface/webprofy_field_types/BlockConten/css/BlockContent.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script src="/local/php_interface/webprofy_field_types/BlockConten/js/jquery.base64.min.js"></script>
	<script src="/local/php_interface/webprofy_field_types/BlockConten/js/BlockContent.js"></script>
SSS;
    }

    // редактирование свойства в форме (главный модуль)
    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        return self::getEditHTML($arHtmlControl['NAME'], $arHtmlControl['VALUE'], false);
    }

    // редактирование свойства в списке (главный модуль)
    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        return self::getViewHTML($arHtmlControl['NAME'], $arHtmlControl['VALUE'], true);
    }

    // представление свойства в списке (главный модуль, инфоблок)
    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName = null)
    {
        return self::getViewHTML($strHTMLControlName['VALUE'], $value['VALUE']);
    }

    // редактирование свойства в форме и списке (инфоблок)
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        return $strHTMLControlName['MODE'] == 'FORM_FILL' ? self::getEditHTML($strHTMLControlName['VALUE'], $value['VALUE'], false) : self::getViewHTML($strHTMLControlName['VALUE'], $value['VALUE']);
    }

    static function ConvertToDB($arProperty, $value)
    {
        $return = false;

        if(!empty($_REQUEST["PROP_del"]))
        {
            foreach($_REQUEST["PROP_del"] as $prop)
            {
                self::search_r($prop, "VALUE", $arrDel);
                foreach($arrDel["VALUE"] as $key=>$arDel)
                {
                    if(is_array($arDel["content"]))
                    {
                        foreach($arDel["content"] as $key_multy=>$multy)
                        {
                            CFile::Delete(intval($value["VALUE"][$key]["content"][$key_multy]));
                            unset($value["VALUE"][$key]["content"][$key_multy]);
                        }
                        if(empty($value["VALUE"][$key]["content"]))
                            unset($value["VALUE"][$key]);
                    }
                    else
                    {
                        CFile::Delete(intval($value["VALUE"][$key]["content"]));
                        if($value["VALUE"][$key]["type"] != "video")
                            unset($value["VALUE"][$key]);
                        //unset($value["VALUE"][$key]["content"]);
                    }
                }
            }
        }

        if(
            is_array($value)
            && array_key_exists("VALUE", $value)
        ) {
            $new_data = array();
            foreach($value["VALUE"] as $key=>&$item)
            {

                switch($item["type"])
                {

                    case 'text':
                    default:
                        $item["content"] = base64_encode(mb_convert_encoding($item["content"], "UTF-8"));
                        //$item["content"] = preg_replace("/\"/", "'", $item["content"]);
                        $new_data[] = $item;
                        break;
                    case 'link':
                        $quoteItems = array();
                        foreach ($item["text2"] as $key => $value) {
                            $quoteItems[] = $value;
                        }
                        $header = array();
                        foreach ($item["header2"] as $key => $value) {
                            $header[] = $value;
                        }


                        if (!empty($quoteItems)) {
                            $new_data[] = array(
                                "content" => array("text2" => $quoteItems,"header2" => $header),
                                "type" => $item["type"]
                            );
                        }
                        break;
                    case 'image':
                        $description = array();
                        self::search_r($_REQUEST["PROP_descr"], $key, $description);
                        $description = $description[$key];

                        $content = array();
                        foreach($item as $key=>$n)
                        {
                            if(is_array($n))
                            {
                                if(preg_match("/^n\d$/", $key))
                                {
                                    $n["tmp_name"] = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp'.$n["tmp_name"];
                                    $arFile = $n;
                                    $arFile["MODULE_ID"] = 'iblock';
                                    $arFile["description"] = $description[$key];

                                    $fid = CFile::SaveFile($arFile, "blockcontent");
                                    if (intval($fid)>0)
                                    {
                                        $content[] = intval($fid);
                                    }
                                }

                                if($key == 'content') {
                                    foreach ($n as $key_one_file=>$one_file) {
                                        if (is_array($one_file) && isset($one_file["error"]) && isset($one_file["tmp_name"])) {

                                            $one_file["tmp_name"] = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp'.$one_file["tmp_name"];
                                            $arFile = $one_file;
                                            $arFile["MODULE_ID"] = 'iblock';
                                            $arFile["description"] = $description[$key];

                                            $fid = CFile::SaveFile($arFile, "blockcontent");
                                            if (intval($fid)>0)
                                            {
                                                $content[] = intval($fid);
                                                unset($item[$key][$key_one_file]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($item["content"]) && is_array($description["content"])) {
                            foreach ($item["content"] as $key => $img) {
                                if (isset($description["content"][$key])) {
                                    CFile::UpdateDesc(intval($img), $description["content"][$key]);
                                }
                            }
                            $content = array_merge($item["content"], $content);
                        }


                        $new_data[] = array(
                            "text" => $item["text"],
                            "type" => $item["type"],
                            "content" => $content
                        );

                        break;
                }
            }
            $return = serialize($new_data);
        }

        return $return;
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        $return = false;
        if (!is_array($value["VALUE"])) {
            $return = array(
                "VALUE" => unserialize($value["VALUE"]),
            );
            if ($return['VALUE'] === false && strlen($value['VALUE']) > 0)
            {
                $return = array(
                    "VALUE" => array(
                        'TEXT' => $value["VALUE"],
                        'TYPE' => 'TEXT'
                    )
                );
            }
            if($value["DESCRIPTION"])
                $return["DESCRIPTION"] = trim($value["DESCRIPTION"]);
        }

        if (is_array($return['VALUE'])) {
            foreach ($return['VALUE'] as $key => &$value) { // Обратите внимание на &
                if ($value['type'] == 'text') {
                    $test = base64_decode($value['content']);

                    $re = '/="(.*?)"/m';
                    $subst = '=\'$1\'';
                    $test = preg_replace($re, $subst, $test);

                    $test = str_replace("&nbsp;", " ", $test);
                    $value['content'] = htmlspecialcharsbx($test);
                } else if ($value['type'] == 'title') {
                    $test = base64_decode($value["content"]);
                    $test = str_replace('"', "'", $test);
                    $test = str_replace("&nbsp;", " ", $test);
                    $value["content"] = $test;
                } else if ($value['type'] == 'link') {
                    $test = base64_decode($value["content"]['text3']);
                    $test = str_replace('"', "'", $test);
                    $test = str_replace("&nbsp;", " ", $test);
                    $value["content"]['text3'] = $test;
                }
            }

        }

        return $return;
    }

    function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if(is_array($value["VALUE"]))
        {
            foreach($value["VALUE"] as &$item)
            {
                if($item["type"] == "text")
                    $item["content"] = base64_decode($item["content"]);
            }
        }
        return $value;
    }

    static function search_r($array, $key, &$results)
    {
        if (!is_array($array)) {
            return;
        }

        if (isset($array[$key]) && !empty($array[$key])) {
            $results = $array;
        }

        foreach ($array as $subarray) {
            self::search_r($subarray, $key, $results);
        }
    }


}

AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("UserDataBlockConten", "GetIBlockPropertyDescription"));
AddEventHandler("main", "OnUserTypeBuildList", array("UserDataBlockConten", "GetUserTypeDescription"));
