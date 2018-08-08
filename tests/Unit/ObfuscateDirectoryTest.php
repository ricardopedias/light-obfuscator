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
        $not_read_dir = self::makeTempPath('app_not_read');

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

        $obf_dir = self::makeTempPath('app_obfuscated');
        $not_dir = '/diretorio/inexistente/';
        $not_write_dir = self::makeTempPath('app_not_write');

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
        $obf_dir = self::makeTempPath('app_obfuscated');

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

        $obf_dir = self::makeTempPath('app_obfuscated');

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

    public function testMakeDir()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $make_path = self::makeTempPath('obfuscate_make_dir_' . uniqid());
        $make_path_add = implode(DIRECTORY_SEPARATOR, [$make_path, 'one', 'two']);

        // Cria um diretório normalmente
        $this->assertTrue($ob->method('makeDir', $make_path, false));
        $this->assertTrue(is_dir($make_path));

        // Ao tentar criar um diretório já existente,
        // o método deve retornar true
        $this->assertTrue($ob->method('makeDir', $make_path, false));

        // Tenta criar uma árvore de diretórios
        $this->assertFalse($ob->method('makeDir', $make_path_add, false));
        $this->assertFalse(is_dir($make_path_add));
        $this->assertEquals('mkdir(): No such file or directory', $ob->getLastErrorMessage());

        // Força a criação de uma érvore de diretórios
        $this->assertTrue($ob->method('makeDir', $make_path_add, true));
        $this->assertTrue(is_dir($make_path_add));

        // Adiciona os diretórios pela ordem de criação
        // para o garbage collector remover
        self::addGarbageItem(implode(DIRECTORY_SEPARATOR, [$make_path, 'one']));
        self::addGarbageItem(implode(DIRECTORY_SEPARATOR, [$make_path, 'one', 'two']));
    }

    public function testIsPhpFilename()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $this->assertTrue($ob->method('isPhpFilename', 'ricardo.php'));
        $this->assertFalse($ob->method('isPhpFilename', 'ricardo.html'));
    }

    public function testObfuscateDirectory()
    {
        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::makeTempPath('app_obfuscated');

        $ob = new ObfuscateDirectoryAccessor;
        $ob->obfuscateDirectory($app_dir, $obf_dir);

        $app_info = self::treeInfo($app_dir, $obf_dir);
        $obf_info = self::treeInfo($obf_dir, $obf_dir);
        $this->assertEquals($app_info, $obf_info);

        // Verifica se todos os arquivos PHP
        // foram devidamente ofuscados
        foreach($obf_info as $item) {
            $ob_file = $obf_dir . $item;
            if ($ob->method('isPhpFilename', $ob_file)) {
                $this->assertTrue($ob->isObfuscatedFile($ob_file));
            }
        }

        // Marca os arquivos e diretórios gerados pela ofuscação
        // para o garbage collector possa remover
        foreach($obf_info as $item) {
            self::addGarbageItem($obf_dir . DIRECTORY_SEPARATOR . $item);
        }
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
