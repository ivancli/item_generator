<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/07/2017
 * Time: 4:11 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\BESTBUYLIGHTING;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    const SELECT_OPTION_XPATH = '//*[@id="product-options-wrapper"]//select';
    const INPUT_OPTION_XPATH = '//*[@id="product-options-wrapper"]//*[@class="options-list"]';


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
            $selectOptions = $crawler->filterXPath(self::SELECT_OPTION_XPATH);
            $inputOptions = $crawler->filterXPath(self::INPUT_OPTION_XPATH);
            if ($selectOptions->count() > 0 || $inputOptions->count() > 0) {
                return true;
            }
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
            $crawler = new Crawler($this->content);
            $selectOptions = $crawler->filterXPath(self::SELECT_OPTION_XPATH);
            $items = [];
            $selectOptions->each(function (Crawler $selectOptionNode) use (&$items) {

                $attributeId = $selectOptionNode->attr('id');
                $attributeId = str_replace('select_', '', $attributeId);

                $item = new \stdClass();
                $item->label = $attributeId;
                $item->options = [];

                $optionNodes = $selectOptionNode->filter('option');
                $optionNodes->each(function (Crawler $optionNode) use (&$item) {
                    $text = $optionNode->text();
                    $value = $optionNode->attr('value');
                    if (!is_null($value) && !empty($value)) {
                        $newOption = new \stdClass();
                        $newOption->value = $value;
                        $newOption->text = $text;
                        $item->options[] = $newOption;
                    }
                });
                $items[] = $item;
            });

            $inputOptions = $crawler->filterXPath(self::INPUT_OPTION_XPATH);

            $inputOptions->each(function (Crawler $inputOptionNode) use (&$items) {
                $attributeId = $inputOptionNode->attr('id');
                $attributeId = str_replace('options-', '', $attributeId);
                $attributeId = str_replace('-list', '', $attributeId);

                $item = new \stdClass();
                $item->label = $attributeId;
                $item->options = [];
                $optionNodes = $inputOptionNode->filter('li');
                $optionNodes->each(function (Crawler $optionNode) use (&$item) {
                    /*get label*/
                    $labelNode = $optionNode->filter('span label')->first();
                    $text = "";
                    if (!is_null($labelNode)) {
                        $text = $labelNode->text();
                    }
                    $value = "";

                    $inputNode = $optionNode->filter('input')->first();
                    $value = "";
                    if (!is_null($inputNode)) {
                        $value = $inputNode->attr('value');
                    }

                    $newOption = new \stdClass();
                    $newOption->value = $value;
                    $newOption->text = $text;
                    $item->options[] = $newOption;
                });

                $items[] = $item;
            });

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