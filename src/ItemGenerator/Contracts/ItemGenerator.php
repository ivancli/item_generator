<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/26/2017
 * Time: 4:21 PM
 */
namespace IvanCLI\ItemGenerator\Contracts;

abstract class ItemGenerator
{
    /**
     * Set content, HTML most of the time
     * @param $content
     * @return mixed
     */
    abstract public function setContent($content);

    /**
     * check if content has multiple items
     * @return bool
     */
    abstract public function hasMultipleItems();

    /**
     * collect label and options from content
     * @return mixed
     */
    abstract public function extractOptions();

    /**
     * returning structural options
     * @return mixed
     */
    abstract public function getOptions();

    /**
     * returning a list of items need to be generated
     * @return mixed
     */
    abstract public function getItems();

    /**
     * combine multiple arrays
     * @param array $data
     * @param array $all
     * @param array $group
     * @param null $value
     * @param int $i
     * @return array
     */
    abstract public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0);
}