<?php

namespace App\Enums;

enum Contract: string
{
    case Clt = 'CLT';
    case Company = 'Pessoa Jurídica';
    case Freelancer = 'Freelancer';
}
