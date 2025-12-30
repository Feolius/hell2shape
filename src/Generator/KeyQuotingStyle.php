<?php

namespace Feolius\Hell2Shape\Generator;

enum KeyQuotingStyle: string
{
    case NoQuotes = 'none';
    case SingleQuotes = 'single';
    case DoubleQuotes = 'double';
}
