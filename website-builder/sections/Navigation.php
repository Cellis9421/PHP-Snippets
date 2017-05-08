<?php
include_once "ContentSection.php";
include_once "../builder/Buildable.php";

class Navigation extends ContentSection implements Buildable
{

    function __construct()
    {
        $this->name = "Navigation";
        $this->data = array();
        return $this;
    }

    public function buildPage(&$doc, $tags)
    {
        $navParentNode = $doc->getElementById('navlist'); //get <ul> element
        if (!$navParentNode) {
            return;
        }
        foreach ($this->data as $navItem) {
            $nodeLi = $doc->createElement("li");
            $node = $doc->createElement("a");

            $node->setAttribute('href', $navItem["href"]);
            $node->setAttribute('id', $navItem["id"]);
            $node->nodeValue = $navItem["name"];

            $nodeLi->appendChild($node); //add <a> to <li>
            $navParentNode->appendChild($nodeLi); //add <li> to <ul>
        }
        echo "<br>Finished building Navigation!";
    }

    public function addNavItem($name, $href, $id)
    {
        echo "<br>Adding " . $name . " to Navigation bar";
        array_push($this->data, array("name" => $name, "href" => $href, "id" => $id));
    }

}