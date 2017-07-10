<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/07/2017
 * Time: 10:28 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\WESTELM;


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

    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {
        $this->__getProductInfo();
        if (!is_null($this->productInfo) && isset($this->productInfo->results)) {
            $items = [];
            foreach ($this->productInfo->results as $productInfo) {

                $item = [];
                $item["Variant"] = new \stdClass();
                $item["Variant"]->text = $productInfo->sku_web_title;
                $item["Variant"]->value = $productInfo->internalid;
                $items[] = $item;
            }
            $this->options = $items;
        }
        return true;
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
            $productInfo = json_decode($this->content);
            if (!is_null($productInfo) && json_last_error() === JSON_ERROR_NONE) {
                $this->productInfo = array_first($productInfo);
                return true;
            }
        }
        return false;
    }
}