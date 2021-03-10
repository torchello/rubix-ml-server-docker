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
| `CERT_FILEPATH`             |             | The path to the certificate used to authenticate and encrypt the HTTP channel (you will need to copy the certificate in your `Dockerfile`). |
| `MAX_CONCURRENT_REQUESTS`   | 10          | The maximum number of requests that can be handled concurrently. |
| `SSE_RECONNECT_BUFFER`      | 50          | The maximum number of events to store in the server-sent events (SSE) reconnect buffer. |

### Trained estimator
The image supports previously trained estimators only. By default, it expects a persistent model to be copied as `data.model`.
You can customize the file path by overriding `MODEL_FILEPATH` env var.

## Server middleware
### Access log
`AccessLog` is enabled by default and logs are written to the `access.log` file. 
You can change the access log file path by overriding `ACCESS_LOG_FILEPATH` env var. Add the following line to you `Dockerfile`
if you want to disable the access log middleware:
```
ENV ACCESS_LOG_FILEPATH=
```

### Basic authenticator
Use the following env variables to enable [`BasicAuthenticator` middleware](https://github.com/RubixML/Server#basic-authenticator) (only one username is supported):

| Environment variable           | Default     | Description |
| ------------------------------ | ----------- | ----------- |
| `BASIC_AUTHENTICATOR_USERNAME` |             | Authenticator's username. |
| `BASIC_AUTHENTICATOR_PASSWORD` |             | Authenticator's password. |
| `BASIC_AUTHENTICATOR_REALM`    | auth        | The unique name given to the scope of permissions required for this server. |

Note: you need to define both username and password to enable the middleware.

### Shared token authenticator
Use the following env variables to enable [`SharedTokenAuthenticator` middleware](https://github.com/RubixML/Server#shared-token-authenticator):

| Environment variable                  | Default     | Description |
| ------------------------------------- | ----------- | ----------- |
| `SHARED_TOKEN_AUTHENTICATOR_TOKENS`   |             | Comma separated list of secret keys (bearer tokens) used to authorize requests. |
| `SHARED_TOKEN_AUTHENTICATOR_REALM`    | auth        | The unique name given to the scope of permissions required for this server. |

Example of tokens definition:
```
ENV SHARED_TOKEN_AUTHENTICATOR_TOKENS=secret,another-secret
```

### Trusted clients
Use the following env variable to enable [`TrustedClients` middleware](https://github.com/RubixML/Server#trusted-clients):

| Environment variable    | Default     | Description |
| ----------------------- | ----------- | ----------- |
| `TRUSTED_CLIENTS_IPS`   |             | Comma separated list of trusted client ip addresses. |

Example:
```
ENV TRUSTED_CLIENTS_IPS=127.0.0.1,192.168.4.1,45.63.67.15
```

## Verbose logging
Verbose logging is enabled by default and logs are written to the `verbose.log` file.
Use `VERBOSE_LOG_FILEPATH` env var to change the log file path or disable it (similarly to the access log).

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

### Ingress and basic authentication
When using the server behind the NGINX Ingress Controller with `BasicAuthenticator` enabled you need to  
configure `External Basic Authentication` as described [here](https://kubernetes.github.io/ingress-nginx/examples/auth/external-auth/)
(point `nginx.ingress.kubernetes.io/auth-url` to your server's web UI).

## Support
Feel free to provide feedback or ask questions in the [RubixML Telegram channel](https://t.me/RubixML). 
