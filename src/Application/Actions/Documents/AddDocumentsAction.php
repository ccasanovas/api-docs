<?php


namespace App\Application\Actions\Documents;

use App\Domain\Documents\DocumentsRepository;
use App\Domain\Services\AzureBlobService;
use GuzzleHttp\Client;
use http\Client\Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7\UploadedFile;
use Slim\Exception\HttpBadRequestException;
use Zend\Hydrator\Exception\DomainException;


class AddDocumentsAction extends DocumentsAction
{
    public function __construct(LoggerInterface $logger, DocumentsRepository $documentsRepository, AzureBlobService $azureBlob)
    {
        parent::__construct($logger, $documentsRepository);
        $this->azureBlob = $azureBlob;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function action(): Response
    {

        $request = $this->getFileRequest();
        $data = json_decode($request->getParsedBody()['headers']);

        /** @var UploadedFile $uploadedFiles */
        $uploadedFiles = $request->getUploadedFiles();
        $localFilePaths = array_map(array($this, 'proccessFiles'), $uploadedFiles);
        $uploadToAzure = array_map(array($this, 'uploadToAzure'), $localFilePaths);

        $returnPost = $this->sendPostData($localFilePaths, $data);


        if ($returnPost->getStatusCode() != 200) {
            throw new DomainException('Falló la escritura en la bd del documento');
        }

        $deleteFromLocal = array_map(array($this, 'deleteFilesFromLocalPath'), $localFilePaths);
        if ($deleteFromLocal) array_map(array($this,'writeLogRegistry'), $localFilePaths);


        return $this->respondWithData($returnPost);
    }

    protected function writeLogRegistry($files)
    {
        $documentName = basename($files);
        $this->logger->info("Se ha subido un nuevo documento de nombre $documentName .");
        return true;
    }

    protected function sendPostData($files, $fileRelatedData)
    {
        $testDocsRequest = new Client();
        $postDataArray = $this->createObjectToSend($files, $fileRelatedData);
        $email = $testDocsRequest->request('POST', 'http://core:8001/apps/docs/api/v1/writeFileUploaded.php',
            [json_encode($postDataArray)]);
        $data = $email->getStatusCode();
        $data2 = $email->getBody()->getContents();
        $data3 = $email->getHeaders();
        return $testDocsRequest->request('POST', 'http://127.0.0.1:8001/apps/docs/api/v1/writeFileUploaded.php',
            ['data' => json_encode($postDataArray)]);
    }

    protected function createObjectToSend($files, $fileRelatedData)
    {
        $postDataArray = [];
        foreach ($files as $arrayToPost){
            array_push($postDataArray,
                [
                    'userId' => $fileRelatedData->user_id,
                    'token' => $fileRelatedData->token,
                    'username' => $fileRelatedData->username,
                    'idOrg' => $fileRelatedData->idOrg,
                    'idServ' => $fileRelatedData->idServ,
                    'idTipo' => $fileRelatedData->idTipo,
                    'idEntidad' => $fileRelatedData->idEntidad,
                    'pathToFile' => 'https://azureapidocs.blob.core.windows.net/images/'.basename($arrayToPost),
                    'fileName' => basename($arrayToPost),
                    'fileSize' => filesize($arrayToPost)
                ]);
        }
        return $postDataArray;
    }

    protected function proccessFiles($files)
    {
        $directory = $_SERVER['DOCUMENT_ROOT'] . '/uploads';
        $getFileOriginalName = pathinfo($files->getClientFilename(), PATHINFO_FILENAME);
        $getFileExtension = pathinfo($files->getClientFilename(), PATHINFO_EXTENSION);
        $newFileName = $this->generateDocumentName() . '-' . $getFileOriginalName . '.' . $getFileExtension;
        $files->moveTo($directory . DIRECTORY_SEPARATOR . $newFileName);
        return $directory . DIRECTORY_SEPARATOR . $newFileName;
    }

    protected function uploadToAzure($files)
    {
        if (!file_exists($files)) {
            throw new HttpBadRequestException($this->request, 'El archivo que se intenta subir a azure no se subió correctamente al server.');
        }

        return $this->azureBlob->uploadFile($files, 'images');
    }

    protected function deleteFilesFromLocalPath($files)
    {
        if (!file_exists($files)) {
            throw new HttpBadRequestException($this->request, 'El archivo que se intenta borrar no se subió correctamente al server.');
        }

        return unlink($files);
    }



}