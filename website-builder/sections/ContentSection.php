<?php
use WHMCS\Database\Capsule;

include_once '../../init.php';

/**
 *
 * This is the parent class to all content sections
 *
 * @author Calvin Ellis - 3/7/2017
 * New-Age Solutions Inc.
 */
class ContentSection
{

    protected $uid;
    protected $name;
    protected $tableName;
    protected $buildable = false; //for navigation. toggled true when data is found
    protected $priority;
    protected $isPopulated;

    /** 2D Array for data storage $data[index][contentName] = content */
    protected $data;


    /**
     * Populates the ContentSections $data variable with the database information
     * To be called in child-class constructor after tableName is assigned
     */
    function populateData()
    {
        /* query db */
        $response = Capsule::table($this->tableName)
            ->where('uid', $this->uid)
            ->get();

        /* handle response */
        if ($response) {
            $this->buildable = true;
            foreach ($response as $row => $rowData) {
                array_push($this->data, $rowData);
            }
            return true;
        }
        return false;
    }

    /**
     * Builds a content sections data in a dynamic display using the given doc as a template/output.
     * Finds the contentsection tag within the given doc, and populates it with the child setions data with the given index width
     *
     * @param   DOMDocument $doc Document to build content section to, passed by refernce
     * @param   int $indexWidth Desired width of the contentsection (i.e. 3 for staff, 2 for faq, 1 for portfolio)
     */
    function buildDynamicContentSection(&$doc, $indexWidth)
    {
        $sectionNode = $doc->getElementsByTagName(strtolower($this->name)); //get the <contentsection> tag

        if ($sectionNode->length > 0 && count($this->data) > 0) {
            $indexesToBuild = count($this->data);
            echo $indexesToBuild . " indexes to insert with a width of " . $indexWidth . "<br>";

            //Assign node elements to be built
            $parentNode = $sectionNode->item(0); //<contentsection> the main tag for dynamic content
            $currentRow = $parentNode->firstChild; //set first row, add more later if needed
            $baseRow = $currentRow->cloneNode(false); //clone row div element, not children elements
            $baseNode = $currentRow->firstChild->cloneNode(true); //clone index template

            //remove template from page now that we have cloned it
            $currentRow->removeChild($currentRow->firstChild);

            if ($indexesToBuild >= $indexWidth) {
                $rows = floor($indexesToBuild / $indexWidth);
                echo "<br>Rows: ", $rows, "<br>";
            }

            $remainder = fmod($indexesToBuild, $indexWidth);
            echo "Remainders: ", $remainder, "<br>";

            $buildIndex = 0;

            //Build full rows first
            if ($rows > 0) {
                for ($rowcount = 0; $rowcount < $rows; $rowcount++) {
                    for ($i = 0; $i < $indexWidth; $i++) {
                        $nodeClone = $this->buildSection($this->data[$buildIndex], $baseNode)->cloneNode(true);
                        $currentRow->appendChild($doc->importNode($nodeClone, true));
                        $buildIndex++;
                    }
                    //after full row is built, start new row if needed
                    if ($buildIndex < $indexesToBuild) {
                        $currentRow = $baseRow->cloneNode(false);
                        $parentNode->appendChild($currentRow);  //add new active row to parent node <portfolio>
                    }
                }
            }

            //Build remaining items
            if ($remainder == 1) {
                $nodeClone = $this->buildSection($this->data[$buildIndex], $baseNode)->cloneNode(true);
                $currentRow->appendChild($doc->importNode($nodeClone, true));
                $buildIndex++;
            } else if ($remainder == 2) {
                for ($i = 0; $i < $remainder; $i++) {
                    //apply $this->data[$buildIndex] to $sectionSource
                    $nodeClone = $this->buildSection($this->data[$buildIndex], $baseNode)->cloneNode(true);
                    $currentRow->appendChild($doc->importNode($nodeClone, true));
                    $buildIndex++;
                }
            }
        }
    }


    /**
     *
     * Build an individual section of html using a $base template,
     * @param   ArrayObject $data $field=>$value pairs of data to be input to section
     * @param   DOMNode $base The base DOMTree to apply $data to
     *
     * @return  DOMNode The cloned and populated $base node
     */
    function buildSection($data, $base)
    {
        $docfrag = new DOMDocument('1.0', 'utf-8');
        $docfrag->appendChild(
            $docfrag->importNode($base->cloneNode(true), true)
        );
        $finder = new DomXPath($docfrag);

        foreach ($data as $field => $value) {
            if ($field == 'id' || $field == 'uid')
                continue;

            $searchTag = strtolower($this->name) . '-' . $field; // 'contentsection-field'
            echo "<br>SEARCHING FOR TAG: " . $searchTag . "<br>";

            //find by tag <contentsection-field>
            foreach ($docfrag->getElementsByTagName($searchTag) as $tagElement) {
                $tagElement->nodeValue = $value;
            }

            //find by class 'contentsection-field' to set values
            //switch on field to set different attributes for different field types
            foreach ($finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $searchTag ')]") as $element) {
                switch ($field) {
                    case 'link':
                        $element->setAttribute("href", $value);
                        break;
                    default:
                        $element->nodeValue = $value;
                        break;
                }
            }
        }
        echo $docfrag->saveHTML();
        return $docfrag->documentElement;
    }

    /* public getters */
    public function getData()
    {
        return $this->data;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function isBuildable()
    {
        return $this->buildable;
    }

}

?>