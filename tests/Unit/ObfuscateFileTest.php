<?php
namespace PhpObfuscator\Tests\Unit;

use Tests\TestCase;
use PhpObfuscator\Tests\Libs\ObfuscateFileAccessor;
use PhpObfuscator\Tests\Libs\BaseTools;
use PhpObfuscator\ObfuscateFile;

class ObfuscateFileTest extends TestCase
{
    use BaseTools;

    private $errors;

    private $test_files = [
        'PhpClass.stub',
        'PhpClassClosed.stub',
        'PhpClassNamespaced.stub',
        'PhpProcedural.stub',
        'PhpProceduralClosed.stub'
    ];

    public function testAddRuntimeMessage()
    {
        $ob = new ObfuscateFileAccessor;
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addRuntimeMessage('aaa'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addRuntimeMessage('bbb'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addRuntimeMessage('ccc'));

        $this->assertCount(3, $ob->getRuntimeMessages());
        $this->assertEquals($ob->getRuntimeMessages()[0], 'aaa');
        $this->assertEquals($ob->getRuntimeMessages()[1], 'bbb');
        $this->assertEquals($ob->getRuntimeMessages()[2], 'ccc');
    }

    public function testLastRuntimeMessage()
    {
        $ob = new ObfuscateFileAccessor;
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addRuntimeMessage('aaa'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addRuntimeMessage('bbb'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addRuntimeMessage('ccc'));

        $this->assertCount(3, $ob->getRuntimeMessages());

        $this->assertEquals($ob->getLastRuntimeMessage(), 'ccc');
        $ob->addRuntimeMessage('ddd');
        $this->assertEquals($ob->getLastRuntimeMessage(), 'ddd');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage aaa
     */
    public function testAddErrorException()
    {
        $ob = new ObfuscateFileAccessor;
        $ob->enableThrowErrors();

        $ob->addErrorMessage('aaa');
    }

    public function testAddErrorMessage()
    {
        $ob = new ObfuscateFileAccessor;
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addErrorMessage('aaa'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addErrorMessage('bbb'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addErrorMessage('ccc'));

        $this->assertCount(3, $ob->getErrorMessages());
        $this->assertEquals($ob->getErrorMessages()[0], 'aaa');
        $this->assertEquals($ob->getErrorMessages()[1], 'bbb');
        $this->assertEquals($ob->getErrorMessages()[2], 'ccc');
    }

    public function testLastErrorMessage()
    {
        $ob = new ObfuscateFileAccessor;
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addErrorMessage('aaa'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addErrorMessage('bbb'));
        $this->assertInstanceOf(ObfuscateFile::class, $ob->addErrorMessage('ccc'));

        $this->assertCount(3, $ob->getErrorMessages());

        $this->assertEquals($ob->getLastErrorMessage(), 'ccc');
        $ob->addErrorMessage('ddd');
        $this->assertEquals($ob->getLastErrorMessage(), 'ddd');
    }

    public function testPhpWrapperRemove()
    {
        foreach ($this->test_files as $file) {

            $code = self::getStubFileContents($file);
            $this->assertContains('<?php', $code);

            $removed = (new ObfuscateFileAccessor)->method('phpWrapperRemove', $code);
            $this->assertNotContains('<?php', $removed);
            $this->assertNotContains('?>', $removed);

        }

        // ---------------------------------------------------------------------
        // Procedural: Abertura + Fechamento + Mixeds
        //
        $code = self::getStubFileContents('PhpProceduralMixed.stub');

        $this->assertContains('<?php', $code);
        $this->assertContains('<?=', $code);
        $this->assertContains('?>', $code);

        $removed = (new ObfuscateFileAccessor)->method('phpWrapperRemove', $code);
        $this->assertFalse($removed);
    }

    //
    // Compressão e descompressão
    //

    public function testBreakOne()
    {
        foreach ($this->test_files as $file) {

            $string = self::getStubFileContents($file);

            $ob = new ObfuscateFileAccessor;
            $compressed = $ob->packerOnePack($string);
            $this->assertEquals($string, $ob->packerOneUnpack($compressed));
        }
    }

    public function testBreakTwo()
    {
        foreach ($this->test_files as $file) {

            $string = self::getStubFileContents($file);

            $ob = new ObfuscateFileAccessor;
            $compressed = $ob->packerTwoPack($string);
            $this->assertEquals($string, $ob->packerTwoUnpack($compressed));
        }
    }

    public function testBreakThree()
    {
        foreach ($this->test_files as $file) {

            $string = self::getStubFileContents($file);

            $ob = new ObfuscateFileAccessor;
            $compressed = $ob->packerThreePack($string);
            $this->assertEquals($string, $ob->packerThreeUnpack($compressed));
        }
    }

    //
    // Funções aleatórias
    //

    public function testGetPackerName()
    {
        $ob = new ObfuscateFileAccessor;
        // $list = $ob->getProperty('map_packer_functions');
        $name_one = $ob->method('getPackerName');
        $name_two = $ob->method('getPackerName');
        $this->assertEquals($name_one, $name_two);
    }

    public function testGetPackerMethodName()
    {
        $ob = new ObfuscateFileAccessor;
        // $list = $ob->getProperty('map_packer_functions');
        $name_one = $ob->method('getPackerMethodName');
        $name_two = $ob->method('getPackerMethodName');
        $this->assertEquals($name_one, $name_two);
    }

    public function testGetArgumenterName()
    {
        $ob = new ObfuscateFileAccessor;
        // $list = $ob->getProperty('map_argumenter_functions');
        $name_one = $ob->method('getArgumenterName');
        $name_two = $ob->method('getArgumenterName');
        $this->assertEquals($name_one, $name_two);
    }

    public function testIsObfuscatedFile()
    {
        $not_obfuscated = self::getStubFile('PhpClass.stub');
        $obfuscated = self::getStubFile('PhpClassObfuscated.stub');

        $this->assertFalse((new ObfuscateFile)->isObfuscatedFile($not_obfuscated));
        $this->assertTrue((new ObfuscateFile)->isObfuscatedFile($obfuscated));
    }

    //
    // Ofuscação e Execução
    //

    public function testObfuscateFileError()
    {
        $ob = new ObfuscateFile;
        $this->assertFalse($ob->obfuscateFile('arquivo-nao-php.html'));
        $this->assertEquals('Only PHP files can be obfuscated!', $ob->getLastErrorMessage());
    }

     /**
      * @expectedException Error
      * @expectedExceptionMessage Class 'PhpClass' not found
      */
    public function testObfuscatePhpClass_Exception()
    {
        $origin = self::getStubFile('PhpClass.stub');
        $saved_file = self::makeTempFile();

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFile;
        $this->assertTrue($ob->obfuscateFile($origin));
        $this->assertTrue($ob->save($saved_file));

        // Esta chamada deve emitir um erro no PHP
        // pois a invocação não é possivel sem a reversão
        (new \PhpClass)->method();
    }

    /**
     * @expectedException Error
     * @expectedExceptionMessage Class 'PhpClassClosed' not found
     */
    public function testObfuscatePhpClassClosed_Exception()
    {
        $origin = self::getStubFile('PhpClassClosed.stub');
        $saved_file = self::makeTempFile();

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFile;
        $this->assertTrue($ob->obfuscateFile($origin));
        $this->assertTrue($ob->save($saved_file));

        // Esta chamada deve emitir um erro no PHP
        // pois a invocação não é possivel sem a reversão
        (new \PhpClassClosed)->method();
    }

    /**
     * @expectedException Error
     * @expectedExceptionMessage Class 'PhpClassNamespaced' not found
     */
    public function testObfuscatePhpClassNamespaced_Exception()
    {
        $origin = self::getStubFile('PhpClassNamespaced.stub');
        $saved_file = self::makeTempFile();

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFile;
        $this->assertTrue($ob->obfuscateFile($origin));
        $this->assertTrue($ob->save($saved_file));

        // Esta chamada deve emitir um erro no PHP
        // pois a invocação não é possivel sem a reversão
        (new \PhpClassNamespaced)->method();
    }

    /**
     * @expectedException Error
     * @expectedExceptionMessage Call to undefined function PhpProcedural()
     */
    public function testObfuscatePhpProcedural_Exception()
    {
        $origin = self::getStubFile('PhpProcedural.stub');
        $saved_file = self::makeTempFile();

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFile;
        $this->assertTrue($ob->obfuscateFile($origin));
        $this->assertTrue($ob->save($saved_file));

        // Esta chamada deve emitir um erro no PHP
        // pois a invocação não é possivel sem a reversão
        \PhpProcedural();
    }

    /**
     * @expectedException Error
     * @expectedExceptionMessage Call to undefined function PhpProceduralClosed()
     */
    public function testObfuscatePhpProceduralClosed_Exception()
    {
        $origin = self::getStubFile('PhpProceduralClosed.stub');
        $saved_file = self::makeTempFile();

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFile;
        $this->assertTrue($ob->obfuscateFile($origin));
        $this->assertTrue($ob->save($saved_file));

        // Esta chamada deve emitir um erro no PHP
        // pois a invocação não é possivel sem a reversão
        \PhpProceduralClosed();
    }

    public function testObfuscatePhpProceduralMixed()
    {
        $origin = self::getStubFile('PhpProceduralMixed.stub');
        $saved_file = self::makeTempFile();

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFile;
        $this->assertTrue($ob->obfuscateFile($origin));
        $this->assertTrue($ob->save($saved_file));

        $this->assertEquals('Mixed code found. File not obfuscated!', $ob->getLastRuntimeMessage());

    }

    public function testGetRevertFileContents()
    {
        $ob = (new ObfuscateFileAccessor)->method('getRevertFileContents');
        $this->assertTrue(true);
    }

    public function testObfuscatePhpClass()
    {
        $origin = self::getStubFile('PhpClass.stub');
        $saved_file = self::makeTempFile();
        $saved_revert_file = self::makeTempFile('revert_obfuscate_');

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFileAccessor;
        $ob->obfuscateFile($origin);
        $this->assertTrue($ob->save($saved_file));
        $this->assertTrue($ob->saveRevertFile($saved_revert_file));

        // Inclusão do arquivo com as funções de reversão
        //dd(file_get_contents($saved_revert_file));
        require_once $saved_revert_file;

        // Inclusão do arquivo ofuscado
        require_once $saved_file;

        // Funções
        $this->assertEquals((new \PhpClass)->method(), 'PhpClass executando com sucesso');
    }

    public function testObfuscatePhpClassClosed()
    {
        $origin = self::getStubFile('PhpClassClosed.stub');
        $saved_file = self::makeTempFile();
        $saved_revert_file = self::makeTempFile('revert_obfuscate_');

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFileAccessor;
        $ob->obfuscateFile($origin);
        $this->assertTrue($ob->save($saved_file));
        $this->assertTrue($ob->saveRevertFile($saved_revert_file));

        // As funções de reversão já estão na memória pois o
        // arquivo foi incluido no teste testObfuscatePhpClass

        // Inclusão do arquivo ofuscado
        require_once $saved_file;

        // Funções
        $this->assertEquals((new \PhpClassClosed)->method(), 'PhpClassClosed executando com sucesso');
    }

    public function testObfuscatePhpClassNamespaced()
    {
        $origin = self::getStubFile('PhpClassNamespaced.stub');
        $saved_file = self::makeTempFile();
        $saved_revert_file = self::makeTempFile('revert_obfuscate_');

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFileAccessor;
        $ob->obfuscateFile($origin);
        $this->assertTrue($ob->save($saved_file));
        $this->assertTrue($ob->saveRevertFile($saved_revert_file));

        // As funções de reversão já estão na memória pois o
        // arquivo foi incluido no teste testObfuscatePhpClass

        // Inclusão do arquivo ofuscado
        require_once $saved_file;

        $this->assertEquals((new \Php\Name\Space\PhpClassNamespaced)->method(), 'Php\Name\Space\PhpClassNamespaced executando com sucesso');
    }

    public function testObfuscatePhpProcedural()
    {
        $origin = self::getStubFile('PhpProcedural.stub');
        $saved_file = self::makeTempFile();
        $saved_revert_file = self::makeTempFile('revert_obfuscate_');

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFileAccessor;
        $ob->obfuscateFile($origin);
        $this->assertTrue($ob->save($saved_file));
        $this->assertTrue($ob->saveRevertFile($saved_revert_file));

        // As funções de reversão já estão na memória pois o
        // arquivo foi incluido no teste testObfuscatePhpClass

        // Inclusão do arquivo ofuscado
        require_once $saved_file;

        // Funções
        $this->assertEquals(\PhpProcedural(), 'PhpProcedural executando com sucesso');
    }

    public function testObfuscatePhpProceduralClosed()
    {
        $origin = self::getStubFile('PhpProceduralClosed.stub');
        $saved_file = self::makeTempFile();
        $saved_revert_file = self::makeTempFile('revert_obfuscate_');

        // Ofusca o arquivo e salva do disco
        $ob = new ObfuscateFileAccessor;
        $ob->obfuscateFile($origin);
        $this->assertTrue($ob->save($saved_file));
        $this->assertTrue($ob->saveRevertFile($saved_revert_file));

        // As funções de reversão já estão na memória pois o
        // arquivo foi incluido no teste testObfuscatePhpClass

        // Inclusão do arquivo ofuscado
        require_once $saved_file;

        // Funções
        $this->assertEquals(\PhpProceduralClosed(), 'PhpProceduralClosed executando com sucesso');
    }

    public function testFinal()
    {
        // Apenas para executar o coletor de lixo gerado pelos testes
        self::garbageCollector();
        $this->assertTrue(true);
    }
}
