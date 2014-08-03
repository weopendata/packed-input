<?php

namespace Tdt\Input\Controllers;

/**
 * @copyright aGPLv3
 */

class UIController extends \Controller
{
    protected function index()
    {
        return \View::make('input::demonstrator');
    }
}
