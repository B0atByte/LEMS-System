<?php
namespace Core;

/**
 * Response Class
 * Handles HTTP responses
 */
class Response
{
    private $statusCode = 200;
    private $headers = [];
    private $body;

    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set response header
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set response body
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Send response
     */
    public function send(): void
    {
        // Set status code
        http_response_code($this->statusCode);

        // Set headers
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // Send body
        echo $this->body;
    }

    /**
     * Redirect to URL
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->setStatusCode($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Send JSON response
     */
    public function json($data, int $statusCode = 200): void
    {
        $this->setStatusCode($statusCode)
             ->setHeader('Content-Type', 'application/json')
             ->setBody(json_encode($data))
             ->send();
        exit;
    }

    /**
     * Send success JSON response
     */
    public function success($data = [], string $message = 'Success', int $statusCode = 200): void
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Send error JSON response
     */
    public function error(string $message = 'Error', $errors = [], int $statusCode = 400): void
    {
        $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Send 404 Not Found response
     */
    public function notFound(string $message = '404 - Page Not Found'): void
    {
        $this->setStatusCode(404);
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }

    /**
     * Send 403 Forbidden response
     */
    public function forbidden(string $message = '403 - Access Forbidden'): void
    {
        $this->setStatusCode(403);
        require_once __DIR__ . '/../views/errors/403.php';
        exit;
    }

    /**
     * Send 500 Internal Server Error response
     */
    public function serverError(string $message = '500 - Internal Server Error'): void
    {
        $this->setStatusCode(500);
        require_once __DIR__ . '/../views/errors/500.php';
        exit;
    }

    /**
     * Download file
     */
    public function download(string $filepath, string $filename = null): void
    {
        if (!file_exists($filepath)) {
            $this->notFound('File not found');
            return;
        }

        $filename = $filename ?? basename($filepath);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        readfile($filepath);
        exit;
    }
}
