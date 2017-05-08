<?php
use WHMCS\Database\Capsule;

include_once "/home1/newageon/public_html/whmcs/init.php";

/**
 *
 * Data object for storing template information relevant to building.
 *
 * @author Calvin Ellis - 3/7/2017
 * New-Age Solutions Inc.
 */
final class Template
{
    private $id;
    private $name;
    private static $path;
    private $data;
    private $sections;

    function __construct($id)
    {
        $this->id = $id;
        try {
            $response = Capsule::table('natemplates')
                ->select('name', 'general', 'aboutus', 'portfolio', 'staff', 'faq') //add new sections here
                ->where('id', '=', $this->id)
                ->get();

            if ($response) {
                $this->sections = array();
                foreach ($response as $rowIndex => $rowData) {
                    foreach ($rowData as $section => $value) {
                        if ($section == "name") {
                            $this->name = $value;
                            continue;
                        }
                        /* Priority: 3 = disabled; 2 = available; 1 = recommended; 0 = required; */
                        $priority = intval($value);
                        if ($priority < 3) { //if its available
                            $this->data[$section] = $value;
                            array_push($this->sections, $section);
                        }
                    }
                }
                echo "<br>Template Name: <h2>" . $this->getName() . "</h2><br>";
                echo "<br>Template Sections: <br>";
                foreach ($this->sections as $section) {
                    echo $section . "<br>";
                }
            }
            echo "<br>Finished Template Object Creation!<br>";
        } catch (Exception $e) {
            echo "<br>Failed Template Object Creation:" . $e->getMessage() . "<br>";
        }
    }

    function getId()
    {
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }

    function getFullPath()
    {
        return "/home1/newageon/templates/" . $this->name;
    }

    function getSections()
    {
        return $this->data;
    }
}