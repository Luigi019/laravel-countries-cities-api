<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;

class Employees extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $name, $mail;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.employees.view', [
            'employees' => Employee::latest()
						->orWhere('name', 'LIKE', $keyWord)
						->orWhere('mail', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
    }
	
    private function resetInput()
    {		
		$this->name = null;
		$this->mail = null;
    }

    public function store()
    {
        $this->validate([
		'name' => 'required',
		'mail' => 'required',
        ]);

        Employee::create([ 
			'name' => $this-> name,
			'mail' => $this-> mail
        ]);
        
        $this->resetInput();
		$this->dispatchBrowserEvent('closeModal');
		session()->flash('message', 'Employee Successfully created.');
    }

    public function edit($id)
    {
        $record = Employee::findOrFail($id);
        $this->selected_id = $id; 
		$this->name = $record-> name;
		$this->mail = $record-> mail;
    }

    public function update()
    {
        $this->validate([
		'name' => 'required',
		'mail' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Employee::find($this->selected_id);
            $record->update([ 
			'name' => $this-> name,
			'mail' => $this-> mail
            ]);

            $this->resetInput();
            $this->dispatchBrowserEvent('closeModal');
			session()->flash('message', 'Employee Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            Employee::where('id', $id)->delete();
        }
    }
}