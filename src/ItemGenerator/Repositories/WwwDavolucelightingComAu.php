<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/26/2017
 * Time: 4:29 PM
 */

namespace IvanCLI\ItemGenerator\Repositories;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class WwwDavolucelightingComAu implements ItemGenerator
{
    const LABEL_XPATH = "//*[starts-with(@name, 'product_options')]/../preceding-sibling::*";
    const SELECT_XPATH = "//*[starts-with(@name, 'product_options')]";
    protected $content;

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
        if (!is_null($this->content) && empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::SELECT_XPATH);
            return $xpathNodes->count() > 0;
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
            //fetch label
            $crawler = new Crawler($this->content);
            $labelNodes = $crawler->filterXPath(self::LABEL_XPATH);



        } else {
            return null;
        }
    }

    /**
     * returning structural options
     * @return mixed
     */
    public function getOptions()
    {
        // TODO: Implement getOptions() method.
    }

}