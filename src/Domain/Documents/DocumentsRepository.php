<?php
declare(strict_types=1);

namespace App\Domain\Documents;

interface DocumentsRepository
{
    /**
     * @return Documents[]
     */
    public function findAllDocuments(): array;

    /**
     * @param string $name
     * @return Documents
     * @throws DocumentNotFoundException
     */
    public function findDocumentsByName(string $name): Documents;

    /**
     * @param int $id
     * @return Documents
     * @throws DocumentNotFoundException
     */
    public function findDocumentsById(int $id): Documents;


    /**
     * @param int $idOrganization
     * @return Documents[]
     * @throws DocumentNotFoundException
     */
    public function findDocumentsByOrganization(int $idOrganization): array;

    /**
     * @param int $idServices
     * @return Documents[]
     * @throws DocumentNotFoundException
     */
    public function findDocumentsByServices(int $idServices): array;

    /**
     * @param int $idUser
     * @return Documents[]
     * @throws DocumentNotFoundException
     */
    public function findDocumentsByUser(int $idUser): array;

    /**
     * @param string $container
     * @return Documents
     * @throws Do
     */
    public function findDocumentsByContainer(string $container): Documents;

}
