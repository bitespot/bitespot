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
            'business_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'address' => ['nullable', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
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
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
            ]);
            event(new Registered($user));
            Auth::login($user);
            $user->refresh();
            $is_new_user = true;
        } else {
            /** @var User $user */
            $user = Auth::user();
            if ($user->role !== 'vendor') {
                $user->role = 'vendor';
                $user->save();
            }
        }

        $slug = Str::slug($request->business_name) . '-' . Str::random(6);

        $vendor = Vendor::create([
            'user_id'       => $user->id,
            'category_id'   => $request->category_id,
            'business_name' => $request->business_name,
            'slug'          => $slug,
            'description'   => $request->description,
            'owner_name'    => $request->name ?? $user->name,
            'address'       => $request->address,
            'district'      => $request->district,
            'city'          => $request->city,
            'province'      => $request->province,
            'lat'           => $request->lat,
            'lng'           => $request->lng,
            'price_tier'    => ['$' => '₱', '$$' => '₱₱', '$$$' => '₱₱₱'][$request->price_tier] ?? $request->price_tier,
            'status'        => 'approved',
        ]);

        if ($is_new_user) {
            // Redirect to the direct setup route (has simpler middleware chain)
            return redirect('/vendor/setup')->with('success', 'Establishment registered! Add photos and details to complete your profile.');
        }

        return redirect('/vendor-dashboard/' . $vendor->id)->with('success', 'Establishment added! Please manage your photos and details.');
    }
}
