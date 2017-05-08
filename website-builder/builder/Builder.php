<?php
include_once "../init.php";
include_once "Template.php";


/** Include all sections */
foreach (glob("sections/*.php") as $filename) {
    include $filename;
}

/**
 *
 * This is the main controller class for the building system.
 *
 * @author Calvin Ellis - 3/7/2017
 * New-Age Solutions Inc.
 */
final class Builder
{
    private $uid;
    private $sectionData; //$sectionData[$section] = ContentSection;
    private $navbar;
    private $template;
    private $rootDir;
    private $userDir;
    private $saveDir;

    /**
     *
     * Used to populate object with data required for website building.
     *
     * @param   int $uid UUID of user data input
     * @param   int $templateId Template UID to build website from
     *
     * @return Builder $this    Newly created object instance
     */
    function __construct($uid, $templateId)
    {
        $this->uid = $uid; //UUID for database queries

        $this->template = new Template($templateId); //template to build to

        /** Get section data for required sections  */
        $this->getSectionData($this->template->getSections()); //populates $sectionData

        /** Set up Navigation bar*/
        $this->navbar = new Navigation();
        $this->populateNavData();

        /** Set paths for loading/saving */
        $this->rootDir = $this->template->getFullPath() . "/"; //template root dir
        $this->userDir = '/home1/newageon/public_html/userdata/' . $this->uid; //public user data storage
        $this->saveDir = $this->userDir . "/" . $this->template->getName(); //template save data

        return $this;
    }


    /**
     *
     * Populates $sectionData based off the required $templateSections
     *
     * @param   ArrayObject $sections Array of section names to get data for
     */
    function getSectionData($sections)
    {
        $this->sectionData = array();
        foreach ($sections as $section => $priority) {
            //add new sections here to assign their object
            switch ($section) {
                case 'general':
                    $this->sectionData[$section] = new General($this->uid);
                    break;
                case 'portfolio':
                    $this->sectionData[$section] = new Portfolio($this->uid);
                    break;
                case 'staff':
                    $this->sectionData[$section] = new Staff($this->uid);
                    break;
                case 'faq':
                    $this->sectionData[$section] = new Faq($this->uid);
                    break;
                case 'aboutus':
                    $this->sectionData[$section] = new Aboutus($this->uid);
                    break;
            }
            echo "<br>Made " . $this->sectionData[$section]->getName() . " Object";
            //if not required and not populated, don't build it
            if (!$this->sectionData[$section]->isBuildable()) {
                echo $section . " is not buldable...<br>";
                unset($this->sectionData[$section]);
            }

        }
        echo "<br>Section data successfully populated!";
    }

    /**
     *
     * Build the HTML files at the given root directory with all of the builder's data
     */
    function build()
    {
        //copy template files
        $this->copyr($this->rootDir, $this->saveDir);

        /** foreach html file in the savedir, build the page
         * glob() : http://php.net/manual/en/function.glob.php */
        foreach (glob($this->saveDir . "/*.html") as $file) {
            echo "<br>File: " . $file . "<br>";
            $doc = Builder::loadDocument($file);

            $tags = get_meta_tags($file);

            $this->navbar->buildPage($doc, $tags); //build navbar to page

            foreach ($this->sectionData as $sectionObject) {
                /* call Buildable::buildPage() on each Buildable section in the array; */
                $sectionObject->buildPage($doc, $tags);
            }
            Builder::saveDocument($doc, $this->saveDir . "/" . basename($file));
        }
    }

    /**
     *
     * Builds the Navigation section of the website
     *
     */
    function populateNavData()
    {
        foreach ($this->sectionData as $section) {
            echo "<br>Found data for " . $section->getName();
            switch (strtolower($section->getName())) {
                case 'general':
                    break;
                case 'portfolio':
                    $this->navbar->addNavItem("Portfolio", "#portfolio", "portfolionav");
                    break;
                case 'staff':
                    $this->navbar->addNavItem("Our Staff", "#staff", "staffnav");
                    break;
                case 'faq':
                    $this->navbar->addNavItem("FAQ", "#faq", "faqnav");
                    break;
                case 'aboutus':
                    $this->navbar->addNavItem("About Us", "#aboutus", "aboutusnav");
                    break;
            }
        }
        //append "contact us"
        $this->navbar->addNavItem("Contact Us", "#contactus", "contactusnav");
    }

    /**
     *
     * Saves a DOMDoc to the $path location
     * @param   DOMDoc $doc The DOMDoc to save
     * @param   string $path Location the document will be saved to
     * @return bool
     */
    public static function saveDocument($doc, $path)
    {
        $bytes = $doc->saveHTMLFile($path);
        echo "Saved " . $bytes . " bytes to " . $path;
        return true;
    }

    /**
     *
     * Saves a DOMDoc to the $path location
     * @param   string $path Location of the document you want to load
     */
    public static function loadDocument($path)
    {
        $document = new DOMDocument();
        $document->loadHTMLFile($path);
        echo "Loaded " . filesize($path) . " bytes from " . $path . "<br>";
        return $document;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string $source Source path
     * @param       string $dest Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    function copyr($source, $dest)
    {
        echo "<br>Copying " . $source . " to " . $dest;
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->copyr("$source/$entry", "$dest/$entry");
        }

        // Clean up
        $dir->close();
        return true;
    }
}


?>