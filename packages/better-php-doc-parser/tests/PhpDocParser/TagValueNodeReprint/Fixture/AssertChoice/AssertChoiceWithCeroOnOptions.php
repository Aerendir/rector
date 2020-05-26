<?php

declare(strict_types=1);

namespace Rector\BetterPhpDocParser\Tests\PhpDocParser\TagValueNodeReprint\Fixture\AssertChoice;

use Symfony\Component\Validator\Constraints as Assert;

class AssertChoiceWithMessage
{
    /**
     * @Assert\Choice(choices={"0", "3023", "3610"})
     */
    private $ratingType;
}
