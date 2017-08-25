<?php

namespace App\Utils\Pinyin;

class Pinyin
{
    protected $chineseToPinyinArray = [];

    public function __construct()
    {
        $this->chineseToPinyinArray = include('PinYinCharacterArray.php');
    }

    public function convert($string)
    {
        if(strlen($string) === mb_strlen($string, "UTF-8")) {
            return $string;
        }

        $prepared = $this->prepareInput($string);

        $converted = $this->formatOutput(
            strtr($prepared, $this->chineseToPinyinArray)
        );

        return ($converted === $this->formatOutput($prepared)) ? $string : $converted;
    }

    private function prepareInput($string)
    {
        $ReplaceCharsWith = [
            '【' => "[",
            '】' => "]",
            '（' => "(",
            '）' => ")",
            '「' => "“",
            '」' => "”",
        ];

        $string = strtr($string, $ReplaceCharsWith);

        // add a space before each english word
        $string = preg_replace_callback('/[a-z0-9_-]+/i', function ($matches) {
            return " {$matches[0]}";
        }, $string);

        // add a space before parentheses, brackets, opening quotes and ampersands
        $string = preg_replace_callback('/\(|\[|“|&/', function ($matches) {
            return " {$matches[0]}";
        }, $string);

        return $string;
    }

    private function formatOutput($string)
    {
        $string = str_replace("( ", "(", $string);
        $string = str_replace("[ ", "[", $string);
        $string = str_replace("“ ", "“", $string);

        $ReplaceCharsWith = [
            "，" => ",",
            "。" => ".",
            "！" => "!",
            "？" => "?",
            "：" => ":",
            "“" => "\"",
            "”" => "\"",
            "‘" => "'",
            "’" => "'",
        ];

        $string = strtr($string, $ReplaceCharsWith);

        // if lines start with a quote, #, or *, remove spaces following it
        $string = preg_replace("/^(#|\*|\'|\") /", "$1", $string);

        // fix spaces in urls (www. zimuku. tv)
        $string = preg_replace_callback('/(www\. [a-z]+\. [a-z]{2,3})/i', function($matches) {
            return str_replace(" ", "", $matches[0]);
        }, $string);

        // fix spaces in url start (http:// www.)
        $string = preg_replace_callback('/(http[s]{0,1}:\/\/ www)/', function($matches) {
            return str_replace(" ", "", $matches[0]);
        }, $string);

        //  replace more than 5 spaces in a row with a placeholder
        $string = preg_replace('/ {5,}/', '%ST-TEMP-SPACES%', $string);

        // replace multiple spaces  with a single space
        $string = preg_replace('/  +/', ' ', $string);

        $string = str_replace("%ST-TEMP-SPACES%", "    ", $string);

        return ltrim($string);
    }
}
