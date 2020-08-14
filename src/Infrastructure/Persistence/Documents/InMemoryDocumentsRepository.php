<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Documents;

use App\Domain\Documents\Documents;
use App\Domain\Documents\DocumentsRepository;
use App\Domain\DomainException\DomainRecordNotFoundException;
use GuzzleHttp\Client;

class InMemoryDocumentsRepository implements DocumentsRepository
{
    /**
     * @var Documents[]
     */
    private $documents;

    /**
     * InMemoryUserRepository constructor.
     *
     * @param array|null $documents
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(array $documents = null)
    {
        $this->documents = $documents ?? [
                1 => new Documents(1, 3, 4, 6, 'AR', '1.24', 'NombreDoc', 'NuevoNombreDoc', 'DirDoc'),
                2 => new Documents(2, 3, 4, 6, 'AR', '1.24', 'NombreDoc', 'NuevoNombreDoc', 'DirDoc'),
                3 => new Documents(3, 3, 4, 6, 'AR', '1.24', 'NombreDoc', 'NuevoNombreDoc', 'DirDoc'),
                4 => new Documents(4, 1, 3, 4,  'AR', '1.24', 'NombreDoc', 'NuevoNombreDoc', 'DirDoc'),
                5 => new Documents(5, 3, 4, 6, 'AR', '1.24', 'NombreDoc', 'NuevoNombreDoc', 'DirDoc')
            ];
    }

    protected function deserializeDocuments($json)
    {
        $documentsArray = [];
        array_push($documentsArray, new Documents($json['id'], $json['id_servicio'], $json['id_org'], $json['id_usuario'], $json['pais'], $json['size'], $json['file_name'], $json['name'], $json['fullPathFile']));
        return $documentsArray;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllDocuments(): array
    {
        return array_values($this->documents);
    }

    /**
     * {@inheritdoc}
     * @throws DomainRecordNotFoundException
     */
    public function findDocumentsById(int $id): Documents
    {
        if (!isset($this->documents[$id])) {
            throw new DomainRecordNotFoundException('No existen documentos para el id solicitado.');
        }

        return $this->documents[$id];
    }

    /**
     * {@inheritdoc}
     * @throws DomainRecordNotFoundException
     */
    public function findDocumentsByOrganization(int $idOrganization): array
    {
        $documentsArray = array_filter($this->documents, function($documents) use ($idOrganization){
            if ($documents->jsonSerialize()['idOrganization'] == $idOrganization){
                return  $documents;
            }
        });

        if (!$documentsArray) {
            throw new DomainRecordNotFoundException('No existen documentos para la organización solicitada.');
        }

        return ($documentsArray);
    }

    /**
     * {@inheritdoc}
     * @throws DomainRecordNotFoundException
     */
    public function findDocumentsByServices(int $idServices): array
    {
        $documentsArray = array_filter($this->documents, function($documents) use ($idServices){
            if ($documents->jsonSerialize()['idServices'] == $idServices){
                return  $documents;
            }
        });
        if (!$documentsArray) {
            throw new DomainRecordNotFoundException('No existen documentos para el servicio solicitado.');
        }
        return ($documentsArray);
    }

    /**
     * {@inheritdoc}
     * @throws DomainRecordNotFoundException
     */
    public function findDocumentsByUser(int $idUser): array
    {
        $documentsArray = array_filter($this->documents, function($documents) use ($idUser){
            if ($documents->jsonSerialize()['idUser'] == $idUser){
                return  $documents;
            }
        });

        if (!$documentsArray) {
            throw new DomainRecordNotFoundException('No existen documentos para el usuario solicitado.');
        }

        return ($documentsArray);
    }

    /**
     * {@inheritdoc}
     * @throws DomainRecordNotFoundException
     */
    public function findDocumentsByContainer(string $container): Documents
    {
        if (!isset($this->documents[$container])) {
            throw new DomainRecordNotFoundException('No existen documentos para el container solicitado.');
        }

        return $this->documents[$container];
    }

    /**
     * {@inheritdoc}
     * @throws DomainRecordNotFoundException
     */
    public function findDocumentsByName(string $name): Documents
    {
        if (!isset($this->documents[$name])) {
            throw new DomainRecordNotFoundException('No existen documentos con el nombre solicitado.');
        }

        return $this->documents[$name];
    }

}
