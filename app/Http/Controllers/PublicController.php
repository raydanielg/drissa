<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Post;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function home()
    {
        $services = Service::where('is_active', true)->orderBy('name')->limit(6)->get();
        $posts = Post::where('is_published', true)->latest('published_at')->limit(3)->get();
        $doctors = User::role('doctor')->limit(4)->get();

        return view('public.home', compact('services', 'posts', 'doctors'));
    }

    public function about()
    {
        $doctors = User::role('doctor')->get();
        return view('public.about', compact('doctors'));
    }

    public function branches()
    {
        return view('public.branches');
    }

    public function services()
    {
        $services = Service::where('is_active', true)->orderBy('name')->paginate(12);
        return view('public.services', compact('services'));
    }

    public function appointments()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $doctors = User::role('doctor')->get();
        return view('public.appointments', compact('services', 'doctors'));
    }

    public function storeAppointment(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'service_type' => 'required|string',
            'reason' => 'nullable|string|max:1000',
        ]);

        $nameParts = explode(' ', trim($data['full_name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $patient = Patient::firstOrCreate(
            ['phone' => $data['phone']],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'mrn' => 'MRN-' . strtoupper(Str::random(8)),
            ]
        );

        $scheduledAt = $data['preferred_date'] . ' ' . $data['preferred_time'] . ':00';

        Appointment::create([
            'patient_id' => $patient->id,
            'scheduled_at' => $scheduledAt,
            'status' => 'scheduled',
            'type' => 'general',
            'notes' => $data['service_type'] . ': ' . ($data['reason'] ?? ''),
        ]);

        return redirect()->route('public.appointments')->with('success', 'Appointment booked successfully! Our team will confirm your visit via SMS.');
    }

    public function blog()
    {
        $posts = Post::where('is_published', true)->latest('published_at')->paginate(9);
        return view('public.blog', compact('posts'));
    }

    public function shop()
    {
        $products = Product::where('is_active', true)->latest()->paginate(12);
        return view('public.shop', compact('products'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        return redirect()->route('public.contact')->with('success', 'Message sent! We will get back to you soon.');
    }
}
