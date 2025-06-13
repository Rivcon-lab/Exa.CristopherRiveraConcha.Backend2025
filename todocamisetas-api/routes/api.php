<?php
/**
 * Rutas de la API TodoCamisetas
 * Maneja el routing de todas las peticiones HTTP
 */

class ApiRoutes {
    
    private $method;
    private $path;
    private $pathParts;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $this->getPath();
        $this->pathParts = explode('/', trim($this->path, '/'));
    }
    
    /**
     * Obtiene la ruta de la petición
     */
    private function getPath() {
        $path = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }
        
        // Remover el prefijo /todocamisetas-api si existe
        if (strpos($path, '/todocamisetas-api') === 0) {
            $path = substr($path, strlen('/todocamisetas-api'));
        }
        
        return $path;
    }
    
    /**
     * Maneja el routing principal
     */
    public function handle() {
        // Manejar CORS preflight
        Response::handleCorsPreFlight();
        
        try {
            // Verificar que empiece con /api
            if (!isset($this->pathParts[0]) || $this->pathParts[0] !== 'api') {
                Response::notFound('Endpoint');
            }
            
            // Obtener recurso
            $resource = isset($this->pathParts[1]) ? $this->pathParts[1] : '';
            
            switch ($resource) {
                case 'camisetas':
                    $this->handleCamisetasRoutes();
                    break;
                    
                case 'clientes':
                    $this->handleClientesRoutes();
                    break;
                    
                case 'tallas':
                    $this->handleTallasRoutes();
                    break;
                    
                default:
                    Response::notFound('Recurso');
            }
            
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Maneja las rutas de camisetas
     */
    private function handleCamisetasRoutes() {
        $id = isset($this->pathParts[2]) ? $this->pathParts[2] : null;
        $subResource = isset($this->pathParts[3]) ? $this->pathParts[3] : null;
        $subId = isset($this->pathParts[4]) ? $this->pathParts[4] : null;
        $subSubResource = isset($this->pathParts[5]) ? $this->pathParts[5] : null;
        $subSubId = isset($this->pathParts[6]) ? $this->pathParts[6] : null;
        
        switch ($this->method) {
            case 'GET':
                if ($id && $subResource === 'precio' && $subId) {
                    // GET /api/camisetas/{id}/precio/{cliente_id}
                    CamisetaController::getPrecioFinal($id, $subId);
                } elseif ($id && $subResource === 'tallas') {
                    // GET /api/camisetas/{id}/tallas
                    CamisetaController::getTallas($id);
                } elseif ($id) {
                    // GET /api/camisetas/{id}
                    CamisetaController::show($id);
                } else {
                    // GET /api/camisetas
                    CamisetaController::index();
                }
                break;
                
            case 'POST':
                if ($id && $subResource === 'tallas') {
                    // POST /api/camisetas/{id}/tallas
                    CamisetaController::addTalla($id);
                } else {
                    // POST /api/camisetas
                    CamisetaController::store();
                }
                break;
                
            case 'PUT':
                if ($id && $subResource === 'tallas' && $subId) {
                    // PUT /api/camisetas/{camiseta_id}/tallas/{talla_id}
                    CamisetaController::updateTallaStock($id, $subId);
                } elseif ($id) {
                    // PUT /api/camisetas/{id}
                    CamisetaController::update($id);
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            case 'DELETE':
                if ($id && $subResource === 'tallas' && $subId) {
                    // DELETE /api/camisetas/{camiseta_id}/tallas/{talla_id}
                    CamisetaController::removeTalla($id, $subId);
                } elseif ($id) {
                    // DELETE /api/camisetas/{id}
                    CamisetaController::destroy($id);
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            default:
                Response::error('Método HTTP no permitido', 405);
        }
    }
    
    /**
     * Maneja las rutas de clientes
     */
    private function handleClientesRoutes() {
        $id = isset($this->pathParts[2]) ? $this->pathParts[2] : null;
        $subResource = isset($this->pathParts[3]) ? $this->pathParts[3] : null;
        
        switch ($this->method) {
            case 'GET':
                if ($id && $subResource === 'camisetas') {
                    // GET /api/clientes/{id}/camisetas
                    ClienteController::getCamisetas($id);
                } elseif ($id) {
                    // GET /api/clientes/{id}
                    ClienteController::show($id);
                } else {
                    // GET /api/clientes
                    ClienteController::index();
                }
                break;
                
            case 'POST':
                if (!$id) {
                    // POST /api/clientes
                    ClienteController::store();
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            case 'PUT':
                if ($id) {
                    // PUT /api/clientes/{id}
                    ClienteController::update($id);
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            case 'DELETE':
                if ($id) {
                    // DELETE /api/clientes/{id}
                    ClienteController::destroy($id);
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            default:
                Response::error('Método HTTP no permitido', 405);
        }
    }
    
    /**
     * Maneja las rutas de tallas
     */
    private function handleTallasRoutes() {
        $id = isset($this->pathParts[2]) ? $this->pathParts[2] : null;
        
        switch ($this->method) {
            case 'GET':
                if ($id) {
                    // GET /api/tallas/{id}
                    TallaController::show($id);
                } else {
                    // GET /api/tallas
                    TallaController::index();
                }
                break;
                
            case 'POST':
                if (!$id) {
                    // POST /api/tallas
                    TallaController::store();
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            case 'PUT':
                if ($id) {
                    // PUT /api/tallas/{id}
                    TallaController::update($id);
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            case 'DELETE':
                if ($id) {
                    // DELETE /api/tallas/{id}
                    TallaController::destroy($id);
                } else {
                    Response::notFound('Endpoint');
                }
                break;
                
            default:
                Response::error('Método HTTP no permitido', 405);
        }
    }
} 