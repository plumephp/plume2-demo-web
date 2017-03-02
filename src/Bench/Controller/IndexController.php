<?php

namespace Bench\Controller;

use Plume\Response;
use Plume\Controller;
use Bench\Model\Name;

class IndexController extends Controller
{
    public function indexAction()
    {
        $name = new Name;
        $name->name = "name";
        $name->save();

        return new Response();
    }

    public function findAction()
    {
        $name = Name::find(1);

        $response = new Response;
        $response->json($name);

        return $response;
    }
}

?>
