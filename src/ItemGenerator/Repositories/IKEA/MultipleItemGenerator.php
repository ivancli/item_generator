<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 19/06/2017
 * Time: 2:16 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\IKEA;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{
    const PRODUCT_DATA_REGEX = '#var jProductData \= (.*?)\;#';

    protected $content;
    protected $items;
    protected $options;

    protected $products = [];
    protected $attributes = [];

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
//        $this->__getProductInfo();
//        if (count($this->products) > 1) {
//            return true;
//        }
//        return false;
    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {
        $this->__getProductInfo();
        $items = [];
        foreach ($this->products as $product) {
            $text = "";
            foreach ($product->validDesign as $index => $design) {
                if (count($this->attributes) > 0 && isset($this->attributes[$index])) {
                    $text .= "{$this->attributes[$index]->name}: $design; ";
                }
            }
            $value = $product->partNumber;

            $item = [];
            $item['Variant'] = new \stdClass();
            $item['Variant']->text = $text;
            $item['Variant']->value = $value;
            $items[] = $item;

        }
        $this->options = $items;
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
     * @param null $label
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0, $label = null)
    {
        $this->items = $this->options;
        return $this->items;
    }

    public function __getProductInfo()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            preg_match(self::PRODUCT_DATA_REGEX, $this->content, $matches);
            if (isset($matches[1])) {
                $productInfo = json_decode($matches[1]);
                if (!is_null($productInfo) && json_last_error() === JSON_ERROR_NONE) {
                    $productInfo = $productInfo->product;
                    $this->attributes = $productInfo->attributes;
                    $this->products = $productInfo->items;
                }
            }
        }
    }
}