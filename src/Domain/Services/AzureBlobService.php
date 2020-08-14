<?php

namespace App\Domain\Services;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\BlockList;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use Psr\Log\LoggerInterface;

class AzureBlobService
{

    private $blobClient;

    public function __construct(LoggerInterface $logger)
    {
        define('CHUNK_SIZE', 4 * 1024 * 1024);
        $this->logger = $logger;

        //TODO: CAMBIAR ESTE TOKEN HARDCODEADO
        $this->blobClient = BlobRestProxy::createBlobService(('DefaultEndpointsProtocol=https;AccountName=azureapidocs;AccountKey=fdeXwi9S09QLR7mR59AHNNy2xtz05VAaW85WLz0GDWrfbhb4zPRbJS7ewJCxoDO8GYI3ly1ooGatE7C3ubicRQ==;EndpointSuffix=core.windows.net'));
    }

    public function getAllContainers()
    {
        try {
            $container_list = $this->blobClient->listContainers();
            return $container_list->getContainers();
        } catch (ServiceException $exception) {
            $this->logger->error('Fallo al obtener todos los containers:'. $exception->getCode() . ':' . $exception->getErrorMessage());
            throw $exception;
        }
    }

    public function getAllBlobs($container = 'images')
    {
        try {
            $blob_list = $this->blobClient->listBlobs($container);
            return $blob_list->getBlobs();
        } catch (ServiceException $exception){
            $this->logger->error('Fallo al obtener todos los blobs:'. $exception->getCode() . ':' . $exception->getErrorMessage());
            throw $exception;
        }
    }

    public function uploadFile($file, $container = 'images')
    {
        try {
            $blockList = new BlockList();
            $fptr = fopen($file, "rb");
            $index = 1;
            $blob_name = basename($file);
            while (!feof($fptr)) {
                $block_id = base64_encode(str_pad($index, 6, "0", STR_PAD_LEFT));
                $blockList->addUncommittedEntry($block_id);
                $data = fread($fptr, CHUNK_SIZE);
                $this->blobClient->createBlobBlock($container, basename($file), $block_id, $data);
                ++$index;
            }
            return $this->blobClient->commitBlobBlocks($container, $blob_name, $blockList);
        } catch (ServiceException $exception){
            $this->logger->error('Fallo al subir el archivo:'. $exception->getCode() . ':' . $exception->getErrorMessage());
            throw $exception;
        }
    }

    public function deleteFile($blobName, $container = 'images')
    {
        try {
           return $this->blobClient->deleteBlob($container, $blobName);
        } catch (ServiceException $exception) {
            $this->logger->error('Fallo al borrar el archivo:'. $exception->getCode() . ':' . $exception->getErrorMessage());
            throw $exception;
        }
    }
}