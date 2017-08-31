<?php

namespace SampleVendor\SamplePackage\Http\Controllers;

use SampleVendor\SamplePackage\Models\Bar;
use Illuminate\Http\Request;

class BarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bars = Bar::paginate();
        return view('sample_package::bars.index', compact('bars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sample_package::bars.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Bar::create($request->all());
        return redirect()->route('sample_package.bars.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \SampleVendor\SamplePackage\Models\Bar  $bar
     * @return \Illuminate\Http\Response
     */
    public function show(Bar $bar)
    {
        return view('sample_package::bars.show', compact('bar'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \SampleVendor\SamplePackage\Models\Bar  $bar
     * @return \Illuminate\Http\Response
     */
    public function edit(Bar $bar)
    {
        return view('sample_package::bars.edit', compact('bar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \SampleVendor\SamplePackage\Models\Bar  $bar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bar $bar)
    {
        $bar->fill($request->all());
        $bar->save();

        return redirect()->route('sample_package.bars.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \SampleVendor\SamplePackage\Models\Bar  $bar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bar $bar)
    {
        $bar->delete();

        return redirect()->route('sample_package.bars.index');
    }
}
