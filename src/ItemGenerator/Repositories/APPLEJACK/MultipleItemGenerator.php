<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 5/07/2017
 * Time: 2:14 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\APPLEJACK;

use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{
    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    /**
     * Set content, HTML most of the time
     * @param $content
     * @return mixed
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * check if content has multiple items
     * @return bool
     */
    public function hasMultipleItems()
    {
        $this->__getProductInfo();
        if (!is_null($this->productInfo)) {
            $items = array_first($this->productInfo->items);
            if (isset($items->matrixchilditems_detail)) {
                return true;
            }
        }
        return false;
    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {
        if ($this->hasMultipleItems()) {

            $matrixItems = array_first($this->productInfo->items);
            $items = [];
            if (isset($matrixItems->matrixchilditems_detail)) {
                foreach ($matrixItems->matrixchilditems_detail as $childItem) {

                    $item = [];
                    $item["Size"] = new \stdClass();
                    $item["Size"]->text = $childItem->custitem_aws_size;
                    $item["Size"]->value = $childItem->internalid;
                    $items[] = $item;
                }
            }
            $this->options = $items;
            return true;
        } else {
            return false;
        }
    }

    /**
     * returning structural options
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * returning a list of items need to be generated
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * combine multiple arrays
     * @param array $data
     * @param array $all
     * @param array $group
     * @param null $value
     * @param int $i
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
    {
        $this->items = $this->options;
        return $this->items;
    }

    private function __getProductInfo()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            $formattedInfo = json_decode($this->content);

            if (!is_null($formattedInfo) && json_last_error() === JSON_ERROR_NONE) {
                $this->productInfo = $formattedInfo;
                return $this->productInfo;
            }
        }
        return false;
    }
}