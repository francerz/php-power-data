<?php

namespace Francerz\PowerData\Typed;

use Exception;
use Francerz\PowerData\Collection;
use Francerz\PowerData\Type;

/**
 * @deprecated v0.1.26
 */
class TypedCollection extends Collection
{
    private $type;
    private $itemType;
    public function __construct(Type $itemType, $data = array())
    {
        $this->type = Type::def(
            $itemType->getType(),
            $itemType->getArrayDepth() + 1
        );
        if (!$this->type->check($data)) {
            throw new Exception("TypedCollection(): Data type mismatch");
        }

        parent::__construct($data);
        $this->itemType = $itemType;
    }
}
