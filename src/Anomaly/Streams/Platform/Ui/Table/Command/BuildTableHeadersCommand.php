<?php namespace Anomaly\Streams\Platform\Ui\Table\Command;

use Anomaly\Streams\Platform\Ui\Table\TableUi;

/**
 * Class BuildTableHeadersCommand
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Table\Command
 */
class BuildTableHeadersCommand
{

    /**
     * The table UI object.
     *
     * @var \Anomaly\Streams\Platform\Ui\Table\TableUi
     */
    protected $ui;

    /**
     * Create a new BuildTableHeadersCommand instance.
     *
     * @param TableUi $ui
     */
    function __construct(TableUi $ui)
    {
        $this->ui = $ui;
    }

    /**
     * Get the table UI object.
     *
     * @return mixed
     */
    public function getUi()
    {
        return $this->ui;
    }

}
 