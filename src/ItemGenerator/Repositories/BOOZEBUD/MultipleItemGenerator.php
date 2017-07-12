<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/07/2017
 * Time: 2:27 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\BOOZEBUD;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{
    const PRODUCT_INFO_XPATH = '//*[@data-product_variations]';
    const PRODUCT_OPTIONS_XPATH = '//*[@id="pa_model"]/option';

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

        $items = [];
        if (!is_null($this->productInfo) && isset($this->productInfo->variants)) {
            foreach ($this->productInfo->variants as $variant) {
                $item = [];
                $item['variant'] = new \stdClass();
                $item['variant']->text = "{$variant->prefix} of {$variant->qty}";
                $item['variant']->value = "{$variant->sku}";
                $items[] = $item;
            }
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
            }
        }
    }
}