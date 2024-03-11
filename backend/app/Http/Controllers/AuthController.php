<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth; // Utilizzo delle facades di Auth per gestire l'autenticazione
use Illuminate\Http\Request; // Importazione della classe Request per gestire le richieste HTTP
use Validator; // Importazione della classe Validator per la convalida dei dati
use App\User; // Importazione del modello User

class AuthController extends Controller
{
    /**
     * Crea una nuova istanza di AuthController.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]); // Imposta il middleware per l'autenticazione API, eccetto per le rotte 'login' e 'register'
    }

    /**
     * Ottieni un JWT tramite le credenziali fornite.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [ // Creazione di un validatore per i campi 'email' e 'password'
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) { // Verifica se la convalida fallisce e restituisce gli errori
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) { // Tentativo di autenticazione dell'utente
            return response()->json(['error' => 'Either email or password is wrong.'], 401);
        }

        return $this->createNewToken($token); // Genera un nuovo token JWT
    }

    /**
     * Registra un Utente.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [ // Creazione di un validatore per i campi 'name', 'email' e 'password'
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) { // Verifica se la convalida fallisce e restituisce gli errori
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge( // Creazione di un nuovo utente con i dati validati e la password crittografata
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    /**
     * Disconnetti l'utente (Invalida il token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout(); // Effettua il logout dell'utente

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Aggiorna un token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh()); // Genera un nuovo token JWT aggiornato
    }

    /**
     * Ottieni l'Utente autenticato.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user()); // Restituisce i dati dell'utente autenticato
    }

    /**
     * Ottieni la struttura dell'array del token.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
