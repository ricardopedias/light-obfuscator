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

    public function testSetUnpackFile()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $obf_dir = self::makeTempPath('app_obfuscated');

        // Por padrão, o arquivo de desempacotamento se chamará App.php
        $this->assertEquals('App.php', $ob->property('unpack_file'));

        // seta o caminho de ofuscação
        $ob->obfuscateDirectory($obf_dir);

        // O caminho é resolvido com o nome personalizado 'AAA.php'
        $this->assertTrue($ob->setUnpackFile('AAA.php'));
        $this->assertEquals('AAA.php', $ob->property('unpack_file'));

        // O caminho é resolvido com o nome personalizado
        // Nomes sem extenção são resolvidos automaticamente
        $this->assertTrue($ob->setUnpackFile('BBB'));
        $this->assertEquals('BBB.php', $ob->property('unpack_file'));
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
        $ob = new ObfuscateDirectoryAccessor;

        $app_dir = self::getTestFilesPath('app');
        $not_dir = '/diretorio/inexistente/';
        $not_read_dir = self::makeTempPath('app_not_read');

        // Diretório setado com sucesso
        $this->assertTrue($ob->obfuscateDirectory($app_dir));
        $this->assertEquals($app_dir, $ob->getPlainPath());

        // Diretório setado com sucesso
        // Barras no final são removidas
        $this->assertTrue($ob->obfuscateDirectory($app_dir . "/"));
        $this->assertEquals($app_dir, $ob->getPlainPath());

        // Diretório não setado: inexistente
        $this->assertFalse($ob->obfuscateDirectory($not_dir));
        $this->assertEquals('The specified directory does not exist', $ob->getLastErrorMessage());


        @mkdir($not_read_dir); // cria o diretório temporário
        chmod($not_read_dir, 0000); // remove permissões de leitura

        // Diretório não setado: não é legível
        $this->assertFalse($ob->obfuscateDirectory($not_read_dir));
        $this->assertEquals('No permissions to read directory', $ob->getLastErrorMessage());

        // adiciona permissões de leitura para o garbage collector excluir
        chmod($not_read_dir, 0755);
    }

    public function testSaveDirectoryErrors()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::makeTempPath('app_obfuscated_errors');
        $not_dir = '/diretorio/inexistente/';
        $not_write_dir = self::makeTempPath('app_not_write');

        $ob->obfuscateDirectory($app_dir);

        // Diretório não setado: inexistente
        $this->assertFalse($ob->saveDirectory($not_dir));
        $this->assertEquals('The specified directory does not exist', $ob->getLastErrorMessage());

        @mkdir($not_write_dir); // cria o diretório temporário
        chmod($not_write_dir, 0000); // remove permissões de leitura

        // Diretório não setado: não é legível
        $this->assertFalse($ob->saveDirectory($not_write_dir));
        $this->assertEquals('No permissions to write to the directory', $ob->getLastErrorMessage());

        // adiciona permissões de leitura para o garbage collector excluir
        chmod($not_write_dir, 0755);
    }

    public function testSaveDirectoryCommon()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::makeTempPath('app_obfuscated_common');

        $ob->obfuscateDirectory($app_dir);

        // Diretório setado com sucesso
        $this->assertTrue($ob->saveDirectory($obf_dir));
        $this->assertEquals($obf_dir, $ob->getObfuscatedPath());

        $app_info = self::treeInfo($app_dir);
        $obf_info = self::treeInfo($obf_dir);

        // remove os arquivos 'App.php' e 'autoloader.php',
        // que são gerados adicionalmente
        $obf_info_cleaned = [];
        foreach ($obf_info as $index => $item) {
            if(preg_match('#App\.php#', $item) || preg_match('#autoloader\.php#', $item)) {
                continue;
            }
            $obf_info_cleaned[] = $item;
        }

        $this->assertEquals($app_info, $obf_info_cleaned);

        // Verifica se todos os arquivos PHP
        // foram devidamente ofuscados
        foreach($obf_info_cleaned as $item) {
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

    public function testSaveDirectoryFinalBar()
    {
        $ob = new ObfuscateDirectoryAccessor;

        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::makeTempPath('app_obfuscated_final_bar');

        $ob->obfuscateDirectory($app_dir);

        // Diretório setado com sucesso
        // Barras no final são removidas
        $this->assertTrue($ob->saveDirectory($obf_dir . "/"));
        $this->assertEquals($obf_dir, $ob->getObfuscatedPath());

        $app_info = self::treeInfo($app_dir);
        $obf_info = self::treeInfo($obf_dir);

        // remove os arquivos 'App.php' e 'autoloader.php',
        // que são gerados adicionalmente
        $obf_info_cleaned = [];
        foreach ($obf_info as $index => $item) {
            if(preg_match('#App\.php#', $item) || preg_match('#autoloader\.php#', $item)) {
                continue;
            }
            $obf_info_cleaned[] = $item;
        }

        $this->assertEquals($app_info, $obf_info_cleaned);

        // Verifica se todos os arquivos PHP
        // foram devidamente ofuscados
        foreach($obf_info_cleaned as $item) {
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

    public function testMakeIndex()
    {
        $app_dir = self::getTestFilesPath('app');

        $ob = new ObfuscateDirectoryAccessor;
        $list_index = $ob->method('makeIndex', $app_dir);

        $app_info = self::treeInfo($app_dir);
        $list_info = [];
        foreach($app_info as $item){
            $item_file = $app_dir . $item;
            if ($ob->method('isPhpFilename', $item_file)) {
                $list_info[] = $item_file;
            }
        }
        $this->assertEquals($list_index, $list_info);
    }

    /**
     * O generateAutoloader sempre retornará true porque a validade do diretório
     * já foi verificada no método saveDiretory(). False só será retornado
     * se for um erro de hardware :(
     *
     * @return void
     */
    public function testGenerateAutoloader()
    {
        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::makeTempPath('app_obfuscated_generate_autoloader');

        $ob = new ObfuscateDirectoryAccessor;

        // Gera uma lista com os arquivos php contidos no diretório especificado
        $list_index = $ob->method('makeIndex', $app_dir);

        // No momento que generateAutoloader é invocado,
        // a biblioteca já validou e possui o diretório de ofuscação
        $ob->property('obfuscated_path', $obf_dir);

        // Agora o autoloader pode ser gerado no diretório de ofuscação
        $this->assertTrue($ob->method('generateAutoloader', $list_index));
        $autoloader_generated = $obf_dir . DIRECTORY_SEPARATOR . 'autoloader.php';
        $this->assertTrue(is_file($autoloader_generated));

        // Verifica se todos os arquivos estão no autoloader
        $contents = file_get_contents($autoloader_generated);
        foreach($list_index as $item) {
            $this->assertRegexp("#{$item}#", $contents);
        }

        // Marca o arquivo gerado para o garbage collector limpar
        self::addGarbageItem($autoloader_generated);
    }

    /**
     * O testSetupAutoloader sempre retornará true porque a validade dos
     * diretórios de origem e ofuscação já foi verificada no método
     * saveDiretory(). False só será retornado se for um erro de hardware :(
     *
     * @return void
     */
    public function testSetupAutoloader()
    {
        $app_dir = self::getTestFilesPath('app');
        $obf_dir = self::makeTempPath('app_obfuscated_setup_autoload');

        // Ofusca o diretório
        $ob = new ObfuscateDirectoryAccessor;

        // No momento que setupAutoloader é invocado,
        // a biblioteca já validou e possui o diretório de origem e de ofuscação
        $ob->property('plain_path', $app_dir);
        $ob->property('obfuscated_path', $obf_dir);

        // Para o autoloader ser gerado é necessário
        // que a biblioteca saiba o diretório dos arquivos ofuscados
        $this->assertTrue($ob->method('setupAutoloader'));

        $autoloader_generated = $obf_dir . DIRECTORY_SEPARATOR . 'autoloader.php';
        $this->assertTrue(is_file($autoloader_generated));

        // Marca o arquivo gerado para o garbage collector limpar
        self::addGarbageItem($autoloader_generated);

        foreach($ob->getObfuscatedIndex() as $item) {
            self::addGarbageItem($item);
        }
    }

    public function testFinal()
    {
        // Apenas para executar o coletor de lixo gerado pelos testes
        self::garbageCollector();
        $this->assertTrue(true);
    }
}
