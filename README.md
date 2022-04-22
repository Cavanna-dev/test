# Refactoring Kata Test

## Introduction

**Ornikar** is sending a lot of notification messages, and we have some message templates we want to send
at different occasion. To do that, we've developed `TemplateManager` whose job is to replace
placeholders in texts by lesson related information.

`TemplateManager` is a class that's been around for years and nobody really knows who coded
it or how it really works. Nonetheless, as the business changes frequently, this class has
already been modified many times, making it harder to understand at each step.

Today, once again, the PO wants to add some new stuff to it and add the management for a new
placeholder. But this class is already complex enough and just adding a new behaviour to it
won't work this time.

Your mission, should you decide to accept it, is to **refactor `TemplateManager` to make it
understandable by the next developer** and easy to change afterwards. Now is the time for you to
show your exceptional skills and make this implementation better, extensible, and ready for future
features.

Sadly for you, the public method `TemplateManager::getTemplateComputed` is called everywhere, 
and **you can't change its signature**. But that's the only one you can't modify (unless explicitly
forbidden in a code comment), **every other class is ready for your changes**.

This exercise **should not last longer than 1 hour 30** (but this can be too short to do it all and
you might take longer if you want).


## Rules
There are some rules to follow:
 - You must commit regularly
 - You must not modify code when comments explicitly forbid it

## Deliverables
What do we expect from you:
 - the link of the git repository
 - several commits, with an explicit message each time
 - a file / message / email explaining your process and principles you've followed

## Hints
- You will be evaluated on your usage of SOLID principle and Refactoring best practices.
- A makefile is provided for you to help you start directly with docker.

**Good luck!**


## Dev notes

- How I work:<br>
The time box is short, I usually take a lot of time to think about what result I need, then decompose all the way until I have the full path for my work.
To complete a refactoring like this, I typically change little piece by piece while running test every time I feel I need to.
<br><br>
- I removed every double quotes from the code because I think you should only use single quote most of the time, and use double quotes when you need to inject vars in a string (but it's error-prone in my opinion).
<br><br>
- I added some tests:<br>
For me, the actual test wasn't enough to cover every condition of the code.
Overmore, I detected that the code had some condition that is not in the test, but for me the code > test. So I based my judgement on "I'm actually working on a refactoring ticket and not a *BUG*, it means the code is working, so I have to add tests accordingly". For example, in the code you can add an instructor in the data, so I tested you can pass an instructor to the TemplateManager data.
<br><br>
- Frustrations about the instructor repository that cannot be changed, and I have a getById that returns mixed :(. <br>For me the get design **MUST** return the inherent type of the repository (InstructorRepository->getOneById() returns Instructor or throws RuntimeException)
<br><br>
- Why did I create a VO identity?<br>
ValueObjects are clearly underestimated for me in the PHP community. I see a lot of stacks in my career and I think VO are a very simple et easy design to apply everywhere.
<br><br>
It solves a lot of design problems in my opinion, but 2 are important. 
1/ Using primitives everywhere is the beginning of chaos. Like you start with a User class that contains only id, name and email, and one day it encapsulates too much logic and begin to be a SuperMassiveUserClass that knows how to deal with passwords, emails, roles, etc...
2/ It improves readability to have a code with VO and delegate responsibilities (SOLID first rule)
```
class Order
{
  public const STATUS_STARTING = 'starting';
  public const STATUS_PENDING = 'pending';
  public const STATUS_DONE = 'done';
  
  private string $status;
  
  public function isStarting(): bool 
  {
    return self::STATUS_STARTING === $this->status;
  }
}
```

```
class Order
{
  private OrderStatus $status;
    
  public function isStarting(): bool 
  {
    return $this->status->isStarting();
  }
}

final class OrderStatus
{
  // private because we do not need to expose them since we have public method that encapsulates the logic for us
  private const STATUS_STARTING = 'starting';
  private const STATUS_PENDING = 'pending';
  private const STATUS_DONE = 'done';
  
  private string $value;
  
  public function isStarting(): bool 
  {
    return self::STATUS_STARTING === $this->value;
  }
}
```
