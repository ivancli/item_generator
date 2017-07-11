<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/07/2017
 * Time: 5:44 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\BRIGHTLIGHTING;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const PRODUCT_INFO_XPATH = '//*[@data-product_variations]';
    const PRODUCT_OPTIONS_XPATH = '//*[@id="pa_model"]/option';

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;
    protected $productOptions;

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
        $this->__getProductOptions();
        if (count($this->productOptions) > 0) {
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
            $items = [];
            foreach ($this->productOptions as $productOption) {
                $productInfo = collect($this->productInfo);
                $matchedOption = $productInfo->filter(function ($option) use ($productOption) {
                    if (isset($option->attributes) && isset($option->attributes->attribute_pa_model)) {
                        return $option->attributes->attribute_pa_model === $productOption->value;
                    }
                    return false;
                })->first();

                $item = [];
                $item['variant'] = new \stdClass();
                if (isset($matchedOption->attributes) && isset($matchedOption->attributes->attribute_pa_model)) {
                    $item['variant']->text = $matchedOption->attributes->attribute_pa_model;
                    $item['variant']->value = $matchedOption->variation_id;
                }
                $items[] = $item;
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
            $crawler = new Crawler($this->content);
            $productInfoNodes = $crawler->filterXPath(self::PRODUCT_INFO_XPATH);
            if ($productInfoNodes->count() > 0) {
                $productInfoNode = $productInfoNodes->first();
                $productInfo = $productInfoNode->attr('data-product_variations');
                $productInfo = html_entity_decode($productInfo);

                $formattedInfo = json_decode($productInfo);
                if (!is_null($formattedInfo) && json_last_error() === JSON_ERROR_NONE) {
                    $this->productInfo = $formattedInfo;
                    return $this->productInfo;
                }
            }
        }
        return false;
    }

    private function __getProductOptions()
    {
        $this->productOptions = [];
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $optionNodes = $crawler->filterXPath(self::PRODUCT_OPTIONS_XPATH);
            $optionNodes->each(function (Crawler $optionNode) {
                $value = $optionNode->attr("value");
                $text = $optionNode->text();
                if (!is_null($value) && !empty($value)) {
                    $option = new \stdClass();
                    $option->value = $value;
                    $option->text = $text;
                    $this->productOptions[] = $option;
                }
            });
        }
    }
}