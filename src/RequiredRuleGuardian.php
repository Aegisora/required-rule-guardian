<?php

namespace Aegisora\RuleGuardians\RequiredRule;

use Aegisora\Guardian\Guardian;

class RequiredRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }
}
