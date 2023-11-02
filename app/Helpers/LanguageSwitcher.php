<?php

namespace App\Helpers;

class LanguageSwitcher
{
    private $active;
    private $main;
    private $values = [];

    public function addValue(LinkItem $linkItem)
    {
    	$this->values[] = $linkItem;
    }

    public function getValues()
    {
    	return $this->values;
    }

    public function getActive()
    {
    	return $this->active;
    }

    public function setActive(LinkItem $linkItem)
    {
    	$this->active = $linkItem;
    }

    public function getMain()
    {
    	return $this->main;
    }

    public function setMain(LinkItem $linkItem)
    {
    	$this->main = $linkItem;
    }
}
