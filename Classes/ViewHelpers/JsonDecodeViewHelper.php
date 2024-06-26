<?php

declare(strict_types=1);

namespace RD\ErrorLog\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class JsonDecodeViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('json', 'string', 'JSON string to decode', true);
        $this->registerArgument('assoc', 'bool', 'Whether to return associative arrays', false, true);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $json = $this->arguments['json'];
        $assoc = $this->arguments['assoc'];
        return json_decode($json, $assoc);
    }
}
