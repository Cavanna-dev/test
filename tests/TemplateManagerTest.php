<?php

namespace Test;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\MeetingPoint;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\TemplateManager;

/**
 * @test
 */
class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp(): void
    {
        InstructorRepository::getInstance()->save(new Instructor(1, 'jean', 'rock'));
        MeetingPointRepository::getInstance()->save(new MeetingPoint(1, 'http://lambda.to', 'paris 5eme'));
        ApplicationContext::getInstance()->setCurrentUser(new Learner(1, 'toto', 'bob', 'toto@bob.to'));
    }

    public function test(): void
    {
        $expectedInstructor = InstructorRepository::getInstance()->getById(1);
        $expectedMeetingPoint = MeetingPointRepository::getInstance()->getById(1);
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();
        $startAt = new \DateTime('2021-01-01 12:00:00');
        $endAt = $startAt->add(new \DateInterval('PT1H'));

        $lesson = new Lesson(1, 1 , 1, $startAt, $endAt);
        LessonRepository::getInstance()->save($lesson);

        $template = new Template(
            1,
            'Votre leçon de conduite avec [lesson:instructor_name]',
            '
Bonjour [user:first_name],

La reservation du [lesson:start_date] de [lesson:start_time] à [lesson:end_time] avec [lesson:instructor_name] a bien été prise en compte!
Voici votre point de rendez-vous: [lesson:meeting_point].

Bien cordialement,

L\'équipe Ornikar
');
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson,
            ]
        );

        $this->assertEquals('Votre leçon de conduite avec ' . $expectedInstructor->firstname, $message->subject);
        $this->assertEquals('
Bonjour '.$expectedUser->getFormattedIdentity().',

La reservation du '.$startAt->format('d/m/Y').' de '.$startAt->format('H:i').' à '.$endAt->format('H:i').' avec '.$expectedInstructor->firstname.' a bien été prise en compte!
Voici votre point de rendez-vous: '.$expectedMeetingPoint->name.'.

Bien cordialement,

L\'équipe Ornikar
', $message->content);
    }

    public function testWithInstructorLink(): void
    {
        $expectedInstructor = InstructorRepository::getInstance()->getById(1);
        $expectedMeetingPoint = MeetingPointRepository::getInstance()->getById(1);
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();
        $startAt = new \DateTime('2021-01-01 12:00:00');
        $endAt = $startAt->add(new \DateInterval('PT1H'));

        $lesson = new Lesson(1, 1 , 1, $startAt, $endAt);
        LessonRepository::getInstance()->save($lesson);

        $template = new Template(
            1,
            'Votre leçon de conduite avec [lesson:instructor_name]',
            '
Bonjour [user:first_name],

La reservation du [lesson:start_date] de [lesson:start_time] à [lesson:end_time] avec [lesson:instructor_name] a bien été prise en compte!
Voici votre point de rendez-vous: [lesson:meeting_point].

Vous pouvez prendre contact avec votre instructeur en cliquant !(ici)[http://ornikar.env.fr/[lesson:instructor_link]].

Bien cordialement,

L\'équipe Ornikar
');
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson,
            ]
        );

        $this->assertEquals('Votre leçon de conduite avec '.$expectedInstructor->firstname, $message->subject);
        $this->assertEquals('
Bonjour '.$expectedUser->getFormattedIdentity().',

La reservation du '.$startAt->format('d/m/Y').' de '.$startAt->format('H:i').' à '.$endAt->format('H:i').' avec '.$expectedInstructor->firstname.' a bien été prise en compte!
Voici votre point de rendez-vous: '.$expectedMeetingPoint->name.'.

Vous pouvez prendre contact avec votre instructeur en cliquant !(ici)[http://ornikar.env.fr/instructors/'.$expectedInstructor->id.'-'.$expectedInstructor->firstname.'].

Bien cordialement,

L\'équipe Ornikar
', $message->content);
    }

    public function testOverloadUser(): void
    {
        $expectedInstructor = InstructorRepository::getInstance()->getById(1);
        $expectedMeetingPoint = MeetingPointRepository::getInstance()->getById(1);
        $startAt = new \DateTime('2021-01-01 12:00:00');
        $endAt = $startAt->add(new \DateInterval('PT1H'));

        $lesson = new Lesson(1, 1 , 1, $startAt, $endAt);
        LessonRepository::getInstance()->save($lesson);
        $learner = new Learner(2, 'horny', 'car', 'hornycar@ornikar.fr');

        $template = new Template(
            1,
            'Votre leçon de conduite avec [lesson:instructor_name]',
            '
Bonjour [user:first_name],

La reservation du [lesson:start_date] de [lesson:start_time] à [lesson:end_time] avec [lesson:instructor_name] a bien été prise en compte!
Voici votre point de rendez-vous: [lesson:meeting_point].

Vous pouvez prendre contact avec votre instructeur en cliquant !(ici)[http://ornikar.env.fr/[lesson:instructor_link]].

Bien cordialement,

L\'équipe Ornikar
');
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson,
                'user' => $learner,
            ]
        );

        $this->assertEquals('Votre leçon de conduite avec '.$expectedInstructor->firstname, $message->subject);
        $this->assertEquals('
Bonjour '.$learner->getFormattedIdentity().',

La reservation du '.$startAt->format('d/m/Y').' de '.$startAt->format('H:i').' à '.$endAt->format('H:i').' avec '.$expectedInstructor->firstname.' a bien été prise en compte!
Voici votre point de rendez-vous: '.$expectedMeetingPoint->name.'.

Vous pouvez prendre contact avec votre instructeur en cliquant !(ici)[http://ornikar.env.fr/instructors/'.$expectedInstructor->id.'-'.$expectedInstructor->firstname.'].

Bien cordialement,

L\'équipe Ornikar
', $message->content);
    }
}
