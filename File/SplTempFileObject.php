<?php

namespace Kairos\GoogleAnalyticsClientBundle\File;

/**
 * Class SplTempFileObject
 * @package Kairos\GoogleAnalyticsClientBundle\File
 */
class SplTempFileObject extends \SplTempFileObject
{
    /**
     * Returns the contents of the file
     *
     * @return string the contents of the file
     *
     * @throws \RuntimeException
     */
    public function getContents()
    {
        ob_start();
        $this->rewind();
        $this->fpassthru();
        return ob_get_clean();
    }
}
