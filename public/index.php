<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/DbOperations.php';

// Crear el contenedor de PHP-DI
$container = new Container();
AppFactory::setContainer($container);

// Crear la aplicación Slim
$app = AppFactory::create();

// Ruta raíz para probar
$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Welcome to Slim!");
    return $response;
});

/*
    endpoint: CrearEquipo
    parametros: nombreEquipo,institucion,departamento,municipio,direccion,telefono
    Metodo: POST
*/ 
$app->post('/createEquipo', function(Request $request, Response $response) {

    if(haveEmptyParameters(array('nombreEquipo', 'institucion', 'departamento', 'municipio', 'direccion', 'telefono'), $response)) {
        return $response; // Retorna la respuesta si hay parámetros vacíos
    }

    $request_data = $request->getParsedBody();
   
    $nombreEquipo = $request_data['nombreEquipo'];
    $institucion = $request_data['institucion'];
    $departamento = $request_data['departamento'];
    $municipio = $request_data['municipio'];
    $direccion = $request_data['direccion'];
    $telefono = $request_data['telefono'];

    $db = new DbOperations;

    $result = $db->createEquipo($nombreEquipo, $institucion, $departamento, $municipio, $direccion, $telefono);

    if ($result == EQUIPO_CREATED) {
        $message = array();
        $message['error'] = false;
        $message['message'] = 'Equipo creado satisfactoriamente';

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
                
    } elseif ($result == EQUIPO_FAILURE) {
        $message = array();
        $message['error'] = true;
        $message['message'] = 'Ha ocurrido un error';

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
                
    } elseif ($result == EQUIPO_EXISTS) {
        $message = array();
        $message['error'] = true;
        $message['message'] = 'El equipo ya existe';

        $response->getBody()->write(json_encode($message));

        return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
    }
    return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
});

function haveEmptyParameters($required_params, $response) {
    $error = false;
    $error_params = '';

    $request_params = $_REQUEST;

    foreach ($required_params as $param) {
       if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0) {
            $error = true;
            $error_params .= $param . ', ';
       }
    }

    if ($error) {
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->getBody()->write(json_encode($error_detail));
        return true; // Retorna verdadero si hay un error
    }
    return false;
}

/*
    endpoint: Buscar y obtener todos los Equipos
    parametros: nombreEquipo,institucion,departamento,municipio,direccion,telefono
    Metodo: GET

*/ 
$app->get('/equipos', function (Request $request, Response $response) {
    $db = new DbOperations;
    $equipos = $db->getAllEquipos();
    $response->getBody()->write(json_encode($equipos));
    return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
});


/*
    endpoint: CrearJuador
    parametros: nombres, apellidos, fechaNacimiento, genero, posicion, idEquipo
    Metodo: POST

*/ 
$app->post('/createJugador', function (Request $request, Response $response) {
    $request_data = $request->getParsedBody();
    if (haveEmptyParameters(array('nombres', 'apellidos', 'fechaNacimiento', 'genero', 'posicion', 'idEquipo'), $response, $request_data)) {
        return $response->withHeader('Content-type', 'application/json')->withStatus(400);
    }

    $nombres = $request_data['nombres'];
    $apellidos = $request_data['apellidos'];
    $fechaNacimiento = $request_data['fechaNacimiento'];
    $genero = $request_data['genero'];
    $posicion = $request_data['posicion'];
    $idEquipo = $request_data['idEquipo'];

    $db = new DbOperations;
    $result = $db->createJugador($nombres, $apellidos, $fechaNacimiento, $genero, $posicion, $idEquipo);

    if ($result == JUGADOR_CREATED) {
        $message = array();
        $message['error'] = false;
        $message['message'] = 'Jugador creado satisfactoriamente';
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-type', 'application/json')->withStatus(201);
    } else {
        $message = array();
        $message['error'] = true;
        $message['message'] = 'Ha ocurrido un error';
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
    }
});

/*
    endpoint: BuscarJugadores
    parametros: nombres, apellidos, fechaNacimiento, genero, posicion, idEquipo
    Metodo: Get
*/ 
$app->get('/jugadores', function (Request $request, Response $response) {
    $db = new DbOperations;
    $jugadores = $db->getAllJugadores();
    $response->getBody()->write(json_encode($jugadores));
    return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
});

$app->run();
