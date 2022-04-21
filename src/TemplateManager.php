<?php
namespace App;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    /**
     * @param <string, Lesson> $data
     */
    private function computeText(string $text, array $data): string
    {
        $applicationContext = ApplicationContext::getInstance();

        $text = $this->computeLessonText($text, $data);

        if (isset($data['instructor'])  and ($data['instructor']  instanceof Instructor))
            $text = str_replace('[instructor_link]',  'instructors/' . $data['instructor']->id .'-'.urlencode($data['instructor']->firstname), $text);
        else
            $text = str_replace('[instructor_link]', '', $text);

        $_user  = (isset($data['user'])  and ($data['user']  instanceof Learner))  ? $data['user']  : $applicationContext->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(strtolower($_user->firstname)), $text);
        }

        return $text;
    }

    private function computeLessonText(?string $text, array $data): string
    {
        $lesson = (isset($data['lesson']) and $data['lesson'] instanceof Lesson) ? $data['lesson'] : null;

        if (!$lesson) {
            return $text;
        }

        $lessonFromRepository = LessonRepository::getInstance()->getById($lesson->id);
        $usefulObject = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);

        if (strpos($text, '[lesson:instructor_link]') !== false) {
            $text = str_replace(
                '[instructor_link]',
                'instructors/'.$instructorOfLesson->id.'-'.urlencode($instructorOfLesson->firstname),
                $text
            );
        }

        $containsSummaryHtml = strpos($text, '[lesson:summary_html]');
        $containsSummary     = strpos($text, '[lesson:summary]');

        if ($containsSummaryHtml !== false) {
            $text = str_replace(
                '[lesson:summary_html]',
                Lesson::renderHtml($lessonFromRepository),
                $text
            );
        }
        if ($containsSummary !== false) {
            $text = str_replace(
                '[lesson:summary]',
                Lesson::renderText($lessonFromRepository),
                $text
            );
        }

        if (strpos($text, '[lesson:instructor_name]') !== false) {
            $text = str_replace('[lesson:instructor_name]', $instructorOfLesson->firstname, $text);
        }

        if(strpos($text, '[lesson:meeting_point]') !== false)
            $text = str_replace('[lesson:meeting_point]', $usefulObject->name, $text);

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
