<?php

function path(string $env, string $data, $mode = true)
{
    $php = $mode ? ".php" : ".txt";

    $templateVendor = __DIR__ . DS . ".." . DS . "%s" . DS . $data . "%s";

    $templateApi = __DIR__ . DS . "%s" . DS . $data . "%s";
    if (isset($env)) {
        switch ($env) {
            case "vendor":
                return sprintf($templateVendor, $env, (string) $php);
                break;
            case "entrance":
                return sprintf($templateApi, ucfirst($env), (string) $php);
                break;
            default:
                break;
        }
    }
    return;
}

function Eloquent(string $string)
{
    $converter = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v',
        'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

        'А' => 'A', 'Б' => 'B', 'В' => 'V',
        'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
        'И' => 'I', 'Й' => 'Y', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U',
        'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
        'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    );
    return strtr($string, $converter);
}

function Transliter(string $str)
{

    $str = Eloquent($str);

    $str = strtolower($str);

    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);

    $str = trim($str, "-");

    return $str;
}

function CorrectorImg($str)
{
    $str = (string) $str;
    if (is_string($str)) {
        $str = str_replace("/logos_full/", "", $str);

        $filter = substr($str, 0, stripos($str, "?"));
        return $filter;
    }
    return;
}

function CorrectorStreet($array)
{
    $str = "";

    $todos = [];

    $todosInit = $array;

    $todos[] = isset($todosInit["city"]) ? $todosInit["city"] : "";
    $todos[] = isset($todosInit["street"]) ? $todosInit["street"] : "";
    $todos[] = isset($todosInit["building"]) ? $todosInit["building"] : "";

    if (is_array($todos) && count($todos) > 0) {
        foreach ($todos as $todo => $value) {
            $str .= $value;
            if (next($todos)) {
                $str .= ", ";
            }
        }
    }

    return $str;
}

function NameCorrector($str)
{
    $mask = ["ИП ", "\r"];
    $str = str_replace($mask, "", $str);
    return $str;
}

function getImgResolution($arr)
{
    if (is_array($arr) && count($arr) > 0) {
        if (!empty($arr["1000"])) {
            return $arr["1000"];
        } elseif (!empty($arr["650"])) {
            return $arr["650"];
        } elseif (!empty($arr["200"])) {
            return $arr["200"];
        } else {
            return " ";
        }
    } else {
        return $arr;
    }
}
function clearImg($str)
{
    $str = getImgResolution($str);

    $str = str_replace("/media/cms/relation_product/", "", $str);

    $str = str_replace("/", "!", $str);

    $pattern = "/(\d+)!(\w+)/i";

    $replacement = '$2';

    return preg_replace($pattern, $replacement, $str);
}

function MassaVolume($value)
{
    if (is_array($value) || isset($value)) {
        if (!empty($value["properties"]["weight"])) {
            return $value["properties"]["weight"];
        } elseif (!empty($value["properties"]["volume"])) {
            return $value["properties"]["volume"];
        } else {
            return " ";
        }
    }
}
