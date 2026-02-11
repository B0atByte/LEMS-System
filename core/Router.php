<?php
namespace Core;

/**
 * Router Class
 * Handles URL routing and dispatches to controllers
 */
class Router
{
    private $routes = [];
    private $request;
    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Load routes from config
     */
    public function loadRoutes(array $routes): void
    {
        foreach ($routes as $route => $handler) {
            $this->addRoute($route, $handler);
        }
    }

    /**
     * Add a route
     * Format: "METHOD /path" => "Controller@method"
     */
    public function addRoute(string $route, string $handler): void
    {
        list($method, $path) = explode(' ', $route, 2);
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->convertToPattern($path)
        ];
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToPattern(string $path): string
    {
        // Convert :id to named capture group
        $pattern = preg_replace('/\/:([a-zA-Z0-9_]+)/', '/(?P<$1>[a-zA-Z0-9_]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        return $pattern;
    }

    /**
     * Dispatch the request to the appropriate controller
     */
    public function dispatch(): void
    {
        $method = $this->request->method();
        $uri = $this->request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Extract route parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->request->setParams($params);

                // Call controller method
                $this->callController($route['handler']);
                return;
            }
        }

        // No route matched - 404
        $this->response->notFound();
    }

    /**
     * Call controller method
     */
    private function callController(string $handler): void
    {
        list($controllerName, $method) = explode('@', $handler);

        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            $this->response->serverError("Controller {$controllerName} not found");
            return;
        }

        $controller = new $controllerClass($this->request, $this->response);

        if (!method_exists($controller, $method)) {
            $this->response->serverError("Method {$method} not found in {$controllerName}");
            return;
        }

        // Call the controller method
        $controller->$method();
    }
}
