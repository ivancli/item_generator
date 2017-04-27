<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/27/2017
 * Time: 2:47 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\DAVOLUCELIGHTING;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const LABEL_XPATH = "//*[starts-with(@name, 'product_options')]/../preceding-sibling::*";
    const SELECT_XPATH = "//*[starts-with(@name, 'product_options')]";
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
            $xpathNodes = $crawler->filterXPath(self::SELECT_XPATH);
            return $xpathNodes->count() > 0;
        }
        return false;
    }

    /**
     * collect label and options from content
     * @return boolean
     */
    public function extractOptions()
    {
        if ($this->hasMultipleItems()) {
            //fetch label
            $crawler = new Crawler($this->content);
            $labelNodes = $crawler->filterXPath(self::LABEL_XPATH);
            $items = array();

            $labelNodes->each(function (Crawler $labelNode, $i) use (&$items) {
                $extraction = $labelNode->text();
                $extraction = str_replace('Select', '', $extraction);
                $item = new \stdClass();
                $item->label = trim($extraction);
                $item->options = [];
                $options = $labelNode->siblings()->filter('select option');
                $options->each(function (Crawler $option) use (&$item) {
                    $newOption = new \stdClass();
                    if (!is_null($option->attr('value')) && !empty($option->attr('value'))) {
                        $newOption->value = $option->attr('value');
                        $newOption->text = $option->text();
                        $item->options[] = $newOption;
                    }
                });
                $items[] = $item;
            });
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