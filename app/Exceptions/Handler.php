<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Excepción de validación de datos
        if($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        // Excepción de modelo no encontrado
        if($exception instanceof ModelNotFoundException){
            $modelo = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado", 404); 
        }

        // Excepción de autenticación
        if($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }

        // Excepción de autorización
        if($exception instanceof AuthorizationException){
            return $this->errorResponse("No posee permisos para realizar esa acción", 403);
        }

        // Excepción de rutas erróneas o no encontradas
        if($exception instanceof NotFoundHttpException){
            return $this->errorResponse('No se ha encontrado la URL especificada.', 404);
        }

        // Excepción método utilizado erróneo
        if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }

        // Excepción general
        if($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        // Excepción violación de condición de integridad referencial
        if($exception instanceof QueryException){
            // dd($exception);
            $code_error = $exception->errorInfo[1];
            if ($code_error == 1451){
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso especificado debido a su relación con otros elementos.', 409);
            }
        }

        /**
         * Resto de errores, como si la base de datos está caída o lo que sea. Aquí estamos
         * diferenciando entre el modo de depuración y el de producción. Si estamos en modo depuración, desarrollo,
         * entonces, nos interesa recibir todos los mensajes de error detallados, sin embargo si nos encontramos 
         * en modo producción, le mostramos al usuario un mensaje sin detalles.
         */
        if(config('app.debug')){
            return parent::render($request, $exception);
        }
        return $this->errorResponse('Error Interno, por favor, vuelva a intentarlo en un momento.', 500);
    }
    
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        return $this->errorResponse($errors, 422);
    }

     /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('Usuario no autenticado', 401);
    }
}
