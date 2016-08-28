<?php

namespace Assurrussa\GridView\Presenters;

/**
 * Class PaginateListLinkPresenter
 *
 * @package Assurrussa\AmiCMS\Presenters
 */
class PaginateListLinkPresenter
{
    /**
     * @var array
     */
    public $html = [];


    /** * Convert the URL window into Zurb Foundation HTML.
     *
     * @return string
     */

    public function render()
    {
        if(!$this->hasPages()) {
            return '';
        }
        $this->getPreviousButton();
        $this->getLinks();
        $this->getNextButton();
        return $this->html;
    }

    /**
     * Get the links for the URLs in the given array.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        foreach($urls as $page => $url) {
            $li = $this->getPageLinkWrapper($url, $page);
            if($li) {
                $this->html[] = $li;
            }
        }
    }

    /**
     * Render the actual link slider.
     *
     * @return string
     */
    protected function getLinks()
    {
        if(is_array($this->window['first'])) {
            $this->getUrlLinks($this->window['first']);
        }

        if(is_array($this->window['slider'])) {
            $this->html[] = $this->getDots();
            $this->getUrlLinks($this->window['slider']);
        }

        if(is_array($this->window['last'])) {
            $this->html[] = $this->getDots();
            $this->getUrlLinks($this->window['last']);
        }
        return $this;
    }

    /**
     * Get the previous page pagination element.
     *
     * @param  string $text
     * @return string
     */
    public function getPreviousButton($text = '&laquo;')
    {
        $this->html[] = parent::getPreviousButton($text);
        return $this;
    }

    /**
     * Get the next page pagination element.
     *
     * @param  string $text
     * @return string
     */
    public function getNextButton($text = '&raquo;')
    {
        $this->html[] = parent::getNextButton($text);
        return $this;
    }

    /**
     * Get HTML wrapper for a page link.
     *
     * @param  string $url
     * @param  int    $page
     * @param  string $rel
     * @return string
     */
    public function getAvailablePageWrapper($url, $page, $rel = null)
    {
        //$rel = is_null($rel) ? '' : ' rel="' . $rel . '"';
        //return '<li><a href="' . $url . '"' . $rel . '>' . $page . '</a></li>';
        $rel = is_null($rel) ? '' : $rel;
        return [
            'status' => '',
            'text'   => '',
            'url'    => $url,
            'rel'    => $rel,
            'page'   => $page,
        ];

    }

    /**
     * Get HTML wrapper for disabled text.
     *
     * @param  string $text
     * @return string
     */
    public function getDisabledTextWrapper($text)
    {
        //return '<li class="disabled"><span>' . $text . '</span></li>';
        return [
            'status' => 'disabled',
            'text'   => $text
        ];
    }

    /**
     * Get HTML wrapper for active text.
     *
     * @param  string $text
     * @return string
     */
    public function getActivePageWrapper($text)
    {
        //return '<li class="active"><span>' . $text . '</span></li>';
        return [
            'status' => 'active',
            'text'   => $text
        ];
    }

}