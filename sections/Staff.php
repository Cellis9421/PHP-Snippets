<?php
include_once "ContentSection.php";
include_once "../builder/Buildable.php";

class Staff extends ContentSection implements Buildable
{

    function __construct($uid)
    {
        $this->uid = $uid;
        $this->name = "Staff";
        $this->tableName = "nastaff";
        $this->data = array();
        $this->populateData();
        return $this;
    }

    function buildPage(&$document, $tags)
    {
        if (!is_null($tags["staff-width"])) {
            $this->buildDynamicContentSection($document, $tags["staff-width"]);
        } else {
            $this->buildDynamicContentSection($document, 3); //apply data to a dynamic content section
        }
    }

}