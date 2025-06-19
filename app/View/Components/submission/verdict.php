<?php

namespace App\View\Components\submission;

use Illuminate\View\Component;
use App\Models\Submission;

class verdict extends Component
{

    /**
     * The alert type.
     *
     * @var object
     */
    public $submission;

    /**
     * Create a new component instance.
     * @param object $submission
     * @return void
     */
    public function __construct($submission)
    {
        //
        $this->submission = $submission;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.submission.verdict');
    }
}
