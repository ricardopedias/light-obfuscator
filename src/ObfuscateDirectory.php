<?php
/**
 * @see       https://github.com/rpdesignerfly/light-obfuscator
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/light-obfuscator/blob/master/license.md
 */

declare(strict_types=1);

namespace LightObfuscator;

class ObfuscateDirectory extends ObfuscateFile
{
    /**
     * Caminho completo até o diretório contendo
     * os arquivos que devem ser ofuscados.
     *
     * @var string
     */
    protected $plain_path = null;

    /**
     * Caminho completo até o diretório onde os arquivos
     * ofuscados serão salvos.
     *
     * @var string
     */
    protected $obfuscated_path = null;

    /**
     * Nome do arquivo que conterá as funções de reversão.
     * Este arquivo será gerado pelo processo de ofuscação automaticamente
     * e adicionado no arquivo 'autoloader.php' da aplicação.
     *
     * @var string
     */
    protected $unpack_file = 'App.php';

    /**
     * Links encontrados no processo de ofuscação.
     *
     * @var array
     */
    protected $links = [];

    /**
     * Lista de arquivos resultantes da ofuscação.
     * Esta lista é populada na invocação no método setupAutoloader()
     *
     * @var array
     */
    protected $obfuscated_index = [];

    /**
     * Seta o nome do arquivo que conterá as funções de reversão.
     * Este arquivo sejá gerado pelo processo de ofuscação automaticamente
     * e adicionado no arquivo 'autoloader.php' da aplicação.
     *
     * @return ObfuscateDirectory
     */
    public function setUnpackFile(string $php_file): ObfuscateDirectory
    {
        $this->unpack_file = \pathinfo($php_file, PATHINFO_FILENAME) . ".php";
        return $this;
    }

    /**
     * Verifica se o diretório especificado já está ofuscado.
     *
     * @param  string $obfuscated_directory
     * @return boolean
     */
    public function isObfuscatedDirectory(string $obfuscated_directory) : bool
    {
        return true;
    }

    /**
     * Seta o caminho completo até o diretório contendo
     * os arquivos que devem ser ofuscados.
     *
     * @param  string $path
     * @return boolean
     */
    public function obfuscateDirectory(string $path) : bool
    {
        $this->plain_path = rtrim($path, "/");
        if (is_dir($this->plain_path) === false) {
            $this->addErrorMessage("The specified directory does not exist");
            return false;
        }

        if (is_readable($this->plain_path) == false) {
            $this->addErrorMessage("No permissions to read directory");
            return false;
        }

        return true;
    }

    /**
     * Salva um diretório com todos os arquivos PHP ofuscados
     * no caminho especificado.
     *
     * @param  string $path
     * @return bool
     */
    public function saveDirectory(string $path) : bool
    {
        $this->obfuscated_path = rtrim($path, "/");
        if(is_dir($this->obfuscated_path) === false) {
            $this->addErrorMessage("The specified directory does not exist");
            return false;
        }

        if (is_writable($this->obfuscated_path) == false) {
            $this->addErrorMessage("No permissions to write to the directory");
            return false;
        }

        if ($this->obfuscateDirectoryDeep($this->plain_path, $this->obfuscated_path) == true ) {
            // Diretório ofuscado com sucesso,
            // gera o autoloader
            return $this->setupAutoloader();
        }

        return false;
    }

    /**
     * Devolve a localização do diretório contendo
     * os arquivos que devem ser ofuscados.
     *
     * @return string
     */
    public function getPlainPath()
    {
        return $this->plain_path;
    }

    /**
     * Devolve a localização do diretório onde os arquivos
     * ofuscados devem ser salvos.
     *
     * @return string
     */
    public function getObfuscatedPath()
    {
        return $this->obfuscated_path;
    }

    /**
     * Devolve a lista de arquivos contidos no diretório de destino
     * após o processo de ofuscação.
     *
     * @return array
     */
    public function getObfuscatedIndex() : array
    {
        return $this->obfuscated_index;
    }

    /**
     * Cria o diretório especificado no sistema de arquivos.
     *
     * @param  string  $path
     * @param  bool $force
     * @return bool
     */
    protected function makeDir(string $path, bool $force = false) : bool
    {
        if (is_dir($path) == true) {
            return true;
        }

        $result = false;

        try {
            $result = mkdir($path, 0755, $force);
        } catch(\ErrorException $e) {
            $this->addErrorMessage( $e->getMessage() );
        }

        return $result;
    }

    /**
     * Verifica se o nome especificado é para um arquivo PHP.
     *
     * @param  string $filename
     * @return bool
     */
    protected function isPhpFilename(string $filename) : bool
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return (strtolower($extension) == 'php');
    }

    /**
     * Varre o diretório com os arquivos a serem ofuscados, ofusca-os e salva no diretório
     * de ofuscação.
     *
     * @param  string $path_plain
     * @param  string $path_obfuscated
     * @return bool
     */
    private function obfuscateDirectoryDeep(string $path_plain, string $path_obfuscated) : bool
    {
        // Lista os arquivos do diretório
        $list = scandir($path_plain);
        if (count($list) == 2) { // '.', '..'
            // se não houverem arquivos, ignora o diretório
            return true;
        }

        // Cria o mesmo diretório para os arquivos ofuscados
        if ($this->makeDir($path_obfuscated) == false) {
            return false;
        }

        foreach ($list as $item) {

            if (in_array($item, ['.', '..']) ) {
                continue;
            }

            $iterate_current_item    = $path_plain . DIRECTORY_SEPARATOR . $item;
            $iterate_obfuscated_item = $path_obfuscated . DIRECTORY_SEPARATOR . $item;

            if (is_link($iterate_current_item)) {

                // LINKS
                // TODO: recriar os links
                $link = readlink($iterate_current_item);
                $this->links[$iterate_current_item] = $link;
                continue;

            } elseif (is_file($iterate_current_item) == true) {

                if ($this->isPhpFilename($iterate_current_item) == true) {

                    // Arquivos PHP são ofuscados
                    if($this->obfuscateFile($iterate_current_item) == false) {
                        $this->addErrorMessage("Ocorreu um erro ao tentar ofuscar o arquivo: {$iterate_current_item}");
                        return false;
                    }

                    // Salva o arquivo sem a opção de 'auto-contido',
                    // ou seja, ele não pode ser revertido sem os outros arquivos do diretório
                    if ($this->save($iterate_obfuscated_item, false) == false) {
                        $this->addErrorMessage("Ocorreu um erro ao tentar salvar o arquivo ofuscado: {$iterate_obfuscated_item}");
                        return false;
                    }

                } else {

                    // Arquivos não-PHP são simplesmente copiados
                    if (copy($iterate_current_item, $iterate_obfuscated_item) === false) {
                        $this->addErrorMessage("Arquivo " . $iterate_current_item . " não pôde ser copiado");
                        return false;
                    }
                }

            } elseif (is_dir($iterate_current_item) ) {

                if ($this->obfuscateDirectoryDeep($iterate_current_item, $iterate_obfuscated_item) == false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Configura os arquivos resultantes do processo de ofuscação
     * para que funcionem no mesmo ambiente que os originais funcionavem.
     *
     * @return bool
     */
    protected function setupAutoloader() : bool
    {
        // Gera uma lista com todos os arquivos PHP
        // que foram ofuscados
        $obfuscated_path = $this->getObfuscatedPath();
        $index = $this->makeIndex($obfuscated_path);

        // Salva o arquivo contendo as funções
        // que desfazem a ofuscação do código
        $revert_file = $this->getUnpackFile();
        if($this->saveRevertFile($revert_file) == false) {
            $this->addErrorMessage("Error creating the unpack file");
            return false;
        }

        // Adiciona o arquivo de reversão como o
        // primeiro da lista no autoloader
        $this->obfuscated_index = array_merge([$revert_file], $index);

        // Cria o autoloader com os arquivos ofuscados
        if ($this->generateAutoloader($this->obfuscated_index) == false) {
            $this->addErrorMessage("Error creating autoloader file");
            return false;
        }

        return true;
    }

    /**
     * Devogera uma lista com os arquivos php
     * contidos no diretório especificado.
     *
     * @param  string $destiny
     * @return array
     */
    protected function makeIndex(string $path) : array
    {
        $index = [];

        $list = scandir($path);
        foreach ($list as $item) {

            if (in_array($item, ['.', '..']) ) {
                continue;
            }

            $iterator_index_item = $path . DIRECTORY_SEPARATOR . $item;

            if (is_link($iterator_index_item)) {
                // LINKS
                // são ignorados neste ponto
                continue;

            } elseif (is_file($iterator_index_item) == true) {

                if ($this->isPhpFilename($iterator_index_item) == true) {
                    $index[] = $iterator_index_item;
                } else {
                    // Arquivos não-PHP
                    // são ignorados neste ponto
                    continue;
                }

            } elseif (is_dir($iterator_index_item) ) {
                // DIRETÓRIOS
                $list = $this->makeIndex($iterator_index_item);
                foreach ($list as $file) {
                    $index[] = $file;
                }
            }
        }

        return $index;
    }

    /**
     * Devolve a localização do arquivo que conterá as funções de reversão.
     * Este arquivo sejá gerado pelo processo de ofuscação automaticamente
     * e adicionado no arquivo 'autoloader.php' da aplicação.
     *
     * @return string
     */
    private function getUnpackFile()
    {
        return $this->getObfuscatedPath() . DIRECTORY_SEPARATOR . $this->unpack_file;
    }

    /**
     * Gera um carregador para os arquivos ofuscados.
     *
     * @param  array $list_files
     * @return bool
     */
    protected function generateAutoloader(array $list_files) : bool
    {
        $file = $this->getObfuscatedPath() . DIRECTORY_SEPARATOR . 'autoloader.php';

        $contents = "<?php \n\n";

        // Array de includes
        $contents .= "\$includes = array(\n";
        $contents .= "    '" . implode("',\n    '", $list_files) . "'\n";
        $contents .= ");\n\n";

        // Loop nos includes
        // TODO: incluir uma função ofuscada para fazer o loop
        // contendo as funções de desempacotamento
        $contents .= "foreach(\$includes as \$file) {\n";
        $contents .= "    require_once(\$file);\n";
        $contents .= "}\n\n";

        return (file_put_contents($file, $contents) !== false);
    }
}
