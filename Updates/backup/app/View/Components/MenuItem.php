<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MenuItem extends Component
{

    public $icon;
    public $text;
    public $link;
    public $active;
    public $addon;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($icon, $text, $link = null, $active = false, $addon = false)
    {
        $this->text = $text;
        $this->icon = $icon;
        $this->link = $link;
        $this->active = $active;
        $this->addon = $addon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.menu-item');
    }

}
