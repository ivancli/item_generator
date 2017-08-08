<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/08/2017
 * Time: 2:32 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\WEGO;


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
        if (!is_null($this->productInfo) && isset($this->productInfo->trip) && isset($this->productInfo->trip->fares) && is_array($this->productInfo->trip->fares)) {
            $items = [];
            foreach ($this->productInfo->trip->fares as $fare) {
                $item = [];
                $item["Variant"] = new \stdClass();
                $item["Variant"]->text = $fare->provider->name;
                $item["Variant"]->value = $fare->provider->code;
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
                $this->productInfo = $productInfo;
                return true;
            }
        }
        return false;
    }
}