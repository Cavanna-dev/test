<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\MeetingPoint;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        $replaced = clone($tpl);

        $replaced->subject = $this->computeTextSubject($replaced->subject, $data);
        $replaced->content = $this->computeTextContent($replaced->content, $data);

        return $replaced;
    }

    /**
     * @param array<string, Instructor|Lesson|Learner> $data
     */
    private function computeTextSubject(string $text, array $data): string
    {
        $lesson = (isset($data['lesson']) and $data['lesson'] instanceof Lesson) ? $data['lesson'] : null;

        if (!$lesson) {
            return $text;
        }

        /** @var Instructor $instructor */
        $instructor = InstructorRepository::getInstance()->getById($lesson->instructorId);

        if (strpos($text, '[lesson:instructor_name]') !== false) {
            $text = str_replace('[lesson:instructor_name]', $instructor->identity->firstName, $text);
        }

        return $text;
    }

    /**
     * @param array<string, Instructor|Lesson|Learner> $data
     */
    private function computeTextContent(string $text, array $data): string
    {
        $text = $this->computeLessonContent($text, $data);

        if (isset($data['instructor'])  and ($data['instructor'] instanceof Instructor))
            $text = str_replace('[instructor_link]',  'instructors/' . $data['instructor']->id .'-'.urlencode($data['instructor']->identity->firstName), $text);
        else
            $text = str_replace('[instructor_link]', '', $text);

        $user = (isset($data['user']) and ($data['user'] instanceof Learner)) ? $data['user'] : ApplicationContext::getInstance()->getCurrentUser();
        if (strpos($text, '[user:first_name]') !== false) {
            $text = str_replace('[user:first_name]', $user->getFormattedIdentity(), $text);
        }

        return $text;
    }

    /**
     * @param array<string, Instructor|Lesson|Learner> $data
     */
    private function computeLessonContent(string $text, array $data): string
    {
        $lesson = (isset($data['lesson']) and $data['lesson'] instanceof Lesson) ? $data['lesson'] : null;

        if (!$lesson) {
            return $text;
        }

        $meetingPoint = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        /** @var Instructor $instructor */
        $instructor = InstructorRepository::getInstance()->getById($lesson->instructorId);

        if (strpos($text, '[lesson:instructor_link]') !== false) {
            $text = str_replace(
                '[lesson:instructor_link]',
                'instructors/'.$instructor->id.'-'.urlencode($instructor->identity->firstName),
                $text
            );
        }
        if (strpos($text, '[lesson:instructor_name]') !== false) {
            $text = str_replace('[lesson:instructor_name]', $instructor->identity->firstName, $text);
        }
        if(strpos($text, '[lesson:summary_html]') !== false) {
            $text = str_replace('[lesson:summary_html]', Lesson::renderHtml($lesson), $text);
        }
        if(strpos($text, '[lesson:summary]') !== false) {
            $text = str_replace('[lesson:summary]', Lesson::renderText($lesson), $text);
        }
        if(strpos($text, '[lesson:meeting_point]') !== false) {
            $text = str_replace('[lesson:meeting_point]', $meetingPoint->name, $text);
        }
        if (strpos($text, '[lesson:start_date]') !== false) {
            $text = str_replace('[lesson:start_date]', $lesson->startTime->format('d/m/Y'), $text);
        }
        if (strpos($text, '[lesson:start_time]') !== false) {
            $text = str_replace('[lesson:start_time]', $lesson->startTime->format('H:i'), $text);
        }
        if (strpos($text, '[lesson:end_time]') !== false) {
            $text = str_replace('[lesson:end_time]', $lesson->endTime->format('H:i'), $text);
        }

        return $text;
    }
}
