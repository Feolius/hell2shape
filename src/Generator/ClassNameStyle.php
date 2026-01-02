<?php

namespace Feolius\Hell2Shape\Generator;

enum ClassNameStyle: string
{
    case Unqualified = 'uqn';
    case Qualified = 'qn';
    case FullyQualified = 'fqn';
}
