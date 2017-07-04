<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/07/2017
 * Time: 12:56 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\GOLIGHTS;

use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $products;

    const PRODUCT_OPTIONS_XPATH = '//*[@class="product__summary"]//div[@itemprop="offers"][not(contains(@class, "offer-info"))]';
    const SKU_XPATH = '//*[@itemprop="sku"]/@content';

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
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::PRODUCT_OPTIONS_XPATH);
            return $xpathNodes->count() > 1;
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
            if (!is_null($this->content) && !empty($this->content)) {
                $crawler = new Crawler($this->content);
                $xpathNodes = $crawler->filterXPath(self::PRODUCT_OPTIONS_XPATH);
                $items = [];
                $xpathNodes->each(function (Crawler $option) use (&$items) {
                    $skuNodes = $option->filterXPath(self::SKU_XPATH);
                    foreach ($skuNodes as $skuNode) {
                        if ($skuNode->nodeValue) {
                            $sku = $skuNode->nodeValue;
                        } else {
                            $sku = $skuNode->textContent;
                        }
                        $item = [];
                        $item['SKU'] = new \stdClass();
                        $item['SKU']->text = $sku;
                        $item['SKU']->value = $sku;
                        $items[] = $item;
                    }
                });
                $this->options = $items;
            }
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
}