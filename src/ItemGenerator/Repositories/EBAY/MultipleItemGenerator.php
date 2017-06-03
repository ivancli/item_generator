<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 2/06/2017
 * Time: 11:31 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\EBAY;


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

        if (json_decode($content) == null && json_last_error() !== JSON_ERROR_NONE) {
            $this->content = $content;
        } else {
            $this->content = json_decode($content);
        }
    }

    /**
     * check if content has multiple items
     * @return bool
     */
    public function hasMultipleItems()
    {
        if (is_object($this->content)) {
            return isset($this->content->items);
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
            $localizedAspects = collect();

            foreach ($this->content->items as $item) {
                if (isset($item->localizedAspects)) {
                    $localizedAspect = array_pluck($item->localizedAspects, 'value', 'name');

                    $localizedAspects->push($localizedAspect);
                }
            }

            $firstLocalizedAspect = $localizedAspects->first();
            $diffKeys = collect();

            foreach ($localizedAspects as $localizedAspect) {
                foreach ($localizedAspect as $key => $value) {
                    if (!array_has($firstLocalizedAspect, $key) || array_get($firstLocalizedAspect, $key) != $value) {
                        $diffKeys->push($key);
                    }
                }
            }

            $this->options = $diffKeys->unique()->all();
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
        $targetItems = [];

        foreach ($this->content->items as $item) {
            $targetItem = [];

            $localizedAspects = array_pluck($item->localizedAspects, 'value', 'name');
            foreach ($data as $option) {
                $attributes = new \stdClass();
                $attributes->value = $item->itemId;
                $attributes->text = array_get($localizedAspects, $option);
                $targetItem[$option] = $attributes;
            }
            $targetItems[] = $targetItem;
        }
        $this->items = $targetItems;
        return $this->items;
    }
}