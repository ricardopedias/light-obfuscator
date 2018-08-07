<?php
namespace PhpObfuscator\Tests\Unit;

use Tests\TestCase;

class BaseTest extends TestCase
{
    public static function getTestFilesPath($path = '')
    {
        return rtrim(implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'Files', $path]), "/");
    }

    public static function getTempFile($prefix = 'obfuscating_')
    {
        return tempnam(sys_get_temp_dir(), $prefix) . ".php";
    }

    public static function getTestFile($filename)
    {
        $stub_file = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'Files', $filename]);
        $stub_contents = file_get_contents($stub_file);

        $temp_file = tempnam(sys_get_temp_dir(), 'obfuscating_class_') . ".php";
        file_put_contents($temp_file, $stub_contents);

        return $temp_file;
    }

    public static function getTestFileContents($filename)
    {
        return file_get_contents(self::getTestFile($filename));
    }
}
