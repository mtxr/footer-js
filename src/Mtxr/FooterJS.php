<?php

namespace Mtxr;

class FooterJS
{
    private static $jsQueue = array(
        'inline' => array(),
        'files' => array()
    );

    public static function getQueue()
    {
        return self::$jsQueue;
    }

    public static function collect($position = null)
    {
        self::$jsQueue['inline'][$position] = '';
        ob_start();
    }

    public static function endCollect($position = null)
    {
        $content = ob_get_contents();
        ob_end_clean();
        if ($position) {
            self::$jsQueue['inline'][$position] = $content;
            return;
        }
        self::$jsQueue['inline'][] = $content;
    }

    public static function addFile($path, array $attributtes = array())
    {
        $defaults = array('type' => 'text/javascript', 'src' => $path);
        $attributtes = array_merge_recursive($attributtes, $defaults);
        self::$jsQueue['files'][$path] = $attributtes;
    }

    public static function output()
    {
        $html = '';
        foreach (self::$jsQueue['files'] as $js) {
            $attrStr = '';
            foreach ($js as $key => $value) {
                $attrStr .= " {$key}='{$value}' ";
            }
            $html .= "<script $attrStr></script>" . PHP_EOL;
        }

        foreach (self::$jsQueue['inline'] as $js) {
            $html .= $js . PHP_EOL;
        }
        echo $html;
    }
}
