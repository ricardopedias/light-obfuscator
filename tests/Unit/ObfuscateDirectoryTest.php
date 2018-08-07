<?php
namespace PhpObfuscator\Tests\Unit;

use Tests\TestCase;
use PhpObfuscator\Tests\Libs\ObfuscateDirectoryAccessor;
use PhpObfuscator\Tests\Libs\BaseTools;
use PhpObfuscator\ObfuscateDiretory;

class ObfuscateDiretoryTest extends TestCase
{
    use BaseTools;

    public function testAddErrorMessage()
    {
        $ob = new ObfuscateDirectoryAccessor;
        $ob->addErrorMessage('aaa');
        $ob->addErrorMessage('bbb');
        $ob->addErrorMessage('ccc');

        $this->assertCount(3, $ob->getErrorMessages());
        $this->assertEquals($ob->getErrorMessages()[0], 'aaa');
        $this->assertEquals($ob->getErrorMessages()[1], 'bbb');
        $this->assertEquals($ob->getErrorMessages()[2], 'ccc');
    }

    public function testLastErrorMessage()
    {
        $ob = new ObfuscateDirectoryAccessor;
        $ob->addErrorMessage('aaa');
        $ob->addErrorMessage('bbb');
        $ob->addErrorMessage('ccc');

        $this->assertCount(3, $ob->getErrorMessages());

        $this->assertEquals($ob->getLastErrorMessage(), 'ccc');
        $ob->addErrorMessage('ddd');
        $this->assertEquals($ob->getLastErrorMessage(), 'ddd');
    }

    public function testSetPlainPath()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $app_dir = self::getTestFilesPath('app');
        $not_dir = '/diretorio/inexistente/';
        $not_read_dir = self::getTempPath('app_not_read');

        // Diretório setado com sucesso
        $this->assertTrue($ob->setPlainPath($app_dir));
        $this->assertEquals($app_dir, $ob->getPlainPath());

        // Diretório setado com sucesso
        // Barras no final são removidas
        $this->assertTrue($ob->setPlainPath($app_dir . "/"));
        $this->assertEquals($app_dir, $ob->getPlainPath());

        // Diretório não setado: inexistente
        $this->assertFalse($ob->setPlainPath($not_dir));
        $this->assertEquals('The specified directory does not exist', $ob->getLastErrorMessage());


        @mkdir($not_read_dir); // cria o diretório temporário
        chmod($not_read_dir, 0000); // remove permissões de leitura

        // Diretório não setado: não é legível
        $this->assertFalse($ob->setPlainPath($not_read_dir));
        $this->assertEquals('No permissions to read directory', $ob->getLastErrorMessage());

        // adiciona permissões de leitura para o garbage collector excluir
        chmod($not_read_dir, 0755);
    }

    public function testSetObfuscatedPath()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $obf_dir = self::getTempPath('app_obfuscated');
        $not_dir = '/diretorio/inexistente/';
        $not_write_dir = self::getTempPath('app_not_write');

        // Diretório setado com sucesso
        $this->assertTrue($ob->setObfuscatedPath($obf_dir));
        $this->assertEquals($obf_dir, $ob->getObfuscatedPath());

        // Diretório setado com sucesso
        // Barras no final são removidas
        $this->assertTrue($ob->setObfuscatedPath($obf_dir . "/"));
        $this->assertEquals($obf_dir, $ob->getObfuscatedPath());

        // Diretório não setado: inexistente
        $this->assertFalse($ob->setObfuscatedPath($not_dir));
        $this->assertEquals('The specified directory does not exist', $ob->getLastErrorMessage());

        @mkdir($not_write_dir); // cria o diretório temporário
        chmod($not_write_dir, 0000); // remove permissões de leitura

        // Diretório não setado: não é legível
        $this->assertFalse($ob->setObfuscatedPath($not_write_dir));
        $this->assertEquals('No permissions to write to the directory', $ob->getLastErrorMessage());

        // adiciona permissões de leitura para o garbage collector excluir
        chmod($not_write_dir, 0755);
    }

    public function testGetObfuscatedPath()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::getTempPath('app_obfuscated');

        // Nada foi setado ainda
        $this->assertNull($ob->getObfuscatedPath());

        // Seta o diretório com os arquivos a serem ofuscados
        $this->assertTrue($ob->setPlainPath($app_dir));
        $this->assertEquals($app_dir, $ob->getPlainPath());

        // Quando um diretóirio de destivo não foi setado ainda,
        // ele é gerado com base no diretório dos arquivos
        // adicionando o sufixo '_obfuscated'
        $this->assertNotNull($ob->getObfuscatedPath());
        $this->assertEquals($app_dir . '_obfuscated', $ob->getObfuscatedPath());

        // Seta normalmente
        $this->assertTrue($ob->setObfuscatedPath($obf_dir));
        $this->assertEquals($obf_dir, $ob->getObfuscatedPath());
    }

    public function testGetUnpackFile()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $obf_dir = self::getTempPath('app_obfuscated');

        // O caminho do arquivo de reveroa precisa do caminho de ofuscação
        $this->assertNull($ob->getUnpackFile());
        $this->assertEquals('The obfuscation directory was not set', $ob->getLastErrorMessage());

        // seta o caminho de ofuscação
        $ob->setObfuscatedPath($obf_dir);

        // O caminho é resolvido com o nome padrão 'App.php'
        $this->assertEquals($obf_dir . DIRECTORY_SEPARATOR . 'App.php', $ob->getUnpackFile());

        // O caminho é resolvido com o nome personalizado 'AAA.php'
        $this->assertTrue($ob->setUnpackFile('AAA.php'));
        $this->assertEquals($obf_dir . DIRECTORY_SEPARATOR . 'AAA.php', $ob->getUnpackFile());

        // O caminho é resolvido com o nome personalizado
        // Nomes sem extenção são resolvidos automaticamente
        $this->assertTrue($ob->setUnpackFile('BBB'));
        $this->assertEquals($obf_dir . DIRECTORY_SEPARATOR . 'BBB.php', $ob->getUnpackFile());
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

    public function testFinal()
    {
        // Apenas para executar o coletor de lixo gerado pelos testes
        self::garbageCollector();
        $this->assertTrue(true);
    }
}
