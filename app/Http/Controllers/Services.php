<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Services extends Controller
{
    public function index(): \Inertia\Response
    {
        $services = \App\Models\Service::paginate(15)->through(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'image' => $service->image,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'status' => $service->status,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ];
        });

        return Inertia::render('Services/ServicesList', [
            'services' => $services
        ]);
    }

    public function show($slug): \Inertia\Response {
        return Inertia::render('Services/Service', []);
    }
}