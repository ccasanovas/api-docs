<?php


namespace App\Application\Actions\Documents;


use App\Domain\Documents\DocumentsRepository;
use App\Domain\Services\AzureBlobService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Zend\Hydrator\Exception\DomainException;

class DeleteDocumentsAction extends DocumentsAction
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
        $fileName = $this->resolveArg('fileName');
        $getAllBlobs = $this->azureBlob->getAllBlobs();

        $matchDocument = $this->matchDocumentOnAzure($fileName, $getAllBlobs);
        if (!$matchDocument) {
            throw new HttpBadRequestException($this->request, 'No se pudo borrar encontrar el archivo solicitado de azure');
        }

        $deleteRequest = $this->sendDeleteRequest($fileName);

        if ($deleteRequest->getStatusCode() != 200){
            throw new DomainException('Falló el request para borrar el archivo de la bd');
        }

        $this->logger->info("El archivo de nombre $fileName fue borrado.");

        return $this->respondWithData($deleteRequest->getBody());
    }

    protected function matchDocumentOnAzure($fileName, $arrayFiles)
    {
        $boolResponse = false;

        array_walk($arrayFiles, function ($individualBlobFiles) use ($fileName, &$boolResponse){
            if ($individualBlobFiles->getName() == $fileName) {
                $boolResponse = true;
                $this->azureBlob->deleteFile($fileName, 'images');
                return true;
            }
        }, $boolResponse);
        if ($boolResponse) return true;

        return false;
    }

    protected function sendDeleteRequest($fileName)
    {
        $testDocsRequest = new Client();
        return $testDocsRequest->post('https://jas-docs.com/api/v1/deleteFileUploaded.php', ['name' => $fileName]);
    }
}