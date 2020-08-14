<?php


namespace App\Application\Actions\Documents;


use App\Domain\Documents\DocumentsRepository;
use App\Domain\Services\AzureBlobService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class EditDocumentsAction extends DocumentsAction
{
    public function __construct(LoggerInterface $logger, DocumentsRepository $documentsRepository, AzureBlobService $azureBlob)
    {
        parent::__construct($logger, $documentsRepository);
        $this->azureBlob = $azureBlob;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $documents = $this->documentsRepository->findAllDocuments();

        $this->logger->info("El listado de documentos fue visto.");
        return $this->respondWithData($documents);
    }

}