[![Latest Stable Version](https://poser.pugx.org/axios/api-tool/v/stable)](https://packagist.org/packages/axios/api-tool)
[![License](https://poser.pugx.org/axios/api-tool/license)](https://packagist.org/packages/axios/api-tool)

## 接口通信组件

> `Request`和`Response` 基于thinkphp5的 Request 和 Response 重构而来

> `Http` 是基于 GuzzleHttp 的封装

### 安装

> composer require axios/api-tool


### Http 发送请求

* 请求配置

``` php
$options = [
    'http_errors'     => false,
    'connect_timeout' => 30,
    'read_timeout'    => 80,
    'urlencode'       => 1,
    'format'          => 'array',  //array|json|xml
    // or another guzzlehttp options
];
```

> 更多配置项 -> [GuzzleHttp中文文档](http://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)

* 发送请求

```php
$response = \api\tool\Http::instance()
    ->setDomain("https://www.sojson.com")
    ->setMethod('GET')
    ->curl("open/api/weather/json.shtml",[
        "city"=>'北京'
    ]);

dump($response->getContent());
// or $response->getData();

//获取某一层级的数据
dump($response->getData('data.yesterday.date'));

//支持静态调用
Http::clear();
Http::instance($options);
Http::setHeader([]);
Http::setDomain("https://www.sojson.com");
Http::setMethod('GET');
Http::setParam('city','北京');
$response = Http::curl("open/api/weather/json.shtml");
dump($response->getData());
```

---

### Request 接收请求

```php
$param = \api\tool\Request::instance()->param();

dump($param);

/**
 * More Document :
 *       https://www.kancloud.cn/manual/thinkphp5/158834
 */
```

---

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

---

## License
licensed under the [MIT](https://rem.mit-license.org/)