<?php

declare(strict_types=1);

namespace App\Helper;

use App\Enum\NoteTypeEnum;
use App\Model\Note;

class NoteHelper
{
    /**
     * @param Note $note
     *
     * @return bool
     */
    public static function isNoteValid(Note $note): bool
    {
        $errors = [];

        if (!is_string($note->getDescription()) || empty($note->getDescription())) {
            $errors[] = 'Note description is empty or not a string';
        }

        if (
            !is_string($note->getType())
            || empty($note->getType())
            || !in_array($note->getType(), NoteTypeEnum::TYPES)
        ) {
            $noteTypes = implode(", ", NoteTypeEnum::TYPES);
            $errors[] = "Note type is empty, not a string or not a valid type ({$noteTypes})";
        }

        $note->setErrors($errors);

        return count($note->getErrors()) === 0;
    }
}
