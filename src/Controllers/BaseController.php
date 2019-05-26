<?php
namespace Controllers;

use Components\Controller\Controller;
use Components\Http\Request;

class BaseController extends Controller
{
    public function index(Request $request) {
        $this->renderView('home/index');
    }
}