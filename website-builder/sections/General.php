<?php
include_once "ContentSection.php";
include_once "../builder/Buildable.php";

class General extends ContentSection implements Buildable
{

    function __construct($uid)
    {
        $this->uid = $uid;
        $this->name = "General";
        $this->tableName = "nageneral";
        $this->data = array();
        $this->populateData();
        return $this;
    }

    function buildPage(&$doc, $tags)
    {
        $this->buildPageMetaData($doc);

        $finder = new DomXPath($doc);

        foreach ($this->data[0] as $field => $value) {
            if ($field == 'id' || $field == 'uid')
                continue;

            $searchTag = strtolower($this->name) . '-' . $field; // 'general-field'
            echo "<br>SEARCHING FOR TAG: " . $searchTag . "<br>";

            //find by class 'general-field' to set values
            //switch on field to set different attributes for different field types
            foreach ($finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $searchTag ')]") as $element) {
                switch ($field) {
                    case 'email':
                        $element->setAttribute('href', "mailto:" . $value);
                    default:
                        $element->nodeValue = $value;
                        break;
                }
            }
        }
    }

    /**
     *
     * Used to build page data that needs to be accessed directly
     *
     * @param DOMDocument $doc - the document to build to
     */
    function buildPageMetaData(&$doc)
    {
        //set title
        $fieldNodes = $doc->getElementsByTagName('title'); //lowercase section name

        foreach ($fieldNodes as $node) {
            $node->nodeValue = $this->data[0]->organization;
        }

    }

}