# Docker image for Rubix ML Server
Based on `php:7.4-cli` image.

## Supported tags and respective `Dockerfile` links
Image tags follow PHP versions:
* `latest` `7.4` ([7.4/Dockerfile](https://github.com/torchello/rubix-ml-server-docker/blob/master/7.4/Dockerfile))

## Rubix ML component versions
The image uses the following versions of the Rubix ML components (the versions are fixed when image is built):

| Component                                   | Version      |
| ------------------------------------------- | ------------ |
| [Server](https://github.com/RubixML/Server) | `dev-master` |
| [Tensor](https://github.com/RubixML/Tensor) | `^2.0`       |
| [ML](https://github.com/RubixML/ML)         | `^0.2.4`     |

## Tensor extension
Tensor extension is included into the image to boost the performance (thanks to the awesome 
[installer](https://github.com/mlocati/docker-php-extension-installer) by [Michele Locati](https://github.com/mlocati)).

## HTTP Server configuration
You can configure the server by customizing environment variables. 

| Environment variable        | Default     | Description |
| --------------------------- | ----------- | ----------- |
| `HOST`                      | 127.0.0.1   | The host address to bind the server to. Use `0.0.0.0` to bind to all interfaces. |
| `PORT`                      | 8000        | The network port to run the HTTP services on. |
| `CERT_FILEPATH`             | null        | The path to the certificate used to authenticate and encrypt the HTTP channel (you will need to copy the certificate in your `Dockerfile`). |
| `MAX_CONCURRENT_REQUESTS`   | 10          | The maximum number of requests that can be handled concurrently. |
| `SSE_RECONNECT_BUFFER`      | 50          | The maximum number of events to store in the server-sent events (SSE) reconnect buffer. |

### Trained estimator
The image supports previously trained estimators only. By default, it expects a persistent model to be copied as `data.model`.
You can customize the file path by overriding `MODEL_FILEPATH`.

## How to use?
The minimal version of the `Dockerfile` to run the server would be:
```
FROM torchello/rubix-ml-server-docker:latest

COPY my-trained-model.model data.model
```

### Gitlab Auto DevOps
The simplest customized version for [Gitlab Auto DevOps](https://docs.gitlab.com/ee/topics/autodevops/) would look like:
```
FROM torchello/rubix-ml-server-docker:latest

ENV HOST=0.0.0.0
ENV PORT=5000

COPY my-trained-model.model data.model
```

## TODO
This is just a proof of concept. The following configuration capabilities need to be added to make it production ready:
* authenticator configuration
* trusted clients list
* logger configuration

We may consider adding [Event](https://pecl.php.net/package/event) extension by default also.
