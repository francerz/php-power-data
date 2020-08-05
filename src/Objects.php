<?php

namespace Francerz\PowerData;

class Objects
{
    static public function getHash(object $obj) : string
    {
        return spl_object_hash($obj);
    }
}