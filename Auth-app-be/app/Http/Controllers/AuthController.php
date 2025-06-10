<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Registracija novog korisnika
     */
    public function register(Request $request)
    {
        // Validacija podataka
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'name.required' => 'Ime je obavezno',
            'name.min' => 'Ime mora imati najmanje 2 karaktera',
            'email.required' => 'Email je obavezan',
            'email.email' => 'Email mora biti valjan',
            'email.unique' => 'Korisnik sa ovim email-om već postoji',
            'password.required' => 'Lozinka je obavezna',
            'password.min' => 'Lozinka mora imati najmanje 6 karaktera',
            'password.confirmed' => 'Lozinke se ne poklapaju'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Kreiranje korisnika
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Generisanje Sanctum tokena
            $token = $user->createToken('auth-token', ['*'], now()->addDays(30))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Uspešno ste se registrovali!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ],
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri registraciji: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prijava korisnika
     */
    public function login(Request $request)
    {
        // Validacija podataka
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ], [
            'email.required' => 'Email je obavezan',
            'email.email' => 'Email mora biti valjan',
            'password.required' => 'Lozinka je obavezna'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Pronalaženje korisnika
            $user = User::where('email', $request->email)->first();

            // Proverava lozinku
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Neispravni podaci za prijavu'
                ], 401);
            }

            // Brisanje starih tokena (opciono)
            $user->tokens()->delete();

            // Generisanje novog tokena
            $token = $user->createToken('auth-token', ['*'], now()->addDays(30))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Uspešno ste se prijavili!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ],
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri prijavi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Odjava korisnika
     */
    public function logout(Request $request)
    {
        try {
            // Brisanje trenutnog tokena
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Uspešno ste se odjavili!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri odjavi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Podaci o trenutnom korisniku
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri dohvatanju korisnika: ' . $e->getMessage()
            ], 500);
        }
    }
}
