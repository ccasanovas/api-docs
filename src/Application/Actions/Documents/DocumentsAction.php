<?php
declare(strict_types=1);

namespace App\Application\Actions\Documents;

use App\Application\Actions\Action;
use App\Domain\Documents\DocumentsRepository;
use PascalDeVink\ShortUuid\ShortUuid;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

abstract class DocumentsAction extends Action
{
    /**
     * @var DocumentsRepository
     */
    protected $documentsRepository;

    /**
     * @param LoggerInterface $logger
     * @param DocumentsRepository  $documentsRepository
     * @param AzureBlob  $azureBlob
     */
    public function __construct(LoggerInterface $logger, DocumentsRepository $documentsRepository)
    {
        parent::__construct($logger);
        $this->documentsRepository = $documentsRepository;
    }


    protected function generateDocumentName()
    {
        try {
            $uuid = Uuid::uuid4();
            $shortUuid = new ShortUuid();
            $password = $shortUuid->encode($uuid);
            return $password;
        } catch (\Exception $e) {
            throw new \Exception('Uuid no se pudo resolver!');
        }
    }
}
