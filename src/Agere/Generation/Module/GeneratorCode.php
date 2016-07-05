<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 24.09.13
 * Time: 12:32
 * To change this template use File | Settings | File Templates.
 */

namespace Agere\Generation\Module;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;
use Zend\Code\Generator\ValueGenerator;

class GeneratorCode
{
    private $nameModule;

    public function __construct($nameModule) {
        $this->nameModule = $nameModule;
    }

    /**
     * Returns the string in lowercase
     * @param $string
     * @return string
     */
    private function getLowercase($string) {
        return strtolower($string);
    }

    /**
     * Generate class for file Module.php
     * @return string
     */
    public function genClassModule() {
        $module = new ZendClassGenerator();
        $module->setNamespaceName('Magere\\' . $this->nameModule)
            ->setName('Module')
            ->addMethods(array(
                // Method passed as array
                MethodGenerator::fromArray(array(
                    'name'       => 'getConfig',
                    'parameters' => array(),
                    'body'       => 'return include __DIR__ . \'/config/module.config.php\';',
                )),

                // Method passed as concrete instance
                new MethodGenerator(
                    'getAutoloaderConfig',
                    array(),
                    MethodGenerator::FLAG_PUBLIC,
                    'return array(' . "\n\t" .
                        '\'Zend\Loader\StandardAutoloader\' => array(' . "\n\t\t" .
                        ' \'namespaces\' => array(' . "\n\t\t\t" .
                        '__NAMESPACE__ => __DIR__ . \'/src/\' . explode(\'\\\\\', __NAMESPACE__)[1],' . "\n\t\t" .
                        '),' . "\n\t" .
                        '),' . "\n" .
                        ');'
                ),
            ));

        return $module->generate();
    }

    /**
     * Generated content for file module.config.php
     */
    public function genClassModuleconfig() {
        $config = new FileGenerator();
        $config->setNamespace('Magere\\' . $this->nameModule)
            ->setBody('return array(' . "\n\t" .
                    '\'controllers\' => array(' . "\n\t\t" .
                          '\'invokables\' => array(' . "\n\t\t\t" .
                                "'" . $this->getLowercase($this->nameModule) . "' => 'Magere\\" . $this->nameModule . "\\Controller\\" . $this->nameModule . "Controller'" . "\n\t\t" .
                          '),' . "\n\t" .
                    '),' . "\n\n\t" .

                    '\'service_manager\' => array(' . "\n\t\t" .
                          '\'aliases\' => array(' . "\n\t\t\t" .
                              '\'' . $this->nameModule . 'Service\'	=> \'Magere\\' . $this->nameModule . '\Service\\' . $this->nameModule . 'Service\',' . "\n\t\t\t" .
                              '\'' . $this->nameModule . 'Mapper\'	=> \'Magere\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\Mapper\\' . $this->nameModule . 'Mapper\',' . "\n\t\t" .
                          '),' . "\n\n\t\t" .

                          '\'factories\' => array(' . "\n\t\t\t" .
                              '\'Magere\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\Mapper\\' . $this->nameModule . 'Mapper\' => function ($sm) {' . "\n\t\t\t\t" .
                                  'return \Agere\Domain\Factory\Helper::getFinder(\'' . $this->getLowercase($this->nameModule) . '/' . $this->getLowercase($this->nameModule) . '\');' . "\n\t\t\t" .
                              '},' . "\n\n\t\t\t" .

                              '\'Magere\\' . $this->nameModule . '\Service\\' . $this->nameModule . 'Service\' => function ($sm) {' . "\n\t\t\t\t" .
                                  '$mapper = $sm->get(\'' . $this->nameModule . 'Mapper\');' . "\n\t\t\t\t" .
                                  '$service = \Agere\Service\Factory\Helper::create(\'' . $this->getLowercase($this->nameModule) . '/' . $this->getLowercase($this->nameModule) . '\');' . "\n\t\t\t\t" .
                                  '$service->setMapper(\'' . $this->getLowercase($this->nameModule) . '/' . $this->getLowercase($this->nameModule) . '\', $mapper);' . "\n\t\t\t\t" .
                                  'return $service;' . "\n\t\t\t" .
                              '},' . "\n\t\t" .
                          '),' . "\n\t" .
                    '),' . "\n\n\t" .

                    '\'view_manager\' => array(' . "\n\t\t" .
                          '\'template_path_stack\' => array(' . "\n\t\t\t" .
                                ' __NAMESPACE__ => __DIR__ . \'/../view\',' . "\n\t\t" .
                          '),' . "\n\t" .
                    '),'  . "\n" .
                ');'
            );

        return $config->generate();
    }

    /**
     * Generate class for file Model/nameModule/nameModule.php
     */
    public function genClass() {
        $model = new ZendClassGenerator();

        $model->setNamespaceName('Magere\\' . $this->nameModule . '\\Model\\' . $this->nameModule)
            ->addUse('Agere\Domain\Domain')
            ->setName($this->nameModule)
            ->setExtendedClass('Domain');

        return $model->generate();
    }

    /**
     * Generate class for file Model/nameModule/nameModuleDto.php
     */
    public function genClassDto() {
        $modelDto = new ZendClassGenerator();
        $modelDto->setNamespaceName('Magere\\' . $this->nameModule . '\\Model\\' . $this->nameModule)
            ->addUse('Agere\Domain\Dto\Dto')
            ->setName($this->nameModule . 'Dto')
            ->setExtendedClass('Dto')
            ->addProperties(array(
                array('name', null, PropertyGenerator::FLAG_PROTECTED),))
            ->addMethods(array(
                // Method passed as array
                MethodGenerator::fromArray(array(
                    'name'       => 'getName',
                    'parameters' => array(),
                    'body'       => 'return $this->name;',
                )),
            ));

        return $modelDto->generate();
    }

    /**
     * Generate class Mapper for file: Model/nameModule/Mapper/nameModuleMapper.php
     */
    public function genClassMapper() {

        $cacheOptions = array(
            "\n\t\t" . 'collection' => array(
                'flag'		=> false,
                'expire'	=> 8600,
                'tag'		=> array($this->getLowercase($this->nameModule) . 'one')
            )
        );

        $mapper = new ZendClassGenerator();
        $mapper->setNamespaceName('Magere\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\Mapper')
            ->addUse('Agere\Domain\Mapper\AbstractMapper')
            ->addUse('Agere\Memcache')
            ->addUse('Magere\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\\' . $this->nameModule)
            ->setName($this->nameModule . 'Mapper')
            ->setExtendedClass('AbstractMapper')
            ->addProperties(array(
                array('cacheOptions', $cacheOptions, PropertyGenerator::FLAG_PROTECTED),
                array('docTable', $this->getLowercase($this->nameModule),   PropertyGenerator::FLAG_PROTECTED),
                array('alias',  $this->getLowercase($this->nameModule)[0],   PropertyGenerator::FLAG_PROTECTED),
            ))
            ->addMethods(array(
                // Method passed as array
                MethodGenerator::fromArray(array(
                    'name'       => 'getCollection',
                    'parameters' => array(),
                )),
                // Method passed as concrete instance
                new MethodGenerator(
                    'doSave',
                    array(new ParameterGenerator('obj', '\Agere\Domain\Domain')),
                    MethodGenerator::FLAG_PROTECTED,
                    '$this->_save($obj, $obj->toArray());'
                ),
                // Method passed as concrete instance
                new MethodGenerator(
                    'countStatement',
                    array(),
                    MethodGenerator::FLAG_PROTECTED,
                    '$carcass = "SELECT count(`{$this->alias}`.`id`) FROM `{$this->docTable}` AS `{$this->alias}`' . "\n\t\t\t" .
                        'WHERE 1>0' . "\n\t\t\t\t" .
                        '{$this->getCondition()}' . "\n\t\t\t\t" .
                        '{$this->getGroupBy()}' . "\n\t\t\t\t" .
                        '{$this->getOrderBy()}";' . "\n" .
                        'return $carcass;'
                ),
                // Method passed as concrete instance
                new MethodGenerator(
                    'findStatement',
                    array(),
                    MethodGenerator::FLAG_PROTECTED,
                    '$carcass = "SELECT * FROM `{$this->docTable}` AS `{$this->alias}`' . "\n\t\t\t" .
                        'WHERE 1>0' . "\n\t\t\t\t" .
                        '{$this->getCondition()}' . "\n\t\t\t\t" .
                        '{$this->getGroupBy()}' . "\n\t\t\t\t" .
                        '{$this->getOrderBy()}";' . "\n" .
                        'return $carcass;'
                ),
                // Method passed as concrete instance
                new MethodGenerator(
                    'carcassSelect',
                    array(),
                    MethodGenerator::FLAG_PROTECTED,
                    '$carcass = "SELECT * FROM `{$this->docTable}` AS `{$this->alias}` WHERE id = ?";' . "\n" .
                        'return $carcass;'
                ),
                // Method passed as concrete instance
                new MethodGenerator(
                    'selectStmt',
                    array(),
                    MethodGenerator::FLAG_PROTECTED,
                    'return $this->carcassSelect();'
                ),
                // Method passed as concrete instance
                new MethodGenerator(
                    'doDelete',
                    array('id'),
                    MethodGenerator::FLAG_PROTECTED,
                    'self::$DB->exec("DELETE FROM `{$this->docTable}` WHERE id = \'{$id}\'");'
                ),
            ));

        return $mapper->generate();
    }

    /**
     * Generate class for file Model/nameModule/Mapper/nameModuleObjectFactory.php
     * @return string
     */
    public function genClassObjectFactory() {
        $mapper = new ZendClassGenerator();
        $mapper->setNamespaceName('Magere\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\Mapper')
            ->addUse('Agere\Domain\Mapper\DomainObjectFactory')
            ->setName($this->nameModule . 'ObjectFactory')
            ->setExtendedClass('DomainObjectFactory')
            ->addMethods(array(
                // Method passed as concrete instance
                new MethodGenerator(
                    'doCreateObject',
                    array(new ParameterGenerator('array', 'array')),
                    MethodGenerator::FLAG_PROTECTED,
                    '$class = $this->targetClass();' . "\n" .
                        '$classDto = $class . \'Dto\';'  . "\n\n" .
                        '$dtoObj = new $classDto($id = $this->getId($array));'  . "\n" .
                        '$dtoObj->setProperties($array);'  . "\n\n" .
                        '$obj = new $class($dtoObj);'  . "\n" .
                        'return $obj;'
                ),
            ))
            ->setContentClass('use \Agere\Domain\Mapper\Traits\TargetTrait;');

        return $mapper->generate();
    }

    /**
     * Generate class for file Model/nameModule/Mapper/nameModuleCollection.php
     * @return string
     */
    public function genClassCollection() {
        $mapper = new ZendClassGenerator();
        $mapper->setNamespaceName('Magere\Module\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\Mapper')
            ->setName($this->nameModule . 'Collection')
            ->setExtendedClass('\Agere\Domain\Mapper\Collection\AbstractCollection')
            ->addMethods(array(
                // Method passed as array
                MethodGenerator::fromArray(array(
                    'name'       => 'targetClass',
                    'parameters' => array(),
                    'body'       => '\'return ' . '\\\\' . 'Magere' . '\\\\' . 'Module' . '\\\\' . $this->nameModule . '\\\\' . 'Model' . '\\\\' . $this->nameModule . '\\\\' . $this->nameModule . "';",
                )),
            ));

        return $mapper->generate();
    }

    /**
     * Generate class for file Model/nameModule/Mapper/nameModule/DeferredCollection.php
     */
    public function genClassDeferredCollection() {
        $mapper = new ZendClassGenerator();

        $mapper->setNamespaceName('Magere\\' . $this->nameModule . '\Model\\' . $this->nameModule . '\Mapper')
            ->addUse('Agere\Domain\Mapper\Collection')
            ->setName($this->nameModule . 'DeferredCollection')
            ->setExtendedClass('Collection\DeferredCollection')
            ->setContentClass('use \Agere\Domain\Mapper\Traits\TargetTrait;');

        return $mapper->generate();
    }

    /**
     * Generate class controller
     */
    public function genClassController() {
        $controller = new ZendClassGenerator();
        $controller->setNamespaceName('Magere\\' . $this->nameModule . '\Controller')
            ->addUse('Zend\Mvc\Controller\AbstractActionController')
            ->addUse('Zend\View\Model\ViewModel')
            ->setName($this->nameModule . 'Controller')
            ->setExtendedClass('AbstractActionController')
            ->addMethod($this->getLowercase($this->nameModule) . 'Action', array(), MethodGenerator::FLAG_PUBLIC);

        return $controller->generate();
    }

    /**
     * Generate class Service for file: src/nameModule/Service/nameModuleService.php
     * @return string
     */
    public function genClassService() {
        $service = new ZendClassGenerator();
        $service->setNamespaceName('Magere\\' . $this->nameModule . '\Service')
            ->addUse('Agere\Db\Query\Where')
            ->addUse('Agere\Service\AbstractService')
            ->addUse('Agere\Service\Factory\Helper', 'ServiceFactoryHelper')
            ->setName($this->nameModule . 'Service')
            ->setExtendedClass('AbstractService')
            ->addMethods(array(
                // Method passed as concrete instance
                new MethodGenerator(
                    'getItem',
                    array("id"),
                    MethodGenerator::FLAG_PUBLIC,
                    '$mapper = $this->getMapper(\'' . $this->getLowercase($this->nameModule) . '/' . $this->getLowercase($this->nameModule) . '\');' . "\n" .
                        '$' . $this->getLowercase($this->nameModule) . '= $mapper->find($id);' . "\n\n" .
                        'if (!$' . $this->getLowercase($this->nameModule) . ') {' . "\n\t" .
                        '$'  . $this->getLowercase($this->nameModule) . ' = $mapper->getObjectFactory()->createObject(array()); // create fake object' . "\n" .
                        '}'  . "\n" .
                        'return $' . $this->getLowercase($this->nameModule) . ';',
                    DocBlockGenerator::fromArray(array(
                        'shortDescription' => 'Get user object by id',
                        'longDescription'  => 'Return valid ' . $this->nameModule . ' object otherwise non valid object',
                        'tags'             => array(
                            new Tag\ParamTag(array(
                                'datatype'  => 'int $id'
                            )),
                            new Tag\ReturnTag(array(
                                'datatype'  => '\Magere\\' . $this->nameModule . "\\Model\\" . $this->nameModule . "\\" . $this->nameModule,
                            )),
                        ),
                    ))
                ),
                // Method passed as concrete instance
                new MethodGenerator(
                    'getItemCollection',
                    array(new ParameterGenerator('where', 'Where')),
                    MethodGenerator::FLAG_PUBLIC,
                    '$mapper = $this->getMapper(\'' . $this->getLowercase($this->nameModule) . '/' . $this->getLowercase($this->nameModule) . '\');' . "\n\n" .
                        '$collection = $mapper->limit(0)'  . "\n\t" .
                        '->findWhere($where);'  . "\n\n" .
                        'return $collection;',
                    DocBlockGenerator::fromArray(array(
                        'longDescription'  => null,
                        'tags'             => array(
                            new Tag\ParamTag(array(
                                'datatype'  => 'Where $where'
                            )),
                            new Tag\ReturnTag(array(
                                'datatype'  => '\Magere\\' . $this->nameModule . "\\Model\\" . $this->nameModule . "\\Mapper\\" . $this->nameModule . "DeferredCollection",
                            )),
                        ),
                    ))
                ),
            ));

        return $service->generate();
    }
}