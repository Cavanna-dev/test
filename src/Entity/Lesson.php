<?php

namespace App\Entity;

class Lesson
{
    public int $id;
    public int $meetingPointId;
    public int $instructorId;
    public \DateTime $startTime;
    public \DateTime $endTime;

    public function __construct(
        int $id,
        int $meetingPointId,
        int $instructorId,
        \DateTime $startTime,
        \DateTime $endTime
    ) {
        $this->id = $id;
        $this->meetingPointId = $meetingPointId;
        $this->instructorId = $instructorId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public static function renderHtml(Lesson $lesson): string
    {
        return '<p>' . $lesson->id . '</p>';
    }

    public static function renderText(Lesson $lesson): string
    {
        return (string) $lesson->id;
    }
}
