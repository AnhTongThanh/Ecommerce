<?php

namespace App\Http\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithPagination;

/**
 * Summary of Index
 */
class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    /**
     * Summary of category_id
     * @var
     */
    public $name, $slug, $status, $brand_id, $category_id;

    /**
     * Summary of rules
     * @return array<string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string',
            'category_id' => 'required|integer',
            'status' => 'nullable'
        ];
    }

    /**
     * Summary of resetInput
     * @return void
     */
    public function resetInput()
    {
        $this->name = NULL;
        $this->slug = NULL;
        $this->status = NULL;
        $this->brand_id = NULL;
        $this->category_id = NULL;
    }

    /**
     * Summary of storeBrand
     * @return void
     */
    public function storeBrand()
    {
        $validatedData = $this->validate();
        Brand::create([
            'name' => $this->name,
            'slug' => Str::slug($this->slug),
            'status' => $this->status == true ? '1' : '0',
            'category_id' => $this->category_id,
        ]);
        session()->flash('message', 'Brand Added Successfully');
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
    }

    /**
     * Summary of closeModal
     * @return void
     */
    public function closeModal()
    {
        $this->resetInput();
    }

    /**
     * Summary of openModal
     * @return void
     */
    public function openModal()
    {
        $this->resetInput();
    }

    /**
     * Summary of editBrand
     * @param int $brand_id
     * @return void
     */
    public function editBrand(int $brand_id)
    {
        $this->brand_id = $brand_id;
        $brand = Brand::findOrFail($brand_id);
        $this->name = $brand->name;
        $this->slug = $brand->slug;
        $this->status = $brand->status;
        $this->category_id = $brand->category_id;
    }

    /**
     * Summary of updateBrand
     * @return void
     */
    public function updateBrand()
    {
        $validatedData = $this->validate();
        Brand::findOrFail($this->brand_id)->update([
            'name' => $this->name,
            'slug' => Str::slug($this->slug),
            'status' => $this->status == true ? '1' : '0',
            'category_id' => $this->category_id,
        ]);
        session()->flash('message', 'Brand Updated Successfully');
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
    }

    /**
     * Summary of deleteBrand
     * @param mixed $brand_id
     * @return void
     */
    public function deleteBrand($brand_id)
    {
        $this->brand_id = $brand_id;
    }

    /**
     * Summary of destroyBrand
     * @return void
     */
    public function destroyBrand()
    {
        Brand::findOrFail($this->brand_id)->delete();
        session()->flash('message', 'Brand Deleted Successfully');
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
    }

    /**
     * Summary of render
     * @return mixed
     */
    public function render()
    {
        $categories = Category::where('status', '0')->get();
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);

        return view('livewire.admin.brand.index', ['brands' => $brands, 'categories' => $categories])
            ->extends('layouts.admin')->section('content');
    }
}