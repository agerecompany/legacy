<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 26.09.13
 * Time: 11:15
 * To change this template use File | Settings | File Templates.
 */

namespace Agere\Generation\Module;

use Zend\Code\Generator\ClassGenerator;

class ZendClassGenerator extends ClassGenerator{

    protected $content;

    /**
     * Attach a line or lines to the generated class
     * @param $content
     */
    public function setContentClass($content) {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContentClass() {
        return $this->content;
    }

    public function generate()
    {
        if (!$this->isSourceDirty()) {
            $output = $this->getSourceContent();
            if (!empty($output)) {
                return $output;
            }
        }

        $output = '';

        if (null !== ($namespace = $this->getNamespaceName())) {
            $output .= 'namespace ' . $namespace . ';' . self::LINE_FEED . self::LINE_FEED;
        }

        $uses = $this->getUses();
        if (!empty($uses)) {
            foreach ($uses as $use) {
                $output .= 'use ' . $use . ';' . self::LINE_FEED;
            }
            $output .= self::LINE_FEED;
        }

        if (null !== ($docBlock = $this->getDocBlock())) {
            $docBlock->setIndentation('');
            $output .= $docBlock->generate();
        }

        if ($this->isAbstract()) {
            $output .= 'abstract ';
        }

        $output .= 'class ' . $this->getName();

        if (!empty($this->extendedClass)) {
            $output .= ' extends ' . $this->extendedClass;
        }

        $implemented = $this->getImplementedInterfaces();
        if (!empty($implemented)) {
            $output .= ' implements ' . implode(', ', $implemented);
        }

        $output .= self::LINE_FEED . '{' . self::LINE_FEED . self::LINE_FEED;

        $classContent = $this->getContentClass();
        if (!empty($classContent)) {
            $output .= $this->indentation . $classContent . self::LINE_FEED . self::LINE_FEED;
        }

        $properties = $this->getProperties();
        if (!empty($properties)) {
            foreach ($properties as $property) {
                $output .= $property->generate() . self::LINE_FEED . self::LINE_FEED;
            }
        }

        $methods = $this->getMethods();
        if (!empty($methods)) {
            foreach ($methods as $method) {
                $output .= $method->generate() . self::LINE_FEED;
            }
        }

        $output .= self::LINE_FEED . '}' . self::LINE_FEED;

        return $output;
    }

}