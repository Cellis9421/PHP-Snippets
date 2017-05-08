<?php
include_once "ContentSection.php";
include_once "../builder/Buildable.php";

class FAQ extends ContentSection implements Buildable
{

    function __construct($uid)
    {
        $this->uid = $uid;
        $this->name = "FAQ";
        $this->tableName = "nafaq";
        $this->data = array();
        $this->populateData();
        return $this;
    }

    function buildPage(&$document, $tags)
    {
        if (!is_null($tags["faq-width"])) {
            $this->buildDynamicContentSection($document, $tags["faq-width"]);
        } else {
            $this->buildDynamicContentSection($document, 2); //apply data to a dynamic content section
        }
    }

}