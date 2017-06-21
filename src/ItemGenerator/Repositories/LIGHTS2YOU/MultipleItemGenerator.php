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

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    const LABELS_XPATH = '//dt/label';
    const SELECTS_XPATH = '//*[contains(@class, "product-custom-option")]';
    const OPTIONS_XPATH = '//option[@value!=""]';
    const SELECTS_CONTAINER_XPATH = '//*[@id="product-options-wrapper"]//dl';


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
//        if (!is_null($this->productInfo) && count($this->productInfo) > 1) {
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
        $crawler = new Crawler($this->content);

        $selectContainers = $crawler->filterXPath(self::SELECTS_CONTAINER_XPATH);

        if ($selectContainers->count() > 0) {
            $selectContainer = $selectContainers->first();
            $labelNodes = $selectContainer->filterXPath(self::LABELS_XPATH);
            $selectNodes = $selectContainer->filterXPath(self::SELECTS_XPATH);
            $items = [];

            $selectNodes->each(function (Crawler $selectNode, $index) use ($labelNodes, &$items) {
                $item = new \stdClass();
                $labelNode = $labelNodes->getNode($index);
                $item->label = $labelNode->textContent;
                $item->options = [];

                $optionNodes = $selectNode->filterXPath(self::OPTIONS_XPATH);
                $optionNodes->each(function (Crawler $optionNode) use (&$item) {
                    $newOption = new \stdClass();
                    $newOption->text = $optionNode->text();
                    $newOption->value = $optionNode->attr("value");
                    $item->options[] = $newOption;
                });
                $items[] = $item;
            });
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
     * @param null $label
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0, $label = null)
    {
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