<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Peter Proell <peter@alinbu.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   50: class tx_yiidlike_pi1 extends tslib_pibase
 *   67:     function main($content, $conf)
 *   89:     protected function init()
 *  141:     protected function fetchConfigurationValue($param)
 *  151:     protected function getWidth()
 *  166:     protected function showButton()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Yiid it! Button' for the 'yiid_like' extension.
 *
 * @author	Peter Proell <peter@alinbu.net>
 * @package	TYPO3
 * @subpackage	tx_yiidlike
 */
class tx_yiidlike_pi1 extends tslib_pibase {

    var $prefixId = 'tx_yiidlike_pi1';
    // Same as class name
    var $scriptRelPath = 'pi1/class.tx_yiidlike_pi1.php';
    // Path to this script relative to the extension dir.
    var $extKey = 'yiid_like';
    // The extension key.
    var $pi_checkCHash = true;

    /**
     * The main method of the PlugIn
     *
     * @param	string		$content: The PlugIn content
     * @param	array		$conf: The PlugIn configuration
     * @return	string     The content that is displayed on the website
     */
    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        // Check environment
        if (!isset($conf['type'])) {
            return $this->pi_wrapInBaseClass($this->pi_getLL('no_ts_template'));
        }

        // Initialize the plugin configuration
        $this->init();

        // return html
        return $this->pi_wrapInBaseClass($this->showButton());
    }

    /**
     * Initializes the plugin configuration
     *
     */
    protected function init() {

        $this->pi_initPIflexForm();

        // Get values
        $this->conf['type'] = (string) $this->fetchConfigurationValue('type');
        $this->conf['color'] = (string) $this->fetchConfigurationValue('color');
        $this->conf['width'] = intval($this->fetchConfigurationValue('width'));
        $this->conf['dislike'] = $this->fetchConfigurationValue('dislike');
        $this->conf['templateFile'] = (string) $this->fetchConfigurationValue('templateFile');
        $this->conf['photo'] = $this->fetchConfigurationValue('photo');
        $this->conf['title'] = $this->fetchConfigurationValue('title');
        $this->conf['description'] = $this->fetchConfigurationValue('description');
        $this->conf['cult'] = $GLOBALS['TSFE']->tmpl->setup['config.']['language'];

        // Set default values if necessary
        if (!$this->conf['type']) {
            $this->conf['type'] = 'like';
        }
        if (!$this->conf['color']) {
            $this->conf['color'] = '#000000';
        }
        if (!$this->conf['dislike']) {
            $this->conf['dislike'] = FALSE;
        }
        if (!$this->conf['templateFile']) {
            $this->conf['templateFile'] = 'EXT:' . $this->extKey . '/res/template.html';
        }
        if (!$this->conf['title']) {
            $this->conf['title'] = $GLOBALS['TSFE']->page['title'];
        }
        if (!$this->conf['description']) {
            $this->conf['description'] = $GLOBALS['TSFE']->page['description'];
        }
        if (!$this->conf['cult']) {
            $this->conf['cult'] = 'en';
        }

        // Get width depending on style and like
        $this->conf['width'] = $this->getWidth();

        // Load template code
        $this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
    }

    /**
     * Fetches configuratin value given its name.
     * Merges flexform and TS configuration values.
     *
     * @param	string		$param Configuration value name
     * @return	string		HTML
     */
    protected function fetchConfigurationValue($param) {
        $value = trim($this->pi_getFFvalue($this->cObj->data['pi_flexform'], $param));
        return $value ? $value : $this->conf[$param];
    }

    /**
     * Calculates the minimum with of the widget
     *
     * @return	int
     */
    protected function getWidth() {
        // TODO: Proper calculation of $minwith should be implemented here
        $minwith = 420;
        if ($minwith < $this->conf['width']) {
            return $this->conf['width'];
        } else {
            return $minwith;
        }
    }

    /**
     * Generates the HTML output for the frontend
     *
     * @return	string		HTML output for FE
     */
    protected function showButton() {

        // Set width
        $markers['###WIDTH###'] = $this->conf['width'];

        // Set the widget URL
        $markers['###WIDGETURL###'] = 'http://widgets.yiid.com/w/like/';
        $this->conf['dislike'] ? $markers['###WIDGETURL###'] .= 'full' : $markers['###WIDGETURL###'] .= 'like';
        $markers['###WIDGETURL###'] .= '.php?';

        // Set mandatory parameters
        $markers['###WIDGETURL###'] .= 'cult=' . $this->conf['cult'];
        $markers['###WIDGETURL###'] .= '&type=' . $this->conf['type'];
        $markers['###WIDGETURL###'] .= '&url=' . urlencode('http://' . t3lib_div::getThisUrl());

        // Set optional parameters
        if ($this->conf['color']) {
            $markers['###WIDGETURL###'] .= '&color=' . $this->conf['color'];
        }
        if ($this->conf['photo']) {
            $markers['###WIDGETURL###'] .= '&photo=' . urlencode($GLOBALS['TSFE']->baseUrl . $this->conf['photo']);
        }
        if ($this->conf['title']) {
            $markers['###WIDGETURL###'] .= '&title=' . urlencode($this->conf['title']);
        }
        if ($this->conf['description']) {
            $markers['###WIDGETURL###'] .= '&description=' . urlencode($this->conf['description']);
        }

        // Send error if length of widgeturl exceeds 2000 characters
        if (strlen($markers['###WIDGETURL###']) > 2000) {
            return $this->pi_wrapInBaseClass($this->pi_getLL('widgeturl_too_long') .
                    strlen($markers['###WIDGETURL###']) - 2000 .
                    $this->pi_getLL('characters'));
        }

        // Get template for the button
        $template = $this->cObj->getSubpart($this->templateCode, '###BUTTON###');

        // Render output
        $content = $this->cObj->substituteMarkerArray($template, $markers);

        // Return HTML
        return $content;
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/yiid_like/pi1/class.tx_yiidlike_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/yiid_like/pi1/class.tx_yiidlike_pi1.php']);
}
?>