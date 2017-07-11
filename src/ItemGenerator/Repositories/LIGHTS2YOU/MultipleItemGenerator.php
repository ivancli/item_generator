<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 21/06/2017
 * Time: 1:40 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\LIGHTS2YOU;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    const PRODUCT_INFO_REGEX = '#var spConfig = new Product.Config\((.*?)\);#';


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
        if (!is_null($this->productInfo) && isset($this->productInfo->attributes) && count($this->productInfo->attributes) > 0) {
            return true;
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
            /*collecting product ids*/
            $productIds = [];
            foreach ($this->productInfo->attributes as $attribute) {
                $options = $attribute->options;
                foreach ($options as $option) {
                    $products = $option->products;
                    foreach ($products as $product) {
                        if (!in_array($product, $productIds)) {
                            $productIds[] = $product;
                        }
                    }
                }
            }

            $items = [];

            foreach ($productIds as $productId) {
                $item = [];
                foreach ($this->productInfo->attributes as $attribute) {
                    foreach ($attribute->options as $option) {
                        if (in_array($productId, $option->products)) {
                            if (!isset($item[$attribute->label])) {
                                $item[$attribute->label] = new \stdClass();
                            }
                            $item[$attribute->label]->text = $option->label;
                            $item[$attribute->label]->value = $option->id;
                            break;
                        }
                    }
                }
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
     * @param null $label
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0, $label = null)
    {
        $this->items = $this->options;
        return $this->items;
    }

    private function __getProductInfo()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            preg_match(self::PRODUCT_INFO_REGEX, $this->content, $matches);
            if (isset($matches[1])) {
                $productInfo = json_decode($matches[1]);
                if (!is_null($productInfo) && json_last_error() === JSON_ERROR_NONE) {
                    $this->productInfo = $productInfo;
                    return $this->productInfo;
                }
            }
        }
        return null;
    }
}