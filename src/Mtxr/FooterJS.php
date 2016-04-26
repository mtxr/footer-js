<?php

namespace Mtxr;

class FooterJS
{
    private static $jsQueue = array(
        'inline' => array(),
        'files' => array()
    );

    private static $debug = false;
    private static $position = 0;

    public static function getQueue()
    {
        return self::$jsQueue;
    }

    public static function setDebug($status)
    {
        return self::$debug = $status;
    }

    public static function collect($position = null)
    {
        self::$position = $position ? : self::$position;
        self::$jsQueue['inline'][self::$position] = '';
        if (self::$debug) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            if (!empty($caller['file']) && !empty($caller['line'])) {
                $caller['line']++;
                self::$jsQueue['inline'][self::$position] = "<!-- {$caller['file']}:{$caller['line']} -->" . PHP_EOL;
            }
        }
        ob_start();
    }

    public static function endCollect($position = null)
    {
        self::$position = $position ? : self::$position;
        $content = ob_get_contents();
        ob_end_clean();
        self::$jsQueue['inline'][self::$position] .= $content;
        self::$position++;
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
