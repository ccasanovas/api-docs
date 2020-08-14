<?php
declare(strict_types=1);

namespace App\Application\Actions\Documents;

use App\Domain\Documents\DocumentsRepository;
use App\Domain\Services\AzureBlobService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class ViewDocumentsAction extends DocumentsAction
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
        $searchParam = (string) $this->resolveArg('searchParam');
        $argument    = $this->resolveArg('arg');
        $documents = $this->findDocuments($searchParam, $argument);
        $this->logger->info("El documento buscado por '$searchParam' de argumento `$argument` fue visto.");
        return $this->respondWithData($documents);
    }

    private function findDocuments($searchParam, $arg)
    {
        switch ($searchParam)
        {
            case $searchParam === "id":
                return $this->documentsRepository->findDocumentsById((int) $arg);
                break;
            case $searchParam === "user":
                return $this->documentsRepository->findDocumentsByUser((int) $arg);
                break;
            case $searchParam === "name":
                return $this->documentsRepository->findDocumentsByName((string) $arg);
                break;
            case $searchParam === "services":
                return $this->documentsRepository->findDocumentsByServices((int) $arg);
                break;
            case $searchParam === "organization":
                return $this->documentsRepository->findDocumentsByOrganization((int) $arg);
                break;
            default:
                throw new HttpBadRequestException($this->request, "No se pudo resolver el argumento `{$searchParam}`.");
        }
    }

}
