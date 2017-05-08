<?php

/**
 *
 * This interface is to make a class buildable by the controller.
 *
 * @author Calvin Ellis - 3/7/2017
 * New-Age Solutions Inc.
 */
interface Buildable
{

    /**
     *
     * Builds the given document using the given tags. Pass by reference. Called by controller to build sections.
     * @param   DOMDocument $doc UUID of user data input
     * @param   ArrayObject $tags The tags to build into, handled uniquely by each section
     */
    public function buildPage(&$doc, $tags);
}