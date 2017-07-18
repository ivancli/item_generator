<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 18/07/2017
 * Time: 11:48 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\TOMTOP;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{
    const MULTIPLE_ITEMS_DETECT_XPATH = '//*[@itemtype="http://schema.org/Product"]';

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
        if (!is_null($this->productInfo)) {
            $items = [];
            foreach ($this->productInfo as $product) {
                $whouse = $product->whouse;
                foreach ($whouse as $index => $country) {
                    $item = [];
                    $item["Variant"] = new \stdClass();
                    $item["Variant"]->text = $product->sku . ":" . $country->depotName;
                    $item["Variant"]->value = $product->sku . ":" . $country->depotName;
                    $items[] = $item;
                }
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