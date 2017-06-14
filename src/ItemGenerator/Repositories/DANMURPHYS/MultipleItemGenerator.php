<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 14/06/2017
 * Time: 4:52 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\DANMURPHYS;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const PRICE_LIST_XPATH = '//ul[@class="pricepoint-list"]/li';
    const OPTION_LIST_XPATH = '//ul[@class="pricepoint-list"]/li/p/span[2]';

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
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
    {
        dd($data);
        $keys = array_keys($data);
        if (isset($value) === true) {
            if (!is_null($label)) {
                array_set($group, $label, $value);
            } else {
                array_push($group, $value);
            }
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];
            foreach ($currentElement->options as $val) {
                $this->combinations($data, $all, $group, $val, $i + 1, $currentElement->label);
            }
        }

        $this->items = $all;
    }
}