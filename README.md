## 接口通信组件

### Http 发送请求

```php
$response = \api\tool\Http::instance()
    ->setDomain("https://www.sojson.com")
    ->setMethod('GET')
    ->curl("open/api/weather/json.shtml",[
        "city"=>'北京'
    ]);

dump($response->getContent());
```


### Request 接收请求

```php
$param = \api\tool\Request::instance()->param();

dump($param);

/**
 * More Document :
 *       https://www.kancloud.cn/manual/thinkphp5/158834
 */
```

### Response 回复请求
> 支持 json|jsonp|xml|html 四种格式，默认使用json格式

```php

/**
 * default json format
 * support "json|jsonp|xml|html"
 */
\api\tool\Response::instance('json')
    ->response('hello,world!');

```


## License
licensed under the [MIT](https://rem.mit-license.org/)