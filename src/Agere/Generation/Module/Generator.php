<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 23.09.13
 * Time: 9:23
 * To change this template use File | Settings | File Templates.
 */

namespace Agere\Generation\Module;

use Agere\Base\App\Exception;

class Generator
{
    private $nameModule; // module name
    private $patchDirs; // array to store the files and paths
    private $patchRootFolder; // the path to the root of the module

    public function __construct($nameModule, $pathDir) {
        $this->patchRootFolder = $pathDir . '/' . $nameModule;
        $this->nameModule = $nameModule;
        $this->createPatchDirs();
    }

    /**
     * Generate module
    */
    public function generate() {
        $this->createSkeleton();
        $this->generateFiles();
    }

    /**
     * Generate array to store the files and paths
    */
    protected function createPatchDirs() {
        $this->patchDirs = [
            $this->patchRootFolder => ['Module.php'],
            $this->patchRootFolder . '/' . 'config' => ['module.config.php'],
            $this->patchRootFolder . '/' . 'language' => ['en_GB.mo', 'en_GB.po', 'ru_RU.mo', 'ru_RU.po', 'uk_UA.mo', 'uk_UA.po'],
            $this->patchRootFolder . '/' . 'src' => null,
            $this->patchRootFolder . '/' . 'view' => null,
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule => null,
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/Controller' => [$this->nameModule . 'Controller.php'],
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/Model' => null,
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/Service' => [$this->nameModule . 'Service.php'],
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/View' => null,
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/Controller/Plugin' => null,
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/Model/' . $this->nameModule => [ $this->nameModule . '.php' , $this->nameModule . 'Dto.php'],
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/Model/' . $this->nameModule .'/Mapper' => [
                //$this->nameModule . 'Collection.php',
                $this->nameModule . 'DeferredCollection.php',
                $this->nameModule . 'Mapper.php',
                $this->nameModule . 'ObjectFactory.php'
            ],
            $this->patchRootFolder . '/' . 'src' . '/' . $this->nameModule . '/View/Helper' => null,
            $this->patchRootFolder . '/' . 'view' . '/magere' => null,
            $this->patchRootFolder . '/' . 'view' . '/magere/' . strtolower($this->nameModule) => null
        ];
    }

    /**
     * Create a skeleton (a set of folders) module
    */
    protected function createSkeleton() {
        foreach($this->patchDirs as $path => $file) {
            $this->createFolder($path);
        }
    }

    /**
     * Generation all files for module
     */
    protected function generateFiles() {
        $generatorCode = new GeneratorCode($this->nameModule); // new \Agere\Generation\Module
        $openingPhpfile = "<?php " . "\n";

        foreach($this->patchDirs as $path => $files) {
            if($files == null) {
                continue;
            }

            foreach($files as $file) {
                $part_file = pathinfo($file); // share a file name from the extension
                $part_file['filename'] = str_replace('.', '', $part_file['filename']); // remove points from the name file
                $part_file['filename'] = str_replace($this->nameModule, '', $part_file['filename']); // remove modulename from the file
                $part_file['filename'] = ucfirst($part_file['filename']);
                $method = 'genClass' . $part_file['filename']; // the name of the function that generates the contents of the file

                // if the method is to generate the code of the file exists
                $content = method_exists($generatorCode, $method) ? $generatorCode->$method() : '';

                if($part_file['extension'] == 'php' && $file != 'module.config.php') {
                    $this->createFile($file, $path, $openingPhpfile . $content);
                    continue;
                }
                $this->createFile($file, $path, $content);
            }
        }
    }

    /**
     * @param $path
     */
    protected function createFolder($path) {
        mkdir($path);
        chmod($path, 0755);
    }

    /**
     * Creates a file and stores it in the data
     * @param $name
     * @param $path
     * @param $content
     */
    protected function createFile($name, $path, $content) {
        $file = fopen($path . '/'. $name, 'x');

        if($file) {
            fwrite($file, $content);
            fclose($file);
        }
    }
}