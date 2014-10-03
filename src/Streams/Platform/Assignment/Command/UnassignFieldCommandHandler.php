<?php namespace Streams\Platform\Assignment\Command;

use Streams\Platform\Field\FieldModel;
use Laracasts\Commander\CommandHandler;
use Streams\Platform\Stream\StreamModel;
use Laracasts\Commander\Events\EventDispatcher;
use Streams\Platform\Assignment\AssignmentModel;

class UnassignFieldCommandHandler implements CommandHandler
{
    /**
     * The event dispatcher.
     *
     * @var \Laracasts\Commander\Events\EventDispatcher
     */
    protected $dispatcher;

    /**
     * The stream model.
     *
     * @var \Streams\Platform\Stream\StreamModel
     */
    protected $stream;

    /**
     * The field model.
     *
     * @var \Streams\Platform\Field\FieldModel
     */
    protected $field;

    /**
     * The assignment model.
     *
     * @var \Streams\Platform\Assignment\AssignmentModel
     */
    protected $assignment;

    /**
     * Create a new UnassignFieldCommandHandler instance.
     *
     * @param EventDispatcher $dispatcher
     * @param StreamModel     $stream
     */
    function __construct(
        EventDispatcher $dispatcher,
        StreamModel $stream,
        FieldModel $field,
        AssignmentModel $assignment
    ) {
        $this->field      = $field;
        $this->stream     = $stream;
        $this->assignment = $assignment;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the command.
     *
     * @param $command
     * @return $this|mixed
     */
    public function handle($command)
    {
        $stream = $this->stream->findByNamespaceAndSlug(
            $command->getNamespace(),
            $command->getStream()
        );

        $field = $this->field->findByNamespaceAndSlug(
            $command->getNamespace(),
            $command->getField()
        );


        if ($stream and $field) {
            if ($assignment = $this->assignment->remove($stream->getKey(), $field->getKey())) {
                $this->dispatcher->dispatch($assignment->releaseEvents());

                return $assignment;
            }
        }

        return false;
    }
}
 