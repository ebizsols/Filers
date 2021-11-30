<?php

namespace App\View\Components\Cards;

use Illuminate\View\Component;

class Data extends Component
{

    public $title;
    public $padding;
    public $otherClasses;
    public $height;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = false, $padding = true, $otherClasses = '', $height = 'auto')
    {
        $this->title = $title;
        $this->padding = $padding;
        $this->otherClasses = $otherClasses;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.cards.data');
    }

}
