<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\NoteDto;
use App\Model\Note;

class NoteService extends AbstractBaseService
{
    /**
     * @param Note $note
     *
     * @return NoteDto
     * @throws Exception
     */
    public function createNote(Note $note): NoteDto
    {
        if (!$note->isValid()) {
            return new NoteDto($note, 400, implode(', ', $note->getErrors()));
        }

        $endpoint = sprintf('%s/orders/%s/notes/create', self::FIXABLY_API_URL, $note->getOrderId());

        $requestBody = [
            'body' => [
                'Type' => $note->getType(),
                'Description' => $note->getDescription(),
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->createHeaders(), $requestBody
        ));

        $message = 'Error while creating note';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $note->setId($response->json()['id']);
            $message = sprintf('Note %s created', $note->getId());
        }

        return new NoteDto($note, $response->getStatusCode(), $message);
    }
}
