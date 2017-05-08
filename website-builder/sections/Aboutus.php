<?php
include_once "ContentSection.php";
include_once "../builder/Buildable.php";

class Aboutus extends ContentSection implements Buildable
{

    function __construct($uid)
    {
        $this->uid = $uid;
        $this->name = "Aboutus";
        $this->tableName = "naaboutus";
        $this->data = array();
        $this->populateData();
        return $this;
    }

    function buildPage(&$document, $tags)
    {
        if (!is_null($tags["aboutus-width"])) {
            $this->buildDynamicContentSection($document, $tags["aboutus-width"]);
        } else {
            $this->buildDynamicContentSection($document, 1); //apply data to a dynamic content section
        }
    }

}

?>