# INFO


## Init BDD

### security.yml
        main:
            pattern: ^/api
            stateless: false



### AuthTokenAuthenticator
        if ($request->getMethod() === "POST" || $this->httpUtils->checkRequestPath($request, $targetUrl)) {
            return;
            
            

