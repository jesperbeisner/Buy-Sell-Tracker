<?php

declare(strict_types=1);

namespace App\Action;

use App\Result\ActionResult;

interface ActionInterface
{
    public function execute(): ActionResult;
}
