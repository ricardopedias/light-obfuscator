<?php
namespace PhpObfuscator\Tests\Unit;

use PhpObfuscator\ObfuscateDiretory;
use PhpObfuscator\Tests\Libs\ObfuscateDirectoryAccessor;

class BaseCommandTest extends BaseTest
{
    public function testAddErrorMessage()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testSetPlainPath()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testGetPlainPath()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testSetObfuscatedPath()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testGetObfuscatedPath()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testGetUnpackFile()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testObfuscateDirectory()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testMakeDir()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testIsPhpFile()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testSetupAutoloader()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testMakeIndex()
    {
        $this->markTestIncomplete('Não implementado');
    }

    public function testGenerateAutoloader()
    {
        $this->markTestIncomplete('Não implementado');
    }


    //
    // Files Path
    //

    /*
    public function testGetFilesPath_Exception()
    {
        $this->expectException(\InvalidArgumentException::class);

        $ob = new BaseObfuscateAccessor;
        $this->assertNull($ob->method('getFilesPath'));

        // Diretório 'www' não contem um arquivo composer.json correspondente
        $ob->method('setFilesPath', '/var/www');
    }

    public function testGetFilesPath_Setted()
    {
        $ob = new BaseObfuscateAccessor;
        $this->assertNull($ob->method('getFilesPath'));

        $app_test = self::getTestFilesPath('app');
        $ob->method('setFilesPath', $app_test);
        $this->assertNotNull($ob->method('getFilesPath'));
        $this->assertEquals($app_test, $ob->method('getFilesPath'));
    }

    public function testGetFilesPath_Fixed()
    {
        $ob = new BaseObfuscateAccessor;
        $this->assertNull($ob->method('getFilesPath'));

        $app_test = self::getTestFilesPath('app') . "/"; // barra adicional no final
        $app_test_no_bar = self::getTestFilesPath('app');
        $ob->method('setFilesPath', $app_test);
        $this->assertNotNull($ob->method('getFilesPath'));
        $this->assertNotEquals($app_test, $ob->method('getFilesPath'));
        $this->assertEquals($app_test_no_bar, $ob->method('getFilesPath'));
    }

    public function testObfuscatedPath()
    {
        $ob = new BaseObfuscateAccessor;
        $this->assertNull($ob->method('getFilesPath'));
        $app_test = self::getTestFilesPath('app');
        $ob->method('setFilesPath', $app_test);

        // Nomes padrões
        $ob_dir = $ob->property('obfuscated_dir');
        $ob_path = dirname($ob->method('getFilesPath')) . DIRECTORY_SEPARATOR . $ob_dir;
        $this->assertNotNull($ob->method('getObfuscatedPath'));
        $this->assertEquals($ob_path, $ob->method('getObfuscatedPath'));

        // Nomes personalizados
        $ob->property('obfuscated_dir', 'custom_ob');

        $ob_dir = $ob->property('obfuscated_dir');
        $ob_path = dirname($ob->method('getFilesPath')) . DIRECTORY_SEPARATOR . $ob_dir;
        $this->assertNotNull($ob->method('getObfuscatedPath'));
        $this->assertEquals($ob_path, $ob->method('getObfuscatedPath'));
    }

    public function testUnpackFile()
    {
        $ob = new BaseObfuscateAccessor;
        $this->assertNull($ob->method('getFilesPath'));
        $app_test = self::getTestFilesPath('app');
        $ob->method('setFilesPath', $app_test);

        // Nomes padrões
        $file_name = $ob->property('unpack_file');
        $file = $ob->method('getFilesPath') . DIRECTORY_SEPARATOR . $file_name;
        $this->assertNotNull($ob->method('getUnpackFile'));
        $this->assertEquals($file, $ob->method('getUnpackFile'));

        // Nomes personalizados
        $ob->property('unpack_file', 'Unp.php');

        $file_name = $ob->property('unpack_file');
        $file = $ob->method('getFilesPath') . DIRECTORY_SEPARATOR . $file_name;
        $this->assertNotNull($ob->method('getUnpackFile'));
        $this->assertEquals($file, $ob->method('getUnpackFile'));
    }


    */
}
