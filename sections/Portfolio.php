<?php
include_once "ContentSection.php";
include_once "../builder/Buildable.php";

class Portfolio extends ContentSection implements Buildable
{

    function __construct($uid)
    {
        $this->uid = $uid;
        $this->name = "Portfolio";
        $this->tableName = "naportfolio";
        $this->data = array();
        $this->populateData();
        return $this;
    }

    /**
     * Builds Portfolio content section in a boostrap style 3-coloum fashion
     *
     * @see Buildable::buildPage()
     */
    function buildPage(&$document, $tags)
    {

        if (!is_null($tags["portfolio-width"])) {
            $this->buildDynamicContentSection($document, $tags["portfolio-width"]);
        } else {
            $this->buildDynamicContentSection($document, 3); //apply data to a dynamic content section
        }
    }


}