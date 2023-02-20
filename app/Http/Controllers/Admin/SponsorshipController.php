<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoresponsorshipRequest;
use App\Http\Requests\UpdatesponsorshipRequest;
use App\Models\sponsorship;
use App\Http\Controllers\Controller;

class SponsorshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sponsorships = sponsorship::all();

        return view('admin.sponsors.index', compact('sponsorships'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoresponsorshipRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoresponsorshipRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\sponsorship  $sponsorship
     * @return \Illuminate\Http\Response
     */
    public function show(sponsorship $sponsorship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sponsorship  $sponsorship
     * @return \Illuminate\Http\Response
     */
    public function edit(sponsorship $sponsorship)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatesponsorshipRequest  $request
     * @param  \App\Models\sponsorship  $sponsorship
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatesponsorshipRequest $request, sponsorship $sponsorship)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sponsorship  $sponsorship
     * @return \Illuminate\Http\Response
     */
    public function destroy(sponsorship $sponsorship)
    {
        //
    }
}
