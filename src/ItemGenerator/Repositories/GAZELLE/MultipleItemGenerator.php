<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/08/2017
 * Time: 1:08 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\GAZELLE;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $products;

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
        $items = [];

        $brokenPowerOn = [];
        $brokenPowerOn['Condition'] = new \stdClass();
        $brokenPowerOn['Condition']->text = 'Broken with power on';
        $brokenPowerOn['Condition']->value = 'broken_power_on';

        $brokenPowerOff = [];
        $brokenPowerOff['Condition'] = new \stdClass();
        $brokenPowerOff['Condition']->text = 'Broken with power off';
        $brokenPowerOff['Condition']->value = 'broken_power_off';

        $good = [];
        $good['Condition'] = new \stdClass();
        $good['Condition']->text = 'Good';
        $good['Condition']->value = 'good';

        $flawless = [];
        $flawless['Condition'] = new \stdClass();
        $flawless['Condition']->text = 'Flawless';
        $flawless['Condition']->value = 'flawless';

        $items[] = $brokenPowerOn;
        $items[] = $brokenPowerOff;
        $items[] = $good;
        $items[] = $flawless;
        $this->options = $items;
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
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
    {
        $this->items = $this->options;
        return $this->items;
    }
}