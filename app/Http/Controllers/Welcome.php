<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class Welcome extends Controller
{

    public function index(): \Inertia\Response
    {
        $services = \App\Models\Service::paginate(15)->through(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' =>  $service->image,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'duration' => $service->duration,
                'status' => $service->status,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ];
        });
        $customer = Auth::guard('customer')->user();

        return Inertia::render('Welcome', [
            'customer' => $customer,
            'services' => $services,
        ]);
    }
}
