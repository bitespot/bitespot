<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,vendor'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }

    /**
     * Display the vendor registration view.
     */
    public function createVendor(): View
    {
        $categories = Category::all();
        return view('auth.vendor-register', compact('categories'));
    }

    /**
     * Handle vendor registration and establishment creation.
     *
     * @throws ValidationException
     */
    public function storeVendor(Request $request): RedirectResponse
    {
        $rules = [
            // Establishment details
            'business_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email_business' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'price_tier' => ['required', 'in:$,$$,$$$'],
        ];

        if (!Auth::check()) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $is_new_user = false;
        if (!Auth::check()) {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
            ]);
            event(new Registered($user));
            Auth::login($user);
            Auth::user()->refresh();
            $is_new_user = true;
        } else {
            $user = Auth::user();
            if ($user->role !== 'vendor') {
                $user->update(['role' => 'vendor']);
            }
        }

        // Create vendor establishment
        $slug = Str::slug($request->business_name) . '-' . Str::random(6);
        
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'business_name' => $request->business_name,
            'slug' => $slug,
            'description' => $request->description,
            // Use owner_name or request->name or user->name
            'owner_name' => $request->owner_name ?? ($request->name ?? $user->name),
            'phone' => $request->phone,
            'email' => $request->email_business,
            'website' => $request->website,
            'address' => $request->address,
            'district' => $request->district,
            'city' => $request->city,
            'province' => $request->province,
            'price_tier' => $request->price_tier,
            'status' => 'pending', // Requires admin approval
        ]);

        if ($is_new_user) {
            // Redirect to the direct setup route (has simpler middleware chain)
            return redirect('/vendor/setup')->with('success', 'Establishment registered! Please add photos and wait for admin approval.');
        }

        return redirect('/vendor-dashboard/' . $vendor->id)->with('success', 'Establishment added! Please manage your photos and details.');
    }
}
