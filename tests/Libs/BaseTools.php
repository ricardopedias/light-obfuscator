<?php
namespace PhpObfuscator\Tests\Libs;

trait BaseTools
{
    static protected $garbage = [];

    public static function addGarbageItem($path)
    {
        self::$garbage[] = $path;
    }

    public static function garbageCollector()
    {
        foreach(self::$garbage as $item) {
            if(is_file($item)) {
                unlink($item);
            }
        }

        krsort(self::$garbage);

        foreach(self::$garbage as $item) {
            if(is_dir($item)) {
                rmdir($item);
            }
        }
    }

    /**
     * Cria um novo diretório temporário e devolve seu caminho completo.
     *
     * @param  string $add_path
     * @return string
     */
    public static function getTempPath($add_path = '')
    {
        $path = empty($add_path) ? '' : DIRECTORY_SEPARATOR . trim($add_path, "/");
        $path = sys_get_temp_dir() . $path;
        if (is_dir($path) == false) {
            mkdir($path, 0777);
        }

        if($path !== sys_get_temp_dir()) {
            self::addGarbageItem($path);
        }

        return $path;
    }

    /**
     * Cria um novo arquivo temporário no sistema e devolve o seu caminho completo.
     *
     * @param  string $add_path
     * @return string
     */
    public static function getTempFile($add_path = '', $extension = '.php')
    {
        $prefix = preg_replace('#[^a-zA-Z0-9]#', '', get_called_class());
        $file = tempnam(self::getTempPath($add_path), $prefix . "_");
        rename($file, $file . $extension);
        self::addGarbageItem($file . $extension);
        return $file . $extension;
    }

    /**
     * Devolve o conteudo de um arquivo .stub em forma de string.
     *
     * @param  string $filename
     * @return string
     */
    public static function getStubFileContents($filename)
    {
        $stub_file = self::getStubFile($filename);
        $contents = file_get_contents($stub_file);
        self::addGarbageItem($stub_file);
        return $contents;
    }

    /**
     * Pega um arquivo '.stub' (para testes) e copia ele para a pasta
     * temporária do sistema mudando a extenção para '.php'.
     * Em seguida, devolve o caminho completo para esta versão em PHP.
     *
     * Arquivos .stub são exatamente arquivos PHP, mas com extenção diferente.
     * Existem para evitar que sejam carragados automaticamente por
     * algum autoloader que por ventura esteja sendo executado.
     *
     * @param  string $filename
     * @return string
     */
    public static function getStubFile($filename)
    {
        $stub_file = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'Files', $filename]);
        $stub_contents = file_get_contents($stub_file);

        $temp_file = tempnam(sys_get_temp_dir(), 'obfuscating_class_');

        rename($temp_file, $temp_file . ".php");
        self::addGarbageItem($temp_file . ".php");
        file_put_contents($temp_file . ".php", $stub_contents);

        return $temp_file . ".php";
    }

    public static function getTestFilesPath($add_path = '')
    {
        return rtrim(implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'Files', $add_path]), "/");
    }
}
