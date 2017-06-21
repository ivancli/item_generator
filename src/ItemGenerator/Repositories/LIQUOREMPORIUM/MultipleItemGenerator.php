<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 21/06/2017
 * Time: 3:59 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\LIQUOREMPORIUM;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    const LD_JSON_XPATH = '//script[@type="application/ld+json"]';

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
        $this->getAppData();
        return count($this->productInfo->managedProductItems) > 0;
    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {
        if ($this->hasMultipleItems()) {
            $items = [];
            $options = array_first($this->productInfo->options->options);
            foreach ($options->selections as $product) {
                $item = [];
                $item['Variant'] = new \stdClass();
                $item['Variant']->text = $product->value;
                $item['Variant']->value = $product->id;
                $items[] = $item;
            }
            $this->options = $items;
            return true;
        }
        return false;
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

    protected function getAppData()
    {
        preg_match('#\'productPageApp\', (.*?), \'#', $this->content, $matches);
        if (isset($matches[1])) {
            $appData = json_decode($matches[1]);
            if (!is_null($appData) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($appData->appData) && isset($appData->appData->productPageData) && isset($appData->appData->productPageData->product)) {
                    $this->productInfo = $appData->appData->productPageData->product;
                }
            }
        }
    }
}