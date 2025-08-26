<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DevelopmentNotice extends Component
{
    /**
     * The notice message.
     *
     * @var string
     */
    public $message;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $message
     * @return void
     */
    public function __construct($message = null)
    {
        $this->message = $message ?? "We're swimming through the PepePool, adding new features and improvements. Some things might be a bit splashy as we refine the platform. Thanks for your patience! 🐸🏊‍♂️";
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.development-notice');
    }
}
