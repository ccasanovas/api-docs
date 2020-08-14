<?php
declare(strict_types=1);

namespace App\Domain\Documents;

use App\Domain\DomainException\DomainRecordNotFoundException;

class DocumentNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'El documento que ha solicitado no existe.';
}
