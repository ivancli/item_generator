<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/26/2017
 * Time: 4:21 PM
 */
namespace IvanCLI\ItemGenerator\Contracts;

interface ItemGenerator
{
    /**
     * Set content, HTML most of the time
     * @param $content
     * @return mixed
     */
    public function setContent($content);

    /**
     * check if content has multiple items
     * @return bool
     */
    public function hasMultipleItems();

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions();

    /**
     * returning structural options
     * @return mixed
     */
    public function getOptions();
}