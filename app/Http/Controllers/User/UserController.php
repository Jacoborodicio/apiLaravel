<?php

namespace App\Http\Controllers\User;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;
use Illuminate\Support\Facades\Mail;

class UserController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Lista todos los usuarios disponibles en ese momento
        $usuarios = User::all();
        // return response()->json(['data' => $usuarios], 200);
        return $this->showAll($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Crear un nuevo usuario
        
        // Validación
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $rules);
        $campos = $request->all();

        $campos['password'] = bcrypt($request->password);
        $campos['verified'] = User::USUARIO_NO_VERIFICADO;
        $campos['verification_token'] = User::generarVerificationToken();
        $campos['admin'] = User::USUARIO_REGULAR;

        $usuario = User::create($campos);
        // return response()->json(['data' => $usuario], 201);
        return $this->showOne($usuario, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Retorna al usuario con id $id
        // $usuario = User::findOrFail($id);
        // return response()->json(['data' => $usuario], 200);
        // if(!$usuario){
        //     return $this->errorResponse('Usuario no encontrado', 404);
        // }
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Primero "encontramos" al usuario a actualizar
        // $usuario = User::findOrFail($id);
        
        // Validación
        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::USUARIO_ADMINISTRADOR . ',' . User::USUARIO_REGULAR,
        ];

        $this->validate($request, $rules);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email != $request->email) {
            // Debemos generar un nuevo token ya que será un usuario no verificado
            $user->verified = User::USUARIO_NO_VERIFICADO;
            $user->verification_token = User::generarVerificationToken();
            $user->email = $request->email;    
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {
            if(!$user->esVerificado()) {
                // return response()->json(['error' => 'Únicamente los usuarios verificados pueden cambiar su valor de administrador', 'code' => 409], 409);
                return $this->errorResponse('Únicamente los usuarios verificados pueden cambiar su valor de administrador', 409);
            }
            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            // return response()->json(['error' => 'Se debe especificar al menos un valor diferente para actualizar', 'code' => 422], 422);
            return $this->errorResponse('Se debe especificar al menos un valor diferente apra actualizar', 422);
        }

        $user->save();

        // return response()->json(['data' => $usuario], 200);
        return $this->showOne($user);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Permite borrar un usuario
        // $user = User::findOrFail($id);

        $user->delete();

        // return response()->json(['data' => $user], 200);
        return $this->showOne($user);
    }

    public function verify($token){
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = User::USUARIO_VERIFICADO;
        // Evitamos que pueda seguir utilizando el mismo token 
        $user->verification_token = null;

        $user->save();
        return $this->showMsg('La cuenta ha sido verificada');
    }

    public function resend(User $user){
        if ($user->esVerificado()) {
            return $this->errorResponse('Este usuario ya ha sido verificado.', 409);
        }
        retry(5, function() use($user){
            Mail::to($user)->send(new UserCreated($user));
        }, 100);
        
        return $this->showMsg('El correo de verificación se ha reenviado.');
    }
}
