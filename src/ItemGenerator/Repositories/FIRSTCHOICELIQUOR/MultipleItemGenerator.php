<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 16/06/2017
 * Time: 2:43 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\FIRSTCHOICELIQUOR;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const PRICE_LIST_XPATH = '//*[@class="priceStockDetail"]//dt';
    const OPTION_LIST_XPATH = '//*[@class="priceStockDetail"]//dt';

    protected $content;
    protected $items;
    protected $options;

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
            $xpathNodes = $crawler->filterXPath(self::PRICE_LIST_XPATH);
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
            $crawler = new Crawler($this->content);
            $items = [];

            $element = "CONDITION";
            $item = new \stdClass();
            $item->label = $element;
            $item->options = [];
            $options = $crawler->filterXPath(self::OPTION_LIST_XPATH);
            $options->each(function (Crawler $option) use (&$item) {
                $newOption = new \stdClass();
                if (!is_null($option->text()) && !empty($option->text())) {
                    $newOption->text = $option->text();
                    $item->options[] = $newOption;
                }
            });
            $items[] = $item;
            $this->options = $items;
            return true;
        } else {
            return false;
        }

        ////*[@class="pricepoint-list"]/li[p/span[text()="per case of 6"]]/p/span[@class="price"]
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
        $data = array_first($data);
        $options = $data->options;
        $targetItems = [];
        $targetItem = [];
        foreach ($options as $option) {

            $attributes = new \stdClass();
            $attributes->value = $option->text;
            $attributes->text = $option->text;
            $targetItem[$data->label] = $attributes;
            $targetItems[] = $targetItem;
        }

        $this->items = $targetItems;
        return $this->items;
    }
}