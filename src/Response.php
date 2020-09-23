<?php

namespace api\tool;

use tpr\App;
use tpr\core\request\DefaultRequest;
use tpr\core\request\RequestAbstract;
use tpr\exception\Handler;
use tpr\exception\HttpResponseException;
use tpr\models\ResponseModel;

/**
 * Class Response
 *
 * @package api\tool
 * @method string        getType()
 * @method \tpr\core\Response setType(string $response_type)
 * @method ResponseModel options()
 * @method Response      addDriver(string $response_type, string $driver)
 * @method array         getHeaders()
 * @method Response      setHeaders(array $headers, bool $cover = false)
 * @method string        getHeader(string $key)
 * @method Response      setHeader(string $key, string $value)
 * @method Response      assign(string $key, $value)
 * @method void          addTemplateFunc(string $name, callable $func)
 * @method void          response($result = '', $status = 200, $msg = '', array $headers = [])
 * @method void          success($data = [])
 * @method void          error($code = 500, $msg = 'error')
 * @method string        fetch(string $template = '', array $vars = [])
 */
class Response
{
    protected ?RequestAbstract $request = null;

    private \tpr\core\Response $response;

    public function __construct()
    {
        App::drive('name');
        $this->request  = new DefaultRequest();
        $this->response = new \tpr\core\Response();
    }

    public function __call($name, $arguments)
    {
        try {
            $result = \call_user_func_array([$this->response, $name], $arguments);
            throw new HttpResponseException($result);
        } catch (HttpResponseException $e) {
            $this->send($e);
        } catch (\Exception $e) {
            Handler::render($e, $this->response);
        }
        return \call_user_func_array([$this->response, $name], $arguments);
    }

    private function send(HttpResponseException $httpException)
    {
        if (!headers_sent() && !empty($httpException->headers)) {
            // 发送状态码
            http_response_code($httpException->http_status);
            // 发送头部信息
            foreach ($httpException->headers as $name => $val) {
                if (null === $val) {
                    header($name);
                } else {
                    header($name . ':' . $val);
                }
            }
        }
        echo $httpException->result;
        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        unset($httpException->result);
    }

    public function removeHeaders($headers = [])
    {
        if (\is_string($headers)) {
            $headers = [$headers];
        }
        if (empty($headers)) {
            $headers = App::drive()->getConfig()->remove_headers;
        }
        if (!headers_sent() && !empty($headers)) {
            foreach ($headers as $header) {
                header_remove($header);
            }
        }
    }

    public function redirect(string $destination, bool $permanent = true)
    {
        if (false === strpos($destination, '://')) {
            $protocol    = 'https' === $this->request->protocol() ? 'https' : 'http';
            $destination = $protocol . '://' . $this->request->host() . $destination;
        }

        if (true === $permanent) {
            $code    = 301;
            $message = $code . ' Moved Permanently';
        } else {
            $code    = 302;
            $message = $code . ' Found';
        }

        header('HTTP/1.1 ' . $message, true, $code);
        header('Status: ' . $message, true, $code);

        header('Location: ' . $destination);
        exit();
    }
}
