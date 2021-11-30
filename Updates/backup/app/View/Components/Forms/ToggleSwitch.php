<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class ToggleSwitch extends Component
{

    public $fieldLabel;
    public $fieldRequired;
    public $fieldValue;
    public $fieldName;
    public $fieldId;
    public $fieldHelp;
    public $checked;
    public $popover;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($fieldLabel, $fieldRequired = false, $fieldValue = null, $fieldName, $fieldId, $fieldHelp = null, $checked = false, $popover = null)
    {
        $this->fieldLabel = $fieldLabel;
        $this->fieldRequired = $fieldRequired;
        $this->fieldValue = $fieldValue;
        $this->fieldName = $fieldName;
        $this->fieldId = $fieldId;
        $this->fieldHelp = $fieldHelp;
        $this->checked = $checked;
        $this->popover = $popover;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.forms.toggle-switch');
    }

}
