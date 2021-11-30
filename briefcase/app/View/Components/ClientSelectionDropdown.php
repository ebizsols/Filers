<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ClientSelectionDropdown extends Component
{

    public $clients;
    public $selected;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($clients, $selected = null)
    {
        $this->clients = $clients;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.client-selection-dropdown');
    }

}
