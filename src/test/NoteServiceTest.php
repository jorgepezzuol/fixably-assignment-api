<?php

declare(strict_types=1);

namespace App\Mock\Test;

use App\Model\Note;
use App\Service\NoteService;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../vendor/autoload.php';

class NoteServiceTest extends TestCase
{

    /**
     * @test
     * @return void
     */
    public function testCreateNote(): void
    {
        $expectedOrderId = 17489;
        $expectedNoteId = 17489;

        $expectedResponse = [
            "message" => 'Note created',
            "id" => $expectedNoteId,
        ];

        $note = new Note($expectedOrderId, 'Issue', 'test');

        $expectedStatusCode = 200;

        $noteService = $this->getMockedNoteService($expectedStatusCode, $expectedResponse);
        $response = $noteService->createNote($note);

        $expectedMessage = sprintf('Note %s created', $note->getId());

        static::assertEquals($expectedNoteId, $response->getNote()->getId());
        static::assertEquals($expectedMessage, $response->getMessage());
        static::assertEquals($expectedStatusCode,$response->getStatusCode());
    }

    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return NoteService
     */
    public function getMockedNoteService(int $expectedStatusCode, array $expectedResponse): NoteService
    {
        $mockedGuzzleClient = GuzzleClientMock::getGuzzleClient($expectedStatusCode, $expectedResponse);
        $mockedTokenManager = TokenManagerMock::getTokenManager();

        return new NoteService($mockedGuzzleClient, $mockedTokenManager);
    }
}
